<?php
// index.php
require_once __DIR__ . '/conexion.php';

// Asegurar sesión activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unificar la detección del ID de usuario (soporta tanto 'id' como 'usuario_id')
$userId = $_SESSION['id'] ?? $_SESSION['usuario_id'] ?? null;
$sesionActiva = !empty($userId);

$nombreUsuario = $_SESSION['nombre'] ?? $_SESSION['usuario'] ?? 'Usuario';
$rolUsuario = $_SESSION['rol'] ?? 'usuario';

// Definir la ruta del panel según el rol
$urlPanel = ($rolUsuario === 'administrador') ? 'admin.php' : 'vista_general.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIARM: Sistema Inteligente de Alerta de Lluvia y Protección para el Hogar.">
    <meta name="theme-color" content="#0b2545">
    <title>SIARM | Protección inteligente para el hogar</title>

    <link rel="stylesheet" href="principal.css">
    <style>
        .navegacion .boton-nav {
            background-color: #38bdf8;
            color: #0b2545 !important;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.2s;
            display: inline-block;
        }
        .navegacion .boton-nav:hover {
            background-color: #7dd3fc;
        }
    </style>
</head>

<body>

<!-- ENCABEZADO -->
<header class="encabezado">
    <div class="contenedor navegacion">
        <a href="#inicio" class="logo">
            <span class="logo-icono">🌧️</span>
            <span>
                SIARM
                <small>PROTECCIÓN INTELIGENTE</small>
            </span>
        </a>

        <nav aria-label="Navegación principal">
            <a href="#inicio">Inicio</a>
            <a href="#proyecto">Proyecto</a>
            <a href="#sensores">Sensores</a>
            <a href="#funcionamiento">Funcionamiento</a>
            <a href="#objetivos">Objetivos</a>
            <a href="#acceso">Acceso</a>

            <?php if ($sesionActiva): ?>
                <a href="cerrar_sesion.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php" class="boton-nav">Iniciar sesión</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main>
    <!-- INICIO -->
    <section class="portada" id="inicio">
        <div class="contenedor portada-contenido">
            <div class="portada-texto">
                <span class="etiqueta">
                    IoT · Inteligencia Artificial · Nube
                </span>

                <h1>
                    Protege tu hogar
                    <span>antes de que llegue el agua.</span>
                </h1>

                <p>
                    SIARM es un proyecto tecnológico que busca ayudar
                    a las familias de Mérida, Yucatán, a detectar
                    condiciones de riesgo por lluvia y posibles
                    inundaciones dentro de su vivienda.
                </p>

                <?php if ($sesionActiva): ?>
                    <a href="<?= htmlspecialchars($urlPanel, ENT_QUOTES, 'UTF-8') ?>" class="boton">
                        Ir a mi panel →
                    </a>
                <?php else: ?>
                    <a href="#proyecto" class="boton">
                        Conocer el proyecto →
                    </a>
                    <a href="login.php" class="boton boton-outline">
                        Iniciar sesión
                    </a>
                <?php endif; ?>
            </div>

            <div class="portada-visual">
                <div class="circulo">
                    <div class="casa">⌂</div>
                    <span class="gota gota-1">💧</span>
                    <span class="gota gota-2">💧</span>
                    <span class="gota gota-3">💧</span>
                </div>

                <div class="etiqueta-flotante">
                    <span>📡</span>
                    <div>
                        <strong>Monitoreo inteligente</strong>
                        <small>Información ambiental</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROYECTO -->
    <section class="seccion" id="proyecto">
        <div class="contenedor">
            <div class="titulo-seccion">
                <span>CONOCE SIARM</span>
                <h2>Una idea para cuidar lo más importante</h2>
                <p>Tecnología aplicada a la prevención de inundaciones en una vivienda familiar de Mérida, Yucatán.</p>
            </div>

            <div class="dos-columnas">
                <article class="tarjeta">
                    <div class="icono">🌧️</div>
                    <h3>El problema</h3>
                    <p>Las lluvias intensas pueden provocar acumulación de agua en patios y entradas. Cuando el nivel sube rápidamente, la familia tiene poco tiempo de reacción.</p>
                </article>

                <article class="tarjeta">
                    <div class="icono">🛡️</div>
                    <h3>La solución propuesta</h3>
                    <p>SIARM monitorea las condiciones ambientales mediante sensores, analiza los datos y genera avisos oportunos para prevenir riesgos.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ACCESO -->
    <section class="registro-seccion" id="acceso">
        <div class="contenedor">
            <span class="etiqueta">ACCESO AL SISTEMA</span>
            <h2>
                <?php if ($sesionActiva): ?>
                    Bienvenido, <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?>
                <?php else: ?>
                    Accede a SIARM
                <?php endif; ?>
            </h2>

            <?php if ($sesionActiva): ?>
                <p>Tu sesión está activa actualmente.</p>
                <div class="tarjetas dos">
                    <article class="tarjeta">
                        <div class="icono">🏠</div>
                        <h3>Mi panel</h3>
                        <p>Entra a tu área personal para consultar las funciones y telemetría en vivo.</p>
                        <a href="<?= htmlspecialchars($urlPanel, ENT_QUOTES, 'UTF-8') ?>" class="boton">Ir a mi panel</a>
                    </article>

                    <article class="tarjeta">
                        <div class="icono">🔒</div>
                        <h3>Cerrar sesión</h3>
                        <p>Finaliza tu sesión de manera segura cuando termines de utilizar el sistema.</p>
                        <a href="cerrar_sesion.php" class="boton">Cerrar sesión</a>
                    </article>
                </div>
            <?php else: ?>
                <p>Inicia sesión si ya tienes una cuenta o regístrate para acceder al sistema.</p>
                <div class="tarjetas dos">
                    <article class="tarjeta">
                        <div class="icono">🔐</div>
                        <h3>Iniciar sesión</h3>
                        <p>Accede con tus credenciales registradas.</p>
                        <a href="login.php" class="boton">Iniciar sesión</a>
                    </article>

                    <article class="tarjeta">
                        <div class="icono">👤</div>
                        <h3>Crear cuenta</h3>
                        <p>Registra una cuenta de usuario para acceder a la plataforma.</p>
                        <a href="registro.php" class="boton">Crear cuenta</a>
                    </article>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer>
    <div class="contenedor pie">
        <div>
            <h3>SIARM 🌧️</h3>
            <p>Sistema Inteligente de Alerta de Lluvia y Protección para el Hogar</p>
        </div>
        <p>Proyecto académico · Mérida, Yucatán, México</p>
        <p>SIARM © <?= date('Y') ?></p>
    </div>
</footer>

</body>
</html>
