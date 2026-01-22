<?php
session_start();

if (isset($_GET["lang"])) {
    $_SESSION["idioma"] = $_GET["lang"];
    header("Location: pagina1.php");
}
?>
<!DOCTYPE html>
<html>
<body>
<h2>Selecciona idioma</h2>
<a href="?lang=es">Español</a> |
<a href="?lang=en">English</a>
</body>
</html>
