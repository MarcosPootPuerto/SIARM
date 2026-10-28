// Variables de Estado
let systemState = {
    waterLevel: 100,        // Ajustado al valor actual de tu panel
    rainStatus: 'Lluvia Intensa',
    temp: 29.0,
    humidity: 75,
    warnThreshold: 50,
    critThreshold: 80,
    pumpActive: true,
    sirenActive: true
};

let chartInstance = null;

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    initChart();
    updateUI(); // 👈 ¡ESTO FALTABA! Actualiza el estado, la barra y el badge al cargar la página.
});

// Gráfica con Chart.js
function initChart() {
    const ctx = document.getElementById('telemetryChart').getContext('2d');
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['10:00', '10:05', '10:10', '10:15', '10:20', '10:25'],
            datasets: [{
                label: 'Nivel de Agua (%)',
                data: [10, 12, 15, 20, 30, 100], // Sincronizado con el 100% final
                borderColor: '#0284c7',
                backgroundColor: 'rgba(2, 132, 199, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { min: 0, max: 100, grid: { color: '#334155' } },
                x: { grid: { color: '#334155' } }
            },
            plugins: { legend: { labels: { color: '#f8fafc' } } }
        }
    });
}

// Actualizar Dashboard
function updateUI() {
    // Tanque
    document.getElementById('waterFill').style.height = `${systemState.waterLevel}%`;
    document.getElementById('waterPercentage').innerText = `${systemState.waterLevel}%`;
    document.getElementById('waterMM').innerText = `${systemState.waterLevel * 10} mm`;

    // Sensores
    document.getElementById('rainStatus').innerText = systemState.rainStatus;
    document.getElementById('tempValue').innerText = systemState.temp;
    document.getElementById('humidityValue').innerText = systemState.humidity;

    // Estado Global y Alertas (Evaluación de Umbrales)
    const badge = document.getElementById('riskBadge');
    const riskText = document.getElementById('riskText');

    if (systemState.waterLevel >= systemState.critThreshold) {
        badge.style.background = '#7f1d1d';
        badge.style.color = '#fca5a5';
        riskText.innerText = 'ESTADO: CRÍTICO';
    } else if (systemState.waterLevel >= systemState.warnThreshold) {
        badge.style.background = '#78350f';
        badge.style.color = '#fde68a';
        riskText.innerText = 'ESTADO: PRECAUCIÓN';
    } else {
        badge.style.background = '#064e3b';
        badge.style.color = '#6ee7b7';
        riskText.innerText = 'ESTADO: NORMAL';
    }
}

// Modal y Simulador
function toggleSimulator() {
    const modal = document.getElementById('simulatorModal');
    modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
}

function applySimulation() {
    systemState.waterLevel = parseInt(document.getElementById('simWater').value);
    systemState.rainStatus = document.getElementById('simRain').value;
    systemState.temp = parseFloat(document.getElementById('simTemp').value);
    systemState.humidity = parseInt(document.getElementById('simHum').value);

    updateUI();
    addChartData(systemState.waterLevel);
    addLog('Simulación Manual', `${systemState.waterLevel}%`, systemState.waterLevel >= systemState.critThreshold ? 'CRÍTICO' : 'INFO');
    toggleSimulator();
}

function triggerPreset(type) {
    if (type === 'flood') {
        document.getElementById('simWater').value = 85;
        document.getElementById('simRain').value = 'Lluvia Intensa';
    } else {
        document.getElementById('simWater').value = 20;
        document.getElementById('simRain').value = 'Sin Lluvia';
    }
    applySimulation();
}

// Disparadores
function togglePump(val) {
    systemState.pumpActive = val;
    document.getElementById('relayStatusText').innerText = val ? 'Bomba ACTIVA' : 'Inactivo';
    addLog(val ? 'Bomba Encendida (Manual)' : 'Bomba Apagada', `${systemState.waterLevel}%`, 'INFO');
}

function toggleSiren(val) {
    systemState.sirenActive = val;
    addLog(val ? 'Sirena Activada' : 'Sirena Desactivada', `${systemState.waterLevel}%`, 'WARNING');
}

function updateThresholds() {
    systemState.warnThreshold = parseInt(document.getElementById('warningRange').value);
    systemState.critThreshold = parseInt(document.getElementById('criticalRange').value);
    document.getElementById('warnVal').innerText = `${systemState.warnThreshold}%`;
    document.getElementById('critVal').innerText = `${systemState.critThreshold}%`;
    updateUI();
}

// Notificaciones y Logs
function triggerAlert(level, msg) {
    const chat = document.getElementById('whatsappChat');
    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    const msgDiv = document.createElement('div');
    msgDiv.className = 'message incoming';
    msgDiv.innerHTML = `<strong>[ALERTA ${level}]</strong><br>${msg}<span class="msg-time">${time}</span>`;
    
    chat.appendChild(msgDiv);
    chat.scrollTop = chat.scrollHeight;
}

function addChartData(val) {
    if (!chartInstance) return;
    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    chartInstance.data.labels.push(time);
    chartInstance.data.datasets[0].data.push(val);
    if (chartInstance.data.labels.length > 8) {
        chartInstance.data.labels.shift();
        chartInstance.data.datasets[0].data.shift();
    }
    chartInstance.update();
}

function addLog(event, water, level) {
    const tbody = document.getElementById('logsBody');
    const time = new Date().toLocaleString();
    const badgeClass = level === 'CRÍTICO' ? 'badge-danger' : (level === 'WARNING' ? 'badge-warning' : 'badge-info');
    
    const row = `<tr>
        <td>${time}</td>
        <td>${event}</td>
        <td>${water}</td>
        <td><span class="badge ${badgeClass}">${level}</span></td>
    </tr>`;
    
    tbody.innerHTML = row + tbody.innerHTML;
}

function exportCSV() {
    alert('Exportando reporte de eventos de SIARM a formato .CSV...');
}