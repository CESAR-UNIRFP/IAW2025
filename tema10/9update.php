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

// Datos a actualizar
$nuevo_email = "actualizado@email.com";
$id_usuario = 1; // ID del usuario a actualizar

// Consulta SQL para actualizar datos
$sql = "UPDATE usuarios SET email='$nuevo_email' WHERE id=$id_usuario";

if ($conn->query($sql) === TRUE) {
  	echo "Registro actualizado con éxito";
} else {
  	echo "Error al actualizar el registro: " . $conn->error;
}

$conn->close();
?>
