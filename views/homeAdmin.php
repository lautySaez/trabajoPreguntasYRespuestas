<?php include("views/partials/header.php"); ?>
<?php
require_once("helper/VerificacionDeRoles.php");
verificarRol("Administrador");
?>
    <h1>Panel del Administrador</h1>

<?php include("views/partials/footer.php"); ?>