<?php
$ip = $_SERVER["REMOTE_ADDR"];
$navegador = $_SERVER["HTTP_USER_AGENT"];

$esMovil = preg_match("/mobile|android|iphone/i", $navegador);

echo "IP del usuario: $ip <br>";
echo "Navegador: $navegador <br>";
echo "Dispositivo: " . ($esMovil ? "Móvil" : "Escritorio");
