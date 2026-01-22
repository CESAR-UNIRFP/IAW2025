<?php
$edad = 22;

if ($edad < 18) {
    header("Location: menor.html");
    exit();
}
?>
<!DOCTYPE html>
<html>
<body>
<h1>Acceso permitido</h1>
</body>
</html>
