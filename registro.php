<?php
// registro.php
require_once __DIR__ . '/conexion.php';

$mensaje_error = '';
$mensaje_exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');

    if (!empty($nombre) && !empty($correo) && !empty($contrasena)) {
        // Verificar si el correo ya existe
        $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmtCheck->execute([$correo]);
        
        if ($stmtCheck->rowCount() > 0) {
            $mensaje_error = "El correo electrónico ya está registrado.";
        } else {
            // Insertar usuario nuevo con rol 'usuario' por defecto
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, telefono) VALUES (?, ?, ?, 'usuario', ?)");
            // Nota: Si tus contraseñas usan password_hash(), asegúrate de aplicarlo:
            $passHash = password_hash($contrasena, PASSWORD_DEFAULT);
            
            // Si guardas contraseñas en texto plano en tu proyecto, cambia $passHash por $contrasena
            $stmt->execute([$nombre, $correo, $passHash, $telefono]);

            $mensaje_exito = "¡Cuenta creada exitosamente! Ya puedes iniciar sesión.";
        }
    } else {
        $mensaje_error = "Por favor completa todos los campos obligatorios.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de usuario | SIARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body {
            background-color: #06111c;
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .register-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .register-card h2 { font-size: 1.5rem; color: #0b192c; margin-bottom: 4px; }
        .register-card h4 { font-size: 0.9rem; color: #38bdf8; margin-bottom: 20px; font-weight: 600; }
        .register-card p { font-size: 0.85rem; color: #64748b; margin-bottom: 20px; }
        
        label { font-size: 0.8rem; font-weight: 600; color: #475569; display: block; margin-top: 12px; }
        input {
            width: 100%;
            padding: 10px 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            border-radius: 6px;
            margin-top: 4px;
            font-size: 0.9rem;
        }
        input:focus { outline: none; border-color: #38bdf8; background: #fff; }
        
        .btn {
            background: #0b192c;
            color: #fff;
            border: none;
            width: 100%;
            padding: 11px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.2s;
        }
        .btn:hover { background: #1e293b; }
        
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 0.85rem; }
        .alert-error { background: #fee2e2; border: 1px solid #f87171; color: #991b1b; }
        .alert-success { background: #dcfce7; border: 1px solid #4ade80; color: #166534; }
        
        .login-link { text-align: center; margin-top: 15px; font-size: 0.85rem; color: #64748b; }
        .login-link a { color: #0284c7; text-decoration: none; font-weight: 600; }
        .login-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="register-card">
        <h2>SIARM.</h2>
        <h4>Crear cuenta</h4>
        <p>Regístrate como usuario familiar de SIARM.</p>

        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_exito, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="registro.php">
            <label>Nombre Completo</label>
            <input type="text" name="nombre" required placeholder="Ej. Familia Poot">

            <label>Correo Electrónico</label>
            <input type="email" name="correo" required placeholder="correo@ejemplo.com">

            <label>Contraseña</label>
            <input type="password" name="contrasena" required placeholder="********">

            <label>Teléfono (Para alertas WhatsApp)</label>
            <input type="text" name="telefono" placeholder="Ej. 529991234567">

            <button type="submit" class="btn">Registrarse</button>
        </form>

        <div class="login-link">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
    </div>

</body>
</html>