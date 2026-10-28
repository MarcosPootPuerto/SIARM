<?php
// cerrar_sesion.php

// 1. Iniciar o verificar la sesión antes de destruirla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Vaciar el arreglo de sesión
$_SESSION = array();

// 3. Eliminar la cookie de sesión si está activa
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión por completo
session_destroy();

// 5. Redirigir de inmediato al login sin generar salidas previas
header("Location: login.php");
exit();
