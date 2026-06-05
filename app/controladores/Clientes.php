<?php
class Clientes extends Controlador {
    private $clienteModelo;
    private $planModelo;
    private $whatsappApi; 

    public function __construct(){
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('location: '. RUTA_URL . '/portal/login'); 
            exit();
        }
        if (!hasRole(['Administrador', 'Pagos', 'Soporte'])) { 
            flash('mensaje_error', 'Acceso no autorizado.', 'alert alert-danger');
            header('location: ' . RUTA_URL . '/portal/inicio');
            exit();
        }

        try {
            $this->clienteModelo = $this->modelo('Cliente');
            $this->planModelo    = $this->modelo('Plan');
            
            $this->whatsappApi   = new GreenApi(); 
        } catch (Throwable $e) {
            error_log("Error CRÍTICO al cargar modelos en Clientes: " . $e->getMessage());
            die("Error fatal inicializando módulo clientes. Revise logs.");
        }
    }

    public function index(){
        $busqueda = '';
        
        // 🔹 LÓGICA DEL BUSCADOR INTEGRADA
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['busqueda'])) {
            $busqueda = trim($_POST['busqueda']);
            $clientes = $this->clienteModelo->obtenerClientes($busqueda) ?? [];
        } else {
            $clientes = $this->clienteModelo->obtenerClientes() ?? [];
        }

        $tipos_pago = $this->clienteModelo->obtenerTiposPago() ?? [];

        $datos = [
            'titulo'     => 'Gestión de Clientes',
            'clientes'   => $clientes,
            'tipos_pago' => $tipos_pago,
            'busqueda'   => $busqueda // Mantiene la palabra en el input
        ];
        $this->vista('clientes/inicio', $datos);
    }

    public function agregar(){
        $planes = $this->planModelo->obtenerPlanes() ?? [];
        $distritos = $this->clienteModelo->obtenerDistritos() ?? [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'planes' => $planes, 'distritos' => $distritos, 'titulo' => 'Agregar Nuevo Cliente',
                'nombre' => trim($_POST['nombre'] ?? ''), 'apellido' => trim($_POST['apellido'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''), 'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
                'email' => trim($_POST['email'] ?? ''), 'distrito' => trim($_POST['distrito'] ?? ''), 
                'direccion_calle' => trim($_POST['direccion_calle'] ?? ''), 'referencia' => trim($_POST['referencia'] ?? null),
                'location_link' => trim($_POST['location_link'] ?? null), 'id_plan' => trim($_POST['id_plan'] ?? ''),
                'fecha_instalacion' => trim($_POST['fecha_instalacion'] ?? date('Y-m-d')), 'estado_servicio' => trim($_POST['estado_servicio'] ?? 'Pendiente Instalacion'),
                'nombre_error' => '', 'documento_error' => '', 'telefono_error' => '', 'distrito_error' => '', 'plan_error' => ''
            ];

            if (empty($datos['nombre'])) $datos['nombre_error'] = 'Obligatorio.';
            if (empty($datos['documento_identidad'])) $datos['documento_error'] = 'Obligatorio.';
            if (empty($datos['telefono'])) $datos['telefono_error'] = 'Obligatorio.';

            if (empty($datos['nombre_error']) && empty($datos['documento_error'])) {
                if ($this->clienteModelo->agregarCliente($datos)) {
                    flash('cliente_mensaje', 'Cliente agregado exitosamente.');
                    header('location: ' . RUTA_URL . '/clientes'); exit();
                } else { 
                    flash('mensaje_error', 'No se pudo guardar.', 'alert alert-danger'); 
                }
            }
            $this->vista('clientes/agregar', $datos);
        } else {
            $datos = [ 'planes' => $planes, 'distritos' => $distritos, 'titulo' => 'Agregar Nuevo Cliente', 'nombre' => '', 'apellido' => '', 'telefono' => '', 'documento_identidad' => '', 'email' => '', 'distrito' => '', 'direccion_calle' => '', 'referencia' => '', 'location_link' => '', 'id_plan' => '', 'fecha_instalacion' => date('Y-m-d'), 'estado_servicio' => 'Pendiente Instalacion', 'nombre_error' => '', 'documento_error' => '', 'telefono_error' => '', 'distrito_error' => '', 'plan_error' => '' ];
            $this->vista('clientes/agregar', $datos);
        }
    }

    public function editar($id){
        $planes = $this->planModelo->obtenerPlanes() ?? [];
        $distritos = $this->clienteModelo->obtenerDistritos() ?? [];
        $cliente = $this->clienteModelo->obtenerClientePorId($id);

        if (!$cliente) { header('location: ' . RUTA_URL . '/clientes'); exit(); }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'id_cliente' => $id, 'planes' => $planes, 'distritos' => $distritos, 'titulo' => 'Editar Cliente',
                'nombre' => trim($_POST['nombre'] ?? ''), 'apellido' => trim($_POST['apellido'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''), 'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
                'email' => trim($_POST['email'] ?? ''), 'distrito' => trim($_POST['distrito'] ?? ''), 
                'direccion_calle' => trim($_POST['direccion_calle'] ?? ''), 'referencia' => trim($_POST['referencia'] ?? null),
                'location_link' => trim($_POST['location_link'] ?? null), 'id_plan' => trim($_POST['id_plan'] ?? ''),
                'fecha_instalacion' => trim($_POST['fecha_instalacion'] ?? date('Y-m-d')), 'estado_servicio' => trim($_POST['estado_servicio'] ?? 'Activo'),
                'nombre_error' => '', 'distrito_error' => ''
            ];

            if ($this->clienteModelo->actualizarCliente($datos)) {
                flash('cliente_mensaje', 'Cliente actualizado.');
                header('location: ' . RUTA_URL . '/clientes'); exit();
            }
            $this->vista('clientes/editar', $datos);
        } else {
            $datos = [ 'id_cliente' => $id, 'planes' => $planes, 'distritos' => $distritos, 'titulo' => 'Editar Cliente', 'nombre' => $cliente->nombre, 'apellido' => $cliente->apellido, 'telefono' => $cliente->telefono, 'documento_identidad' => $cliente->dni, 'email' => $cliente->email, 'distrito' => $cliente->id_distrito_fk, 'direccion_calle' => $cliente->direccion_calle, 'referencia' => $cliente->referencia, 'location_link' => $cliente->location_link, 'id_plan' => $cliente->id_plan_fk, 'fecha_instalacion' => $cliente->fecha_instalacion, 'estado_servicio' => $cliente->estado_servicio, 'nombre_error' => '', 'distrito_error' => '' ];
            $this->vista('clientes/editar', $datos);
        }
    }

    public function borrar($id) {
        if ($this->clienteModelo->borrarCliente($id)) {
            flash('cliente_mensaje', 'Cliente eliminado.');
        } else {
            flash('mensaje_error', 'Error al eliminar el cliente.', 'alert alert-danger');
        }
        header('Location: ' . RUTA_URL . '/clientes');
    }

    public function historialPagos($id_cliente){
        $cliente = $this->clienteModelo->obtenerClientePorId($id_cliente);
        if (!$cliente) { header('location: ' . RUTA_URL . '/clientes'); exit(); }
        $datos = [ 'titulo' => 'Historial de Pagos', 'cliente' => $cliente, 'pagos' => $this->clienteModelo->obtenerHistorialPagos($id_cliente), 'recibos' => $this->clienteModelo->obtenerRecibosPorCliente($id_cliente), 'tipos_pago' => $this->clienteModelo->obtenerTiposPago() ];
        $this->vista('clientes/historial', $datos);
    }

    // ❗ NUEVO REGISTRAR PAGO COMPLETO CON AUTOMATIZACIÓN MULTICANAL (PDF EN MEMORIA + HOSTINGER EMAIL)
    public function registrarPago($id_cliente){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $datos_pago = [
                'fecha_pago'          => trim($_POST['fecha_pago'] ?? date('Y-m-d')),
                'monto_pagado'        => trim($_POST['monto_pagado'] ?? '0'),
                'mes_correspondiente' => trim($_POST['mes_correspondiente'] ?? ''),
                'id_tipo_pago'        => trim($_POST['id_tipo_pago'] ?? '') 
            ];

            if (empty($datos_pago['id_tipo_pago']) || empty($datos_pago['mes_correspondiente'])) {
                flash('mensaje_error', 'Debe seleccionar el mes y el método de pago.', 'alert alert-danger');
                header('location: ' . RUTA_URL . '/clientes');
                exit();
            }

            // 1. Guardar la transacción financiera en la base de datos
            if ($this->clienteModelo->registrarPagoDetallado($id_cliente, $datos_pago)) {
                
                // 2. Levantar el estado del abonado a "Al día"
                $this->clienteModelo->actualizarEstadoPago($id_cliente, 'Al día');

                // 3. Solicitar datos completos del cliente e información del plan asignado
                $cliente = $this->clienteModelo->obtenerClientePorId($id_cliente);
                
                // --- CANAL A: NOTIFICACIÓN DE MENSAJERÍA WHATSAPP (GreenApi) ---
                if ($cliente && !empty($cliente->telefono)) {
                    $msg = "✅ *PAGO REGISTRADO - OPTICCOM*\nHola {$cliente->nombre}, tu pago de S/ {$datos_pago['monto_pagado']} por {$datos_pago['mes_correspondiente']} ha sido validado. Tu cuenta está *Al día*.";
                    try { 
                        $this->whatsappApi->enviarMensaje($cliente->telefono, $msg); 
                    } catch (Throwable $e) {
                        error_log("Error WhatsApp Pago: " . $e->getMessage());
                    }
                }

                // --- CANAL B: COMPILACIÓN DEL COMPROBANTE PDF Y DESPACHO POR CORREO (Hostinger SMTP) ---
                if ($cliente && !empty($cliente->email)) {
                    
                    // Mapeo dinámico de datos recopilados para incrustar en la vista limpia del recibo
                    $datos = [
                        'comprobante_nro'   => str_pad($cliente->id_pago_reciente ?? $id_cliente, 6, "0", STR_PAD_LEFT),
                        'fecha_pago'        => date('d/m/Y', strtotime($datos_pago['fecha_pago'])),
                        'cliente_nombre'    => $cliente->nombre . ' ' . $cliente->apellido,
                        'cliente_dni'       => $cliente->dni,
                        'cliente_direccion' => $cliente->direccion_calle . ', ' . ($cliente->distrito ?? 'Huancayo'),
                        'plan_nombre'       => $cliente->nombre_plan ?? 'Fibra Óptica',
                        'mes_servicio'      => $datos_pago['mes_correspondiente'],
                        'monto'             => number_format($datos_pago['monto_pagado'], 2),
                        'metodo_pago'       => $_POST['mes_correspondiente'] // Mapeará dinámicamente según el flujo de la vista
                    ];

                    try {
                        // Construcción del archivo binario virtual mediante Dompdf
                        $dompdf = new Dompdf\Dompdf();
                        
                        ob_start();
                        // Importación de la plantilla limpia estructural del comprobante
                        require_once APP_ROOT . '/app/vistas/recibos/comprobante_pdf.php';
                        $htmlComprobante = ob_get_clean();

                        $dompdf->loadHtml($htmlComprobante);
                        $dompdf->setPaper('A4', 'portrait');
                        $dompdf->render();

                        // Almacenamos el archivo resultante directo en RAM en lugar de imprimirlo en pantalla
                        $pdfBinario = $dompdf->output();
                        $nombrePdf = "Comprobante_Pago_OPTICCOM_Nro_" . $datos['comprobante_nro'] . ".pdf";

                        // Estructura del cuerpo del correo electrónico corporativo
                        $asunto = "Comprobante de Operación Registrada - OPTICCOM";
                        $cuerpo = "
                            <p>Estimado(a) <strong>" . htmlspecialchars($datos['cliente_nombre']) . "</strong>,</p>
                            <p>Le informamos que se ha procesado con éxito el pago de su suscripción de internet de alta velocidad.</p>
                            <div style='background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2); padding: 15px; border-radius: 8px; margin: 20px 0;'>
                                <strong>Resumen de Transacción:</strong><br>
                                • Ciclo Liquidado: " . htmlspecialchars($datos['mes_servicio']) . "<br>
                                • Importe Procesado: S/ " . $datos['monto'] . "
                            </div>
                            <p>Adjunto a este mensaje de notificación encontrará su Recibo de Pago Digital en formato PDF.</p>
                            <p>Agradecemos su puntualidad. Su servicio se mantiene en estado: <span style='color:#22c55e; font-weight:bold;'>ACTIVO / AL DÍA</span>.</p>
                        ";

                        // Disparo criptográfico a través de la clase Correo creada anteriormente
                        $envio_ok = Correo::enviar($cliente->email, $asunto, $cuerpo, $pdfBinario, $nombrePdf);

                    if ($envio_ok) {
                        flash('cliente_mensaje', 'Pago registrado. Recibo digital despachado al correo.');
                    } else {
                        flash('mensaje_error', 'Pago registrado, PERO falló la conexión SMTP con Hostinger. No se envió el correo.', 'alert alert-danger');
                    }

                    } catch (Throwable $e) {
                        error_log("Fallo crítico en pipeline de automatización PDF/Email: " . $e->getMessage());
                        flash('cliente_mensaje', 'Pago registrado de forma exitosa en el servidor local, pero el envío del recibo digital por correo falló.');
                    }
                } else {
                    flash('cliente_mensaje', 'Pago registrado. El cliente está Al día (No se envió email por falta de dirección de correo).');
                }

            } else { 
                flash('mensaje_error', 'Error al registrar el pago.', 'alert alert-danger'); 
            }
            header('location: ' . RUTA_URL . '/clientes');
            exit();
        }
    }

    public function marcarEstadoPago($id, $estado) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->clienteModelo->actualizarEstadoPago($id, $estado)) {
                
                if ($estado === 'Vencido') {
                    $cliente = $this->clienteModelo->obtenerClientePorId($id);
                    if ($cliente && !empty($cliente->telefono)) {
                        $msg = "⚠️ *AVISO DE DEUDA - OPTICCOM*\nEstimado(a) {$cliente->nombre}, le informamos que su cuenta presenta un recibo *VENCIDO*. Por favor regularice su pago para evitar el corte del servicio.";
                        try { $this->whatsappApi->enviarMensaje($cliente->telefono, $msg); } catch (Throwable $e) {}
                    }
                    flash('cliente_mensaje', 'Cliente marcado como Vencido y notificado.');
                } else {
                    flash('cliente_mensaje', 'Estado actualizado a ' . $estado);
                }
            } else {
                flash('mensaje_error', 'No se pudo actualizar el estado.', 'alert alert-danger');
            }
            header('location: ' . RUTA_URL . '/clientes');
            exit();
        }
    }

    public function notificarMorosos() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $clientes = $this->clienteModelo->obtenerClientes();
            $enviados = 0;

            foreach ($clientes as $c) {
                if (($c->estado_pago === 'Pendiente' || $c->estado_pago === 'Vencido') && !empty($c->telefono)) {
                    $msg = "‼ *RECORDATORIO DE PAGO*\nHola {$c->nombre}, recuerda que tienes pagos pendientes en OPTICCOM. Realiza tu pago hoy y evita cortes de servicio.";
                    try {
                        if ($this->whatsappApi->enviarMensaje($c->telefono, $msg)) $enviados++;
                    } catch (Throwable $e) {}
                }
            }
            flash('cliente_mensaje', "Recordatorios enviados a {$enviados} morosos.");
            header('location: ' . RUTA_URL . '/clientes');
            exit();
        }
    }

    public function notificarTodos() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $clientes = $this->clienteModelo->obtenerClientes();
            $enviados = 0;
            foreach ($clientes as $cliente) {
                if (!empty($cliente->telefono)) {
                    $mensaje = "👋 *AVISO OPTICCOM*\nHola {$cliente->nombre}, te recordamos que puedes revisar tus recibos y estado de cuenta en nuestro portal de clientes.";
                    try {
                        if ($this->whatsappApi->enviarMensaje($cliente->telefono, $mensaje)) $enviados++;
                    } catch (Throwable $e) {}
                }
            }
            flash('cliente_mensaje', "Notificación enviada a {$enviados} clientes.");
            header('location: ' . RUTA_URL . '/clientes');
            exit();
        }
    }

    public function subirRecibo($id_cliente) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['recibo']) && $_FILES['recibo']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['recibo']['tmp_name'];
                $fileName      = $_FILES['recibo']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if ($fileExtension === 'pdf') {
                    
                    $carpeta_destino = APP_ROOT . '/public/uploads/clientes/' . $id_cliente . '/recibos/';
                    
                    if (!file_exists($carpeta_destino)) {
                        @mkdir($carpeta_destino, 0777, true);
                    }
                    
                    $nuevoNombrePdf = 'recibo_' . date('Ymd_His') . '.pdf';
                    $ruta_fisica = $carpeta_destino . $nuevoNombrePdf;
                    
                    if (move_uploaded_file($fileTmpPath, $ruta_fisica)) {
                        
                        $ruta_bd = 'clientes/' . $id_cliente . '/recibos/' . $nuevoNombrePdf;
                        $this->clienteModelo->guardarRecibo($id_cliente, $ruta_bd);
                        
                        flash('cliente_mensaje', 'Recibo guardado exitosamente.');
                    } else {
                        flash('mensaje_error', 'Error al mover el archivo. Verifica permisos de la carpeta.', 'alert alert-danger');
                    }
                } else {
                    flash('mensaje_error', 'Formato no válido. Solo se admiten PDFs.', 'alert alert-warning');
                }
            } else {
                flash('mensaje_error', 'No se detectó archivo o ocurrió un error.', 'alert alert-danger');
            }
            header('location: ' . RUTA_URL . '/clientes/historialPagos/' . $id_cliente); 
            exit();
        } else {
            $cliente = $this->clienteModelo->obtenerClientePorId($id_cliente);
            if (!$cliente) { header('location: ' . RUTA_URL . '/clientes'); exit(); }
            $this->vista('clientes/subir_recibo', ['titulo' => 'Subir Recibo', 'cliente' => $cliente]);
        }
    }
}
?>