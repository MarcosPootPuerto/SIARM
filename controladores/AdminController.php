<?php
// controladores/AdminController.php

// 1. Iniciar sesión y declarar la función de seguridad obligatoria
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('exigir_sesion')) {
    function exigir_sesion() {
        if (!isset($_SESSION['id'])) {
            header("Location: ../login.php");
            exit();
        }
    }
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../modelos/UsuarioModel.php';

class AdminController {
    public function gestionar($pdo) {
        exigir_sesion();

        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: panel_usuario.php');
            exit;
        }

        $nombre = $_SESSION['nombre'] ?? 'Administrador';
        $mensaje_error = '';
        $mensaje_exito = '';

        // Procesar acciones del CRUD
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'crear') {
                $nom = trim($_POST['nombre'] ?? '');
                $correo = trim($_POST['correo'] ?? '');
                $pass = $_POST['contrasena'] ?? '';
                $rol = $_POST['rol'] ?? 'usuario';

                if (!empty($nom) && !empty($correo) && !empty($pass)) {
                    if (UsuarioModel::existeCorreo($pdo, $correo)) {
                        $mensaje_error = "El correo electrónico ya está registrado.";
                    } else {
                        UsuarioModel::crear($pdo, $nom, $correo, $pass, $rol);
                        $mensaje_exito = "Usuario creado exitosamente.";
                    }
                } else {
                    $mensaje_error = "Todos los campos son obligatorios.";
                }
            } 
            elseif ($accion === 'editar') {
                $id = (int) ($_POST['id'] ?? 0);
                $nom = trim($_POST['nombre'] ?? '');
                $correo = trim($_POST['correo'] ?? '');
                $rol = $_POST['rol'] ?? 'usuario';
                $pass = $_POST['contrasena'] ?? '';

                if ($id <= 0) {
                    $mensaje_error = "No se pudo identificar al usuario.";
                } elseif (!empty($nom) && !empty($correo)) {
                    if (UsuarioModel::existeCorreoEnOtroUsuario($pdo, $correo, $id)) {
                        $mensaje_error = "El correo electrónico ya está registrado en otro usuario.";
                    } else {
                        UsuarioModel::actualizar($pdo, $id, $nom, $correo, $rol, $pass);
                        $mensaje_exito = "Usuario actualizado correctamente.";
                    }
                } else {
                    $mensaje_error = "Nombre y correo son obligatorios.";
                }
            } 
            elseif ($accion === 'eliminar') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id == ($_SESSION['id'] ?? 0)) {
                    $mensaje_error = "No puedes eliminar tu propia cuenta activa.";
                } else {
                    UsuarioModel::eliminar($pdo, $id);
                    $mensaje_exito = "Usuario eliminado correctamente.";
                }
            }
        }

        // Obtener datos para la vista
        $usuarios = UsuarioModel::obtenerTodosConDispositivos($pdo);
        $total_usuarios = count($usuarios);
        $total_admins = count(array_filter($usuarios, fn($u) => $u['rol'] === 'administrador'));
        $total_regulares = $total_usuarios - $total_admins;

        // Cargar la Vista pasando las variables de forma segura
        $vistaPath = __DIR__ . '/../vistas/admin/panel_admin.php';
        if (file_exists($vistaPath)) {
            require_once $vistaPath;
        } else {
            echo "<div style='background: #fee2e2; color: #991b1b; padding: 20px; font-family: sans-serif; margin: 20px; border-radius: 8px;'>";
            echo "<h3>Error de Ruta:</h3>";
            echo "<p>No se pudo encontrar el archivo de la vista en: <code>" . htmlspecialchars($vistaPath) . "</code></p>";
            echo "</div>";
        }
    }
}

// Ejecutar controlador
$controller = new AdminController();
$controller->gestionar($pdo);
