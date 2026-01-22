<?php
  $hora = date("H");

  if ($hora < 12) {
  	echo "Buenos días";
  } else if ($hora < 18) {
  	echo "Buenas tardes";
  } else {
  	echo "Buenas noches";
  }
?>




<?php 
    // Aquí va el código PHP 
?>




<?php
$nombre = "Juan";
$edad = 30;
?>



<?php
echo "Hola, mundo!"; 
?>





echo - Imprime una o más cadenas de texto.
		<?php echo "Hola, "; echo "mundo!"; ?>
print - Similar a echo, pero solo imprime una cadena y siempre devuelve 1.
		<?php print "Hola, mundo!"; ?>
var_dump - Muestra información estructurada sobre una o más expresiones, incluyendo el tipo y valor.
<?php 
$variable = array(1, 2, 3);
var_dump($variable); 
?>
print_r - Imprime información legible para humanos sobre una variable.

<?php 
$variable = array(1, 2, 3);
print_r($variable); 
?>


<?php include 'mi_archivo.php'; ?>



<?php
function saludar($nombre) {
    echo "Hola, " . $nombre . "!";
}
saludar("Maria");
?>




if ($edad >= 18) {
  	echo "Eres mayor de edad.";
} else {
  	echo "Eres menor de edad.";
}


for ($i = 0; $i < 10; $i++) {
echo $i . "<br>";
}


switch ($dia) {
  case "lunes":
    	echo "Hoy es lunes.";
   	 break;
  case "martes":
    	echo "Hoy es martes.";
    	break;
  default:
    	echo "Hoy no es ni lunes ni martes.";
}



function sumar($a, $b) {
  	return $a + $b;
}
$resultado = sumar(5, 3); // $resultado será igual a 8



