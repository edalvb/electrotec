const apiBase = '/api/dashboard.php';
const numberFormatter = new Intl.NumberFormat('es-PE');
const percentFormatter = new Intl.NumberFormat('es-PE', { minimumFractionDigits: 0, maximumFractionDigits: 1 });
const dateFormatter = new Intl.DateTimeFormat('es-PE');
let certificatesChart;
let equipmentChart;
let failRateChart;

document.addEventListener('DOMContentLoaded', () => {
    loadOverview();
    const monthsRange = document.getElementById('months-range');
    const failRange = document.getElementById('fail-range');
    const riskLimit = document.getElementById('risk-limit');
    const expiringRange = document.getElementById('expiring-range');
    if (monthsRange) {
        loadCertificatesByMonth(parseInt(monthsRange.value, 10));
        monthsRange.addEventListener('change', () => {
            loadCertificatesByMonth(parseInt(monthsRange.value, 10));
        });
    }
    if (failRange) {
        loadFailRates(parseInt(failRange.value, 10));
        failRange.addEventListener('change', () => {
            loadFailRates(parseInt(failRange.value, 10));
        });
    }
    if (riskLimit) {
        loadRiskRanking(parseInt(riskLimit.value, 10));
        riskLimit.addEventListener('change', () => {
            loadRiskRanking(parseInt(riskLimit.value, 10));
        });
    } else {
        loadRiskRanking(10);
    }
    if (expiringRange) {
        loadExpiringSoon(parseInt(expiringRange.value, 10));
        expiringRange.addEventListener('change', () => {
            loadExpiringSoon(parseInt(expiringRange.value, 10));
        });
    } else {
        loadExpiringSoon(30);
    }
    loadDistributionByEquipmentType();
    loadCoverageByClient();
    loadMissingPdfCertificates(20);
    loadEquipmentWithoutCertificates();
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
        const data = await fetchJson('overview');
        updateMetric('certificates-this-month', numberFormatter.format(data.certificates.this_month ?? 0));
        updateMetric('equipment-compliant', numberFormatter.format(data.equipment.compliant ?? 0));
        updateMetric('equipment-due-30', numberFormatter.format(data.equipment.due_30 ?? 0));
        updateMetric('equipment-overdue', numberFormatter.format(data.equipment.overdue ?? 0));
        updateMetric('pdf-completion', `${percentFormatter.format(data.certificates.pdf_completion_pct ?? 0)}%`);
        updateMetric('clients-active', numberFormatter.format(data.clients.active ?? 0));
        updateMetric('equipment-without-certificate', numberFormatter.format(data.equipment.without_certificate ?? 0));
        updateMetric('clients-new-this-month', numberFormatter.format(data.clients.new_this_month ?? 0));
        const withoutCount = document.querySelector('[data-metric="equipment-without-count"]');
        if (withoutCount) {
            const count = data.equipment.without_certificate ?? 0;
            withoutCount.textContent = `${numberFormatter.format(count)} equipos`;
        }
    } catch (error) {
        console.error(error);
    }
}

async function loadCertificatesByMonth(months) {
    try {
        const data = await fetchJson('certificatesByMonth', { months });
        const labels = data.map(item => item.yyyymm);
        const values = data.map(item => Number(item.total ?? 0));
        const ctx = document.getElementById('chart-certificates');
        if (!ctx) {
            return;
        }
        if (certificatesChart) {
            certificatesChart.data.labels = labels;
            certificatesChart.data.datasets[0].data = values;
            certificatesChart.update();
            return;
        }
        certificatesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Certificados',
                        data: values,
                        borderColor: '#5C66CC',
                        backgroundColor: 'rgba(92, 102, 204, 0.3)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadDistributionByEquipmentType() {
    try {
        const data = await fetchJson('distributionByEquipmentType');
        const labels = data.map(item => item.equipment_type ?? 'Sin tipo');
        const values = data.map(item => Number(item.total ?? 0));
        const ctx = document.getElementById('chart-equipment');
        if (!ctx) {
            return;
        }
        const colors = ['#5C66CC', '#2A2F6C', '#10B981', '#F59E0B', '#EF4444', '#3B82F6', '#8B5CF6', '#F472B6'];
        const datasetColors = values.map((_, index) => colors[index % colors.length]);
        if (equipmentChart) {
            equipmentChart.data.labels = labels;
            equipmentChart.data.datasets[0].data = values;
            equipmentChart.data.datasets[0].backgroundColor = datasetColors;
            equipmentChart.update();
            return;
        }
        equipmentChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: datasetColors,
                        borderColor: 'rgba(255, 255, 255, 0.8)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadFailRates(months) {
    try {
        const data = await fetchJson('failRates', { months });
        const labels = data.map(item => item.yyyymm);
        const failRate = data.map(item => Number(item.fail_rate_pct ?? 0));
        const fails = data.map(item => Number(item.fails ?? 0));
        const ctx = document.getElementById('chart-fail-rate');
        if (!ctx) {
            return;
        }
        const datasets = [
            {
                type: 'line',
                label: 'Tasa de fallos (%)',
                data: failRate,
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239, 68, 68, 0.25)',
                tension: 0.3,
                yAxisID: 'y1',
                fill: true
            },
            {
                type: 'bar',
                label: 'Certificados fallidos',
                data: fails,
                backgroundColor: 'rgba(247, 142, 30, 0.7)',
                yAxisID: 'y2'
            }
        ];
        if (failRateChart) {
            failRateChart.data.labels = labels;
            failRateChart.data.datasets = datasets;
            failRateChart.update();
            return;
        }
        failRateChart = new Chart(ctx, {
            data: {
                labels,
                datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y1: {
                        beginAtZero: true,
                        position: 'left'
                    },
                    y2: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadCoverageByClient() {
    try {
        const rows = await fetchJson('coverageByClient');
        const tbody = document.querySelector('#table-coverage tbody');
        if (!tbody) {
            return;
        }
        tbody.innerHTML = '';
        rows.forEach(item => {
            const tr = document.createElement('tr');
            const coverage = item.coverage_pct !== null && item.coverage_pct !== undefined ? `${percentFormatter.format(item.coverage_pct)}%` : '0%';
            tr.appendChild(createCell(item.nombre ?? 'Sin nombre'));
            tr.appendChild(createCell(numberFormatter.format(item.total_assigned_equipment ?? 0), true));
            tr.appendChild(createCell(numberFormatter.format(item.compliant_equipment ?? 0), true));
            tr.appendChild(createCell(numberFormatter.format(item.overdue_equipment ?? 0), true));
            tr.appendChild(createCell(coverage, true));
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadRiskRanking(limit) {
    try {
        const rows = await fetchJson('riskRanking', { limit });
        const tbody = document.querySelector('#table-risk tbody');
        if (!tbody) {
            return;
        }
        tbody.innerHTML = '';
        rows.forEach(item => {
            const tr = document.createElement('tr');
            tr.appendChild(createCell(item.nombre ?? 'Sin nombre'));
            tr.appendChild(createCell(numberFormatter.format(item.overdue_equipment ?? 0), true));
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadExpiringSoon(days) {
    try {
        const rows = await fetchJson('expiringSoon', { days });
        const tbody = document.querySelector('#table-expiring tbody');
        if (!tbody) {
            return;
        }
        tbody.innerHTML = '';
        rows.forEach(item => {
            const tr = document.createElement('tr');
            tr.appendChild(createCell(item.certificate_number ?? ''));
            tr.appendChild(createCell(item.serial_number ?? ''));
            tr.appendChild(createCell(item.client_name ?? 'Sin cliente'));
            const date = item.next_calibration_date ? dateFormatter.format(new Date(item.next_calibration_date)) : '';
            tr.appendChild(createCell(date, true));
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadMissingPdfCertificates(limit) {
    try {
        const rows = await fetchJson('missingPdfCertificates', { limit });
        const tbody = document.querySelector('#table-missing-pdf tbody');
        if (!tbody) {
            return;
        }
        tbody.innerHTML = '';
        rows.forEach(item => {
            const tr = document.createElement('tr');
            tr.appendChild(createCell(item.certificate_number ?? ''));
            tr.appendChild(createCell(item.client_name ?? 'Sin cliente'));
            tr.appendChild(createCell(item.serial_number ?? ''));
            const date = item.calibration_date ? dateFormatter.format(new Date(item.calibration_date)) : '';
            tr.appendChild(createCell(date, true));
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
    }
}

async function loadEquipmentWithoutCertificates() {
    try {
        const rows = await fetchJson('equipmentWithoutCertificates');
        const tbody = document.querySelector('#table-equipment-without tbody');
        if (!tbody) {
            return;
        }
        tbody.innerHTML = '';
        rows.forEach(item => {
            const tr = document.createElement('tr');
            tr.appendChild(createCell(item.serial_number ?? ''));
            tr.appendChild(createCell(item.brand ?? ''));
            tr.appendChild(createCell(item.model ?? ''));
            tr.appendChild(createCell(item.equipment_type ?? ''));
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error(error);
    }
}

function updateMetric(name, value) {
    const el = document.querySelector(`[data-metric="${name}"]`);
    if (el) {
        el.textContent = value;
    }
}

function createCell(value, alignEnd = false) {
    const td = document.createElement('td');
    td.textContent = value;
    if (alignEnd) {
        td.classList.add('text-end');
    }
    return td;
}
