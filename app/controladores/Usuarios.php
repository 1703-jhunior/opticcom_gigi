<?php
class Usuarios extends Controlador {
    private $usuarioModelo;
    private $clienteModelo;

    public function __construct() {
        $this->usuarioModelo = $this->modelo('Usuario');
        $this->clienteModelo = $this->modelo('Cliente'); 

        $ruta_actual = $_GET['url'] ?? '';

        if (
            stripos($ruta_actual, 'usuarios/login') !== false ||
            stripos($ruta_actual, 'usuarios/logout') !== false ||
            stripos($ruta_actual, 'usuarios/registrar') !== false 
        ) {
            return; 
        }

        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('location: ' . RUTA_URL . '/portal/login');
            exit();
        }

        if (!hasRole(['Administrador'])) {
            flash('mensaje_error', 'No tiene permisos para gestionar usuarios.', 'alert alert-danger');
            $this->_redirectSegunRol(currentRole());
            exit();
        }
    }

    public function index() {
        $usuarios_admin = $this->usuarioModelo->obtenerUsuarios() ?? [];
        $clientes = $this->clienteModelo->obtenerClientes() ?? []; 
        
        $datos = [
            'titulo' => 'Gestión de Accesos y Usuarios',
            'usuarios_admin' => $usuarios_admin,
            'clientes' => $clientes 
        ];

        $this->vista('usuarios/inicio', $datos);
    }

    public function agregar() {
        // Necesitamos mandar los roles a la vista para el <select>
        $roles = $this->usuarioModelo->obtenerRolesActivos();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'titulo' => 'Agregar Usuario Administrativo',
                'roles' => $roles,
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']), // Ahora recibe un id_rol numérico
                'password' => trim($_POST['password']),
                'confirmar_password' => trim($_POST['confirmar_password']),
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];

            if (empty($datos['nombre'])) $datos['nombre_error'] = 'Nombre obligatorio.';
            if (empty($datos['email'])) $datos['email_error'] = 'Email obligatorio.';
            elseif ($this->usuarioModelo->obtenerUsuarioPorEmail($datos['email']))
                $datos['email_error'] = 'Email ya registrado.';
            if (empty($datos['rol'])) $datos['rol_error'] = 'Rol obligatorio.';
            if (empty($datos['password'])) $datos['password_error'] = 'Contraseña obligatoria.';
            elseif (strlen($datos['password']) < 6) $datos['password_error'] = 'Mínimo 6 caracteres.';
            if ($datos['password'] != $datos['confirmar_password'])
                $datos['password_error'] = 'Las contraseñas no coinciden.';

            if (empty($datos['nombre_error']) && empty($datos['email_error']) &&
                empty($datos['rol_error']) && empty($datos['password_error'])) {

                $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
                if ($this->usuarioModelo->agregarUsuario($datos)) {
                    flash('usuario_mensaje', 'Usuario agregado correctamente.');
                    header('Location: ' . RUTA_URL . '/usuarios');
                    exit();
                } else {
                    flash('mensaje_error', 'Error al guardar usuario.', 'alert alert-danger');
                }
            }
            $this->vista('usuarios/agregar', $datos);
        } else {
            $datos = [
                'titulo' => 'Agregar Usuario Administrativo',
                'roles' => $roles,
                'nombre' => '', 'email' => '', 'rol' => '',
                'password' => '', 'confirmar_password' => '',
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];
            $this->vista('usuarios/agregar', $datos);
        }
    }
    
    public function editar($id_usuario) {
        $usuario = $this->usuarioModelo->obtenerUsuarioPorId($id_usuario);
        $roles = $this->usuarioModelo->obtenerRolesActivos();

        if (!$usuario) {
             flash('mensaje_error', 'Usuario no encontrado.', 'alert alert-danger');
             header('Location: ' . RUTA_URL . '/usuarios'); exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
             $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
             $datos = [
                'titulo' => 'Editar Usuario Administrativo',
                'roles' => $roles,
                'id_usuario' => $id_usuario,
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']),
                'password' => trim($_POST['password']),
                'confirmar_password' => trim($_POST['confirmar_password']),
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];
            
            if (empty($datos['nombre'])) $datos['nombre_error'] = 'Nombre obligatorio.';
            if (empty($datos['email'])) $datos['email_error'] = 'Email obligatorio.';
            if (empty($datos['rol'])) $datos['rol_error'] = 'Rol obligatorio.';
            
            if (!empty($datos['password'])) {
                 if (strlen($datos['password']) < 6) $datos['password_error'] = 'Mínimo 6 caracteres.';
                 if ($datos['password'] != $datos['confirmar_password']) $datos['password_error'] = 'Las contraseñas no coinciden.';
            }

            if (empty($datos['nombre_error']) && empty($datos['email_error']) &&
                empty($datos['rol_error']) && empty($datos['password_error'])) {
                
                if ($this->usuarioModelo->actualizarUsuario($datos)) { 
                    flash('usuario_mensaje', 'Usuario actualizado correctamente.');
                    header('Location: ' . RUTA_URL . '/usuarios');
                    exit();
                } else {
                    flash('mensaje_error', 'Error al actualizar usuario.', 'alert alert-danger');
                }
            }
            $this->vista('usuarios/editar', $datos);

        } else {
             $datos = [
                'titulo' => 'Editar Usuario Administrativo',
                'roles' => $roles,
                'id_usuario' => $id_usuario,
                'nombre' => $usuario->nombre, 
                'email' => $usuario->email, 
                'rol' => $usuario->rol, // Es el id_rol_fk numérico
                'password' => '', 'confirmar_password' => '',
                'nombre_error' => '', 'email_error' => '', 'rol_error' => '', 'password_error' => ''
            ];
            $this->vista('usuarios/editar', $datos);
        }
    }

    public function borrar($id_usuario) {
        if ($id_usuario == $_SESSION['id_usuario']) {
            flash('mensaje_error', 'No puedes eliminar tu propia cuenta de administrador.', 'alert alert-danger');
            header('Location: ' . RUTA_URL . '/usuarios');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = $this->usuarioModelo->obtenerUsuarioPorId($id_usuario);
            if (!$usuario) {
                 flash('mensaje_error', 'Usuario no encontrado.', 'alert alert-danger');
                 header('Location: ' . RUTA_URL . '/usuarios'); exit();
            }

            if ($this->usuarioModelo->borrarUsuario($id_usuario)) {
                flash('usuario_mensaje', 'Usuario eliminado correctamente.');
            } else {
                flash('mensaje_error', 'Error al eliminar el usuario.', 'alert alert-danger');
            }
            header('Location: ' . RUTA_URL . '/usuarios');
            exit();

        } else {
            header('Location: ' . RUTA_URL . '/usuarios');
            exit();
        }
    }

    public function login() {
        if (isLoggedIn()) {
            $this->_redirectSegunRol(currentRole());
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $usuario = trim($_POST['usuario'] ?? ''); 
            $password = trim($_POST['password'] ?? '');

            $datos = ['titulo' => 'Iniciar Sesión', 'usuario' => $usuario, 'password_error' => ''];

            $admin = $this->usuarioModelo->login($usuario, $password);
            
            if ($admin) {
                // Validación para evitar que técnicos entren a la web
                if ($admin->rol === 'Tecnico') {
                    $datos['password_error'] = 'Acceso denegado. Eres técnico, usa la App Móvil.';
                    $this->vista('portal/login', $datos);
                    return;
                }

                $_SESSION['id_usuario'] = $admin->id_usuario;
                $_SESSION['nombre_usuario'] = $admin->nombre;
                $_SESSION['rol_usuario'] = $admin->rol; // Guarda "Administrador" o "Ventas"
                $this->_redirectSegunRol($admin->rol);
            }

            $cliente = $this->clienteModelo->loginCliente($usuario, $password);
            if ($cliente) {
                $_SESSION['id_cliente'] = $cliente->id_cliente;
                $_SESSION['nombre_cliente'] = $cliente->nombre . ' ' . $cliente->apellido;
                $_SESSION['rol_usuario'] = 'Cliente';
                $this->_redirectSegunRol('Cliente');
            }

            $datos['password_error'] = 'Credenciales incorrectas o cuenta inactiva.';
            $this->vista('portal/login', $datos);
        } else {
            $datos = ['titulo' => 'Iniciar Sesión', 'usuario' => '', 'password_error' => ''];
            $this->vista('portal/login', $datos);
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . RUTA_URL . '/portal/login');
        exit();
    }

    public function crearAccesoCliente($id_cliente) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $password = trim($_POST['password'] ?? '');
            $confirmar = trim($_POST['confirmar_password'] ?? '');

            if (empty($password) || strlen($password) < 6 || $password !== $confirmar) {
                flash('mensaje_error', 'Contraseña inválida o no coincide (mínimo 6 caracteres).', 'alert alert-danger');
                header('Location: ' . RUTA_URL . '/usuarios');
                exit();
            }

            if ($this->clienteModelo->crearPasswordCliente((int)$id_cliente, $password)) {
                flash('usuario_mensaje', 'Acceso de cliente actualizado.');
            } else {
                flash('mensaje_error', 'No se pudo crear el acceso.', 'alert alert-danger');
            }

            header('Location: ' . RUTA_URL . '/usuarios');
            exit();
        }
    }

    private function _redirectSegunRol($rol) {
        $rol_normalizado = normalizeRole($rol); 
        
        $redirect_url = match ($rol_normalizado) {
            'Administrador' => RUTA_URL . '/dashboard',
            'Ventas'        => RUTA_URL . '/ventas',
            'Cliente'       => RUTA_URL . '/portal/inicio',
            default         => RUTA_URL . '/portal/login', 
        };
        
        header('Location: ' . $redirect_url);
        exit();
    }
}
?>