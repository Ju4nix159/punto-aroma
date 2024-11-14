function incrementQuantity(sku, aroma) {
    var quantityInput = document.getElementById('quantity-' + sku);
    var currentQuantity = parseInt(quantityInput.value);
    quantityInput.value = currentQuantity + 1;
    updateTotalPrice();
}

function decrementQuantity(sku, aroma) {
    var quantityInput = document.getElementById('quantity-' + sku);
    var currentQuantity = parseInt(quantityInput.value);
    if (currentQuantity > 0) {
        quantityInput.value = currentQuantity - 1;
        updateTotalPrice();
    }
}

function updateTotalPrice() {
    var total = 0;
    var quantities = document.querySelectorAll('.cantidad');
    var precio = document.getElementById('precio-producto').value;
    quantities.forEach(function(input) {
        var quantity = parseInt(input.value);
        total += quantity * precio;
    });
    document.getElementById('total-price').innerText = total.toFixed(2);
}


function listarFragancias() {
    const fragancias = [];
    // Obtener todos los contenedores de fragancias
    const fragranceItems = document.querySelectorAll('.fragrance-item');

    fragranceItems.forEach(item => {
        // Extraer datos de cada fragancia
        const sku = item.getAttribute('data-sku');
        const aroma = item.getAttribute('data-aroma');
        const cantidad = parseInt(item.querySelector('.cantidad').value) || 0; // Evita NaN si el valor es vacío

        // Crear un objeto con la información y agregarlo a la lista
        if (cantidad > 0) {
            fragancias.push({
            aroma: aroma,
            sku: sku,
            cantidad: cantidad
            });
        }
    });
    // Imprimir la lista de fragancias en consola
    console.log(fragancias);
    // Si quieres que devuelva la lista en lugar de solo imprimirla:
    return fragancias;
}

function addToCart() {
    var nombreProducto = document.getElementById('nombre-producto').value;
    var precioProducto = parseFloat(document.getElementById('precio-producto').value);
    var idProducto = document.getElementById('id-producto').value;
    var fragancias = listarFragancias();
    if (fragancias.length === 0) {
        alert("Por favor, seleccione al menos una fragancia.");
        return;
    }

    var producto = {
        nombre: nombreProducto,
        precio: precioProducto,
        id: idProducto,
        fragancias: fragancias
    };

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "añadir_carrito.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert("Producto(s) agregado(s) al carrito exitosamente.");
            } else {
                alert("Hubo un problema al agregar el producto al carrito.");
            }
        }
    };

    xhr.send(JSON.stringify({
        producto: producto
    }));
    document.querySelectorAll('.cantidad').forEach(input => input.value = 0);
};
