<?php 

session_start(); // Asegúrate de iniciar la sesión
print_r($_SESSION); // Esto imprimirá todo lo que hay almacenado en la sesión
echo "<br>";
print_r($_SESSION['carrito']); // Esto imprimirá solo el carrito


?>
