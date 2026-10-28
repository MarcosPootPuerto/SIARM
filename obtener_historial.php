<?php
// obtener_historial.php
require_once __DIR__ . '/conexion.php';
exigir_sesion();

header('Content-Type: application/json');

try {
    // Consulta optimizada para PostgreSQL usando TO_CHAR y ordenando por ID descendente
    $stmt = $pdo->query("SELECT TO_CHAR(fecha_hora, 'YYYY-MM-DD HH24:MI:SS') as hora, evento, nivel FROM bitacora_eventos ORDER BY id DESC LIMIT 50");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devuelve los registros en formato JSON limpio para que JavaScript los pinte en la tabla
    echo json_encode($registros);
} catch (Exception $e) {
    // En caso de error, devuelve un arreglo vacío para evitar romper el script del frontend
    echo json_encode([]);
}
