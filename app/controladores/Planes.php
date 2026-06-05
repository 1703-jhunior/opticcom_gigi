<?php
class Planes extends Controlador {
    private $planModelo;

    public function __construct(){
        if (!isLoggedIn()) {
            header('location: ' . RUTA_URL . '/usuarios/login');
            exit();
        }

        // Admin, Ventas y Pagos pueden ver
        if (!hasRole(['Administrador', 'Ventas', 'Pagos'])) {
            flash('mensaje_error', 'No tienes permiso para gestionar los planes de servicio.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/dashboard');
            exit();
        }

        $this->planModelo = $this->modelo('Plan');
    }

    public function index(){
        $planes = $this->planModelo->obtenerPlanes();
        $datos = [
            'titulo' => 'Gestión de Planes de Servicio',
            'planes' => $planes
        ];
        $this->vista('planes/inicio', $datos);
    }

    public function agregar(){
        if (!hasRole(['Administrador', 'Ventas'])) {
            flash('mensaje_error', 'No tienes permiso para crear planes.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'titulo' => 'Crear Nuevo Plan',
                'nombre_plan' => trim($_POST['nombre_plan']),
                'velocidad' => trim($_POST['velocidad']),
                'precio_mensual' => trim($_POST['precio_mensual']),
                'descripcion' => trim($_POST['descripcion']),
                'nombre_error' => '',
                'velocidad_error' => '',
                'precio_error' => ''
            ];

            if (empty($datos['nombre_plan'])) $datos['nombre_error'] = 'El nombre del plan es obligatorio.';
            if (empty($datos['velocidad'])) $datos['velocidad_error'] = 'La velocidad es obligatoria.';
            if (empty($datos['precio_mensual'])) {
                $datos['precio_error'] = 'El precio es obligatorio.';
            } elseif (!is_numeric($datos['precio_mensual'])) {
                $datos['precio_error'] = 'El precio debe ser un número.';
            }

            if (empty($datos['nombre_error']) && empty($datos['velocidad_error']) && empty($datos['precio_error'])) {
                if ($this->planModelo->agregarPlan($datos)) {
                    flash('plan_mensaje', 'Plan creado exitosamente.');
                    header('location: ' . RUTA_URL . '/planes');
                    exit();
                } else {
                    flash('mensaje_error', 'Error al crear el plan.', 'alert alert-danger');
                    $this->vista('planes/agregar', $datos);
                }
            } else {
                $this->vista('planes/agregar', $datos);
            }
        } else {
            $datos = [
                'titulo' => 'Crear Nuevo Plan',
                'nombre_plan' => '',
                'velocidad' => '',
                'precio_mensual' => '',
                'descripcion' => '',
                'nombre_error' => '',
                'velocidad_error' => '',
                'precio_error' => ''
            ];
            $this->vista('planes/agregar', $datos);
        }
    }

    public function editar($id_plan){
        if (!hasRole(['Administrador', 'Ventas'])) {
            flash('mensaje_error', 'No tienes permiso para editar planes.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        $plan = $this->planModelo->obtenerPlanPorId($id_plan);
        if (!$plan) {
            flash('mensaje_error', 'Plan no encontrado.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $datos = [
                'id_plan' => $id_plan,
                'titulo' => 'Editar Plan',
                'nombre_plan' => trim($_POST['nombre_plan']),
                'velocidad' => trim($_POST['velocidad']),
                'precio_mensual' => trim($_POST['precio_mensual']),
                'descripcion' => trim($_POST['descripcion']),
                'nombre_error' => '',
                'velocidad_error' => '',
                'precio_error' => ''
            ];

            if (empty($datos['nombre_plan'])) $datos['nombre_error'] = 'El nombre del plan es obligatorio.';
            if (empty($datos['velocidad'])) $datos['velocidad_error'] = 'La velocidad es obligatoria.';
            if (empty($datos['precio_mensual'])) {
                $datos['precio_error'] = 'El precio es obligatorio.';
            } elseif (!is_numeric($datos['precio_mensual'])) {
                $datos['precio_error'] = 'El precio debe ser un número.';
            }

            if (empty($datos['nombre_error']) && empty($datos['velocidad_error']) && empty($datos['precio_error'])) {
                if ($this->planModelo->actualizarPlan($datos)) {
                    flash('plan_mensaje', 'Plan actualizado.');
                    header('location: ' . RUTA_URL . '/planes');
                    exit();
                } else {
                    flash('mensaje_error', 'No se pudo actualizar el plan.', 'alert alert-danger');
                    $this->vista('planes/editar', $datos);
                }
            } else {
                $this->vista('planes/editar', $datos);
            }

        } else {
            $datos = [
                'id_plan' => $plan->id_plan,
                'titulo' => 'Editar Plan',
                'nombre_plan' => $plan->nombre_plan,
                'velocidad' => $plan->velocidad,
                'precio_mensual' => $plan->precio_mensual,
                'descripcion' => $plan->descripcion,
                'nombre_error' => '',
                'velocidad_error' => '',
                'precio_error' => ''
            ];
            $this->vista('planes/editar', $datos);
        }
    }

    public function desactivar($id_plan){
        if (!hasRole(['Administrador', 'Ventas'])) {
            flash('mensaje_error', 'No tienes permiso para desactivar planes.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        $this->planModelo->desactivarPlan($id_plan);
        flash('plan_mensaje', 'Plan desactivado. Ya no se mostrará al público.');
        header('location: ' . RUTA_URL . '/planes');
        exit();
    }

    public function activar($id_plan){
        if (!hasRole(['Administrador', 'Ventas'])) {
            flash('mensaje_error', 'No tienes permiso para activar planes.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        $this->planModelo->activarPlan($id_plan);
        flash('plan_mensaje', 'Plan activado.');
        header('location: ' . RUTA_URL . '/planes');
        exit();
    }

    public function eliminar($id_plan){
        if (!hasRole(['Administrador'])) {
            flash('mensaje_error', 'Solo el Administrador puede eliminar planes.', 'alert alert-danger');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        if ($this->planModelo->tieneClientes($id_plan)) {
            flash('mensaje_error', 'No se puede eliminar: el plan está asignado a clientes. Desactívalo mejor.', 'alert alert-warning');
            header('location: '. RUTA_URL . '/planes');
            exit();
        }

        $this->planModelo->eliminarPlan($id_plan);
        flash('plan_mensaje', 'Plan eliminado.');
        header('location: ' . RUTA_URL . '/planes');
        exit();
    }
}
?>