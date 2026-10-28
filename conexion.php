<?php
$host = "aws-0-us-east-1.pooler.supabase.com"; // Host del pooler de Supabase
$port = "6543";                                 // Puerto del pooler (IPv4)
$dbname = "postgres";
$user = "postgres.nybwjlhesdqzzsqicjry";       // Tu usuario de Supabase
$password = "AfBJe,+AR/wv4MT";                  // Tu contraseña asignada

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $conn = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo "Error de Conexión a la Base de Datos (Supabase): " . $e->getMessage();
}
?>
