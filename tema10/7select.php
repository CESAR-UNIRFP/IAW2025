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

// Consulta SQL para obtener los datos
$sql = "SELECT id, nombre, apellido, email FROM usuarios";
$result = $conn->query($sql);

// Mostrar los datos en una tabla HTML
if ($result->num_rows > 0) {
echo "<table><tr><th>ID</th><th>Nombre</th><th>Apellido</th><th>Email</th></tr>";
  // Salida de datos de cada fila
  while($row = $result->fetch_assoc()) {
echo "<tr><td>".$row["id"]."</td><td>".$row["nombre"]."</td><td>".$row["apellido"]."</td><td>".$row["email"]."</td></tr>";
  }
  	echo "</table>";
} else {
  	echo "0 resultados";
}
$conn->close();
?>
