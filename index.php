<?php

require_once __DIR__ . '/conexion.php';
header("Location: login.php");
exit();

$sesionActiva = isset($_SESSION['usuario_id']);
$nombreUsuario = $_SESSION['nombre'] ?? 'Usuario';
$rolUsuario = $_SESSION['rol'] ?? 'usuario';
$urlPanel = ($rolUsuario === 'administrador') ? 'panel_admin.php' : 'panel_usuario.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIARM: Sistema Inteligente de Alerta de Lluvia y Protección para el Hogar.">
    <meta name="theme-color" content="#0b2545">
    <title>SIARM | Protección inteligente para el hogar</title>

    <!-- Utilizamos una sola hoja de estilos -->
    <link rel="stylesheet" href="principal.css">
    <style>
        /* Estilo compacto y proporcionado para el botón del menú superior */
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
                    <a href="<?= $urlPanel ?>" class="boton">
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
                <p>
                    Tecnología aplicada a la prevención de inundaciones
                    en una vivienda familiar de Mérida, Yucatán.
                </p>
            </div>

            <div class="dos-columnas">
                <article class="tarjeta">
                    <div class="icono">🌧️</div>
                    <h3>El problema</h3>
                    <p>
                        Las lluvias intensas pueden provocar acumulación
                        de agua en patios y entradas. Cuando el nivel sube
                        rápidamente, la familia puede tener poco tiempo
                        para proteger sus pertenencias.
                    </p>
                </article>

                <article class="tarjeta">
                    <div class="icono">🛡️</div>
                    <h3>La solución propuesta</h3>
                    <p>
                        SIARM busca monitorear las condiciones ambientales
                        mediante sensores, analizar los datos y generar
                        avisos para que la familia pueda tomar
                        precauciones a tiempo.
                    </p>
                </article>
            </div>

        </div>
    </section>

    <!-- SENSORES -->
    <section class="seccion seccion-azul" id="sensores">
        <div class="contenedor">

            <div class="titulo-seccion">
                <span>TECNOLOGÍA</span>
                <h2>¿Qué puede monitorear SIARM?</h2>
                <p>
                    Los sensores permitirán conocer las condiciones
                    ambientales alrededor de la vivienda.
                </p>
            </div>

            <div class="rejilla-sensores">

                <article class="sensor">
                    <div class="icono">🌧️</div>
                    <h3>Lluvia y precipitación</h3>
                    <p>
                        Permite detectar la lluvia y medir la
                        precipitación cuando el sensor esté conectado.
                    </p>
                </article>

                <article class="sensor">
                    <div class="icono">🌊</div>
                    <h3>Nivel del agua</h3>
                    <p>
                        Vigila la acumulación de agua en el patio
                        o la entrada para identificar posibles riesgos.
                    </p>
                </article>

                <article class="sensor">
                    <div class="icono">🌡️</div>
                    <h3>Temperatura</h3>
                    <p>
                        Registra la temperatura del ambiente y muestra
                        los valores disponibles en la página.
                    </p>
                </article>

                <article class="sensor">
                    <div class="icono">💧</div>
                    <h3>Humedad</h3>
                    <p>
                        Mide la humedad relativa del aire para conocer
                        las condiciones ambientales.
                    </p>
                </article>

                <article class="sensor">
                    <div class="icono">🍃</div>
                    <h3>Calidad del aire</h3>
                    <p>
                        Puede incorporar un sensor adicional para
                        consultar indicadores de calidad del aire.
                    </p>
                </article>

                <article class="sensor">
                    <div class="icono">📲</div>
                    <h3>Alertas por WhatsApp</h3>
                    <p>
                        Se contempla enviar notificaciones a la familia
                        cuando se detecten condiciones de riesgo.
                    </p>
                </article>

            </div>

        </div>
    </section>

    <!-- FUNCIONAMIENTO -->
    <section class="seccion" id="funcionamiento">
        <div class="contenedor">

            <div class="titulo-seccion">
                <span>¿CÓMO FUNCIONA?</span>
                <h2>De los sensores a la alerta</h2>
                <p>
                    SIARM busca transformar los datos recopilados
                    en información útil para la familia.
                </p>
            </div>

            <div class="pasos">

                <article class="paso">
                    <span class="numero">01</span>
                    <h3>Medición</h3>
                    <p>
                        Los sensores recopilan información sobre lluvia,
                        nivel del agua y condiciones ambientales.
                    </p>
                </article>

                <article class="paso">
                    <span class="numero">02</span>
                    <h3>Análisis</h3>
                    <p>
                        El sistema compara las lecturas con reglas
                        de prevención para identificar riesgos.
                    </p>
                </article>

                <article class="paso">
                    <span class="numero">03</span>
                    <h3>Almacenamiento</h3>
                    <p>
                        La nube puede guardar los datos y permitir
                        consultarlos desde un dispositivo con internet.
                    </p>
                </article>

                <article class="paso">
                    <span class="numero">04</span>
                    <h3>Notificación</h3>
                    <p>
                        Se contempla avisar a la familia cuando
                        se detecte una situación de riesgo.
                    </p>
                </article>

            </div>

        </div>
    </section>

    <!-- OBJETIVOS -->
    <section class="seccion seccion-objetivos" id="objetivos">
        <div class="contenedor">

            <div class="titulo-seccion">
                <span>PROPÓSITO DEL PROYECTO</span>
                <h2>¿Qué queremos lograr?</h2>
            </div>

            <article class="objetivo-principal">
                <h3>Objetivo general</h3>
                <p>
                    Desarrollar una propuesta de sistema inteligente
                    que utilice Internet de las Cosas, inteligencia
                    artificial y servicios en la nube para monitorear
                    las condiciones ambientales y advertir sobre
                    posibles riesgos de inundación en una vivienda familiar.
                </p>
            </article>

            <div class="dos-columnas">

                <article class="tarjeta">
                    <div class="icono">🎯</div>
                    <h3>Objetivos específicos</h3>
                    <ul>
                        <li>Recopilar información mediante sensores.</li>
                        <li>Mostrar las mediciones en una interfaz web.</li>
                        <li>Definir niveles de alerta.</li>
                        <li>Diseñar notificaciones para la familia.</li>
                        <li>Evaluar el uso de datos para estimar riesgos.</li>
                    </ul>
                </article>

                <article class="tarjeta">
                    <div class="icono">🧠</div>
                    <h3>Innovación tecnológica</h3>
                    <p>
                        El análisis de datos históricos podría ayudar
                        a identificar tendencias y estimar la probabilidad
                        de inundación. Para hacerlo correctamente,
                        se necesitarán datos reales y pruebas del sistema.
                    </p>
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

                <p>Tu sesión está iniciada.</p>

                <div class="tarjetas dos">
                    <article class="tarjeta">
                        <div class="icono">🏠</div>
                        <h3>Mi panel</h3>
                        <p>
                            Entra a tu área personal para consultar
                            las funciones disponibles.
                        </p>
                        <a href="<?= $urlPanel ?>" class="boton">Ir a mi panel</a>
                    </article>

                    <article class="tarjeta">
                        <div class="icono">🔒</div>
                        <h3>Cerrar sesión</h3>
                        <p>
                            Finaliza tu sesión cuando termines
                            de utilizar el sistema.
                        </p>
                        <a href="cerrar_sesion.php" class="boton">
                            Cerrar sesión
                        </a>
                    </article>
                </div>

            <?php else: ?>

                <p>
                    Inicia sesión si ya tienes una cuenta o crea
                    una cuenta de usuario para acceder al sistema.
                </p>

                <div class="tarjetas dos">

                    <article class="tarjeta">
                        <div class="icono">🔐</div>
                        <h3>Iniciar sesión</h3>
                        <p>
                            Accede con los datos de tu cuenta registrada.
                        </p>
                        <a href="login.php" class="boton">
                            Iniciar sesión
                        </a>
                    </article>

                    <article class="tarjeta">
                        <div class="icono">👤</div>
                        <h3>Crear cuenta</h3>
                        <p>
                            Registra una cuenta de usuario para
                            acceder a las funciones disponibles.
                        </p>
                        <a href="registro.php" class="boton">
                            Crear cuenta
                        </a>
                    </article>

                </div>

                <p class="nota">
                    El registro público debe crear cuentas de usuario,
                    no cuentas de administrador. El acceso de administrador
                    debe configurarse de forma controlada.
                </p>

            <?php endif; ?>

        </div>
    </section>

    <!-- AVISO -->
    <section class="seccion">
        <div class="contenedor">
            <div class="aviso">
                <span>⚠️</span>
                <div>
                    <h3>Proyecto en desarrollo</h3>
                    <p>
                        Las mediciones en tiempo real, las predicciones
                        y las alertas por WhatsApp requieren integrar
                        sensores, programación y servicios externos.
                        Este sistema no sustituye las indicaciones
                        de Protección Civil.
                    </p>
                </div>
            </div>
        </div>
    </section>

</main>

<!-- PIE DE PÁGINA -->
<footer>
    <div class="contenedor pie">
        <div>
            <h3>SIARM 🌧️</h3>
            <p>
                Sistema Inteligente de Alerta de Lluvia
                y Protección para el Hogar
            </p>
        </div>

        <p>Proyecto académico · Mérida, Yucatán, México</p>
        <p>SIARM © <?= date('Y') ?></p>
    </div>
</footer>

</body>
</html>
