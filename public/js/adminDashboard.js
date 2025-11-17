document.addEventListener('DOMContentLoaded', () => {

    const url = 'index.php?controller=admin&method=statsJson';

    fetch(url)
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(data => {

            qs('#kpi-total-preguntas').innerText = data.total_preguntas?.toLocaleString() ?? '0';
            qs('#kpi-categorias').innerText = (data.por_categoria?.length)?.toLocaleString() ?? '0';
            qs('#kpi-partidas').innerText = data.total_partidas?.toLocaleString() ?? '0';

            graph('chart-edades', 'bar',
                (data.edades || []).map(e => e.rango),
                (data.edades || []).map(e => e.cantidad)
            );

            graph('chart-genero', 'pie',
                (data.genero || []).map(g => g.genero),
                (data.genero || []).map(g => g.cantidad)
            );

            graph('chart-categorias', 'doughnut',
                (data.por_categoria || []).map(c => c.nombre),
                (data.por_categoria || []).map(c => c.total)
            );

            const lugaresLabels = (data.lugares || []).map(l => `${l.ciudad}, ${l.pais}`);
            const lugaresValues = (data.lugares || []).map(l => l.cantidad);

            graph('mapa-usuarios', 'bar',
                lugaresLabels,
                lugaresValues
            );

            const ul = qs('#top-faciles-list');
            ul.innerHTML = '';
            (data.top_faciles || []).slice(0, 10).forEach(q => {
                const li = document.createElement('li');

                const preguntaTexto = q.pregunta ? q.pregunta.substring(0, 80) : 'N/A';
                const porcentaje = parseFloat(q.porcentaje_acierto).toFixed(1);

                li.innerHTML = `<strong>${porcentaje}%</strong> — ${preguntaTexto}...`;
                ul.append(li);
            });

            const tbody = qs('#top-jugadores-body');
            tbody.innerHTML = '';
            (data.top_jugadores || []).slice(0, 10).forEach(u => {
                const tr = document.createElement('tr');

                const avatar = u.foto_perfil ?
                    `<img src="${u.foto_perfil}" class="avatar-small">` :
                    `<div class="avatar-small placeholder-small">${u.nombre_usuario.substring(0,1).toUpperCase()}</div>`;

                tr.innerHTML = `
                    <td>${avatar}</td>
                    <td>${u.nombre_usuario}</td>
                    <td>${(u.total_puntos ?? 0).toLocaleString()}</td>
                    <td>${(u.partidas_jugadas ?? 0).toLocaleString()}</td>
                `;
                tbody.appendChild(tr);
            });

            const ul2 = qs('#ultimos-informes-list');
            ul2.innerHTML = '';
            (data.informes || []).slice(0, 10).forEach(i => {
                const li = document.createElement('li');

                const motivoTexto = i.motivo ? i.motivo.substring(0, 45) : 'Sin motivo';
                const editor = i.editor_nombre ?? 'Sistema';

                li.innerHTML = `<strong>${i.tipo_accion}</strong>: ${editor}<br><small title="${i.motivo}">${motivoTexto}...</small>`;
                ul2.append(li);
            });

        })
        .catch(err => {
            console.error('Error al cargar las estadísticas:', err);
            qs('#top-jugadores-body').innerHTML = '<tr><td colspan="4">Error al cargar datos.</td></tr>';
        });
});

function qs(s) { return document.querySelector(s); }

function graph(id, type, labels, values) {
    let el = document.getElementById(id);
    if (!el) return;

    if (id === 'mapa-usuarios' && el.tagName !== 'CANVAS') {
        const parent = el.parentElement;
        parent.removeChild(el);
        const newCanvas = document.createElement('canvas');
        newCanvas.id = id;
        parent.appendChild(newCanvas);
        el = newCanvas;
    }

    const colors = [
        '#2563eb', '#059669', '#f59e0b', '#ef4444', '#6366f1', '#14b8a6', '#f97316', '#a855f7'
    ];

    let options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: (type !== 'bar' && type !== 'line'),
                position: type === 'pie' || type === 'doughnut' ? 'right' : 'top',
                labels: {
                    font: { size: 12, family: 'Inter' }
                }
            },
            tooltip: {
                bodyFont: { family: 'Inter' }
            }
        },
        scales: {}
    };

    let datasetConfig = {
        data: values,
        backgroundColor: colors.map(c => c + 'd0'),
        borderColor: colors,
        borderWidth: 1,
    };

    if (type === 'bar' || type === 'line') {
        options.scales.y = {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: '#e2e8f0' }
        };
        options.scales.x = {
            grid: { display: false }
        };

        datasetConfig.backgroundColor = '#2563eb';
        datasetConfig.borderColor = '#2563eb';
        datasetConfig.borderWidth = 0;
        datasetConfig.label = 'Cantidad de Usuarios';
    }

    if (type === 'pie' || type === 'doughnut') {
        datasetConfig.backgroundColor = colors;
        datasetConfig.hoverOffset = 8;
        datasetConfig.borderColor = '#fff';
        datasetConfig.label = 'Distribución';
    }

    new Chart(el, {
        type,
        data: {
            labels,
            datasets: [datasetConfig]
        },
        options: options
    });
}