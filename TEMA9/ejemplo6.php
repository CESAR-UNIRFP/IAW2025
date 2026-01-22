<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bucles</title>
</head>
<body>

<h3>Bucle for</h3>
<?php
for ($i = 1; $i <= 5; $i++) {
    echo "Número: $i <br>";
}
?>

<h3>Bucle foreach</h3>
<?php
$alumnos = ["Ana", "Luis", "Carlos"];

foreach ($alumnos as $alumno) {
    echo $alumno . "<br>";
}
?>

</body>
</html>
