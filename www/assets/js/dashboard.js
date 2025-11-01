const apiBase = '/api/dashboard.php';
const numberFormatter = new Intl.NumberFormat('es-PE');

document.addEventListener('DOMContentLoaded', () => {
    loadOverview();
});

async function fetchJson(action, params = {}) {
    const url = new URL(apiBase, window.location.origin);
    url.searchParams.set('action', action);
    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
            url.searchParams.set(key, String(value));
        }
    });
    const response = await fetch(url.toString());
    if (!response.ok) {
        throw new Error('No se pudo obtener datos');
    }
    return response.json();
}

async function loadOverview() {
    try {
        const response = await fetchJson('overview');
        // El API devuelve {ok: true, data: {...}}
        const data = response.data || response;
        
        console.log('Dashboard data:', data); // Debug
        
        // Actualizar solo las dos métricas del dashboard simplificado
        updateMetric('certificates-this-month', numberFormatter.format(data.certificates?.this_month ?? 0));
        updateMetric('equipment-overdue', numberFormatter.format(data.equipment?.overdue ?? 0));
    } catch (error) {
        console.error('Error al cargar las métricas del dashboard:', error);
        // Mostrar 0 en caso de error
        updateMetric('certificates-this-month', '0');
        updateMetric('equipment-overdue', '0');
    }
}

function updateMetric(name, value) {
    const el = document.querySelector(`[data-metric="${name}"]`);
    if (el) {
        el.textContent = value;
    }
}
