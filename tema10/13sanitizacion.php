<?php
// Sanitización + guardado seguro (PDO preparado)

// 1) Recoger datos
$nombre = $_POST["nombre"] ?? "";
$comentario = $_POST["comentario"] ?? "";

// 2) "Limpiar" un poco (sanitización básica)
$nombre = trim($nombre);
$comentario = trim($comentario);

// 3) Validación mínima (no confiar en el usuario)
if ($nombre === "" || $comentario === "") {
    die("Faltan datos.");
}

// 4) Insertar en BD (ANTI SQL Injection: consulta preparada)
$pdo = new PDO("mysql:host=localhost;dbname=mi_base;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "INSERT INTO mensajes (nombre, comentario) VALUES (:nombre, :comentario)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":nombre" => $nombre,
    ":comentario" => $comentario
]);

// 5) Mostrar al usuario de forma segura (ANTI XSS: escapar HTML)
echo "Guardado OK.<br>";
echo "Nombre: " . htmlspecialchars($nombre, ENT_QUOTES, "UTF-8") . "<br>";
echo "Comentario: " . htmlspecialchars($comentario, ENT_QUOTES, "UTF-8");
?>
