<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario</title>
</head>
<body>

<form method="post">
    Nombre: <input type="text" name="nombre">
    <input type="submit" value="Enviar">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Hola " . $_POST["nombre"];
}
?>

</body>
</html>
