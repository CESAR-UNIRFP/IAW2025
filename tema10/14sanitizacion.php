<?php
$conexion = mysqli_connect("localhost", "root", "", "mi_base");

$texto = $_POST["texto"] ?? "";
$texto = trim($texto);

// Escapa comillas y caracteres especiales para SQL
$textoSeguro = mysqli_real_escape_string($conexion, $texto);

// Ejemplo de consulta "armada" (mejor evita esto)
$sql = "INSERT INTO comentarios (texto) VALUES ('$textoSeguro')";
mysqli_query($conexion, $sql);

// Al mostrar en HTML:
echo htmlspecialchars($texto, ENT_QUOTES, "UTF-8");
?>