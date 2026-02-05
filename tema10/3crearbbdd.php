<?php
$servername = "localhost";
$username = "root";
$password = "";
// Crear conexión
$conn = new mysqli($servername, $username, $password);

// Verificar la conexión
if ($conn->connect_error) {
  	die("Conexión fallida: " . $conn->connect_error);
}

$dbname = "mi_nueva_base_de_datos2";

// Crear la tabla
$sql = "CREATE DATABASE " .$dbname ;
if ($conn->query($sql) === TRUE) {
  	echo "Base de datos creada con éxito";
} else {
  	echo "Error al crear la base de datos: " . $conn->error;
}
$conn->close();
?>
