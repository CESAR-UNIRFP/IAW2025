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
$sql = "CREATE PROCEDURE obtener_usuarios()
        BEGIN
          SELECT * FROM usuarios;
        END;";
if ($conn->query($sql) === TRUE) {
  	echo "Procedimiento almacenado 'obtener_usuarios' creado con éxito";
} else {
  	echo "Error al crear el procedimiento almacenado: " . $conn->error;
}
$conn->close();
?>
