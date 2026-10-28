<?php

// Asegúrate de requerir la conexión o el archivo de funciones globales al inicio
require_once __DIR__ . '/../conexion.php'; 

class AdminController {
    public function gestionar($pdo) {
        // Validación de sesión
        exigir_sesion();
        
        // Resto de la lógica del controlador...
    }
}
// Activar visualización estricta de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Capturar cualquier error fatal de PHP para que no se quede en blanco
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; font-family: sans-serif; margin: 20px; border-radius: 8px; border: 1px solid #f87171;'>";
        echo "<h3 style='margin-top:0;'>Error Crítico Detectado:</h3>";
        echo "<p><b>Mensaje:</b> " . htmlspecialchars($error['message']) . "</p>";
        echo "<p><b>Archivo:</b> " . htmlspecialchars($error['file']) . " (Línea <b>" . $error['line'] . "</b>)</p>";
        echo "</div>";
    }
});

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Cargar el controlador principal del panel de administración
    require_once __DIR__ . '/controladores/AdminController.php';

} catch (Throwable $e) {
    echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; font-family: sans-serif; margin: 20px; border-radius: 8px; border: 1px solid #f87171;'>";
    echo "<h3 style='margin-top:0;'>Excepción Capturada:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><b>Archivo:</b> " . $e->getFile() . " (Línea " . $e->getLine() . ")</p>";
    echo "<pre style='background: #fef2f2; padding: 10px; overflow-x: auto;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}
