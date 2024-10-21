<?php
include 'header.php';

?>

<!DOCTYPE html>
<html lang="es">

<body>


    <main>
        <section id="hero" class="py-5 text-center bg-primary-light">
            <div class="container">
                <h1 class="display-4 fw-bold text-primary-custom mb-4" data-aos="fade-up">Descubre la Magia de los Aromas</h1>
                <p class="lead mb-4" data-aos="fade-up" data-aos-delay="200">Transforma tu espacio con nuestros sahumerios, velas aromáticas y perfumes exclusivos.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center" data-aos="fade-up" data-aos-delay="400">
                    <a href="catalogo.php"><button type="button" class="btn btn-primary-custom btn-lg px-4 gap-3">Explorar Catalogo</button></a>
                    <a href="nosotros.php"><button type="button" class="btn btn-secondary-custom btn-lg px-4">Sobre nosotros</button></a>
                </div>
            </div>
        </section>

        <section id="destacados" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5 text-primary-custom" data-aos="fade-up">Nuestros Productos Destacados</h2>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                    <div class="col" data-aos="fade-up">
                        <div class="h-100 bg-primary-light border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-tree fs-1 text-primary-custom mb-3"></i>
                                <h3 class="card-title text-black">Sahumerios</h3>
                                <p class="card-text">Aromas naturales para purificar tu espacio.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="200">
                        <div class=" h-100 bg-primary-light border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-fire fs-1 text-primary-custom mb-3"></i>
                                <h3 class="card-title text-black">Velas Aromáticas</h3>
                                <p class="card-text">Ilumina y perfuma tu hogar con nuestras velas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="400">
                        <div class=" h-100 bg-primary-light border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-droplet fs-1 text-primary-custom mb-3"></i>
                                <h3 class="card-title text-black">Perfumes</h3>
                                <p class="card-text">Fragancias únicas para cada ocasión.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col" data-aos="fade-up" data-aos-delay="600">
                        <div class="card h-100 bg-primary-light border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-flower1 fs-1 text-primary-custom mb-3"></i>
                                <h3 class="card-title text-black">Aceites Esenciales</h3>
                                <p class="card-text">Esencias puras para aromaterapia y bienestar.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container py-5">
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <div class="col">
                <div class="card text-white">
                    <img src="i1.jpg" class="card-img" alt="Sahumerios Vishnu Masala">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Sahumerios Vishnu Masala</h5>
                        <p class="card-index-text">Perfumes, flores y fibras vegetales de alta calidad. Aromas: Antiestrés, Energía, Relajación, Sensual, Meditación, Frescura El aroma perdura por más tiempo en el ambiente.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="i2.jpg" class="card-img" alt="Sahumerios Holi India">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Sahumerios Holi India</h5>
                        <p class="card-index-text">Renovamos la línea de sahumerios Holi India Pack con más color y los excelentes aromas premium de siempre. Presentación aromas surtido x 100 unidades.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="i3.jpg" class="card-img" alt="Aromatizantes textiles">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Aromatizantes textiles</h5>
                        <p class="card-index-text">Perfume para aromatizar ropa y ambientes. Sentirás bien fresco un aroma especial.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card text-white">
                    <img src="i4.png" class="card-img" alt="Aceites para hornito">
                    <div class="card-img-overlay d-flex flex-column justify-content-end">
                        <h5 class="card-index-title">Aceites para hornito</h5>
                        <p class="card-index-text">Nuevos aromas y nueva presentación x 5 unidades. Más variedad por el mismo precio.</p>
                        <a href="#" class="btn btn-primary-custom">CONOCELOS</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <section id="ofertas" class="py-5 bg-primary-light">
            <div class="container">
                <h2 class="text-center mb-5 text-primary-custom" data-aos="fade-up">Galería de Productos</h2>
                <div class="product-gallery">
                <?php for ($i = 0; $i < 8; $i++) { ?>
                    <img src="1.webp" alt="Perfume <?php echo $i?>" data-aos="zoom-in">
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
    </main>
    <button class="button" onclick="scrollToTop()">
        <svg class="svgIcon" viewBox="0 0 384 512">
            <path
                d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"></path>
        </svg>
    </button>

    <footer class="py-3 bg-primary-light mt-5">
        <div class="container">
            <p class="text-center text-muted mb-0">&copy; 2024 Punto Aroma. Todos los derechos reservados.</p>
        </div>
    </footer>


    <script>
        AOS.init({
            duration: 1000,
            once: true,
        });

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    </script>
</body>

</html>