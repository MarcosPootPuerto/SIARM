<?php
session_start();
require_once 'conexion.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT to_char(hora, 'HH24:MI:SS') as hora, evento, nivel FROM historial ORDER BY id DESC LIMIT 20");
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($registros);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
