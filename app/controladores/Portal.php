<?php
class Portal extends Controlador {
    private $usuarioModelo;
    private $clienteModelo;
    private $planModelo; 
    private $solicitudModelo; 

    public function __construct(){
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->clienteModelo = $this->modelo('Cliente');
        $this->planModelo = $this->modelo('Plan'); 
        $this->solicitudModelo = $this->modelo('Solicitud'); 
    }
    
    public function index(){
        if (isLoggedIn()) {
            if (currentRole() === 'Cliente') {
                header('Location: ' . RUTA_URL . '/portal/inicio');
                exit();
            } else {
                header('Location: ' . RUTA_URL . '/dashboard');
                exit();
            }
        } else {
            header('Location: ' . RUTA_URL . '/portal/login');
            exit();
        }
    }

    public function login(){
        if (isLoggedIn()) {
            if (currentRole() === 'Cliente') {
                header('Location: ' . RUTA_URL . '/portal/inicio'); exit();
            } else {
                header('Location: ' . RUTA_URL . '/dashboard'); exit();
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $usuario  = trim($_POST['usuario'] ?? '');  
            $password = trim($_POST['password'] ?? '');

            if ($usuario === '' || $password === '') {
                flash('mensaje_error','Complete usuario y contraseña.','alert alert-danger');
                header('Location: ' . RUTA_URL . '/portal/login'); exit();
            }

            if (strpos($usuario, '@') !== false) {
                $admin = $this->usuarioModelo->login($usuario, $password);
                if ($admin) {
                    $_SESSION['id_usuario']     = $admin->id_usuario;
                    $_SESSION['nombre_usuario'] = $admin->nombre;
                    $_SESSION['rol_usuario']    = normalizeRole($admin->rol);
                    header('Location: ' . RUTA_URL . '/dashboard'); exit();
                }
            }

            $cliente = $this->clienteModelo->loginCliente($usuario, $password);
            if ($cliente) {
                $_SESSION['id_cliente']   = $cliente->id_cliente;
                $_SESSION['nombre_cliente']= trim($cliente->nombre.' '.$cliente->apellido);
                $_SESSION['rol_usuario']   = 'Cliente';
                header('Location: ' . RUTA_URL . '/portal/inicio'); exit();
            }

            flash('mensaje_error','Credenciales inválidas.','alert alert-danger');
            header('Location: ' . RUTA_URL . '/portal/login'); exit();
        }

        $this->vista('portal/login', ['titulo' => 'Mi Portal']);
    }

    public function inicio(){
        if (!isLoggedIn() || currentRole() !== 'Cliente') {
            header('Location: ' . RUTA_URL . '/portal/login');
            exit();
        }

        $id_cliente = $_SESSION['id_cliente'] ?? null;
        $cliente = $this->clienteModelo->obtenerClientePorId($id_cliente);
        
        $plan = ($cliente) ? $this->clienteModelo->obtenerPlanPorCliente($id_cliente) : null;
        $pagos = ($cliente) ? $this->clienteModelo->obtenerPagosPorCliente($id_cliente, 5) : [];
        $recibos = ($cliente) ? $this->clienteModelo->obtenerRecibosPorCliente($id_cliente) : [];

        $datos = [
            'titulo' => 'Mi Portal',
            'cliente' => $cliente,
            'plan' => $plan,
            'pagos' => $pagos,
            'recibos' => $recibos
        ];

        $this->vista('portal/inicio', $datos);
    }

    public function cambiarPlan() {
        if (!isLoggedIn() || currentRole() !== 'Cliente') {
            header('Location: ' . RUTA_URL . '/portal/login');
            exit();
        }

        $id_cliente = $_SESSION['id_cliente'];
        $cliente = $this->clienteModelo->obtenerClientePorId($id_cliente);
        $plan_actual = $this->clienteModelo->obtenerPlanPorCliente($id_cliente);
        $planes_activos = $this->planModelo->obtenerPlanesActivos() ?? [];
        
        $id_plan_actual = $cliente ? $cliente->id_plan_fk : null; 
        $planes_disponibles = [];
        
        foreach ($planes_activos as $plan) {
            if ($plan->id_plan != $id_plan_actual) {
                $planes_disponibles[] = $plan;
            }
        }

        $datos_vista = [
            'titulo' => 'Cambiar de Plan',
            'plan_actual' => $plan_actual,
            'planes_disponibles' => $planes_disponibles,
            'plan_error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $id_plan_nuevo = trim($_POST['id_plan_nuevo'] ?? '');

            if (empty($id_plan_nuevo)) {
                flash('mensaje_error', 'Debe seleccionar un nuevo plan.', 'alert alert-danger');
                $this->vista('portal/cambiar-plan', $datos_vista);
                return;
            }

            // 🔹 BLINDAJE DE DATOS: Usamos ?? para asegurar que nada llegue nulo a la BD
            $datos_solicitud = [
                'nombres'             => $cliente->nombre ?? 'Cliente',
                'apellidos'           => $cliente->apellido ?? '',
                'telefono'            => $cliente->telefono ?? '000000000',
                'documento_identidad' => $cliente->dni ?? '00000000',
                'email'               => $cliente->email ?? '',
                'departamento'        => $cliente->departamento ?? 'No especificado',
                'provincia'           => $cliente->provincia ?? 'No especificado',
                'distrito'            => $cliente->distrito ?? 'No especificado',
                'direccion_calle'     => $cliente->direccion_calle ?? 'Revisar en Sistema',
                'referencia'          => $cliente->referencia ?? 'Revisar en Sistema',
                'location_link'       => $cliente->location_link ?? '',
                'id_plan_interesado'  => $id_plan_nuevo,
                'id_cliente_fk'       => $id_cliente,
                'tipo_solicitud'      => 'Cambio de Plan'
            ];

            try {
                if ($this->solicitudModelo->agregarSolicitud($datos_solicitud)) {
                    flash('plan_mensaje', 'Solicitud enviada. Nos comunicaremos pronto.', 'alert alert-success');
                    header('Location: ' . RUTA_URL . '/portal/inicio');
                    exit();
                } else {
                    flash('mensaje_error', 'No se pudo enviar la solicitud en la Base de Datos.', 'alert alert-danger');
                    $this->vista('portal/cambiar-plan', $datos_vista);
                }
            } catch (Throwable $e) {
                flash('mensaje_error', 'Error crítico al enviar.', 'alert alert-danger');
                $this->vista('portal/cambiar-plan', $datos_vista);
            }
        } else {
            $this->vista('portal/cambiar-plan', $datos_vista);
        }
    }

    public function logout(){
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
        header('Location: ' . RUTA_URL . '/portal/login'); 
        exit();
    }
}
?>