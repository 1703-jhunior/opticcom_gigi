<?php
// Iniciar la sesión para toda la aplicación.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Establecer la Zona Horaria por defecto para toda la aplicación PHP (Hora de Perú)
date_default_timezone_set('America/Lima');

// 1. Cargar helpers.
require_once 'helpers/session_helper.php';

// 2. Definir constantes.
// APP_ROOT: Ruta física a la carpeta raíz del proyecto
define('APP_ROOT', dirname(dirname(__FILE__)));

// RUTA_URL: Detecta automáticamente si estás en local o en producción
if (!empty($_SERVER['HTTP_HOST'])) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST']; // Captura automáticamente IP:puerto o dominio
    define('RUTA_URL', $protocolo . '://' . $host);
} else {
    define('RUTA_URL', 'http://192.168.1.128:3000'); // Fallback local
}

// 3. Cargar la conexión a la base de datos.
require_once APP_ROOT . '/config/Conexion.php';

/* =========================================================
 * AUTOLOADER DE CLASES
 * ========================================================= */
spl_autoload_register(function ($nombreClase) {
    $carpetas = [
        APP_ROOT . '/app/librerias/',
        APP_ROOT . '/app/modelos/',
        APP_ROOT . '/app/controladores/'
    ];

    foreach ($carpetas as $carpeta) {
        $rutaArchivo = $carpeta . $nombreClase . '.php';
        if (file_exists($rutaArchivo)) {
            require_once $rutaArchivo;
            return;
        }
    }
});

// 4. Cargar Core y Controlador base
require_once APP_ROOT . '/app/librerias/Core.php';
require_once APP_ROOT . '/app/controladores/Controlador.php';

// 5. Cargar Dompdf
if (file_exists(APP_ROOT . '/app/librerias/dompdf/autoload.inc.php')) {
    require_once APP_ROOT . '/app/librerias/dompdf/autoload.inc.php';
} else {
    error_log("Error crítico: No se encontró el cargador de Dompdf en app/librerias/dompdf/autoload.inc.php");
}
