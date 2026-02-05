
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

// Datos a insertar
$nombre = "Nuevo Usuario2";
$apellido = "Apellido2";
$email = "nuevo2@email.com";

// Consulta SQL para insertar datos
$sql = "INSERT INTO usuarios (nombre, apellido, email) VALUES ('$nombre', '$apellido', '$email')";

if ($conn->query($sql) === TRUE) {
  	echo "Nuevo registro creado con éxito";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
