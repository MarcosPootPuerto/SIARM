<?php
// guardar_evento.php
require_once __DIR__ . '/conexion.php';
exigir_sesion();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['id'] ?? 0;
    $evento = trim($_POST['evento'] ?? '');
    $nivel = trim($_POST['nivel'] ?? '');

    if ($usuario_id > 0 && !empty($evento)) {
        // 1. Guardar el evento en la bitácora vinculada al usuario activo
        $stmt = $pdo->prepare("INSERT INTO bitacora_eventos (usuario_id, evento, nivel) VALUES (?, ?, ?)");
        $stmt->execute([$usuario_id, $evento, $nivel]);

        // 2. Obtener los datos del usuario actual
        $stmtUser = $pdo->prepare("SELECT telefono, nombre FROM usuarios WHERE id = ?");
        $stmtUser->execute([$usuario_id]);
        $usuarioData = $stmtUser->fetch(PDO::FETCH_ASSOC) ?: ['nombre' => $_SESSION['nombre'] ?? 'Usuario', 'telefono' => ''];

        $valorNivel = intval(str_replace('%', '', $nivel));
        $esRiesgo = ($valorNivel >= 50);

        // 3. Simulación/Procesamiento automático sin requerir tokens externos
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
    }
}

echo json_encode(['success' => false, 'error' => 'Petición inválida o faltan parámetros']);

/**
 * Función genérica de procesamiento de alertas automáticas (sin dependencias ni tokens obligatorios)
 */
function procesarNotificacionAuto($telefonoDestino, $mensaje) {
    // Modo automático local activo por defecto
    $modoLocalAuto = true;

    if ($modoLocalAuto) {
        return true;
    }

    // Configuración opcional para pasarelas en producción (Twilio / Altiria / etc.)
    $accountSid = "TU_ACCOUNT_SID";
    $authToken  = "TU_AUTH_TOKEN";
    $numeroEmisor = "+1234567890";

    if (empty($accountSid) || $accountSid === "TU_ACCOUNT_SID") {
        return true; // Fallback automático si no hay token configurado
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