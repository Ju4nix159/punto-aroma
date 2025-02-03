<!-- carrito.php -->
<div class="position-relative">
    <div class="offcanvas offcanvas-end" tabindex="-1" id="carritoOffcanvas" aria-labelledby="carritoOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="carritoOffcanvasLabel">Tu Carrito</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cart-items">

            </div>
            <div id="empty-cart-message" class="text-center py-4">
                <i class="bi bi-cart-x" style="font-size: 3rem; color: var(--secondary-color);"></i>
                <p class="mt-3">Tu carrito está vacío</p>
                <a href="./catalogo.php" class="btn btn-primary-custom">explorar catalogo</a>
            </div>
            <div id="cart-summary">
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h4 class="text-secondary-custom">Total:</h4>
                    <h4 class="text-primary-custom" id="cart-total">$0.00</h4>
                </div>
                <div class="d-grid gap-2 mt-4">
                    <?php if (isset($_SESSION['usuario'])): ?>
                        <a href="checkout.php" class="btn btn-primary-custom" id="checkout-button">Proceder al pago</a>
                        <button class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Seguir comprando</button>
                    <?php else: ?>
                        <a href="iniciarSesion.php" class="btn btn-primary-custom" id="login-button">Iniciar sesión</a>
                    <p class="mt-3">Para gestionar su pedido, debe iniciar sesión o crear una cuenta.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="carrito.js"></script>
<script>
    
</script>