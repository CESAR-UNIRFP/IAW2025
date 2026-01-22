<?php
session_start();

if (isset($_SESSION["usuario"])) {
    echo "<h1>Bienvenido " . $_SESSION["usuario"] . "</h1>";
} else {
    echo "<h1>Debes iniciar sesión</h1>";
    echo "<a href='login.php'>Ir al login</a>";
}
