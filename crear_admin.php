
<?php
// admin.php (En la raíz del proyecto)
require_once __DIR__ . '/controladores/AdminController.php';

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit('No disponible.');
}

require_once __DIR__ . '/conexion.php';

$total = (int) $pdo->query(
    "SELECT COUNT(*) FROM usuarios WHERE rol = 'administrador'"
)->fetchColumn();

if ($total > 0) {
    exit("Ya existe un administrador. No se creó otra cuenta.\n");
}

$nombre = trim((string) readline('Nombre del administrador: '));
$correo = strtolower(trim((string) readline('Correo electrónico: ')));

if (
    $nombre === '' ||
    strlen($nombre) > 100 ||
    !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
    strlen($correo) > 150
) {
    exit("El nombre o el correo no son válidos.\n");
}

if (!function_exists('readline')) {
    exit("Ejecuta este script desde una terminal compatible con readline.\n");
}

$password = '';

if (DIRECTORY_SEPARATOR === '\\') {
    // Windows: pedir la contraseña sin mostrarla en pantalla.
    $modo = shell_exec('echo %CMDCMDLINE%');

    // La contraseña se solicita de forma visible si no hay
    // un método de entrada oculta disponible en la terminal.
    echo "Escribe una contraseña segura (mínimo 12 caracteres): ";
    $password = (string) fgets(STDIN);
    $password = rtrim($password, "\r\n");
} else {
    echo "Contraseña (mínimo 12 caracteres): ";
    shell_exec('stty -echo');
    $password = (string) fgets(STDIN);
    shell_exec('stty echo');
    echo PHP_EOL;
    $password = rtrim($password, "\r\n");
}

if (strlen($password) < 12 || strlen($password) > 1024) {
    exit("La contraseña debe tener entre 12 y 1024 bytes.\n");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    // Comprobar nuevamente dentro de la operación.
    $total = (int) $pdo->query(
        "SELECT COUNT(*) FROM usuarios WHERE rol = 'administrador'"
    )->fetchColumn();

    if ($total > 0) {
        $pdo->rollBack();
        exit("Ya existe un administrador.\n");
    }

    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (nombre, correo, password, rol)
         VALUES (?, ?, ?, 'administrador')"
    );

    $stmt->execute([$nombre, $correo, $hash]);
    $pdo->commit();

    echo "Administrador creado correctamente.\n";
    echo "Elimina crear_admin.php de la carpeta del proyecto cuando termines.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());
    exit("No fue posible crear la cuenta. Comprueba si el correo ya existe.\n");
}
