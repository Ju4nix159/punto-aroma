window.onload = function() {
    var carritoGuardado = cargarCarritoDesdeLocalStorage();
    
    if (carritoGuardado) {
        // Enviar carrito al servidor para sincronizar
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "sincronizar_carrito.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log("Carrito sincronizado con éxito.");
            }
        };
        
        xhr.send(JSON.stringify({
            carrito: carritoGuardado
        }));
    }
};
