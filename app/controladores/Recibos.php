<?php
class Recibos extends Controlador {
    private $clienteModelo;

    public function __construct() {
        // 1. Verificamos que al menos esté logueado (Cualquiera: Admin o Cliente)
        if (!isLoggedIn()) {
            flash('mensaje_error', 'Debe iniciar sesión para acceder.', 'alert alert-warning');
            header('Location: ' . RUTA_URL . '/portal/login');
            exit();
        }
        $this->clienteModelo = $this->modelo('Cliente');
    }

    // 🔹 CREAMOS UN GUARDIA SOLO PARA FUNCIONES ADMINISTRATIVAS
    private function verificarAccesoAdmin() {
        if (!hasRole(['Administrador', 'Pagos'])) {
            flash('mensaje_error', 'Acceso no autorizado.', 'alert alert-danger');
            header('location: ' . RUTA_URL . '/portal/inicio');
            exit();
        }
    }

    public function index() {
        $this->verificarAccesoAdmin(); // Guardia activo
        flash('mensaje_error', 'Acceso no válido a Recibos.', 'alert alert-warning');
        header('Location: ' . RUTA_URL . '/clientes'); 
        exit();
    }

    // ====== ADMIN: Listar recibos ======
    public function cliente($id_cliente) {
        $this->verificarAccesoAdmin(); // Guardia activo
        
        $rows = $this->clienteModelo->obtenerRecibosPorCliente($id_cliente);
        $recibos = [];
        foreach ($rows as $r) {
            $nombre_bd = (string)$r->nombre_archivo;
            $url_segura = str_replace('%2F', '/', rawurlencode($nombre_bd));
            $recibos[] = [
                'name' => basename($nombre_bd), 
                'url'  => RUTA_URL . '/recibos/descargar/' . $url_segura 
            ];
        }

        $datos = [
            'id_cliente' => (int)$id_cliente,
            'recibos'    => $recibos,
            'titulo'     => 'Recibos del Cliente'
        ];
        $this->vista('recibos/inicio', $datos);
    }

    // ====== ADMIN: Subir PDF ======
    public function subir($id_cliente) {
        $this->verificarAccesoAdmin(); // Guardia activo

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
        }

        if (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
            flash('mensaje_error','Archivo inválido.','alert alert-danger');
            header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
        }

        $archivo = $_FILES['pdf'];
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            flash('mensaje_error','Solo se acepta PDF.','alert alert-warning');
            header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
        }

        $nombre_archivo = 'recibo_' . (int)$id_cliente . '_' . time() . '.pdf';
        
        $carpeta_destino = APP_ROOT . '/public/uploads/clientes/' . $id_cliente . '/recibos/'; 

        if (!file_exists($carpeta_destino)) {
            @mkdir($carpeta_destino, 0777, true);
        }

        $ruta_fisica = $carpeta_destino . $nombre_archivo;

        if (!move_uploaded_file($archivo['tmp_name'], $ruta_fisica)) {
            flash('mensaje_error','No se pudo guardar el archivo. Verifica permisos.','alert alert-danger');
            header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
        }

        $ruta_bd = 'clientes/' . $id_cliente . '/recibos/' . $nombre_archivo;
        $this->clienteModelo->guardarRecibo((int)$id_cliente, $ruta_bd);
        
        flash('cliente_mensaje','Recibo subido y organizado correctamente.');
        header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
    }

    // ====== ADMIN: Borrar PDF ======
    public function borrar($id_cliente) {
        $this->verificarAccesoAdmin(); // Guardia activo

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
        }

        $nombre_bd = trim($_POST['file'] ?? '');
        if ($nombre_bd === '') {
            flash('mensaje_error','Archivo no especificado.','alert alert-danger');
            header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
        }

        if (strpos($nombre_bd, '/') !== false) {
            $ruta_fisica = APP_ROOT . '/public/uploads/' . $nombre_bd;
        } else {
            $ruta_fisica = APP_ROOT . '/public/recibos/' . basename($nombre_bd);
        }

        if (is_file($ruta_fisica)) { @unlink($ruta_fisica); }
        $this->clienteModelo->eliminarReciboPorNombre((int)$id_cliente, $nombre_bd);

        flash('cliente_mensaje','Recibo eliminado.');
        header('Location: ' . RUTA_URL . '/recibos/cliente/' . (int)$id_cliente); exit();
    }

    // ====== PORTAL: Descargar directo (SIN GUARDIA, ACCESIBLE PARA EL CLIENTE) ======
    public function descargar() {
        $args = func_get_args();
        if (empty($args)) {
            flash('mensaje_error', 'Archivo no especificado.', 'alert alert-danger');
            header('Location: ' . RUTA_URL . '/clientes');
            exit();
        }

        $nombre_archivo = implode('/', $args);
        $nombre_archivo = urldecode($nombre_archivo);

        if (strpos($nombre_archivo, '/') !== false) {
            $ruta = APP_ROOT . '/public/uploads/' . $nombre_archivo;
        } else {
            $ruta = APP_ROOT . '/public/recibos/' . basename($nombre_archivo);
        }
        
        if (!is_file($ruta)) {
            flash('mensaje_error', 'Archivo no encontrado en el servidor.', 'alert alert-danger');
            header('Location: ' . RUTA_URL . '/portal/inicio'); // Modificado para redirigir al portal
            exit();
        }
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($ruta) . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    }
}
?>