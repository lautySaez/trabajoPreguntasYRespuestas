document.addEventListener("DOMContentLoaded", () => {

    fetch("index.php?controller=admin&method=statsJson")
        .then(res => res.json())
        .then(data => {
            console.log("DEBUG DATA:", data);

            document.getElementById("kpi-total-preguntas").textContent = data.total_preguntas ?? "0";
            document.getElementById("kpi-partidas").textContent = data.total_partidas ?? "0";
            document.getElementById("kpi-categorias").textContent = data.por_categoria?.length ?? "0";

            const topJugadoresBody = document.getElementById("top-jugadores-body");
            topJugadoresBody.innerHTML = "";

            if (data.top_jugadores?.length > 0) {
                data.top_jugadores.forEach(j => {
                    topJugadoresBody.innerHTML += `
                        <tr>
                            <td>
                                ${j.foto_perfil
                        ? `<img src="${j.foto_perfil}" class="avatar-small">`
                        : `<div class="avatar-small placeholder">${j.nombre_usuario[0].toUpperCase()}</div>`
                    }
                            </td>
                            <td>${j.nombre_usuario}</td>
                            <td>${j.total_puntos}</td>
                            <td>${j.partidas_jugadas}</td>
                        </tr>`;
                });
            } else {
                topJugadoresBody.innerHTML = `<tr><td colspan="4">No hay datos</td></tr>`;
            }

            const topFacilesList = document.getElementById("top-faciles-list");
            topFacilesList.innerHTML = "";

            if (data.top_faciles?.length > 0) {
                data.top_faciles.forEach(p => {
                    topFacilesList.innerHTML += `
                        <li>
                            <strong>${p.pregunta}</strong><br>
                            <small>Acierto: ${p.porcentaje_acierto}% — ${p.veces_mostrada} jugadas</small>
                        </li>`;
                });
            } else {
                topFacilesList.innerHTML = "<li>No hay datos</li>";
            }

            const informesList = document.getElementById("ultimos-informes-list");
            informesList.innerHTML = "";

            if (data.informes?.length > 0) {
                data.informes.forEach(i => {
                    informesList.innerHTML += `
                        <li>
                            <strong>${i.editor_nombre ?? "Editor desconocido"}</strong><br>
                            <small>${i.tipo_accion} — ${i.fecha}</small>
                        </li>`;
                });
            } else {
                informesList.innerHTML = "<li>No hay informes</li>";
            }

            new Chart(document.getElementById("chart-edades"), {
                type: "bar",
                data: {
                    labels: data.edades.map(e => e.rango),
                    datasets: [{
                        label: "Usuarios",
                        data: data.edades.map(e => e.cantidad)
                    }]
                }
            });

            new Chart(document.getElementById("chart-genero"), {
                type: "pie",
                data: {
                    labels: data.genero.map(g => g.genero),
                    datasets: [{
                        data: data.genero.map(g => g.cantidad)
                    }]
                }
            });

            new Chart(document.getElementById("chart-categorias"), {
                type: "bar",
                data: {
                    labels: data.por_categoria.map(c => c.nombre),
                    datasets: [{
                        label: "Preguntas",
                        data: data.por_categoria.map(c => c.total)
                    }]
                }
            });

            const map = L.map("mapa-usuarios").setView([-34.60, -58.38], 3);

            L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
                maxZoom: 18
            }).addTo(map);

            data.lugares?.forEach(l => {
                if (!l.pais || l.pais === "Desconocido") return;

                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${l.ciudad}, ${l.pais}`)
                    .then(res => res.json())
                    .then(loc => {
                        if (loc[0]) {
                            L.marker([loc[0].lat, loc[0].lon]).addTo(map)
                                .bindPopup(`${l.ciudad}, ${l.pais}<br>Partidas: ${l.cantidad}`);
                        }
                    });
            });

        })
        .catch(err => console.error("Error cargando dashboard:", err));
});