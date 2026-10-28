<?php
// cerrar_sesion.php
require_once __DIR__ . '/conexion.php';

// Limpiar todas las variables de sesión
$_SESSION = [];

// Si se usan cookies para la sesión, eliminarlas
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión por completo
session_destroy();

// Redirigir a la página principal
header('Location: index.php');
exit;