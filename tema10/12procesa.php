<?php
// 12procesa.php

// ================================
// 0) Configuración (BD)
// ================================
$host = "localhost";
$db   = "mi_base";
$user = "root";
$pass = "";
$charset = "utf8mb4";

// ================================
// 1) Recoger datos del formulario
// ================================
$nombre    = $_POST["nombre"] ?? "";
$edad      = $_POST["edad"] ?? "";
$email     = $_POST["email"] ?? "";
$fechaNac  = $_POST["fecha_nac"] ?? "";
$rol       = $_POST["rol"] ?? "";
$terminos  = $_POST["terminos"] ?? "0"; // si no marca, no llega

$errores = [];

// ================================
// 2) Validaciones
// ================================

// --- Nombre (texto): requerido + longitud
$nombre = trim($nombre);
if ($nombre === "") {
    $errores[] = "El nombre es obligatorio.";
} elseif (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
    $errores[] = "El nombre debe tener entre 3 y 50 caracteres.";
}

// --- Edad (entero): tipo + rango
if ($edad === "") {
    $errores[] = "La edad es obligatoria.";
} elseif (filter_var($edad, FILTER_VALIDATE_INT) === false) {
    $errores[] = "La edad debe ser un número entero.";
} else {
    $edad = (int)$edad;
    if ($edad < 0 || $edad > 120) {
        $errores[] = "La edad debe estar entre 0 y 120.";
    }
}

// --- Email: formato
$email = trim($email);
if ($email === "") {
    $errores[] = "El email es obligatorio.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "Email inválido.";
}

// --- Fecha: formato (YYYY-MM-DD) + comprobación real
if ($fechaNac === "") {
    $errores[] = "La fecha de nacimiento es obligatoria.";
} else {
    $dt = DateTime::createFromFormat("Y-m-d", $fechaNac);
    $esFechaValida = $dt && $dt->format("Y-m-d") === $fechaNac;
    if (!$esFechaValida) {
        $errores[] = "La fecha de nacimiento no tiene un formato válido.";
    }
}

// --- Rol (select): valores permitidos
$rolesPermitidos = ["admin", "user", "guest"];
if ($rol === "") {
    $errores[] = "Debes seleccionar un rol.";
} elseif (!in_array($rol, $rolesPermitidos, true)) {
    $errores[] = "Rol no permitido.";
}

// --- Términos (checkbox): requerido
if ($terminos !== "1") {
    $errores[] = "Debes aceptar los términos.";
}

// ================================
// 3) Si hay errores, mostrarlos y salir
// ================================
if (!empty($errores)) {
    echo "<h3>Errores de validación:</h3><ul>";
    foreach ($errores as $e) {
        echo "<li>" . htmlspecialchars($e, ENT_QUOTES, "UTF-8") . "</li>";
    }
    echo "</ul>";
    exit;
}

// ================================
// 4) Si todo OK (aquí iría INSERT/UPDATE)
// ================================

// Ejemplo de tabla:
// CREATE TABLE usuarios (
//   id INT AUTO_INCREMENT PRIMARY KEY,
//   nombre VARCHAR(50) NOT NULL,
//   edad INT NOT NULL,
//   email VARCHAR(150) NOT NULL UNIQUE,
//   fecha_nac DATE NOT NULL,
//   rol ENUM('admin','user','guest') NOT NULL,
//   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $user, $pass, $opciones);

    // INSERT con consulta preparada
    $sql = "INSERT INTO usuarios (nombre, edad, email, fecha_nac, rol)
            VALUES (:nombre, :edad, :email, :fecha_nac, :rol)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nombre"    => $nombre,
        ":edad"      => $edad,
        ":email"     => $email,
        ":fecha_nac" => $fechaNac,
        ":rol"       => $rol
    ]);

    echo "Datos válidos e insertados correctamente.";

} catch (PDOException $e) {

    // Ejemplo de restricción de BD: email UNIQUE
    // MySQL suele usar el código 23000 para violaciones de integridad (como duplicados unique).
    if ($e->getCode() === "23000") {
        echo "No se pudo insertar: el email ya existe (restricción UNIQUE).";
    } else {
        echo "Error de base de datos: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, "UTF-8");
    }
}
?>
