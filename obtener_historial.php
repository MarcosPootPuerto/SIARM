<?php
// obtener_historial.php
require_once __DIR__ . '/conexion.php';
exigir_sesion();

$usuario_id = $_SESSION['id'] ?? 0;

header('Content-Type: application/json');

try {
    // Consulta adaptada a la estructura de Supabase (id, usuario_id, evento, nivel, fecha_hora)
    // Formateamos la fecha_hora con TO_CHAR para que el frontend la reciba limpia en la variable 'hora'
    $stmt = $pdo->prepare("SELECT TO_CHAR(fecha_hora, 'YYYY-MM-DD HH24:MI:SS') as hora, evento, nivel FROM bitacora_eventos WHERE usuario_id = ? ORDER BY id DESC LIMIT 50");
    $stmt->execute([$usuario_id]);
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($registros);
} catch (Exception $e) {
    echo json_encode([]);
}
