<?php
session_start();
$idioma = $_SESSION["idioma"] ?? "es";

$textos = [
    "es" => "Bienvenido a la página 1",
    "en" => "Welcome to page 1"
];
?>
<!DOCTYPE html>
<html>
<body>
<h1><?= $textos[$idioma] ?></h1>
<a href="pagina2.php">Ir a página 2</a>
</body>
</html>
