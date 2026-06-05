<?php
class Paginas extends Controlador {
    private $planModelo;
    private $solicitudModelo;
    private $cotizacionModelo;
    private $clienteModelo; 
    private $whatsappApi; 

    public function __construct(){
        try {
            $this->planModelo       = $this->modelo('Plan');
            $this->solicitudModelo  = $this->modelo('Solicitud');
            $this->cotizacionModelo = $this->modelo('Cotizacion');
            $this->clienteModelo    = $this->modelo('Cliente'); 
            // ❗ Mantenemos GreenApi para evitar errores fatales
            $this->whatsappApi      = new GreenApi(); 
        } catch (Throwable $e) {
            error_log("Error CRÍTICO en Paginas: " . $e->getMessage());
            die("Error fatal inicializando módulo Paginas.");
        }
    }

    public function index(){
        $planes = $this->planModelo->obtenerPlanesActivos() ?? [];
        $this->vista('paginas/inicio', ['titulo' => 'Internet Fibra Óptica', 'planes' => $planes]);
    }

    public function hogar(){
        $planes = $this->planModelo->obtenerPlanesActivos() ?? [];
        $this->vista('paginas/hogar', ['titulo' => 'Planes Internet Hogar', 'planes' => $planes]);
    }

    public function nosotros(){
        $this->vista('paginas/nosotros', ['titulo' => 'Sobre Nosotros - OPTICCOM']);
    }

    public function empresas(){
        $this->cotizacion(); 
    }

    // --- SOLICITUD B2C CORREGIDA CON VALIDACIÓN FUERTE ---
    public function solicitud($id_plan = 0){
        $planes = $this->planModelo->obtenerPlanesActivos() ?? [];
        $distritos_db = $this->clienteModelo->obtenerDistritos() ?? []; 

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $datos = [
                'planes' => $planes, 
                'distritos_db' => $distritos_db, 
                'plan_seleccionado' => trim($_POST['id_plan_interesado'] ?? ''),
                'nombres' => trim($_POST['nombres'] ?? ''), 
                'apellidos' => trim($_POST['apellidos'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? 'DNI'),
                'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''), 
                'email' => trim($_POST['email'] ?? ''), 
                'departamento' => trim($_POST['departamento'] ?? ''),
                'provincia' => trim($_POST['provincia'] ?? ''), 
                'distrito' => trim($_POST['distrito'] ?? ''),
                'direccion_calle' => trim($_POST['direccion_calle'] ?? ''), 
                'referencia' => trim($_POST['referencia'] ?? ''),
                'location_link' => trim($_POST['location_link'] ?? ''),
                'nombres_error' => '', 'apellidos_error' => '', 'telefono_error' => '', 
                'documento_error' => '', 'distrito_error' => '', 'plan_error' => '',
                'direccion_calle_error' => '', 'referencia_error' => '', 'location_link_error' => '', 'email_error' => ''
            ];

            // 🔹 VALIDACIONES FUERTES EN PHP (BACKEND)
            if (empty($datos['nombres'])) $datos['nombres_error'] = 'Obligatorio.';
            if (empty($datos['apellidos'])) $datos['apellidos_error'] = 'Obligatorio.';
            if (empty($datos['distrito'])) $datos['distrito_error'] = 'Seleccione distrito.';
            if (empty($datos['plan_seleccionado'])) $datos['plan_error'] = 'Seleccione plan.';
            
            // Validar DNI o CE
            if ($datos['tipo_documento'] === 'DNI') {
                if (!preg_match('/^[0-9]{8}$/', $datos['documento_identidad'])) {
                    $datos['documento_error'] = 'El DNI debe tener 8 números.';
                }
            } else {
                if (strlen($datos['documento_identidad']) < 9) {
                    $datos['documento_error'] = 'CE/Pasaporte inválido (Mínimo 9).';
                }
            }

            // Validar Celular (Perú: Empieza con 9 y tiene 9 dígitos)
            if (!preg_match('/^9[0-9]{8}$/', $datos['telefono'])) {
                $datos['telefono_error'] = 'Ingrese un celular válido (9 dígitos, inicia con 9).';
            }

            // Validar Email
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $datos['email_error'] = 'Correo no válido.';
            }

            // Validar Dirección (Mínimo 2 palabras)
            if (str_word_count($datos['direccion_calle']) < 2) {
                $datos['direccion_calle_error'] = 'La dirección es muy corta.';
            }
            if (strlen($datos['referencia']) < 10) {
                $datos['referencia_error'] = 'Detalle mejor la referencia.';
            }

            // Validar Link de Maps
            if (!preg_match('/(maps\.app\.goo\.gl|goo\.gl\/maps|google\.com\/maps)/i', $datos['location_link'])) {
                $datos['location_link_error'] = 'Debe ser un enlace de Google Maps.';
            }

            // Si NO hay NINGÚN error en ningún campo
            if (empty($datos['nombres_error']) && empty($datos['documento_error']) && empty($datos['telefono_error']) && empty($datos['direccion_calle_error']) && empty($datos['location_link_error']) && empty($datos['email_error'])) {
                
                // Guardar en Base de Datos
                if ($this->solicitudModelo->agregarSolicitud($datos)) {
                    
                    // Enviar alerta WhatsApp al personal
                    try {
                        $msg = "🚀 *NUEVA SOLICITUD*\nCliente: {$datos['nombres']} {$datos['apellidos']}\nCel: {$datos['telefono']}\nUbicación: {$datos['distrito']}";
                        $this->whatsappApi->sendText('51941802780', $msg);
                    } catch (Throwable $e) {}

                    header('location: ' . RUTA_URL . '/paginas/exito'); 
                    exit(); 
                } else {
                    flash('mensaje_error', 'Error interno al guardar la solicitud.', 'alert alert-danger');
                }
            }
            $this->vista('paginas/solicitud', $datos);
        } else {
            // Carga inicial del formulario
            $plan_url = null;
            if($id_plan > 0) {
                foreach($planes as $p) { if($p->id_plan == $id_plan) $plan_url = $p->id_plan; }
            }
            $datos = [ 'planes' => $planes, 'distritos_db' => $distritos_db, 'plan_seleccionado' => $plan_url, 'nombres' => '', 'apellidos' => '', 'telefono' => '', 'documento_identidad' => '', 'email' => '', 'departamento' => '', 'provincia' => '', 'distrito' => '', 'direccion_calle' => '', 'referencia' => '', 'location_link' => '', 'nombres_error' => '', 'apellidos_error' => '', 'telefono_error' => '', 'documento_error' => '', 'distrito_error' => '', 'plan_error' => '', 'direccion_calle_error' => '', 'referencia_error' => '', 'location_link_error' => '', 'email_error' => '' ];
            $this->vista('paginas/solicitud', $datos);
        }
    }

    public function cotizacion() {
        $datos = [ 'titulo' => 'Solicitar Cotización', 'razon_social' => '', 'ruc' => '', 'persona_contacto' => '', 'telefono_contacto' => '', 'email_contacto' => '', 'tipo_servicio' => '', 'departamento' => 'Junín', 'provincia' => 'Huancayo', 'distrito' => '', 'direccion_calle' => '', 'referencia' => '', 'location_link' => '', 'mensaje' => '', 'razon_social_error' => '', 'ruc_error' => '', 'contacto_error' => '', 'telefono_error' => '' ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos['razon_social'] = trim($_POST['razon_social'] ?? '');
            $datos['ruc'] = trim($_POST['ruc'] ?? '');
            $datos['persona_contacto'] = trim($_POST['persona_contacto'] ?? '');
            $datos['telefono_contacto'] = trim($_POST['telefono_contacto'] ?? '');
            $datos['email_contacto'] = trim($_POST['email_contacto'] ?? '');
            $datos['tipo_servicio'] = trim($_POST['tipo_servicio'] ?? '');
            $datos['distrito'] = trim($_POST['distrito'] ?? '');
            $datos['direccion_calle'] = trim($_POST['direccion_calle'] ?? '');
            $datos['mensaje'] = trim($_POST['mensaje'] ?? '');

            if (empty($datos['razon_social'])) $datos['razon_social_error'] = 'Obligatorio.';
            if (empty($datos['ruc'])) $datos['ruc_error'] = 'Obligatorio.';

            if (empty($datos['razon_social_error']) && empty($datos['ruc_error'])) {
                if ($this->cotizacionModelo->agregarCotizacion($datos)) { 
                    header('Location: ' . RUTA_URL . '/paginas/exito'); 
                    exit(); 
                }
            }
        }
        $this->vista('paginas/empresas', $datos);
    }

    public function exito(){ $this->vista('paginas/exito', ['titulo' => '¡Solicitud Enviada!']); }
}