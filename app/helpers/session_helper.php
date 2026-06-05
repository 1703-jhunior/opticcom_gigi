<?php
// ==============================
// SESSION HELPER - OPTICCOM (estable)
// ==============================
if (session_status() === PHP_SESSION_NONE) { session_start(); }

/* ------------------------------
   FLASH
--------------------------------*/
function flash($nombre = '', $mensaje = '', $clase = 'alert alert-success'){
    if(!empty($nombre)){
        if(!empty($mensaje) && empty($_SESSION[$nombre])){
            if(!empty($_SESSION[$nombre.'_clase'])) unset($_SESSION[$nombre.'_clase']);
            $_SESSION[$nombre] = $mensaje;
            $_SESSION[$nombre.'_clase'] = $clase;
        } elseif(empty($mensaje) && !empty($_SESSION[$nombre])){
            $clase = !empty($_SESSION[$nombre.'_clase']) ? $_SESSION[$nombre.'_clase'] : '';
            echo '<div class="'.$clase.'" id="msg-flash">'.$_SESSION[$nombre].'</div>';
            unset($_SESSION[$nombre], $_SESSION[$nombre.'_clase']);
        }
    }
}

/* ------------------------------
   SESIÓN
--------------------------------*/
function isLoggedIn(){ return isset($_SESSION['id_usuario']) || isset($_SESSION['id_cliente']); }
function currentUser(){
    return $_SESSION['nombre_usuario'] ?? ($_SESSION['nombre_cliente'] ?? null);
}

/* ------------------------------
   NORMALIZACIÓN DE ROLES
--------------------------------*/
function _role_alias_map(){
    return [
        // Admin
        'admin'=>'Administrador','administrador'=>'Administrador','root'=>'Administrador','superadmin'=>'Administrador',
        // Ventas
        'ventas'=>'Ventas','vendedor'=>'Ventas','comercial'=>'Ventas','trabajador'=>'Ventas',
        // Pagos
        'pagos'=>'Pagos','cobranza'=>'Pagos','cobranzas'=>'Pagos','finanzas'=>'Pagos',
        // Soporte
        'soporte'=>'Soporte','soporte técnico'=>'Soporte','soporte tecnico'=>'Soporte','técnico'=>'Soporte','tecnico'=>'Soporte',
        // Cliente
        'cliente'=>'Cliente','portal'=>'Cliente','customer'=>'Cliente','user'=>'Cliente',
    ];
}
function normalizeRole($raw){
    if ($raw === null) return 'Invitado';
    $k = mb_strtolower(trim((string)$raw),'UTF-8');
    $map = _role_alias_map();
    return $map[$k] ?? $raw;
}
function currentRole(){
    $stored = $_SESSION['rol_usuario'] ?? 'Invitado';
    $norm = normalizeRole($stored);
    if ($norm !== $stored) $_SESSION['rol_usuario'] = $norm;
    return $norm;
}

/* ------------------------------
   ACCESO
--------------------------------*/
function hasRole($roles = []){
    if(!isLoggedIn()) return false;
    $mine = currentRole();
    $wanted = array_map('normalizeRole', (array)$roles);
    return in_array($mine, $wanted, true);
}
function requireRoleOrRedirect($roles = [], $redirect = '/dashboard'){
    if(!isLoggedIn()){
        header('Location: ' . RUTA_URL . '/portal/login'); exit();
    }
    if(!hasRole($roles)){
        flash('mensaje_error', 'No tienes permiso para acceder a esta sección.', 'alert alert-danger');
        header('Location: ' . RUTA_URL . $redirect); exit();
    }
}
