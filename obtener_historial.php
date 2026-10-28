<?php
// obtener_historial.php
require_once __DIR__ . '/conexion.php';
exigir_sesion();

$usuario_id = $_SESSION['id'] ?? 0;

// Reemplazamos DATE_FORMAT (MySQL) por TO_CHAR (PostgreSQL / Supabase)
$stmt = $pdo->prepare("SELECT TO_CHAR(fecha_hora, 'HH12:MI:SS AM') as hora, evento, nivel FROM bitacora_eventos WHERE usuario_id = ? ORDER BY id DESC LIMIT 50");
$stmt->execute([$usuario_id]);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($registros);
