<?php
// conexion.php

// 1. Asegurar el inicio de sesión global
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Definir la función de seguridad globalmente para toda la aplicación
if (!function_exists('exigir_sesion')) {
    function exigir_sesion() {
        if (!isset($_SESSION['id'])) {
            header("Location: login.php");
            exit();
        }
    }
}

$host = "aws-0-us-east-1.pooler.supabase.com";
$port = "6543";
$dbname = "postgres";
$user = "postgres.nybwjlhesdqzzsqicjry";
$password = "AfBJe,+AR/wv4MT";

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (\PDOException $e) {
    if (getenv('APP_DEBUG') === 'true') {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    } else {
        http_response_code(500);
        echo "Error de conexión a la base de datos.";
        exit();
    }
}
