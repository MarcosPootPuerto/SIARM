<?php
// obtener_historial.php
require_once __DIR__ . '/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    // Consulta general a bitacora_eventos asegurando formato de fecha compatible con PostgreSQL
    $stmt = $pdo->query("SELECT TO_CHAR(fecha_hora, 'YYYY-MM-DD HH24:MI:SS') AS fecha_hora, evento, nivel FROM bitacora_eventos ORDER BY id DESC LIMIT 50");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($registros);
} catch (Exception $e) {
    // En caso de error, devolvemos un arreglo vacío para evitar romper el JSON del frontend
    echo json_encode([]);
}
