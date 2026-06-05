<?php
// 1. PERMISOS CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

require_once 'conexion.php';

try {
    $db_obj = new Conexion();
    $db = $db_obj->conectar();
    
    $id_tecnico = isset($_GET['id_tecnico']) ? intval($_GET['id_tecnico']) : 0;

    if ($id_tecnico === 0) {
        echo json_encode([]); 
        exit;
    }

    // 2. CONSULTA EXACTA CON TODOS LOS CAMPOS QUE LA APP NECESITA
    $sql = "SELECT 
                o.id_orden, 
                CONCAT(c.nombre, ' ', IFNULL(c.apellido, '')) AS cliente,
                t.nombre_tipo AS tipo_trabajo,
                d.nombre AS distrito,
                o.estado_orden,
                DATE_FORMAT(o.fecha_programada, '%d/%m/%Y') AS fecha,
                c.telefono,
                c.direccion_calle AS direccion,
                c.referencia,
                c.location_link AS coordenadas
            FROM ordenes_trabajo o
            INNER JOIN clientes c ON o.id_cliente_fk = c.id_cliente
            INNER JOIN distritos d ON c.id_distrito_fk = d.id_distrito
            INNER JOIN tipos_orden t ON o.id_tipo_orden_fk = t.id_tipo_orden
            WHERE o.id_tecnico_fk = :id_tecnico 
            AND o.estado_orden = 'Finalizado'
            ORDER BY o.fecha_programada DESC";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id_tecnico', $id_tecnico, PDO::PARAM_INT);
    $stmt->execute();
    
    $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ordenes);

} catch (Exception $e) {
    error_log("Error en get_historial: " . $e->getMessage());
    echo json_encode([]);
}
?>