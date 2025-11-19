<?php include("views/partials/header.php"); ?>

    <link rel="stylesheet" href="public/css/ruleta.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div style="background:#ffebee;color:#c62828;padding:10px 15px;border-radius:8px;margin:10px 0;font-weight:600;max-width:800px;">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

    <div class="ruleta-page">
        <div class="ruleta-container">

            <h2>¡Girá la ruleta para elegir categoría inicial!</h2>

            <div class="ruleta-wrapper">
                <div class="flecha"></div>
                <canvas id="ruleta" width="400" height="400"></canvas>
            </div>

            <audio id="sonidoRuleta" preload="auto">
                <source src="public/audios/ruleta_girando.mp3" type="audio/mpeg">
            </audio>
            <button id="boton-girar">Girar</button>
        </div>
    </div>

    <div id="resultado">
        <div class="resultado-card">
            <p id="categoria-elegida"></p>
            <a id="btn-iniciar" class="boton-iniciar" href="#">Iniciar partida</a>
        </div>
    </div>

    <script src="public/js/ruleta.js?v=20251117" type="text/javascript"></script>

<?php include("views/partials/footer.php"); ?>