<?php
// vistas/admin/panel_admin.php
require_once __DIR__ . '/../../conexion.php';

// Asegurar que las variables existan para evitar errores si se accede de forma directa
$nombre = $nombre ?? $_SESSION['nombre'] ?? 'Administrador';
$usuarios = $usuarios ?? [];
$total_usuarios = $total_usuarios ?? count($usuarios);
$total_admins = $total_admins ?? count(array_filter($usuarios, fn($u) => $u['rol'] === 'administrador'));
$total_regulares = $total_regulares ?? ($total_usuarios - $total_admins);
$mensaje_error = $mensaje_error ?? '';
$mensaje_exito = $mensaje_exito ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | SIARM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: system-ui, -apple-system, sans-serif; }
        body { background-color: #06111c; color: #e2e8f0; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        aside { width: 260px; background: #0b192c; border-right: 1px solid #1e293b; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 20px; font-size: 1.2rem; font-weight: bold; color: #38bdf8; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #1e293b; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex: 1; }
        .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #94a3b8; text-decoration: none; transition: 0.2s; }
        .sidebar-menu li.active a, .sidebar-menu li a:hover { background: #1e293b; color: #38bdf8; }
        
        /* Main Content */
        main { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
        header { background: #0b192c; border-bottom: 1px solid #1e293b; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        
        .content { padding: 30px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #0b192c; border: 1px solid #1e293b; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); }
        .stat-card h3 { font-size: 0.85rem; color: #94a3b8; margin-bottom: 8px; }
        .stat-card p { font-size: 1.8rem; font-weight: bold; color: #fff; }

        /* Tables & Alerts */
        .card { background: #0b192c; border: 1px solid #1e293b; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 12px; border-bottom: 1px solid #1e293b; font-size: 0.9rem; }
        th { color: #94a3b8; font-weight: 600; }
        
        .btn { background: #38bdf8; color: #0b192c; border: none; padding: 8px 14px; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { opacity: 0.9; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-secondary { background: #1e293b; color: #f8fafc; border: 1px solid #334155; }
        
        .badge-rol { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; }
        .badge-admin { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
        .badge-user { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }

        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-error { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .alert-success { background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.3); color: #86efac; }

        /* Modales */
        .modal-bg {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            justify-content: center;
            align-items: center;
            z-index: 100;
        }
        .modal {
            background: #0b192c;
            border: 1px solid #1e293b;
            padding: 24px;
            border-radius: 12px;
            width: 100%;
            max-width: 450px;
        }
        .modal label { font-size: 0.85rem; color: #94a3b8; display: block; margin-top: 10px; }
        .modal input, .modal select {
            width: 100%;
            padding: 10px;
            background: #06111c;
            border: 1px solid #334155;
            color: #fff;
            border-radius: 6px;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside>
        <div class="sidebar-brand">
            <i class="fa-solid fa-shield-halved"></i> SIARM Admin
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="admin.php"><i class="fa-solid fa-users-gear"></i> Gestión de Usuarios</a></li>
            <li><a href="vista_general.php"><i class="fa-solid fa-house-laptop"></i> Vista General</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main>
        <header>
            <h2>Panel de Control</h2>
            <div class="user-info">
                <span><i class="fa-solid fa-user-shield"></i> <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></span>
                <a href="cerrar_sesion.php" class="btn btn-danger">
                    <i class="fa-solid fa-power-off"></i> Salir
                </a>
            </div>
        </header>

        <div class="content">
            <?php if (!empty($mensaje_error)): ?>
                <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($mensaje_error, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <?php if (!empty($mensaje_exito)): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($mensaje_exito, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total de Usuarios</h3>
                    <p><?= $total_usuarios ?></p>
                </div>
                <div class="stat-card">
                    <h3>Administradores</h3>
                    <p><?= $total_admins ?></p>
                </div>
                <div class="stat-card">
                    <h3>Usuarios Regulares</h3>
                    <p><?= $total_regulares ?></p>
                </div>
            </div>

            <!-- Tabla CRUD de Usuarios -->
            <div class="card">
                <div class="card-header">
                    <h3>Listado de Usuarios Registrados</h3>
                    <button class="btn" onclick="openModal('userModal')">
                        <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
                    </button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($usuarios)): ?>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <?php if ($u['rol'] === 'administrador'): ?>
                                            <span class="badge-rol badge-admin">Administrador</span>
                                        <?php else: ?>
                                            <span class="badge-rol badge-user">Usuario</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px; align-items: center;">
                                            <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 0.8rem;" title="Editar usuario" onclick="openEditModal(<?= $u['id'] ?>, '<?= str_replace("'", "\\'", htmlspecialchars($u['nombre'], ENT_QUOTES, 'UTF-8')) ?>', '<?= str_replace("'", "\\'", htmlspecialchars($u['correo'], ENT_QUOTES, 'UTF-8')) ?>', '<?= htmlspecialchars($u['rol'], ENT_QUOTES, 'UTF-8') ?>')">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <?php if ($u['id'] != ($_SESSION['id'] ?? 0)): ?>
                                                <form method="POST" action="admin.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 0.8rem;" title="Eliminar usuario">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span style="color: #64748b; font-size: 0.8rem;">(Tu cuenta)</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">No hay usuarios registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal para Crear Usuario -->
    <div class="modal-bg" id="userModal">
        <div class="modal">
            <h3 style="color:#fff; margin-bottom:15px;"><i class="fa-solid fa-user-plus"></i> Registrar Nuevo Usuario</h3>
            <form method="POST" action="admin.php">
                <input type="hidden" name="accion" value="crear">
                
                <label>Nombre Completo</label>
                <input type="text" name="nombre" required placeholder="Ej. Juan Pérez">

                <label>Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="correo@ejemplo.com">

                <label>Contraseña</label>
                <input type="password" name="contrasena" required placeholder="********">

                <label>Rol de Usuario</label>
                <select name="rol">
                    <option value="usuario">Usuario Regular</option>
                    <option value="administrador">Administrador</option>
                </select>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn" style="flex:1;">Guardar Usuario</button>
                    <button type="button" class="btn btn-secondary" style="flex:1;" onclick="closeModal('userModal')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Editar Usuario -->
    <div class="modal-bg" id="editUserModal">
        <div class="modal">
            <h3 style="color:#fff; margin-bottom:15px;"><i class="fa-solid fa-user-pen"></i> Editar Usuario</h3>
            <form method="POST" action="admin.php">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id" id="editUserId">

                <label>Nombre Completo</label>
                <input type="text" name="nombre" id="editNombre" required placeholder="Ej. Juan Pérez">

                <label>Correo Electrónico</label>
                <input type="email" name="correo" id="editCorreo" required placeholder="correo@ejemplo.com">

                <label>Contraseña</label>
                <input type="password" name="contrasena" id="editPassword" placeholder="Dejar vacío para conservar la actual">

                <label>Rol de Usuario</label>
                <select name="rol" id="editRol">
                    <option value="usuario">Usuario Regular</option>
                    <option value="administrador">Administrador</option>
                </select>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn" style="flex:1;">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" style="flex:1;" onclick="closeModal('editUserModal')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function openEditModal(id, nombre, correo, rol) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editNombre').value = nombre;
            document.getElementById('editCorreo').value = correo;
            document.getElementById('editRol').value = rol;
            document.getElementById('editPassword').value = '';
            document.getElementById('editUserModal').style.display = 'flex';
        }
    </script>
</body>
</html>