<?php
include 'header.php';
include 'admin/config/sbd.php';

$sql_destacados = $con->prepare("SELECT p.id_producto, p.nombre, i.nombre AS imagen_principal
FROM productos p
JOIN variantes_tipo_precio vtp ON p.id_producto = vtp.id_producto
JOIN categorias c ON p.id_categoria = c.id_categoria
LEFT JOIN imagenes i ON p.id_producto = i.id_producto AND i.principal = 1
WHERE p.destacado = 1 AND vtp.id_tipo_precio = 1;");
$sql_destacados->execute();
$destacados = $sql_destacados->fetchAll(PDO::FETCH_ASSOC);

$sql_banner = $con->prepare("SELECT * FROM banner");
$sql_banner->execute();
$banners = $sql_banner->fetchAll(PDO::FETCH_ASSOC);

$sql_categorias = $con->prepare("SELECT * FROM categorias WHERE destacado = 1");
$sql_categorias->execute();
$categorias_destacadas = $sql_categorias->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <style>
        .categoria-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .categoria-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .icono img {
            width: 100px;
            /* Ajusta el tamaño según necesites */
            height: 100px;
        }

        /* Estilos para la sección de productos */
        .producto-card {
            height: 500px;
            /* Altura fija para todas las tarjetas */
            overflow: hidden;
            position: relative;
        }

        .img-container {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .card-img-producto {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Cubre toda el área disponible */
            object-position: center;
            /* Centra la imagen */
        }

        .card-img-overlay {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.4) 40%, rgba(0, 0, 0, 0) 70%);
            padding: 20px;
        }

        .card-index-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .card-index-text {
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <section id="hero" class="py-5 text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-primary-custom mb-4" data-aos="fade-up">Descubre la Magia de los Aromas</h1>
            <p class="lead mb-4" data-aos="fade-up" data-aos-delay="200">Transforma tu espacio con nuestros sahumerios, velas aromáticas y perfumes exclusivos.</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center" data-aos="fade-up" data-aos-delay="400">
                <a href="catalogo.php"><button type="button" class="btn btn-primary-custom btn-lg px-4 gap-3">Explorar Catálogo</button></a>
                <a href="nosotros.php"><button type="button" class="btn btn-secondary-custom btn-lg px-4">Sobre nosotros</button></a>
            </div>
        </div>
    </section>




    <section id="banner" class="container py-5">
        <div class="container">
            <h2 class="text-center mb-4 text-secondary-custom">Ofertas Especiales</h2>
            <div id="ofertasCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Variable para controlar el primer elemento como "active"
                    $first = true;
                    foreach ($banners as $banner) {
                        // Extrae los datos del banner
                        $imagen = $banner['ruta'];
                        $titulo = $banner['nombre'];
                        $descripcion = $banner['descripcion'];
                        $enlace = $banner['id_pagina'];
                    ?>
                        <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                            <img src="<?php echo htmlspecialchars($imagen); ?>" class="d-block w-100 img-fluid" alt="<?php echo htmlspecialchars($titulo); ?>" style="max-height: 500px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block">
                                <h3><?php echo htmlspecialchars($titulo); ?></h3>
                                <p><?php echo htmlspecialchars($descripcion); ?></p>
                                <a href="<?php echo htmlspecialchars($enlace); ?>" class="btn btn-primary-custom">Ver oferta</a>
                            </div>
                        </div>
                    <?php
                        // Cambia $first a false después de la primera iteración
                        $first = false;
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#ofertasCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#ofertasCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>


    <section id="categorias" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 text-primary-custom" data-aos="fade-up">Nuestras Categorías Destacadas</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                <?php
                $delay = 0;
                foreach ($categorias_destacadas as $categoria) {
                    $iconoSVG = $categoria['icono']; // Se asume que esta columna contiene el SVG o la ruta del SVG
                ?>
                    <div class="col" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                        <a href="catalogo.php?categoria=<?php echo urlencode($categoria['nombre']); ?>" class="text-decoration-none">
                            <div class="h-100 bg-primary-light border-0 categoria-card">
                                <div class="card-body text-center">
                                    <div class="icono fs-1 text-primary-custom mb-3">
                                        <img src="./assets/iconos/<?php echo $iconoSVG; ?>" alt="<?php echo $categoria['nombre']; ?>" class="fs-1 text-primary-custom mb-3">
                                    </div>
                                    <h3 class="card-title text-black"><?php echo $categoria['nombre']; ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php
                    $delay += 200; // Incrementar el delay para el efecto escalonado
                }
                ?>
            </div>
        </div>
    </section>

    <section id="productos" class="container py-5">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <div class="card text-white producto-card">
                    <div class="img-container">
                        <img src="./assets/productos/imagen/65/FA.webp" class="card-img-producto" alt="aerosoles saphirus">
                    </div>
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Aerosoles SAPHIRUS</h5>
                        <p class="card-index-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptatem voluptate natus dolorem ipsum qui at, commodi id eos nostrum laborum? Libero porro totam ex ab, eveniet vitae? Officia, asperiores ex.</p>
                        <a href="producto.php?id_producto=65" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white producto-card">
                    <div class="img-container">
                        <img src="./assets/productos/imagen/66/FT.webp" class="card-img-producto" alt="textiles saphirus">
                    </div>
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Textiles saphirus</h5>
                        <p class="card-index-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe, ullam nam nobis perferendis fugiat suscipit ut fugit libero ipsam ad illo beatae dicta mollitia labore atque laborum sint dolore tempora?</p>
                        <a href="producto.php?id_producto=66" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white producto-card">
                    <div class="img-container">
                        <img src="./assets/productos/imagen/61/AM.webp" class="card-img-producto" alt="difusores saphirus">
                    </div>
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">difusores saphirus</h5>
                        <p class="card-index-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam dolorem at iure fugit illo optio consectetur tenetur, fugiat, odit quo nihil, adipisci molestias natus esse animi neque commodi id ex?</p>
                        <a href="producto.php?id_producto=61" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white producto-card">
                    <div class="img-container">
                        <img src="./assets/productos/imagen/59/AE.webp" class="card-img-producto" alt="Aceites para hornito">
                    </div>
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Aceites para hornito</h5>
                        <p class="card-index-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Iure numquam laboriosam commodi, ex facere exercitationem soluta magni quo modi tempora similique fugit nesciunt itaque a ea? Neque iusto ullam nemo.</p>
                        <a href="producto.php?id_producto=59" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="destacados" class="featured-products">
        <div class="container">
            <h2 class="text-center mb-5 text-primary-custom" data-aos="fade-up">Nuestros Productos Destacados</h2>

            <div class="row g-4">
                <?php foreach ($destacados as $destacado) { ?>
                    <div class="col-md-3">
                        <a href="producto.php?id_producto=<?php echo $destacado["id_producto"] ?>" class="text-decoration-none">
                            <div class="product-card-destacado">
                                <img src="./assets/productos/imagen/<?php echo $destacado["id_producto"] ?>/<?php echo $destacado["imagen_principal"] ?>" alt="<?php echo $destacado["nombre"] ?>" class="img-fluid">
                                <div class="product-overlay">
                                    <h3 class="product-name"><?php echo $destacado["nombre"] ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section id="testimonios" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 text-primary-custom" data-aos="fade-up">Lo que dicen nuestros clientes</h2>
            <div id="testimonialCarousel" class="carousel slide testimonial-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="card bg-primary-light border-0">
                            <div class="card-body text-center">
                                <p class="card-text">"Los productos de Punto Aroma han transformado mi hogar. Los aromas son increíbles y duraderos."</p>
                                <footer class="blockquote-footer mt-2">María G.</footer>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="card bg-primary-light border-0">
                            <div class="card-body text-center">
                                <p class="card-text">"Las velas aromáticas son perfectas para crear un ambiente relajante después de un largo día de trabajo."</p>
                                <footer class="blockquote-footer mt-2">Carlos R.</footer>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="card bg-primary-light border-0">
                            <div class="card-body text-center">
                                <p class="card-text">"Los sahumerios de Punto Aroma son los mejores que he probado. Calidad superior y aromas únicos."</p>
                                <footer class="blockquote-footer mt-2">Ana L.</footer>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
    </section>

    <section id="contacto" class="py-5 bg-primary-light">
        <div class="container">
            <h2 class="text-center mb-5 text-primary-custom" data-aos="fade-up">Contáctanos</h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form data-aos="fade-up" data-aos-delay="200">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Mensaje"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <button class="button" onclick="scrollToTop()">
        <svg class="svgIcon" viewBox="0 0 384 512">
            <path
                d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"></path>
        </svg>
    </button>
    <?php
    // Variables dinámicas
    $numero = "3517550374";
    $mensaje = "Hola, buenos dias. Quiero realizar una compra por la pagina web Aroma y Bienestar";

    // Codificar el mensaje para URL
    $mensajeCodificado = urlencode($mensaje);
    ?>

    <ul class="wrapper">
        <li class="icon whatsapp" id="whatsappButton">
            <span class="tooltip">WhatsApp</span>
            <i class="fab fa-whatsapp"></i>
        </li>
    </ul>


    <footer class="">
        <?php include 'footer.php'; ?>

    </footer>
    <script>
        // Datos dinámicos desde PHP
        const numero = "<?php echo $numero; ?>";
        const mensaje = "<?php echo $mensajeCodificado; ?>";

        // Agregar evento de clic al botón de WhatsApp
        document.getElementById("whatsappButton").addEventListener("click", function() {
            const url = `https://wa.me/${numero}?text=${mensaje}`;
            window.open(url, "_blank"); // Abre WhatsApp en una nueva pestaña
        });
        AOS.init({
            duration: 1000,
            once: true,
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: "smooth",
            });
        }
    </script>
</body>

</html>