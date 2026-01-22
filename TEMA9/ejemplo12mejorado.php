<?php
session_start();

$usuarioCorrecto = "admin";
$claveCorrecta = "1234";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["usuario"] === $usuarioCorrecto && $_POST["clave"] === $claveCorrecta) {
        $_SESSION["logueado"] = true;
        header("Location: privada.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<h2>Login</h2>

<form method="post">
    Usuario: <input type="text" name="usuario"><br><br>
    Clave: <input type="password" name="clave"><br><br>
    <input type="submit" value="Entrar">
</form>

<p style="color:red"><?= $error ?></p>
</body>
</html>
