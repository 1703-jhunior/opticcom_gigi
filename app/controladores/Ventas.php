<?php
// Ubicación: app/controladores/Ventas.php

class Ventas extends Controlador {
    private $solicitudModelo;
    private $cotizacionModelo;
    private $clienteModelo;
    private $planModelo;
    private $ordenModelo; 
    private $whatsappApi; 

    public function __construct(){
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('location: '. RUTA_URL . '/portal/login'); 
            exit();
        }
        if (!hasRole(['Administrador', 'Ventas', 'Pagos'])) { 
            flash('mensaje_error', 'Acceso no autorizado.', 'alert alert-danger');
            header('location: ' . RUTA_URL . '/portal/inicio');
            exit();
        }

        try {
            $this->solicitudModelo  = $this->modelo('Solicitud');
            $this->cotizacionModelo = $this->modelo('Cotizacion');
            $this->clienteModelo    = $this->modelo('Cliente');
            $this->planModelo       = $this->modelo('Plan');
            $this->ordenModelo      = $this->modelo('OrdenTrabajo'); 
            
            $this->whatsappApi      = new GreenApi(); 
            
        } catch (Throwable $e) {
            error_log("Error CRÍTICO al cargar modelos en Ventas: " . $e->getMessage());
            die("Error fatal inicializando módulo ventas. Revise logs.");
        }
    }

    public function index(){
        $solicitudes  = [];
        $cotizaciones = [];

        try {
            $solicitudes = $this->solicitudModelo->obtenerSolicitudes() ?? [];
        } catch (Throwable $e) { error_log("Error obteniendo solicitudes: ".$e->getMessage()); }

        try {
            $cotizaciones = $this->cotizacionModelo->obtenerCotizaciones() ?? [];
        } catch (Throwable $e) { error_log("Error obteniendo cotizaciones: ".$e->getMessage()); }

        $datos = [
            'titulo'       => 'Gestión de Ventas',
            'solicitudes'  => $solicitudes,
            'cotizaciones' => $cotizaciones
        ];

        $this->vista('ventas/index', $datos);
    }

    /* =========================================================
     * B2C - Solicitudes residenciales
     * ========================================================= */
    public function cambiarEstadoSolicitud($id_solicitud){
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado_solicitud'])) {
            $nuevo_estado    = trim($_POST['estado_solicitud']);
            $estados_validos = ['pendiente', 'contactado', 'con_cobertura', 'sin_cobertura'];

            $sol = $this->solicitudModelo->obtenerSolicitudPorId($id_solicitud);
            if ($sol && strtolower(trim($sol->estado_solicitud)) === 'instalado') {
                flash('mensaje_error', 'Esta solicitud ya fue instalada.', 'alert alert-warning');
                header('location: ' . RUTA_URL . '/ventas'); exit();
            }

            if (in_array($nuevo_estado, $estados_validos)) {
                $this->solicitudModelo->actualizarEstado($id_solicitud, $nuevo_estado);
                flash('venta_mensaje', 'Estado actualizado.');

                if ($nuevo_estado === 'sin_cobertura' && $sol) {
                    try {
                        $msg = "Hola {$sol->nombres}, lamentamos informarte que de momento no contamos con cobertura en tu zona. 😔";
                        $this->whatsappApi->enviarMensaje($sol->telefono, $msg);
                    } catch (Throwable $e) {
                        error_log("Error de WhatsApp Ventas: " . $e->getMessage());
                    }
                }
            }
        }
        header('location: ' . RUTA_URL . '/ventas');
        exit();
    }

    public function convertir($id_solicitud){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $solicitud = $this->solicitudModelo->obtenerSolicitudPorId($id_solicitud);
            
            if(!$solicitud || strtolower(trim($solicitud->estado_solicitud)) !== 'con_cobertura'){
                flash('mensaje_error', 'Solicitud no válida (debe estar "Con Cobertura").', 'alert alert-warning');
                header('location: ' . RUTA_URL . '/ventas'); exit();
            }

            $tipo_solicitud = $solicitud->tipo_solicitud ?? 'Instalacion';
            $id_cliente_existente = $solicitud->id_cliente_fk ?? null;
            $id_plan_nuevo = !empty($solicitud->id_plan_interesado) ? $solicitud->id_plan_interesado : 1; 

            try {
                $plan = $this->planModelo->obtenerPlanPorId($id_plan_nuevo);

                if ($tipo_solicitud === 'Cambio de Plan' && $id_cliente_existente > 0) {
                    $accion_exitosa = $this->clienteModelo->actualizarPlanCliente($id_cliente_existente, $id_plan_nuevo);
                    $mensaje_exito = '¡Cambio de plan aprobado!';

                    if ($accion_exitosa && $plan) {
                        try {
                            $msg = "✅ *CAMBIO DE PLAN APROBADO*\nHola {$solicitud->nombres}, tu cambio al plan {$plan->nombre_plan} ha sido procesado con éxito.";
                            $this->whatsappApi->enviarMensaje($solicitud->telefono, $msg);
                        } catch (Throwable $e) { error_log("Error WS Cambio Plan: ".$e->getMessage()); }
                        
                        $this->solicitudModelo->actualizarEstado($id_solicitud, 'instalado');
                        flash('cliente_mensaje', $mensaje_exito);
                    }
                    header('location: '. RUTA_URL . '/clientes'); 
                    exit();

                } else {
                    // INSTALACIÓN NUEVA
                    
                    // 🔹 BÚSQUEDA INTELIGENTE DE DISTRITO (Lee tu BD real)
                    $distrito_texto = trim($solicitud->distrito ?? '');
                    $provincia_texto = trim($solicitud->provincia ?? '');
                    $departamento_texto = trim($solicitud->departamento ?? '');

                    $id_distrito_final = 1; // Por defecto El Tambo
                    $distritos_db = $this->clienteModelo->obtenerDistritos() ?? [];
                    $distrito_encontrado = false;
                    
                    if (!empty($distrito_texto)) {
                        foreach($distritos_db as $d) {
                            if (strcasecmp(trim($d->distrito), $distrito_texto) == 0) {
                                $id_distrito_final = $d->id_distrito;
                                $distrito_encontrado = true;
                                break;
                            }
                        }
                    }

                    // 🔹 SALVAVIDAS EXTREMO: Si el distrito no está en la DB, lo guardamos en la calle
                    $direccion_final = !empty($solicitud->direccion_calle) ? trim($solicitud->direccion_calle) : 'Sin especificar';
                    
                    if (!$distrito_encontrado) {
                        // Filtra solo los datos que sí existen
                        $ubicacion_extra = array_filter([$distrito_texto, $provincia_texto, $departamento_texto]);
                        if (!empty($ubicacion_extra)) {
                            $direccion_final .= " (Ubic. Web: " . implode(', ', $ubicacion_extra) . ")";
                        }
                    }

                    // 🔹 BLINDAJE DE DNI
                    $dni_final = !empty($solicitud->documento_identidad) ? trim($solicitud->documento_identidad) : 'SOL-' . $id_solicitud;

                    $datos_cliente = [
                        'nombre'              => !empty($solicitud->nombres) ? $solicitud->nombres : 'Cliente',
                        'apellido'            => $solicitud->apellidos ?? '',
                        'telefono'            => !empty($solicitud->telefono) ? $solicitud->telefono : '000000000',
                        'documento_identidad' => $dni_final,
                        'email'               => $solicitud->email ?? '',
                        'distrito'            => $id_distrito_final,
                        'direccion_calle'     => $direccion_final,
                        'referencia'          => $solicitud->referencia ?? '',
                        'location_link'       => $solicitud->location_link ?? '',
                        'id_plan'             => $id_plan_nuevo,
                        'fecha_instalacion'   => date('Y-m-d'), 
                        'estado_servicio'     => 'Pendiente Instalacion', 
                        'detalles'            => 'Convertido desde solicitud #' . $id_solicitud
                    ];
                    
                    // 1. CREAR CLIENTE
                    $accion_exitosa = $this->clienteModelo->agregarCliente($datos_cliente);
                    
                    if ($accion_exitosa) {
                        // 2. ACTUALIZAR SOLICITUD
                        $this->solicitudModelo->actualizarEstado($id_solicitud, 'instalado');
                        
                        // 3. BUSCAR EL ID DEL CLIENTE RECIÉN CREADO
                        $nuevoCliente = $this->clienteModelo->obtenerClientePorDni($datos_cliente['documento_identidad']);
                        
                        if($nuevoCliente) {
                            // 4. GENERAR ORDEN PARA LA "MESA DE DESPACHO"
                            $datos_orden = [
                                'id_cliente'       => $nuevoCliente->id_cliente,
                                'id_tecnico'       => 1, 
                                'id_tipo_orden'    => 1, 
                                'prioridad'        => 'Media',
                                'fecha_programada' => date('Y-m-d'), 
                                'observaciones'    => 'Pendiente de asignar técnico por Operaciones.'
                            ];
                            $this->ordenModelo->crearOrden($datos_orden); 
                        }

                        if ($plan) {
                            try {
                                $msg = "🥳 *¡BIENVENIDO A OPTICCOM!*\nHola {$solicitud->nombres}, tu solicitud para el plan {$plan->nombre_plan} ha sido aceptada. Pronto el área de operaciones se contactará para agendar la instalación.";
                                $this->whatsappApi->enviarMensaje($solicitud->telefono, $msg);
                            } catch (Throwable $e) { error_log("Error WS Instalacion: ".$e->getMessage()); }
                        }

                        flash('cliente_mensaje', '¡Cliente creado y Orden enviada a Despacho!');
                        header('location: '. RUTA_URL . '/clientes'); 
                        exit();
                    } else {
                        flash('mensaje_error', 'Fallo al guardar: Es posible que este DNI ya esté registrado en la BD de clientes.', 'alert alert-danger');
                    }
                }

            } catch (Throwable $e) {
                 error_log("Error al convertir: " . $e->getMessage());
                 flash('mensaje_error', 'Error fatal al procesar la conversión.', 'alert alert-danger');
            }
            header('location: ' . RUTA_URL . '/ventas');
            exit();
        }
    }

    /* =========================================================
     * B2B - Cotizaciones de empresas
     * ========================================================= */
    public function cambiarEstadoCotizacion($id_cotizacion){
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['estado_cotizacion'])) {
            $nuevo_estado    = trim($_POST['estado_cotizacion']);
            $estados_validos = ['pendiente', 'contactado', 'enviada', 'ganada', 'perdida'];

            $cot = $this->cotizacionModelo->obtenerCotizacionPorId($id_cotizacion);
            
            if (in_array($nuevo_estado, $estados_validos)) {
                $this->cotizacionModelo->actualizarEstado($id_cotizacion, $nuevo_estado);
                flash('venta_mensaje', 'Estado de cotización actualizado.');
                
                if ($nuevo_estado === 'perdida' && $cot) {
                    try {
                        $msg = "Hola {$cot->razon_social}, gracias por considerar a OPTICCOM. Lamentamos no poder atender su solicitud en esta ocasión.";
                        $this->whatsappApi->enviarMensaje($cot->telefono_contacto, $msg);
                    } catch (Throwable $e) { error_log("Error WS Cotización Perdida: ".$e->getMessage()); }
                }
            }
        }
        header('location: ' . RUTA_URL . '/ventas');
        exit();
    }

    public function convertirCotizacion($id_cotizacion){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cotizacion = $this->cotizacionModelo->obtenerCotizacionPorId($id_cotizacion);

            if(!$cotizacion || strtolower(trim($cotizacion->estado_cotizacion)) !== 'ganada'){
                flash('mensaje_error', 'Cotización no válida para conversión.', 'alert alert-warning');
                header('location: ' . RUTA_URL . '/ventas'); exit();
            }

            // 🔹 BÚSQUEDA INTELIGENTE DE DISTRITO B2B
            $distrito_texto = trim($cotizacion->distrito ?? '');
            $provincia_texto = trim($cotizacion->provincia ?? '');
            $departamento_texto = trim($cotizacion->departamento ?? '');
            
            $id_distrito_final = 1; 
            $distritos_db = $this->clienteModelo->obtenerDistritos() ?? [];
            $distrito_encontrado = false;
            
            if (!empty($distrito_texto)) {
                foreach($distritos_db as $d) {
                    if (strcasecmp(trim($d->distrito), $distrito_texto) == 0) {
                        $id_distrito_final = $d->id_distrito;
                        $distrito_encontrado = true;
                        break;
                    }
                }
            }

            // 🔹 SALVAVIDAS EXTREMO B2B
            $direccion_final = !empty($cotizacion->direccion_calle) ? trim($cotizacion->direccion_calle) : 'Sin especificar';
            if (!$distrito_encontrado) {
                $ubicacion_extra = array_filter([$distrito_texto, $provincia_texto, $departamento_texto]);
                if (!empty($ubicacion_extra)) {
                    $direccion_final .= " (Ubic. Web: " . implode(', ', $ubicacion_extra) . ")";
                }
            }

            // 🔹 BLINDAJE DE RUC
            $ruc_final = !empty($cotizacion->ruc) ? trim($cotizacion->ruc) : 'COT-' . $id_cotizacion;

            $datos_cliente = [
                'nombre'              => !empty($cotizacion->razon_social) ? $cotizacion->razon_social : 'Empresa Sin Nombre',
                'apellido'            => '', 
                'telefono'            => !empty($cotizacion->telefono_contacto) ? $cotizacion->telefono_contacto : '000000000',
                'documento_identidad' => $ruc_final,
                'email'               => $cotizacion->email_contacto ?? '',
                'distrito'            => $id_distrito_final,
                'direccion_calle'     => $direccion_final,
                'referencia'          => $cotizacion->referencia ?? '',
                'location_link'       => $cotizacion->location_link ?? '',
                'id_plan'             => 1, 
                'fecha_instalacion'   => date('Y-m-d'),
                'estado_servicio'     => 'Pendiente Instalacion',
                'detalles'            => 'Empresa convertida desde cotización #' . $id_cotizacion
            ];

            // 1. CREAR CLIENTE B2B
            if ($this->clienteModelo->agregarCliente($datos_cliente)) {
                $this->cotizacionModelo->actualizarEstado($id_cotizacion, 'instalado');
                
                // 2. BUSCAR EL ID DEL CLIENTE RECIÉN CREADO
                $nuevoCliente = $this->clienteModelo->obtenerClientePorDni($datos_cliente['documento_identidad']);
                
                if($nuevoCliente) {
                    // 3. GENERAR ORDEN PARA LA "MESA DE DESPACHO"
                    $datos_orden = [
                        'id_cliente'       => $nuevoCliente->id_cliente,
                        'id_tecnico'       => 1, 
                        'id_tipo_orden'    => 1, 
                        'prioridad'        => 'Alta', 
                        'fecha_programada' => date('Y-m-d'),
                        'observaciones'    => 'Instalación EMPRESARIAL pendiente de asignación.'
                    ];
                    $this->ordenModelo->crearOrden($datos_orden); 
                }

                try {
                    $msg = "🏢 *SERVICIO EMPRESARIAL APROBADO*\nEstimados {$cotizacion->razon_social}, su servicio ha sido aprobado. Nuestro equipo de operaciones se contactará pronto para agendar la instalación.";
                    $this->whatsappApi->enviarMensaje($cotizacion->telefono_contacto, $msg);
                } catch (Throwable $e) { error_log("Error WS Empresa: ".$e->getMessage()); }

                flash('cliente_mensaje', '¡Empresa convertida a Cliente y Orden enviada a Despacho!');
                header('location: ' . RUTA_URL . '/clientes');
                exit();
            } else {
                flash('mensaje_error', 'Fallo al guardar: Es posible que este RUC ya esté registrado.', 'alert alert-danger');
                header('location: ' . RUTA_URL . '/ventas');
                exit();
            }
        }
    }
}