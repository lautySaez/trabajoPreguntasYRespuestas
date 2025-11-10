let mapaContrincantes;
let marcadoresJugadores = [];

function iniciarAplicacionMapa() {
    console.log('Iniciando Mapa');
    console.log('User Agent:', navigator.userAgent);
    console.log('Navegador detectado:', navigator.userAgent.includes('Edge') ? 'Microsoft Edge' : 'Otro navegador');

    if (typeof L === 'undefined') {
        console.error('Leaflet no está disponible');
        mostrarErrorMapa('Leaflet no se cargó correctamente');
        return;
    }
    console.log('Leaflet disponible');

    const elementoMapa = document.getElementById('mapa-contrincantes');
    if (!elementoMapa) {
        console.error('Elemento del mapa no encontrado');
        return;
    }
    console.log('Elemento del mapa encontrado');

    const rect = elementoMapa.getBoundingClientRect();
    console.log('Dimensiones del contenedor:', rect.width, 'x', rect.height);
    
    if (rect.width === 0 || rect.height === 0) {
        console.warn('Contenedor sin dimensiones, esperando...');
        setTimeout(iniciarAplicacionMapa, 500);
        return;
    }

    setTimeout(() => {
        try {
            console.log('Iniciando inicialización del mapa...');
            inicializarMapa();
            
            setTimeout(() => {
                agregarJugadoresDemostracion();
                configurarEventos();
                console.log('Mapa completamente inicializado');
            }, 300);
            
        } catch (error) {
            console.error('Error durante inicialización:', error);
            mostrarErrorMapa('Error al cargar el mapa: ' + error.message);
        }
    }, 200);
}

function mostrarErrorMapa(mensaje) {
    const elementoMapa = document.getElementById('mapa-contrincantes');
    if (elementoMapa) {
        elementoMapa.innerHTML = 
            '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(255,0,0,0.1); color: white; text-align: center; border-radius: 15px; border: 2px dashed rgba(255,0,0,0.3);">' +
            '<div><h4>Error del Mapa</h4><p>' + mensaje + '</p><button onclick="location.reload()" style="background: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin-top: 10px;">🔄 Recargar Página</button></div></div>';
    }
}

document.addEventListener('DOMContentLoaded', iniciarAplicacionMapa);
window.addEventListener('load', function() {
    // Backup si DOMContentLoaded no funciona
    setTimeout(function() {
        if (!mapaContrincantes) {
            console.log('🔄 Reintentando inicialización...');
            iniciarAplicacionMapa();
        }
    }, 1000);
});

function inicializarMapa() {
    try {
        const coordenadasIniciales = [-34.6118, -58.3960];
        
        console.log('Creando instancia del mapa...');

        const opcionesMapa = {
            center: coordenadasIniciales,
            zoom: 12,
            zoomControl: true,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            dragging: true,
            trackResize: true,
            preferCanvas: false,
            renderer: L.svg()
        };
        
        mapaContrincantes = L.map('mapa-contrincantes', opcionesMapa);
        console.log('Instancia del mapa creada');

        console.log('Agregando capa de tiles...');
        const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            minZoom: 10,
            crossOrigin: true,
            errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iIzMzMzMzMyIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmaWxsPSIjZmZmIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+Tm8gZGF0YTwvdGV4dD48L3N2Zz4='
        });
        
        tileLayer.addTo(mapaContrincantes);
        console.log('Capa de tiles agregada');

        tileLayer.on('loading', function() {
            console.log('📡 Cargando tiles...');
        });
        
        tileLayer.on('load', function() {
            console.log('Tiles cargados completamente');
        });
        
        tileLayer.on('tileerror', function(e) {
            console.warn('Error cargando tile:', e);
        });

        setTimeout(() => {
            if (mapaContrincantes) {
                mapaContrincantes.invalidateSize();
                console.log('Primera invalidación de tamaño');
            }
        }, 100);
        
        setTimeout(() => {
            if (mapaContrincantes) {
                mapaContrincantes.invalidateSize();
                console.log('Segunda invalidación de tamaño');
            }
        }, 500);
        
        setTimeout(() => {
            if (mapaContrincantes) {
                mapaContrincantes.invalidateSize();
                console.log('Tercera invalidación de tamaño (final)');
            }
        }, 1000);
        
    } catch (error) {
        console.error('Error en inicializarMapa:', error);
        mostrarErrorMapa('Error técnico: ' + error.message);
    }
}

function agregarJugadoresDemostracion() {
    console.log('Agregando jugadores de demostración...');

    const jugadoresDemo = [
        {
            nombre: "Carlos_Gamer",
            lat: -34.6037,
            lng: -58.3816,
            nivel: "Experto",
            puntos: 1250,
            estado: "en_linea"
        },
        {
            nombre: "Maria_Quiz",
            lat: -34.6158,
            lng: -58.3731,
            nivel: "Avanzado",
            puntos: 890,
            estado: "en_partida"
        },
        {
            nombre: "Lucas_Pro",
            lat: -34.6092,
            lng: -58.4173,
            nivel: "Intermedio",
            puntos: 640,
            estado: "en_linea"
        }
    ];

    try {
        jugadoresDemo.forEach((jugador, index) => {
            console.log(`Agregando jugador ${index + 1}: ${jugador.nombre}`);
            agregarMarcadorJugador(jugador);
        });
        console.log('Todos los jugadores agregados correctamente');
    } catch (error) {
        console.error('Error al agregar jugadores:', error);
    }
}

function agregarMarcadorJugador(jugador) {
    try {
        console.log(`Creando marcador para ${jugador.nombre} en [${jugador.lat}, ${jugador.lng}]`);

        const marcador = L.marker([jugador.lat, jugador.lng]).addTo(mapaContrincantes);

        const popupContent = `
            <div style="text-align: center; min-width: 150px;">
                <h4 style="margin: 0 0 10px 0; color: #333;">${jugador.nombre}</h4>
                <p style="margin: 5px 0; color: #666;">Nivel: ${jugador.nivel}</p>
                <p style="margin: 5px 0; color: #666;">Puntos: ${jugador.puntos}</p>
                <p style="margin: 5px 0; color: ${jugador.estado === 'en_linea' ? '#4CAF50' : '#FF9800'};">
                    ${jugador.estado === 'en_linea' ? '� En línea' : '🟡 En partida'}
                </p>
                ${jugador.estado === 'en_linea' ? 
                    '<button onclick="desafiarJugador(\'' + jugador.nombre + '\')" style="background: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">🎯 Desafiar</button>' : 
                    '<span style="color: #999;">No disponible</span>'}
            </div>
        `;

        marcador.bindPopup(popupContent);
        marcadoresJugadores.push(marcador);
        
        console.log(`Marcador para ${jugador.nombre} creado correctamente`);
    } catch (error) {
        console.error(`Error al crear marcador para ${jugador.nombre}:`, error);
    }
}

function configurarEventos() {
    document.querySelector('.btn-find-nearby').addEventListener('click', function() {
        this.innerHTML = '🔄 Buscando...';
        
        setTimeout(() => {
            this.innerHTML = '🎯 Buscar cercanos';
            mapaContrincantes.setView([-34.6092, -58.3900], 14);
        }, 1500);
    });

    document.querySelector('.btn-refresh-map').addEventListener('click', function() {
        this.innerHTML = '🔄 Actualizando...';
        
        setTimeout(() => {
            this.innerHTML = '🔄 Actualizar';
            const contador = document.getElementById('players-count');
            const nuevoNumero = Math.floor(Math.random() * 20) + 8;
            contador.textContent = nuevoNumero;
        }, 1000);
    });
}

function desafiarJugador(nombreJugador) {
    const confirmacion = confirm(`¿Deseas desafiar a ${nombreJugador} a una partida?`);
    
    if (confirmacion) {
        alert(`¡Invitación enviada a ${nombreJugador}! Espera su respuesta.`);

        setTimeout(() => {
            window.location.href = 'index.php?controller=partida&method=mostrarReglas';
        }, 2000);
    }
}

const estilosMarcadores = `
<style>
    .marcador-jugador {
        background: transparent;
        border: none;
    }
    
    .icono-jugador {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .icono-jugador:hover {
        transform: scale(1.1);
        border-color: #FFD700;
    }
    
    .inicial-jugador {
        color: white;
        font-weight: bold;
        font-size: 16px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
    }
    
    .info-jugador {
        min-width: 200px;
        text-align: center;
    }
    
    .info-jugador h4 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 16px;
    }
    
    .detalles-jugador {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-bottom: 15px;
        font-size: 12px;
    }
    
    .nivel-jugador, .puntos-jugador {
        color: #666;
    }
    
    .estado-jugador {
        font-weight: bold;
    }
    
    .estado-en_linea { color: #4CAF50; }
    .estado-en_partida { color: #FF9800; }
    .estado-ausente { color: #9E9E9E; }
    
    .btn-desafiar {
        background: linear-gradient(135deg, #4CAF50, #45a049);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.3s ease;
    }
    
    .btn-desafiar:hover {
        background: linear-gradient(135deg, #45a049, #4CAF50);
        transform: translateY(-2px);
    }
    
    .no-disponible {
        color: #999;
        font-style: italic;
        font-size: 12px;
    }
</style>
`;

document.head.insertAdjacentHTML('beforeend', estilosMarcadores);

function detectarYManejarEdge() {
    const esEdge = navigator.userAgent.indexOf('Edge') > -1 || navigator.userAgent.indexOf('Edg') > -1;
    
    if (esEdge) {
        console.log('🌐 Microsoft Edge detectado - Aplicando configuraciones especiales');

        setTimeout(() => {
            const elementoMapa = document.getElementById('mapa-contrincantes');
            if (elementoMapa && !mapaContrincantes) {
                console.log('⚠️ Mapa no inicializado en Edge - Reintentando...');

                elementoMapa.innerHTML = 
                    '<div style="display: flex; align-items: center; justify-content: center; height: 100%; background: rgba(0,0,0,0.2); color: white; text-align: center; border-radius: 15px;">' +
                    '<div><div style="border: 4px solid rgba(255,255,255,0.3); border-radius: 50%; border-top: 4px solid #FFD700; width: 40px; height: 40px; animation: spin 2s linear infinite; margin: 0 auto;"></div><h4 style="margin: 15px 0 5px 0;">Cargando Mapa...</h4><p style="margin: 0; font-size: 0.9rem;">Compatible con Edge</p></div></div>';

                const estilosCarga = `
                    <style>
                        @keyframes spin {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                    </style>
                `;
                document.head.insertAdjacentHTML('beforeend', estilosCarga);

                setTimeout(iniciarAplicacionMapa, 2000);
            }
        }, 3000);
    }
}
detectarYManejarEdge();