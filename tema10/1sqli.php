<?php
$servername = "localhost";

// usuario mysql
$username = "root";

// Contraseña mysql
$password = "";

// bbdd mysql
$dbname = "cesar";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  	die("Conexión fallida: " . $conn->connect_error);
}
echo "Conexión exitosa";

// Cerrar la conexión (opcional en este ejemplo)
$conn->close();
?>
