<?php 

/* session_start(); // Asegúrate de iniciar la sesión
print_r($_SESSION); // Esto imprimirá todo lo que hay almacenado en la sesión

unset($_SESSION['carrito']); // Esto eliminará el carrito de la sesión
unset($_SESSION['cart_temp']); // Esto eliminará el carrito de la sesión

print_r($_SESSION); // Esto imprimirá todo lo que hay almacenado en la sesión */


?>
<script>

    console.log(JSON.parse(sessionStorage.getItem('carritoTemporal')));
</script>

