<?php
$host = "aws-0-us-east-1.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$user = "postgres.nybwjlhesdqzzsqicjry";
$password = "AfBJe,+AR/wv4MT";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de Conexión a la Base de Datos: " . $e->getMessage());
}
?>
