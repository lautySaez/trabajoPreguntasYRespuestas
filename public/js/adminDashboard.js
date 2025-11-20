document.addEventListener("DOMContentLoaded", () => {
    console.log("Iniciando carga del Dashboard...");

    fetch("index.php?controller=admin&method=statsJson")
        .then(res => {
            if (!res.ok) {
                throw new Error(`Error HTTP: ${res.status} - No se pudo obtener el JSON.`);
            }
            return res.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);

            if (data.error) {
                throw new Error(`Error del Servidor: ${data.mensaje}`);
            }

            // 1. KPIs
            document.getElementById("kpi-total-preguntas").textContent = data.total_preguntas ?? "0";
            document.getElementById("kpi-partidas").textContent = data.total_partidas ?? "0";
            document.getElementById("kpi-categorias").textContent = data.por_categoria ? data.por_categoria.length : "0";

            // 2. Top Jugadores
            const topJugadoresBody = document.getElementById("top-jugadores-body");
            if (topJugadoresBody) {
                topJugadoresBody.innerHTML = "";

                if (Array.isArray(data.top_jugadores) && data.top_jugadores.length > 0) {
                    data.top_jugadores.forEach(j => {
                        const inicial = (j.nombre_usuario && j.nombre_usuario.length > 0)
                            ? j.nombre_usuario[0].toUpperCase()
                            : "U";

                        const nombre = j.nombre_usuario || "Usuario Desconocido";

                        topJugadoresBody.innerHTML += `
                        <tr>
                            <td>
                                ${j.foto_perfil
                            ? `<img src="${j.foto_perfil}" class="avatar-small" alt="avatar">`
                            : `<div class="avatar-small placeholder">${inicial}</div>`
                        }
                            </td>
                            <td>${nombre}</td>
                            <td>${j.total_puntos ?? 0}</td>
                            <td>${j.partidas_jugadas ?? 0}</td>
                        </tr>`;
                    });
                } else {
                    topJugadoresBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">No hay jugadores registrados</td></tr>`;
                }
            }

            const topFacilesList = document.getElementById("top-faciles-list");
            if (topFacilesList) {
                topFacilesList.innerHTML = "";
                if (Array.isArray(data.top_faciles) && data.top_faciles.length > 0) {
                    data.top_faciles.forEach(p => {
                        topFacilesList.innerHTML += `
                        <li>
                            <strong>${p.pregunta || "Pregunta sin texto"}</strong><br>
                            <small>Acierto: ${p.porcentaje_acierto ?? 0}%</small> 
                        </li>`;
                    });
                } else {
                    topFacilesList.innerHTML = `<li style="text-align:center; color:#aaa;">No hay datos suficientes</li>`;
                }
            }

            const informesList = document.getElementById("ultimos-informes-list");
            if (informesList) {
                informesList.innerHTML = "";
                if (Array.isArray(data.informes) && data.informes.length > 0) {
                    data.informes.forEach(i => {
                        informesList.innerHTML += `
                        <li>
                            <strong>${i.editor_nombre || "Editor Desconocido"}</strong><br>
                            <small>${i.tipo_accion || "Acción"} — ${i.fecha || ""}</small>
                        </li>`;
                    });
                } else {
                    informesList.innerHTML = `<li style="text-align:center; color:#aaa;">No hay informes recientes</li>`;
                }
            }

            const ctxEdades = document.getElementById("chart-edades");
            if (ctxEdades && Array.isArray(data.edades)) {
                new Chart(ctxEdades, {
                    type: "bar",
                    data: {
                        labels: data.edades.map(e => e.rango || "N/A"),
                        datasets: [{
                            label: "Usuarios",
                            data: data.edades.map(e => e.cantidad),
                            backgroundColor: '#36a2eb'
                        }]
                    }
                });
            }

            const ctxGenero = document.getElementById("chart-genero");
            if (ctxGenero && Array.isArray(data.genero)) {
                new Chart(ctxGenero, {
                    type: "pie",
                    data: {
                        labels: data.genero.map(g => g.genero || "No especificado"),
                        datasets: [{
                            data: data.genero.map(g => g.cantidad),
                            backgroundColor: ['#ff6384', '#36a2eb', '#ffcd56']
                        }]
                    }
                });
            }

            const ctxCat = document.getElementById("chart-categorias");
            if (ctxCat && Array.isArray(data.por_categoria)) {
                new Chart(ctxCat, {
                    type: "bar",
                    data: {
                        labels: data.por_categoria.map(c => c.nombre),
                        datasets: [{
                            label: "Preguntas",
                            data: data.por_categoria.map(c => c.total),
                            backgroundColor: '#4bc0c0'
                        }]
                    }
                });
            }

            const ctxLugares = document.getElementById("chart-lugares");
            if (ctxLugares && Array.isArray(data.lugares)) {
                const lugaresValidos = data.lugares.filter(l => l.ciudad && l.pais && l.pais !== "Desconocido");

                if (lugaresValidos.length > 0) {
                    new Chart(ctxLugares, {
                        type: "bar",
                        data: {
                            labels: lugaresValidos.map(l => `${l.ciudad}, ${l.pais}`),
                            datasets: [{
                                label: "Jugadores",
                                data: lugaresValidos.map(l => l.cantidad),
                                backgroundColor: '#9966ff'
                            }]
                        },
                        options: { responsive: true, scales: { y: { beginAtZero: true } } }
                    });
                }
            }


        })
        .catch(err => {
            console.error("Error cargando dashboard:", err);
            const topJugadoresBody = document.getElementById("top-jugadores-body");
            if(topJugadoresBody) topJugadoresBody.innerHTML = `<tr><td colspan="4" style="color: #ff6b6b; text-align: center;">Error cargando datos: ${err.message}. Revisa la consola (F12).</td></tr>`;
        });

    const btnPrint = document.getElementById('btn-print');
    const btnExportPdf = document.getElementById('btn-export-pdf');

    if (btnPrint) {
        btnPrint.addEventListener('click', () => window.print());
    }

    if (btnExportPdf) {
        btnExportPdf.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const content = document.querySelector('.content');
            const date = new Date().toISOString().slice(0, 10);
            const sidebar = document.querySelector('.sidebar');
            const actions = document.querySelector('.sidebar-actions');
            if(sidebar) sidebar.style.display = 'none';
            if(actions) actions.style.display = 'none';

            html2canvas(content, { scale: 2 }).then(canvas => {
                if(sidebar) sidebar.style.display = 'flex';
                if(actions) actions.style.display = 'flex';

                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF('p', 'mm', 'a4');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = pdf.internal.pageSize.getHeight();
                const imgHeight = canvas.height * pdfWidth / canvas.width;

                let heightLeft = imgHeight;
                let position = 0;

                pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, imgHeight);
                heightLeft -= pdfHeight;

                while (heightLeft >= 0) {
                    position = heightLeft - imgHeight;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, imgHeight);
                    heightLeft -= pdfHeight;
                }

                pdf.save(`Reporte_Admin_${date}.pdf`);
            });
        });
    }
});