<?php
// vista_general.php
require_once __DIR__ . '/conexion.php';
exigir_sesion();

$nombre = $_SESSION['nombre'] ?? 'Administrador';
$es_admin = (($_SESSION['rol'] ?? '') === 'administrador');
?>
<!DOCTYPE html>
<html lang="es" class="device-disconnected">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista General / Telemetría | SIARM</title>
    <!-- FontAwesome para Íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: #06111c;
            color: #e2e8f0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }

        /* Header Minimalista y Amplio */
        header {
            background-color: #0b192c;
            border-bottom: 1px solid #1e293b;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo-area { display: flex; align-items: center; gap: 12px; }
        .logo-area i { font-size: 1.8rem; color: #38bdf8; }
        .logo-area h1 { font-size: 1.25rem; font-weight: 600; color: #fff; }
        .logo-area p { font-size: 0.8rem; color: #94a3b8; }

        .header-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .badge {
            background: #1e293b;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #94a3b8;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .badge .dot { width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; }
        .badge.active .dot { background: #22c55e; }
        .badge.active { color: #f8fafc; }

        .btn {
            background: #38bdf8;
            color: #0b192c;
            border: none;
            padding: 7px 14px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn:hover { opacity: 0.9; }
        .btn-secondary { background: #1e293b; color: #f8fafc; border: 1px solid #334155; }
        .btn-danger { background: #ef4444; color: #fff; }

        /* Contenedor Principal Centrado y Amplio */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 20px;
        }

        /* Grid General */
        .grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }
        .card {
            background: #0b192c;
            border: 1px solid #1e293b;
            border-radius: 12px;
            padding: 20px;
        }
        .card h3 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .col-4 { grid-column: span 4; }
        .col-5 { grid-column: span 5; }
        .col-3 { grid-column: span 3; }
        .col-6 { grid-column: span 6; }
        .col-12 { grid-column: span 12; }

        @media(max-width: 1024px) {
            .col-4, .col-5, .col-3, .col-6 { grid-column: span 12; }
        }

        /* Estado Desconectado */
        .device-disconnected .dimmed { color: #475569 !important; }

        /* Tanque */
        .tank-box { display: flex; align-items: center; gap: 20px; }
        .tank-visual {
            width: 70px;
            height: 160px;
            background: #06111c;
            border: 2px solid #1e293b;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }
        .tank-fill {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 0%;
            background: linear-gradient(to top, #0284c7, #38bdf8);
            transition: height 0.4s ease;
        }
        .tank-data h2 { font-size: 1.8rem; font-weight: bold; color: #38bdf8; margin-bottom: 4px; }
        .tank-data p { font-size: 0.85rem; color: #94a3b8; }

        /* Sensores Grid Extendido */
        .sensors-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .sensor-item {
            background: #06111c;
            border: 1px solid #1e293b;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sensor-item i { font-size: 1.4rem; color: #38bdf8; }
        .sensor-item span { font-size: 0.75rem; color: #94a3b8; display: block; }
        .sensor-item p { font-size: 0.95rem; font-weight: 600; color: #f8fafc; }

        /* Controles Switch y Rangos */
        .control-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #06111c;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #1e293b;
        }
        .control-row span { font-size: 0.85rem; font-weight: 500; }
        .threshold-box { margin-top: 15px; font-size: 0.85rem; color: #94a3b8; }
        .threshold-box input[type="range"] { width: 100%; margin-top: 4px; accent-color: #38bdf8; }

        /* Chat Box */
        .chat-box {
            background: #06111c;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 12px;
            height: 180px;
            overflow-y: auto;
            font-size: 0.85rem;
        }
        .chat-msg {
            background: #1e293b;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 8px;
        }
        .chat-msg span { display: block; font-size: 0.7rem; color: #94a3b8; text-align: right; margin-top: 4px; }

        /* Tabla de Bitácora */
        .table-wrap {
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid #1e293b;
            border-radius: 8px;
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.8rem; text-align: left; }
        th { background: #06111c; color: #94a3b8; padding: 8px; position: sticky; top: 0; }
        td { padding: 8px; border-top: 1px solid #1e293b; color: #cbd5e1; }

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
            max-width: 400px;
        }
        .modal input, .modal select {
            width: 100%;
            padding: 10px;
            background: #06111c;
            border: 1px solid #334155;
            color: #fff;
            border-radius: 6px;
            margin: 12px 0 20px 0;
        }
    </style>
</head>
<body class="device-disconnected">

    <!-- Header -->
    <header>
        <div class="logo-area">
            <i class="fa-solid fa-shield-halved"></i>
            <div>
                <h1>SIARM</h1>
                <p>Vista General · <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        <div class="header-actions">
            <?php if ($es_admin): ?>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fa-solid fa-users-gear"></i> Panel Admin
                </a>
            <?php endif; ?>
            <div class="badge" id="riskBadge">
                <span class="dot" id="statusDot"></span>
                <span id="riskText">SIN DISPOSITIVO</span>
            </div>
            <div class="badge">
                <i class="fa-solid fa-wifi"></i> <span id="esp32Status">Desconectado</span>
            </div>
            <button class="btn" id="btnConnect" onclick="openModal('deviceModal')">
                <i class="fa-solid fa-plug"></i> Vincular
            </button>
            <button class="btn btn-secondary" id="btnSim" onclick="openModal('simulatorModal')" style="display: none;">
                <i class="fa-solid fa-sliders"></i> Simulador
            </button>
            <a href="cerrar_sesion.php" class="btn btn-danger" style="padding: 7px 10px;"><i class="fa-solid fa-power-off"></i></a>
        </div>
    </header>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="grid">

            <!-- Fila 1: Tanque, Sensores y Actuadores -->
            <div class="card col-4">
                <h3><i class="fa-solid fa-water"></i> Nivel de Inundación</h3>
                <div class="tank-box">
                    <div class="tank-visual">
                        <div class="tank-fill" id="waterFill"></div>
                    </div>
                    <div class="tank-data">
                        <h2 id="waterPercentage" class="dimmed">--%</h2>
                        <p>Nivel: <strong id="waterMM" class="dimmed">-- mm</strong></p>
                        <p style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">Max: 1000 mm</p>
                    </div>
                </div>
            </div>

            <div class="card col-5">
                <h3><i class="fa-solid fa-microchip"></i> Telemetría en Vivo</h3>
                <div class="sensors-grid">
                    <div class="sensor-item">
                        <i class="fa-solid fa-cloud-rain"></i>
                        <div><span>Lluvia</span><p id="rainStatus" class="dimmed">--</p></div>
                    </div>
                    <div class="sensor-item">
                        <i class="fa-solid fa-temperature-half"></i>
                        <div><span>Temperatura</span><p><strong id="tempValue" class="dimmed">--</strong> °C</p></div>
                    </div>
                    <div class="sensor-item">
                        <i class="fa-solid fa-droplet"></i>
                        <div><span>Humedad</span><p><strong id="humidityValue" class="dimmed">--</strong> %</p></div>
                    </div>
                    <div class="sensor-item">
                        <i class="fa-solid fa-wifi"></i>
                        <div><span>Señal Wi-Fi</span><p id="wifiRssi" class="dimmed">-- dBm</p></div>
                    </div>
                    <div class="sensor-item">
                        <i class="fa-solid fa-bolt"></i>
                        <div><span>Relevador</span><p id="relayStatusText" class="dimmed">Inactivo</p></div>
                    </div>
                    <div class="sensor-item">
                        <i class="fa-solid fa-car-battery"></i>
                        <div><span>Batería UPS</span><p id="batteryLevel" class="dimmed">-- V</p></div>
                    </div>
                </div>
            </div>

            <div class="card col-3">
                <h3><i class="fa-solid fa-sliders"></i> Controles</h3>
                <div class="control-row">
                    <span>Bomba Desagüe</span>
                    <input type="checkbox" id="pumpToggle" disabled style="transform: scale(1.2);">
                </div>
                <div class="control-row">
                    <span>Sirena Alerta</span>
                    <input type="checkbox" id="sirenToggle" disabled style="transform: scale(1.2);">
                </div>
                <div class="threshold-box">
                    <label>Alerta Amarilla: <b id="warnVal">50%</b></label>
                    <input type="range" id="warningRange" min="10" max="70" value="50" disabled oninput="document.getElementById('warnVal').innerText = this.value + '%'">
                </div>
            </div>

            <!-- Fila 2: Gráfica de Telemetría -->
            <div class="card col-12">
                <h3><i class="fa-solid fa-chart-line"></i> Histórico de Nivel de Agua (%)</h3>
                <div style="height: 220px; width: 100%;">
                    <canvas id="telemetryChart"></canvas>
                </div>
            </div>

            <!-- Fila 3: Consola de Notificaciones y Bitácora -->
            <div class="card col-6">
                <h3><i class="fa-solid fa-bell" style="color: #38bdf8;"></i> Notificaciones de Alerta (Automáticas)</h3>
                <div class="chat-box" id="whatsappChat">
                    <div class="chat-msg">
                        <strong>[SISTEMA SIARM]</strong><br>
                        Esperando vinculación de dispositivo IoT para activar alertas automáticas.
                        <span>--:--</span>
                    </div>
                </div>
            </div>

            <div class="card col-6">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="margin:0;"><i class="fa-solid fa-clock-rotate-left"></i> Historial y Bitácora</h3>
                    <button class="btn btn-secondary" onclick="exportCSV()" id="btnExport" disabled style="padding: 4px 10px; font-size: 0.75rem;"><i class="fa-solid fa-file-csv"></i> Exportar</button>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha / Hora</th>
                                <th>Evento</th>
                                <th>Nivel</th>
                            </tr>
                        </thead>
                        <tbody id="logsBody">
                            <tr>
                                <td colspan="3" style="text-align: center; color: #64748b; padding: 20px;">Cargando historial...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Vincular -->
    <div class="modal-bg" id="deviceModal">
        <div class="modal">
            <h3 style="color:#fff; margin-bottom:12px;"><i class="fa-solid fa-microchip"></i> Vincular ESP32</h3>
            <p style="font-size:0.85rem; color:#94a3b8;">Ingresa el ID o MAC del dispositivo:</p>
            <input type="text" id="deviceIdInput" placeholder="Ej. ESP32-SIARM-01">
            <div style="display: flex; gap: 10px;">
                <button class="btn" style="flex:1;" onclick="connectDevice()">Vincular</button>
                <button class="btn btn-secondary" style="flex:1;" onclick="closeModal('deviceModal')">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal Simulador -->
    <div class="modal-bg" id="simulatorModal">
        <div class="modal">
            <h3 style="color:#fff; margin-bottom:12px;"><i class="fa-solid fa-sliders"></i> Simulador ESP32</h3>
            <label style="font-size:0.85rem; color:#94a3b8;">Nivel de Agua (%): <b id="simValTxt" style="color:#38bdf8;">35</b>%</label>
            <input type="range" id="simWater" min="0" max="100" value="35" oninput="simValTxt.innerText = this.value">
            
            <label style="font-size:0.85rem; color:#94a3b8;">Lluvia:</label>
            <select id="simRain">
                <option value="Sin Lluvia">Sin Lluvia</option>
                <option value="Lluvia Ligera">Lluvia Ligera</option>
                <option value="Lluvia Intensa">Lluvia Intensa</option>
            </select>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button class="btn" style="flex:1;" onclick="applySim()">Aplicar</button>
                <button class="btn btn-secondary" style="flex:1;" onclick="closeModal('simulatorModal')">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Script de Lógica -->
    <script>
        let isConnected = false;
        let chart;

        window.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('telemetryChart').getContext('2d');
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['10:00', '10:05', '10:10', '10:15', '10:20', '10:25'],
                    datasets: [{
                        label: 'Nivel Agua (%)',
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56, 189, 248, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { min: 0, max: 100, grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });

            // Cargar historial de la base de datos al abrir el panel
            cargarHistorialBD();
        });

        function solicitarPermisoNotificaciones() {
            if ("Notification" in window) {
                if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                    Notification.requestPermission();
                }
            }
        }

        function mostrarNotificacionNativa(titulo, texto) {
            if ("Notification" in window && Notification.permission === "granted") {
                new Notification(titulo, {
                    body: texto,
                    icon: "https://cdn-icons-png.flaticon.com/512/564/564619.png"
                });
            }
        }

        function openModal(id) {
            if(id === 'simulatorModal' && !isConnected) return;
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function connectDevice() {
            const id = document.getElementById('deviceIdInput').value.trim();
            if(!id) { alert("Ingresa un ID válido"); return; }
            
            isConnected = true;
            closeModal('deviceModal');

            // Solicitar permisos de notificación nativa del navegador al vincular
            solicitarPermisoNotificaciones();

            document.body.classList.remove('device-disconnected');
            document.getElementById('esp32Status').innerText = 'Conectado (' + id + ')';
            document.getElementById('riskBadge').classList.add('active');

            const btn = document.getElementById('btnConnect');
            btn.innerHTML = '<i class="fa-solid fa-plug-circle-xmark"></i> Desvincular';
            btn.className = 'btn btn-danger';
            btn.setAttribute('onclick', 'disconnectDevice()');
            document.getElementById('btnSim').style.display = 'inline-block';

            document.getElementById('pumpToggle').disabled = false;
            document.getElementById('sirenToggle').disabled = false;
            document.getElementById('warningRange').disabled = false;
            document.getElementById('btnExport').disabled = false;

            updateData(35, 'Lluvia Ligera', 28.5, 72, '-68 dBm', '4.18 V');
            addLog("Dispositivo ESP32 Vinculado", "35%");
            agregarMensajeChat("SISTEMA SIARM", "Dispositivo " + id + " vinculado. Alertas activas.");
        }

        function disconnectDevice() {
            isConnected = false;
            document.body.classList.add('device-disconnected');
            document.getElementById('esp32Status').innerText = 'Desconectado';
            document.getElementById('riskText').innerText = 'SIN DISPOSITIVO';
            document.getElementById('riskBadge').classList.remove('active');
            document.getElementById('riskBadge').style.background = '#1e293b';
            document.getElementById('riskBadge').style.color = '#94a3b8';

            const btn = document.getElementById('btnConnect');
            btn.innerHTML = '<i class="fa-solid fa-plug"></i> Vincular';
            btn.className = 'btn';
            btn.setAttribute('onclick', "openModal('deviceModal')");
            document.getElementById('btnSim').style.display = 'none';

            document.getElementById('pumpToggle').disabled = true;
            document.getElementById('sirenToggle').disabled = true;
            document.getElementById('warningRange').disabled = true;
            document.getElementById('btnExport').disabled = true;

            updateData(0, '--', '--', '--', '-- dBm', '-- V');
            addLog("Dispositivo Desvinculado", "0%");
            agregarMensajeChat("SISTEMA SIARM", "Dispositivo desvinculado. Monitoreo en pausa.");
        }

        function updateData(val, rain, temp, hum, wifi, batt) {
            document.getElementById('waterFill').style.height = val + '%';
            document.getElementById('waterPercentage').innerText = val + '%';
            document.getElementById('waterMM').innerText = (val * 10) + ' mm';
            document.getElementById('rainStatus').innerText = rain;
            document.getElementById('tempValue').innerText = temp;
            document.getElementById('humidityValue').innerText = hum;
            document.getElementById('wifiRssi').innerText = wifi;
            document.getElementById('batteryLevel').innerText = batt;

            if (isConnected) {
                const riskText = document.getElementById('riskText');
                const riskBadge = document.getElementById('riskBadge');
                const warnThreshold = parseInt(document.getElementById('warningRange').value) || 50;
                const critThreshold = 80;
                const waterVal = Number(val);

                if (waterVal >= critThreshold) {
                    riskText.innerText = 'ESTADO: PELIGRO';
                    riskBadge.style.background = '#7f1d1d';
                    riskBadge.style.color = '#fca5a5';
                    mostrarNotificacionNativa("🚨 PELIGRO SIARM", "Nivel crítico de agua alcanzado: " + waterVal + "%");
                } else if (waterVal >= warnThreshold) {
                    riskText.innerText = 'ESTADO: PRECAUCIÓN';
                    riskBadge.style.background = '#78350f';
                    riskBadge.style.color = '#fde68a';
                    mostrarNotificacionNativa("⚠️ Precaución SIARM", "Nivel de agua elevado: " + waterVal + "%");
                } else {
                    riskText.innerText = 'ESTADO: NORMAL';
                    riskBadge.style.background = '#064e3b';
                    riskBadge.style.color = '#6ee7b7';
                }
            }

            if(chart) {
                chart.data.datasets[0].data = [10, 15, 20, 25, 30, val];
                chart.update();
            }
        }

        function addLog(evento, nivel) {
            const formData = new FormData();
            formData.append('evento', evento);
            formData.append('nivel', nivel);

            // Guardar en la base de datos vía PHP
            fetch('guardar_evento.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarHistorialBD();
                    if (data.es_riesgo) {
                        agregarMensajeChat("Alerta Automática", "Notificación generada para el nivel del " + nivel);
                    }
                }
            })
            .catch(error => console.error("Error al guardar en la bitácora:", error));
        }

        function cargarHistorialBD() {
            fetch('obtener_historial.php')
            .then(response => response.json())
            .then(registros => {
                const tbody = document.getElementById('logsBody');
                if (!registros || registros.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="3" style="text-align: center; color: #64748b; padding: 20px;">Sin registros en la bitácora.</td></tr>`;
                    return;
                }
                
                let html = '';
                registros.forEach(reg => {
                    html += `<tr><td>${reg.hora}</td><td>${reg.evento}</td><td>${reg.nivel}</td></tr>`;
                });
                tbody.innerHTML = html;
            })
            .catch(error => {
                console.error("Error al obtener la bitácora:", error);
                document.getElementById('logsBody').innerHTML = `<tr><td colspan="3" style="text-align: center; color: #ef4444; padding: 20px;">Error al cargar datos.</td></tr>`;
            });
        }

        function applySim() {
            const val = document.getElementById('simWater').value;
            const rain = document.getElementById('simRain').value;
            updateData(val, rain, '29.0', '75', '-65 dBm', '4.20 V');
            addLog("Telemetría Actualizada", val + '%');
            closeModal('simulatorModal');
        }

        function exportCSV() {
            alert("Exportando bitácora de eventos a CSV...");
        }

        function agregarMensajeChat(remitente, texto) {
            const chatBox = document.getElementById('whatsappChat');
            if (chatBox.innerHTML.includes('Esperando vinculación')) {
                chatBox.innerHTML = '';
            }
            
            const horaActual = new Date().toLocaleTimeString();
            const msgDiv = document.createElement('div');
            msgDiv.className = 'chat-msg';
            msgDiv.innerHTML = `<strong>[${remitente}]</strong><br>${texto}<span>${horaActual}</span>`;
            
            chatBox.prepend(msgDiv);
        }
    </script>
</body>
</html>