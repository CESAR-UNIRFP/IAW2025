<?php
session_start();

$usuarioCorrecto = "admin";
$claveCorrecta = "1234";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["usuario"] == $usuarioCorrecto && $_POST["clave"] == $claveCorrecta) {
        $_SESSION["logueado"] = true;

        header("Location: privada.php");
    } else {
        $error = "Credenciales incorrectas";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
<form method="post">
    Usuario: <input type="text" name="usuario"><br>
    Clave: <input type="password" name="clave"><br>
    <input type="submit">
</form>

<?php if (isset($error)) echo $error; ?>
</body>
</html>
