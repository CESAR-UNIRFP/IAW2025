<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Switch</title>
</head>
<body>

<?php
$dia = "lunes";

switch ($dia) {
    case "lunes":
        echo "Inicio de semana";
        break;
    case "viernes":
        echo "Fin de semana cerca";
        break;
    default:
        echo "Día normal";
}
?>

</body>
</html>
