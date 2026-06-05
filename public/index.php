<?php
// --- Archivo de Entrada Principal de la Aplicación ---
// Router manual para PHP Built-in Server (reemplaza al .htaccess)

// 1. Obtener la URI actual
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// 2. Si el archivo existe físicamente (CSS, JS, imágenes), servirlo directamente
$filePath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filePath) && is_file($filePath)) {
    return false; // PHP built-in server lo sirve solo
}

// 3. Extraer la parte de la URL después del primer "/"
// Convierte /paginas/hogar → url=paginas/hogar
$url = ltrim($uri, '/');
if (!empty($url)) {
    $_GET['url'] = $url;
}

// 4. Cargar bootstrap y arrancar el sistema MVC
require_once __DIR__ . '/../app/bootstrap.php';
$iniciar = new Core();
