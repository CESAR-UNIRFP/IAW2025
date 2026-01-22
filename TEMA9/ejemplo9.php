<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validación</title>
</head>
<body>

<form method="post">
    Email: <input type="text" name="email">
    <input type="submit">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email válido";
    } else {
        echo "Email incorrecto";
    }
}
?>

</body>
</html>
