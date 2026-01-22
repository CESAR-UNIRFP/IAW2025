<!DOCTYPE html>
<html>
<body>

<?php
try {
    $a = 5;
    $b = 0;

    if ($b == 0) {
        throw new Exception("División por cero");
    }

    echo $a / $b;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

</body>
</html>
