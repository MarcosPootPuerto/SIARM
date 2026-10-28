<?php
// Iniciar sesión de forma segura si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1;port=3307'; // Cambia esto si tu servidor MySQL está en otro host o puerto
$dbname = 'siarm'; // Cambia esto por el nombre de tu base de datos si es diferente
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<div style='background: #fee2e2; color: #991b1b; padding: 20px; font-family: sans-serif; border-radius: 8px; margin: 20px;'>
            <h3>Error de Conexión a la Base de Datos:</h3>
            <p>" . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}

/**
 * Función para exigir que el usuario haya iniciado sesión
 */
if (!function_exists('exigir_sesion')) {
    function exigir_sesion() {
        if (!isset($_SESSION['id'])) {
            header('Location: login.php');
            exit;
        }
    }
}

/**
 * Función global de escape para evitar XSS en las vistas
 */
if (!function_exists('h')) {
    function h($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
    }
}