document.addEventListener('DOMContentLoaded', function() {
    const url = 'index.php?controller=admin&method=statsJson';

    fetch(url)
        .then(r => r.json())
        .then(data => {
            // KPIs
            document.getElementById('kpi-total-preguntas').innerText = data.total_preguntas || 0;
            document.getElementById('kpi-categorias').innerText = (data.por_categoria || []).length || 0;
            document.getElementById('kpi-partidas').innerText = data.lugares ? data.lugares.reduce((s, l) => s + (parseInt(l.sesiones || 0)), 0) : 0;
            
            const edades = (data.edades || []).map(r => r.rango || r.rango_edad || r.rango);
            const edadesVals = (data.edades || []).map(r => parseInt(r.cantidad || 0));
            if (document.getElementById('chart-edades')) {
                new Chart(document.getElementById('chart-edades'), {
                    type: 'bar',
                    data: { labels: edades, datasets: [{ label: 'Usuarios', data: edadesVals }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            const genLabels = (data.genero || []).map(g => g.genero || g.sexo);
            const genVals = (data.genero || []).map(g => parseInt(g.cantidad || 0));
            if (document.getElementById('chart-genero')) {
                new Chart(document.getElementById('chart-genero'), {
                    type: 'pie',
                    data: { labels: genLabels, datasets: [{ data: genVals }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            const catLabels = (data.por_categoria || []).map(c => c.nombre);
            const catVals = (data.por_categoria || []).map(c => parseInt(c.total || 0));
            if (document.getElementById('chart-categorias')) {
                new Chart(document.getElementById('chart-categorias'), {
                    type: 'doughnut',
                    data: { labels: catLabels, datasets: [{ data: catVals }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }

            const ulFac = document.getElementById('top-faciles-list');
            ulFac.innerHTML = '';
            (data.top_faciles || []).forEach(q => {
                const li = document.createElement('li');
                const texto = q.pregunta ? q.pregunta : ('ID ' + q.id);
                const pct = q.porcentaje_acierto !== undefined ? parseFloat(q.porcentaje_acierto).toFixed(1) + '%' : (q.porcentaje ? q.porcentaje + '%' : '');
                li.textContent = `${texto.substring(0,120)} — ${pct}`;
                ulFac.appendChild(li);
            });

            const tbody = document.getElementById('top-jugadores-body');
            tbody.innerHTML = '';
            (data.top_jugadores || []).forEach(u => {
                const tr = document.createElement('tr');
                const avatar = u.foto_perfil ? `<img src="${u.foto_perfil}" width="40" height="40" style="border-radius:50%;">` : `<div style="width:40px;height:40px;border-radius:50%;background:#ccc;"></div>`;
                tr.innerHTML = `<td style="text-align:center;">${avatar}</td>
                        <td>${u.nombre_usuario || u.nombre}</td>
                        <td>${u.total_puntos || 0}</td>
                        <td>${u.partidas_jugadas || 0}</td>`;
                tbody.appendChild(tr);
            });

            const ulInf = document.getElementById('ultimos-informes-list');
            ulInf.innerHTML = '';
            (data.informes || []).forEach(i => {
                const li = document.createElement('li');
                li.innerHTML = `<strong>${i.tipo_accion}</strong> — ${i.editor_nombre || 'Sistema'}<br><small>${(i.motivo || '').substring(0,120)}</small>`;
                ulInf.appendChild(li);
            });

            if (document.getElementById('mapa-usuarios')) {
                const map = L.map('mapa-usuarios', { scrollWheelZoom: false }).setView([ -34.6, -58.4 ], 3);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18
                }).addTo(map);

                (data.lugares || []).slice(0,50).forEach(l => {
                    const popup = `<strong>${l.pais || 'Desconocido'}</strong><br>${l.ciudad || ''}<br>${l.sesiones} sesiones`;
                    L.marker([ -34.6, -58.4 ]).addTo(map).bindPopup(popup);
                });
            }
        })
        .catch(err => {
            console.error('Error cargando estadísticas:', err);
        });
});