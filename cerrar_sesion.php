<?php
// cerrar_sesion.php

// Iniciar la sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciar todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión de forma segura
session_destroy();

// Redirigir al usuario al login
header("Location: login.php");
exit();
