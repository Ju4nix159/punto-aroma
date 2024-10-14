<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto Aroma - Carrito de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo {
            color: #8cc63f;
            font-size: 24px;
            font-weight: bold;
        }
        .cart-icon {
            cursor: pointer;
            font-size: 24px;
        }
        #cart {
            position: fixed;
            top: 60px;
            right: 20px;
            width: 400px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            display: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .cart-title {
            color: #8cc63f;
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .quantity-control {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .quantity-buttons {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .quantity-btn {
            background-color: #f4f4f4;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .quantity-input {
            width: 40px;
            text-align: center;
            margin: 0 5px;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
        .btn {
            background-color: #8e44ad;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #703688;
        }
        .empty-cart {
            text-align: center;
            color: #666;
        }
        .product-info {
            display: flex;
            flex-direction: column;
        }
        .product-name {
            font-weight: bold;
        }
        .product-price {
            font-size: 0.9em;
            color: #666;
        }
        .remove-btn {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 0.8em;
            margin-top: 5px;
        }
        .remove-btn:hover {
            background-color: #ff3333;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Punto Aroma</div>
        <div class="cart-icon" onclick="toggleCart()">🛒</div>
    </div>

    <div id="cart">
        <h2 class="cart-title">Tu Carrito</h2>
        <div id="cart-content"></div>
    </div>

    <script>
        let cartItems = [
            { id: 1, name: "Vela Aromática de Lavanda", price: 14.99, quantity: 2 },
            { id: 2, name: "Sahumerio de Sándalo", price: 9.99, quantity: 1 }
        ];

        function toggleCart() {
            const cart = document.getElementById('cart');
            cart.style.display = cart.style.display === 'none' ? 'block' : 'none';
            updateCart();
        }

        function updateCart() {
            const cartContent = document.getElementById('cart-content');
            if (cartItems.length === 0) {
                cartContent.innerHTML = `
                    <div class="empty-cart">
                        <p>Tu carrito está vacío</p>
                        <p>Para agregar productos al carrito, debes iniciar sesión.</p>
                        <button class="btn">Iniciar Sesión</button>
                    </div>
                `;
            } else {
                let content = `
                    <table>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                `;
                let total = 0;
                cartItems.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    content += `
                        <tr>
                            <td>
                                <div class="product-info">
                                    <span class="product-name">${item.name}</span>
                                    <button class="remove-btn" onclick="removeItem(${item.id})">Eliminar</button>
                                </div>
                            </td>
                            <td>
                                <div class="quantity-control">
                                    <div class="quantity-buttons">
                                        <button class="quantity-btn" onclick="changeQuantity(${item.id}, -1)">-</button>
                                        <input type="number" class="quantity-input" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)">
                                        <button class="quantity-btn" onclick="changeQuantity(${item.id}, 1)">+</button>
                                    </div>
                                    <span class="product-price">$${item.price.toFixed(2)} c/u</span>
                                </div>
                            </td>
                            <td>$${itemTotal.toFixed(2)}</td>
                        </tr>
                    `;
                });
                content += `
                    </table>
                    <div class="total">Total a Pagar: $${total.toFixed(2)}</div>
                    <button class="btn">Terminar Compra</button>
                `;
                cartContent.innerHTML = content;
            }
        }

        function changeQuantity(id, change) {
            const item = cartItems.find(item => item.id === id);
            if (item) {
                item.quantity = Math.max(1, item.quantity + change);
                updateCart();
            }
        }

        function updateQuantity(id, newQuantity) {
            const item = cartItems.find(item => item.id === id);
            if (item) {
                item.quantity = Math.max(1, parseInt(newQuantity) || 1);
                updateCart();
            }
        }

        function removeItem(id) {
            cartItems = cartItems.filter(item => item.id !== id);
            updateCart();
        }

        // Initialize the cart
        updateCart();
    </script>
</body>
</html>