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

// Crear la vista
$sql = "CREATE VIEW vista_usuarios AS SELECT nombre, apellido FROM usuarios";

if ($conn->query($sql) === TRUE) {
  	echo "Vista 'vista_usuarios' creada con éxito";
} else {
  	echo "Error al crear la vista: " . $conn->error;
}
$conn->close();
?>
