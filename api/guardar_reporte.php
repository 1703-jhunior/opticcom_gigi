<?php
// api/guardar_reporte.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'conexion.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if(!isset($data['id_orden'])) { 
    echo json_encode(["success" => false, "mensaje" => "Faltan datos de la orden"]); 
    exit; 
}

try {
    $db = new Conexion();
    $conn = $db->conectar();
    $conn->beginTransaction();

    // 1. OBTENER DATOS DE LA ORDEN Y EL CLIENTE PRIMERO (Para saber dónde guardar)
    $stmtOrd = $conn->prepare("SELECT id_cliente_fk, id_tipo_orden_fk FROM ordenes_trabajo WHERE id_orden = ?");
    $stmtOrd->execute([$data['id_orden']]);
    $orden_info = $stmtOrd->fetch(PDO::FETCH_ASSOC);
    
    if (!$orden_info) {
        throw new Exception("La orden no existe.");
    }
    
    $id_cliente = $orden_info['id_cliente_fk'];

    // 2. CREAR ESTRUCTURA DE CARPETAS DEL CLIENTE
    $carpeta_base = '../public/uploads/clientes/' . $id_cliente . '/evidencias/';
    if (!file_exists($carpeta_base)) {
        mkdir($carpeta_base, 0777, true);
    }

    // 3. GUARDAR EL REPORTE
    $sql = "INSERT INTO reportes_tecnicos 
            (id_orden_fk, coordenadas_lat, coordenadas_lon, potencia_optica, metros_cable_usado, conectores_usados, codigo_nap, puerto_nap, serie_onu, observaciones_tecnico, solucion_aplicada) 
            VALUES (:id, :lat, :lon, :pot, :met, :con, :nap, :pto, :ser, :obs, :sol)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':id'  => $data['id_orden'], 
        ':lat' => $data['lat'] ?? '0.0', 
        ':lon' => $data['lon'] ?? '0.0',
        ':pot' => $data['potencia'] ?? 0.0, 
        ':met' => $data['metros'] ?? 0,
        ':con' => $data['conectores'] ?? 0,
        ':nap' => $data['nap'] ?? '', 
        ':pto' => $data['puerto'] ?? 1,
        ':ser' => $data['serie'] ?? '', 
        ':obs' => $data['observaciones'] ?? '',
        ':sol' => $data['solucion'] ?? ''
    ]);
    
    $id_reporte = $conn->lastInsertId();

    // 🔹 3.5. NUEVO: ACTUALIZAR EL GPS REAL DEL CLIENTE CON EL DEL TÉCNICO
    // Si el técnico envía coordenadas válidas (diferentes de 0.0), las guardamos en el perfil del cliente
    if (!empty($data['lat']) && !empty($data['lon']) && $data['lat'] != '0.0' && $data['lon'] != '0.0') {
        $coordenadas_exactas = $data['lat'] . ',' . $data['lon'];
        $stmtUpdateGps = $conn->prepare("UPDATE clientes SET location_link = ? WHERE id_cliente = ?");
        $stmtUpdateGps->execute([$coordenadas_exactas, $id_cliente]);
    }

    // 🔹 FUNCIÓN AUXILIAR PARA LIMPIAR Y GUARDAR BASE64 CORRECTAMENTE
    function guardarBase64($base64_string, $ruta_destino) {
        if (strpos($base64_string, ',') !== false) {
            $partes = explode(',', $base64_string);
            $base64_string = $partes[1];
        }
        $base64_string = str_replace(' ', '+', $base64_string);
        $datosImagen = base64_decode($base64_string);
        
        if ($datosImagen !== false) {
            file_put_contents($ruta_destino, $datosImagen);
            return true;
        }
        return false;
    }

    // 4. PROCESAR FOTO FACHADA
    if (!empty($data['foto_base64'])) {
        $nombreFoto = 'evidencia_' . $id_reporte . '_fachada_' . time() . '.jpg';
        $rutaCompleta = $carpeta_base . $nombreFoto;
        
        if (guardarBase64($data['foto_base64'], $rutaCompleta)) {
            $ruta_bd = 'clientes/' . $id_cliente . '/evidencias/' . $nombreFoto;
            $stmtFoto = $conn->prepare("INSERT INTO evidencias_fotos (id_reporte_fk, tipo_foto, url_foto) VALUES (?, 'Fachada', ?)");
            $stmtFoto->execute([$id_reporte, $ruta_bd]);
        }
    }
    // 4.5 PROCESAR FOTO EQUIPO
    if (!empty($data['foto_equipo_base64'])) {
        $nombreFotoEq = 'evidencia_' . $id_reporte . '_equipo_' . time() . '.jpg';
        $rutaCompletaEq = $carpeta_base . $nombreFotoEq;
        
        if (guardarBase64($data['foto_equipo_base64'], $rutaCompletaEq)) {
            $ruta_bd_eq = 'clientes/' . $id_cliente . '/evidencias/' . $nombreFotoEq;
            $stmtFotoEq = $conn->prepare("INSERT INTO evidencias_fotos (id_reporte_fk, tipo_foto, url_foto) VALUES (?, 'Equipo', ?)");
            $stmtFotoEq->execute([$id_reporte, $ruta_bd_eq]);
        }
    }

    // 5. PROCESAR FIRMA DEL CLIENTE
    if (!empty($data['firma_base64'])) {
        $nombreFirma = 'firma_' . $id_reporte . '_' . time() . '.png';
        $rutaCompletaFirma = $carpeta_base . $nombreFirma;
        
        if (guardarBase64($data['firma_base64'], $rutaCompletaFirma)) {
            $ruta_bd_firma = 'clientes/' . $id_cliente . '/evidencias/' . $nombreFirma;
            $stmtFirma = $conn->prepare("UPDATE reportes_tecnicos SET firma_cliente_url = ? WHERE id_reporte = ?");
            $stmtFirma->execute([$ruta_bd_firma, $id_reporte]);
        }
    }

    // 6. ACTIVAR CLIENTE SI ES INSTALACIÓN
    if ($orden_info['id_tipo_orden_fk'] == 1) {
        $sqlActivar = "UPDATE clientes SET estado_servicio = 'Activo', fecha_instalacion = CURRENT_DATE() WHERE id_cliente = ?";
        $conn->prepare($sqlActivar)->execute([$id_cliente]);
    }

    // 7. CERRAR ORDEN
    $conn->prepare("UPDATE ordenes_trabajo SET estado_orden='Finalizado' WHERE id_orden=?")->execute([$data['id_orden']]);

    $conn->commit();
    echo json_encode(["success" => true, "mensaje" => "Reporte guardado exitosamente."]);

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error en API Reporte: " . $e->getMessage());
    echo json_encode(["success" => false, "mensaje" => "Error del Servidor: " . $e->getMessage()]);
}
?>