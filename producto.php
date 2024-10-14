<?php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Resumen del Producto - Punto Aroma</title>

</head>

<body>
    <main class="py-5">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-primary-custom">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="catalogo.php" class="text-decoration-none text-primary-custom">Catálogo</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Vela Aromática</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-6">
                    <div class="product-gall">
                        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-VCzua0t5irdT7NNSC6l73qiszWElp2.png" alt="Vela Aromática" class="gall-main-image" id="main-image">
                        <button class="gall-nav prev" onclick="changeImage(-1)">&lt;</button>
                        <button class="gall-nav next" onclick="changeImage(1)">&gt;</button>
                        <div class="gall-thumbnails">
                            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-VCzua0t5irdT7NNSC6l73qiszWElp2.png" alt="Thumbnail 1" class="gall-thumbnail active" onclick="setMainImage(this.src)">
                            <img src="/placeholder.svg?height=60&width=60" alt="Thumbnail 2" class="gall-thumbnail" onclick="setMainImage(this.src)">
                            <img src="/placeholder.svg?height=60&width=60" alt="Thumbnail 3" class="gall-thumbnail" onclick="setMainImage(this.src)">
                            <img src="/placeholder.svg?height=60&width=60" alt="Thumbnail 4" class="gall-thumbnail" onclick="setMainImage(this.src)">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h1 class="mb-3 text-primary-custom">Vela Aromática</h1>
                    <p class="lead">Disfruta de la calidez y el aroma relajante de nuestras velas aromáticas de alta calidad.</p>
                    <p><strong>Precio:</strong> $14.99</p>
                    <p>Elige entre nuestras diferentes fragancias y personaliza tu experiencia aromática.</p>

                    <form id="product-form">
                        <div id="fragrances-list">
                            <div class="fragrance-item">
                                <h5>Lavanda</h5>
                                <div class="product-count">
                                    <div class="d-flex">
                                        <button type="button" class="btn-primary-custom qtyminus" onclick="decrementQuantity('lavanda')">-</button>
                                        <input type="text" name="quantity" value="1" class="qty">
                                        <button type="button" class="btn-primary-custom qtyplus" onclick="incrementQuantity('lavanda')">+</button>
                                    </div>
                                </div>
                            </div>
                            <div class="fragrance-item">
                                <h5>Vainilla</h5>
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn" onclick="decrementQuantity('vainilla')">-</button>
                                    <input type="number" id="quantity-vainilla" class="cantidad" value="0" min="0" readonly>
                                    <button type="button" class="quantity-btn" onclick="incrementQuantity('vainilla')">+</button>
                                </div>
                            </div>
                            <div class="fragrance-item">
                                <h5>Canela</h5>
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn" onclick="decrementQuantity('canela')">-</button>
                                    <input type="number" id="quantity-canela" class="cantidad" value="0" min="0" readonly>
                                    <button type="button" class="quantity-btn" onclick="incrementQuantity('canela')">+</button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h4>Total: $<span id="total-price">0.00</span></h4>
                        </div>

                        <button type="button" class="btn btn-primary-custom btn-lg mt-3" onclick="addToCart()">
                            Agregar al Carrito
                        </button>
                    </form>

                    <a href="#" class="btn btn-outline-secondary mt-3">Volver al Catálogo</a>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-primary-custom">Descripción del Producto</h3>
                    <p>Nuestras velas aromáticas están hechas con cera de soja 100% natural y aceites esenciales de la más alta calidad. Cada vela está diseñada para proporcionar una experiencia sensorial única, llenando tu espacio con aromas relajantes y creando un ambiente acogedor.</p>
                    <p>Características:</p>
                    <ul>
                        <li>Duración aproximada de 30 horas</li>
                        <li>Cera de soja natural y ecológica</li>
                        <li>Mecha de algodón sin plomo</li>
                        <li>Fragancias 100% naturales</li>
                        <li>Envase de vidrio reutilizable</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-3 bg-primary-light mt-5">
        <div class="container">
            <p class="text-center text-muted mb-0">&copy; 2024 Punto Aroma. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>