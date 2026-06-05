<?php
// Iniciar la sesión para toda la aplicación.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ❗ INICIO DE LA CORRECCIÓN DE HORA
// Establecer la Zona Horaria por defecto para toda la aplicación PHP (Hora de Perú)
date_default_timezone_set('America/Lima');
// ❗ FIN DE LA CORRECCIÓN DE HORA

// 1. Cargar helpers.
require_once 'helpers/session_helper.php';

// 2. Definir constantes.
// APP_ROOT: Ruta física a la carpeta raíz del proyecto (public_html)
define('APP_ROOT', dirname(dirname(__FILE__)));
// RUTA_URL: URL base del sitio web (Verifica http/https)
define('RUTA_URL', 'https://opticcomperu.com'); // Asegúrate que esta sea tu URL final

// 3. Cargar el núcleo del sistema, incluyendo la CONEXIÓN.
// (El autoloader de abajo se encargará de Core.php y Controlador.php)
require_once APP_ROOT . '/config/Conexion.php';

/* =========================================================
 * AUTOLOADER DE CLASES (Optimización)
 * Carga automáticamente las clases cuando se necesitan.
 * Ya no necesitas los 'require_once' manuales para Modelos o Librerías.
 * ========================================================= */
spl_autoload_register(function ($nombreClase) {
    // Define las carpetas donde buscará las clases
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

// 4. Cargar la clase Core (que ahora sí encontrará el autoloader)
// y Controlador (la base)
require_once APP_ROOT . '/app/librerias/Core.php';
require_once APP_ROOT . '/app/controladores/Controlador.php';

// =========================================================================
// ❗ INYECCIÓN DE LIBRERÍAS EXTERNAS DE TERCEROS (Manual sin Composer)
// =========================================================================
// Dado que guardaste Correo.php en /app/librerias/, el Autoloader de arriba 
// lo cargará automáticamente en cuanto escribas "Correo::enviar()".
// Pero Dompdf requiere su inicializador maestro de forma obligatoria aquí:
if (file_exists(APP_ROOT . '/app/librerias/dompdf/autoload.inc.php')) {
    require_once APP_ROOT . '/app/librerias/dompdf/autoload.inc.php';
} else {
    error_log("Error crítico: No se encontró el cargador de Dompdf en app/librerias/dompdf/autoload.inc.php");
}
