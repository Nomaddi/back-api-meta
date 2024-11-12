<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Maddi Go</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/style.css">
    <script src="https://maddigo.com.co/js/chat-embed.js?botId=9&botNombre=Maddigo"></script>
    {{-- <script src="http://127.0.0.1:8000/js/chat-embed.js?botId=20&botNombre=Dina"></script> --}}

</head>

<body data-bs-spy="scroll" data-bs-target=".navbar">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <img src="images/logo-letras-maddigo.png" width="120" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#hero">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Acerca de</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contacto</a>
                    </li>
                </ul>
                @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/home') }}"
                                class="btn btn-brand ms-lg-3">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-brand ms-lg-3">Ingresar</a>

                            {{-- @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                            @endif --}}
                        @endauth
                @endif
                {{-- <a href="#" class="btn btn-brand ms-lg-3">Download</a> --}}
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section id="hero" class="min-vh-100 d-flex align-items-center text-center">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 data-aos="fade-left" class="text-uppercase text-white fw-semibold display-1">MADDI GO
                    </h1>
                    <h5 class="text-white mt-3 mb-4" data-aos="fade-right">IMPULSA TU NEGOCIO CON WHATSAPP</h5>
                    <div data-aos="fade-up" data-aos-delay="50">
                        <a href="#services" class="btn btn-brand me-2">Servicios</a>
                        <a href="#contact" class="btn btn-light ms-2">Contactanos</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!-- SOBRE NOSOTROS -->
<section id="about" class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-down" data-aos-delay="50">
                <div class="section-title">
                    <h1 class="display-4 fw-semibold">Sobre nosotros</h1>
                    <div class="line"></div>
                    <p>En Maddi Go nos especializamos en ofrecer soluciones de envíos masivos de WhatsApp a través de la API Cloud de Meta, complementado con inteligencia artificial para una integración fluida con chatbots, mejorando la experiencia de conversación automatizada.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-between align-items-center">
            <div class="col-lg-6" data-aos="fade-down" data-aos-delay="50">
                <img src="images/about.jpg" alt="">
            </div>
            <div data-aos="fade-down" data-aos-delay="150" class="col-lg-5">
                <h1>Sobre Maddi Go</h1>
                <p class="mt-3 mb-4">Somos expertos en crear soluciones innovadoras que permiten a las empresas gestionar mejor su comunicación con clientes a través de WhatsApp, optimizando el flujo de conversaciones gracias a la inteligencia artificial.</p>
                <div class="d-flex pt-4 mb-3">
                    <div class="iconbox me-4">
                        <i class="ri-mail-send-fill"></i>
                    </div>
                    <div>
                        <h5>Automatización efectiva</h5>
                        <p>Implementamos inteligencia artificial para crear chatbots capaces de tener conversaciones más naturales y eficientes.</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="iconbox me-4">
                        <i class="ri-user-5-fill"></i>
                    </div>
                    <div>
                        <h5>Envíos masivos con precisión</h5>
                        <p>Gestionamos envíos masivos de WhatsApp con alta precisión y personalización para cada cliente.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="iconbox me-4">
                        <i class="ri-rocket-2-fill"></i>
                    </div>
                    <div>
                        <h5>Soluciones personalizadas</h5>
                        <p>Ofrecemos soluciones adaptadas a las necesidades específicas de tu negocio para mejorar la comunicación con tus clientes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICIOS -->
<section id="services" class="section-padding border-top">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-down" data-aos-delay="150">
                <div class="section-title">
                    <h1 class="display-4 fw-semibold">Nuestros Servicios</h1>
                    <div class="line"></div>
                    <p>En Maddi Go ofrecemos soluciones tecnológicas que optimizan la gestión de la comunicación de tu empresa.</p>
                </div>
            </div>
        </div>
        <div class="row g-4 text-center">
            <div class="col-lg-4 col-sm-6" data-aos="fade-down" data-aos-delay="150">
                <div class="service theme-shadow p-lg-5 p-4">
                    <div class="iconbox">
                        <i class="ri-mail-send-fill"></i>
                    </div>
                    <h5 class="mt-4 mb-3">Envíos Masivos</h5>
                    <p>Enviamos mensajes a gran escala a través de la API Cloud de WhatsApp para optimizar tu comunicación.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6" data-aos="fade-down" data-aos-delay="250">
                <div class="service theme-shadow p-lg-5 p-4">
                    <div class="iconbox">
                        <i class="ri-chat-3-fill"></i>
                    </div>
                    <h5 class="mt-4 mb-3">Chatbots Inteligentes</h5>
                    <p>Implementamos inteligencia artificial para integrar chatbots capaces de conversaciones fluidas y naturales.</p>
                </div>
            </div>
            <div class="col-lg-4 col-sm-6" data-aos="fade-down" data-aos-delay="350">
                <div class="service theme-shadow p-lg-5 p-4">
                    <div class="iconbox">
                        <i class="ri-dashboard-fill"></i>
                    </div>
                    <h5 class="mt-4 mb-3">Integración y Gestión</h5>
                    <p>Ofrecemos plataformas de gestión para administrar tus envíos y automatizaciones de manera sencilla.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTÁCTANOS -->
<section class="section-padding bg-light" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center" data-aos="fade-down" data-aos-delay="150">
                <div class="section-title">
                    <h1 class="display-4 text-white fw-semibold">Contáctanos</h1>
                    <div class="line bg-white"></div>
                    <p class="text-white">Si quieres saber más sobre nuestros servicios, no dudes en contactarnos.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" data-aos="fade-down" data-aos-delay="250">
            <div class="col-lg-8">
                <form action="#" class="row g-3 p-lg-5 p-4 bg-white theme-shadow">
                    <div class="form-group col-lg-6">
                        <input type="text" class="form-control" placeholder="Ingresa tu nombre">
                    </div>
                    <div class="form-group col-lg-6">
                        <input type="text" class="form-control" placeholder="Ingresa tu apellido">
                    </div>
                    <div class="form-group col-lg-12">
                        <input type="email" class="form-control" placeholder="Ingresa tu correo">
                    </div>
                    <div class="form-group col-lg-12">
                        <input type="text" class="form-control" placeholder="Asunto">
                    </div>
                    <div class="form-group col-lg-12">
                        <textarea name="message" rows="5" class="form-control" placeholder="Mensaje"></textarea>
                    </div>
                    <div class="form-group col-lg-12 d-grid">
                        <button class="btn btn-brand">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- PIE DE PÁGINA -->
<footer class="bg-dark">
    <div class="footer-top">
        <div class="container">
            <div class="row gy-5">
                <div class="col-lg-3 col-sm-6">
                    <a href="#"><img src="images/logo-letras-maddigo.png" width="150" alt=""></a>
                    <div class="line"></div>
                    <p>En Maddi Go ofrecemos soluciones para que tu empresa mejore su comunicación con clientes a través de WhatsApp e inteligencia artificial.</p>
                    <div class="social-icons">
                        <a href="#"><i class="ri-twitter-fill"></i></a>
                        <a href="#"><i class="ri-instagram-fill"></i></a>
                        <a href="#"><i class="ri-linkedin-fill"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <h5 class="mb-0 text-white">SERVICIOS</h5>
                    <div class="line"></div>
                    <ul>
                        <li><a href="#">Envíos Masivos</a></li>
                        <li><a href="#">Chatbots</a></li>
                        <li><a href="#">Automatización</a></li>
                        <li><a href="#">Gestión de Mensajes</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <h5 class="mb-0 text-white">ACERCA DE</h5>
                    <div class="line"></div>
                    <ul>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Nosotros</a></li>
                        <li><a href="#">Carreras</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <h5 class="mb-0 text-white">CONTACTO</h5>
                    <div class="line"></div>
                    <ul>
                        <li>Cl. 26 #36-49, Villavicencio, Colombia</li>
                        <li>+57 320 4956302</li>
                        <li>javier@nomaddi.com</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row g-4 justify-content-between">
                <div class="col-auto">
                    <p class="mb-0">© Copyright Maddi Go. Todos los derechos reservados</p>
                </div>
                <div class="col-auto">
                    <p class="mb-0">Diseñado con 💜 por Maddi Go</p>
                </div>
            </div>
        </div>
    </div>
</footer>







    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/main.js"></script>
</body>

</html>
