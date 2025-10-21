<?php include("views/partials/header.php"); ?>
<?php
require_once("helper/VerificacionDeRoles.php");
verificarRol("editor");
?>
    <h1>Página del editor</h1>

<?php include("views/partials/footer.php"); ?>