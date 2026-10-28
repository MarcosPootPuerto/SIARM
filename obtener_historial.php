<?php
// obtener_historial.php
require_once __DIR__ . '/conexion.php';
exigir_sesion();

$usuario_id = $_SESSION['id'] ?? 0;

$stmt = $pdo->prepare("SELECT DATE_FORMAT(fecha_hora, '%r') as hora, evento, nivel FROM bitacora_eventos WHERE usuario_id = ? ORDER BY id DESC LIMIT 50");
$stmt->execute([$usuario_id]);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($registros);