<?php
session_start();
$idioma = $_SESSION["idioma"] ?? "es";

$textos = [
    "es" => "Estás en la página 2",
    "en" => "You are on page 2"
];
?>
<!DOCTYPE html>
<html>
<body>
<h1><?= $textos[$idioma] ?></h1>
<br>
<?php
echo $idioma;
?>
</body>
</html>
