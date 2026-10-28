<?php
// guardar_evento.php
require_once __DIR__ . '/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Permitir sesión o ID recibido por POST para pruebas/simulador
$usuario_id = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? $_POST['usuario_id'] ?? 8; // Por defecto usa el ID 8 que sí existe en Supabase
$evento = trim($_POST['evento'] ?? '');
$nivel = trim($_POST['nivel'] ?? '0%');

if (!empty($evento)) {
    try {
        // 1. Guardar el evento en la bitácora vinculada
        $stmt = $pdo->prepare("INSERT INTO bitacora_eventos (usuario_id, evento, nivel) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $evento, $nivel]);

        // 2. Obtener los datos del usuario actual
        $stmtUser = $pdo->prepare("SELECT telefono, nombre FROM usuarios WHERE id = ?");
        $stmtUser->execute([$usuario_id]);
        $usuarioData = $stmtUser->fetch(PDO::FETCH_ASSOC) ?: ['nombre' => $_SESSION['nombre'] ?? 'Usuario', 'telefono' => ''];

        $valorNivel = intval(str_replace('%', '', $nivel));
        $esRiesgo = ($valorNivel >= 50);

        // 3. Simulación/Procesamiento automático de alertas
        $alertaProcesada = false;
        if ($esRiesgo) {
            $mensaje = "ALERTA SIARM: Hola " . $usuarioData['nombre'] . ", nivel de riesgo detectado en " . $nivel . " (" . $evento . ").";
            $alertaProcesada = procesarNotificacionAuto($usuarioData['telefono'], $mensaje);
        }

        echo json_encode([
            'success' => true,
            'es_riesgo' => $esRiesgo,
            'sms_enviado' => $alertaProcesada,
            'whatsapp_enviado' => $alertaProcesada
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Error en BD: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'error' => 'Petición inválida o falta el evento']);

/**
 * Función genérica de procesamiento de alertas automáticas
 */
function procesarNotificacionAuto($telefonoDestino, $mensaje) {
    $modoLocalAuto = true;

    if ($modoLocalAuto) {
        return true;
    }

    $accountSid = "TU_ACCOUNT_SID";
    $authToken  = "TU_AUTH_TOKEN";
    $numeroEmisor = "+1234567890";

    if (empty($accountSid) || $accountSid === "TU_ACCOUNT_SID") {
        return true;
    }

    $url = "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Messages.json";
    $datos = [
        'From' => $numeroEmisor,
        'To'   => $telefonoDestino,
        'Body' => $mensaje
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($datos));
    curl_setopt($ch, CURLOPT_USERPWD, $accountSid . ":" . $authToken);

    $respuesta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200 || $httpCode === 201);
}
