<?php
$servername = "localhost";

$dbname = "mi_nueva_base_de_datos2";
$username = "root";
$password = "";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
  	die("Conexión fallida: " . $conn->connect_error);
}

// Crear la tabla
$sql = "CREATE TABLE usuarios (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(30) NOT NULL,
apellido VARCHAR(30) NOT NULL,
email VARCHAR(50),
reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
  	echo "Tabla 'usuarios' creada con éxito";
} else {
  	echo "Error al crear la tabla: " . $conn->error;
}

$conn->close();
?>
