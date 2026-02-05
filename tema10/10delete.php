<?php
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "mi_base_de_datos";

$dbname = "mi_nueva_base_de_datos2";
$username = "root";
$password = "";


// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  	die("Conexión fallida: " . $conn->connect_error);
}

// ID del usuario a eliminar
$id_usuario = 1;

// Consulta SQL para eliminar datos
$sql = "DELETE FROM usuarios WHERE id=$id_usuario";

if ($conn->query($sql) === TRUE) {
  	echo "Registro eliminado con éxito";
} else {
  	echo "Error al eliminar el registro: " . $conn->error;
}

$conn->close();
?>
