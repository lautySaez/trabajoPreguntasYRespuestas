<?php include("views/partials/header.php"); ?>

<link rel="stylesheet" href="public/css/home.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<!-- Fallback CSS para Edge -->
<style>
    /* Forzar visualización del mapa en Edge */
    #mapa-contrincantes {
        min-height: 400px !important;
        background-color: #2c3e50 !important;
        display: block !important;
    }
</style>

<?php
$usuario = $_SESSION["usuario"] ?? null;
?>

<div class="home-container">
    <?php if ($usuario): ?>
        <div class="welcome-section">
            <h2>¡Bienvenido de vuelta!</h2>
            <p>¿Estás listo para poner a prueba tus conocimientos?</p>
            
            <!-- Sección del mapa de contrincantes -->
            <div class="map-section">
                <h3>Encuentra tu contrincante</h3>
                <p class="map-description">Explora el mapa y encuentra jugadores cerca de ti para desafiar</p>
                
                <div class="map-container">
                    <div id="mapa-contrincantes" class="mapa-home"></div>
                    
                    <!-- Panel de información del mapa -->
                    <div class="map-info-panel">
                        <div class="online-players">
                            <span class="indicator-online"></span>
                            <span>Jugadores en línea: <strong id="players-count">12</strong></span>
                        </div>
                        <div class="map-controls">
                            <button class="btn-find-nearby">Buscar cercanos</button>
                            <button class="btn-refresh-map">Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="index.php?controller=partida&method=mostrarReglas" class="boton-partida">Iniciar una Partida</a>
        </div>
    <?php else: ?>
        <div class="error-section">
            <p>Error: no se encontró información del usuario.</p>
            <a href="index.php?controller=LoginController&method=inicioSesion" class="boton-login">Iniciar Sesión</a>
        </div>
    <?php endif; ?>
</div>

    <!-- Carga sincrónica de Leaflet para compatibilidad con Edge -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" type="text/javascript"></script>
    <script type="text/javascript">
        // Función de inicialización específica para Edge
        window.inicializarMapaEdge = function() {
            console.log('Iniciando mapa para Edge...');
            if (typeof L !== 'undefined') {
                console.log('Leaflet disponible, versión:', L.version);
                return true;
            } else {
                console.error('Leaflet no disponible');
                return false;
            }
        };
        
        // Verificar inmediatamente
        window.inicializarMapaEdge();
    </script>
    <script src="public/js/home.js" type="text/javascript"></script>

<?php include("views/partials/footer.php"); ?>