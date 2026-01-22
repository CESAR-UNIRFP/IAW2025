<?php
session_start();
$_SESSION["usuario"] = "admin";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sesiones</title>
</head>
<body>

<?php
echo "Usuario en sesión: " . $_SESSION["usuario"];
?>

</body>
</html>
