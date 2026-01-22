<?php
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<body>

<h1>Zona privada</h1>
<p>Solo usuarios autenticados</p>

</body>
</html>
