<?php
session_start();

if (!isset($_SESSION["logueado"])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
hola pajarito
</body>
</html>