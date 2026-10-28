<?php
// login.php
session_start();

// Corregido: Asegúrate de incluir la barra diagonal "/"
require_once __DIR__ . '/conexion.php';

$mensaje_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if (!empty($correo) && !empty($contrasena)) {
        // Validar que $pdo esté disponible antes de preparar la consulta
        if (isset($pdo)) {
            $stmt = $pdo->prepare("SELECT id, nombre, correo, contrasena, rol FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                // Verificamos la contraseña (compatible con password_hash o texto plano temporal)
                if (password_verify($contrasena, $usuario['contrasena']) || $contrasena === $usuario['contrasena']) {
                    // Iniciar sesión correctamente
                    $_SESSION['id'] = $usuario['id'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['rol'] = $usuario['rol'];

                    // Redirigir según el rol
                    if ($usuario['rol'] === 'administrador') {
                        header("Location: admin.php");
                    } else {
                        header("Location: vista_general.php");
                    }
                    exit;
                } else {
                    $mensaje_error = "Contraseña incorrecta.";
                }
            } else {
                $mensaje_error = "No existe una cuenta registrada con este correo.";
            }
        } else {
            $mensaje_error = "Error crítico: No hay conexión con la base de datos.";
        }
    } else {
        $mensaje_error = "Por favor completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | SIARM</title>
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
        .login-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .login-card h2 { font-size: 1.5rem; color: #0b192c; margin-bottom: 4px; }
        .login-card h4 { font-size: 0.9rem; color: #38bdf8; margin-bottom: 20px; font-weight: 600; }
        
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
        
        .register-link { text-align: center; margin-top: 15px; font-size: 0.85rem; color: #64748b; }
        .register-link a { color: #0284c7; text-decoration: none; font-weight: 600; }
        .register-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>SIARM.</h2>
        <h4>Iniciar Sesión</h4>

        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label>Correo Electrónico</label>
            <input type="email" name="correo" required placeholder="correo@ejemplo.com">

            <label>Contraseña</label>
            <input type="password" name="contrasena" required placeholder="********">

            <button type="submit" class="btn">Entrar</button>
        </form>

        <div class="register-link">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </div>
    </div>

</body>
</html>
