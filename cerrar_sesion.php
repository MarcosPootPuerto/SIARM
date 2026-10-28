<?php
// cerrar_sesion.php

// 1. Asegurar que la sesión esté iniciada antes de destruirla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Limpiar todas las variables de sesión
$_SESSION = array();

// 3. Borrar la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Destruir la sesión por completo
session_destroy();

// 5. Redirigir limpiamente al login sin que se envíen avisos previos
header("Location: login.php");
exit();
