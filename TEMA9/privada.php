<?php
session_start();

if (!isset($_SESSION["logueado"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
<h1>Zona privada</h1>
<p>Usuario autenticado correctamente</p>
<a href="logout.php">Cerrar sesión</a>
</body>
</html>
