<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Certificación Eléctrica Profesional</title>
    <meta name="description" content="ELECTROTEC - Empresa líder en certificación eléctrica. Ofrecemos servicios profesionales de inspección, certificación y gestión de equipos eléctricos con la más alta calidad y confiabilidad.">
    <meta name="keywords" content="certificación eléctrica, inspección eléctrica, equipos eléctricos, certificados técnicos, electrotecnia">
    
    <link rel="icon" type="image/x-icon" href="./assets/images/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon-16x16.png">

    <!-- CSS Global -->
    <link href="assets/css/global.css" rel="stylesheet">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrotec.com/">
    <meta property="og:title" content="ELECTROTEC | Certificación Eléctrica Profesional">
    <meta property="og:description" content="Empresa líder en certificación eléctrica con servicios profesionales de inspección y gestión de equipos eléctricos.">
    <meta property="og:image" content="https://electrotec.com/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://electrotec.com/">
    <meta property="twitter:title" content="ELECTROTEC | Certificación Eléctrica Profesional">
    <meta property="twitter:description" content="Empresa líder en certificación eléctrica con servicios profesionales de inspección y gestión de equipos eléctricos.">
    <meta property="twitter:image" content="https://electrotec.com/og-image.jpg">
</head>
<body>
    <!-- Navigation Header -->
    <nav class="navbar glass" style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 1rem 0;">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                <!-- Logo y Brand -->
                <div class="brand">
                    <div class="brand-logo">
                        <img src="assets/images/logo.png" alt="ELECTROTEC Logo" style="width: 40px; height: 40px;" >
                    </div>
                    <div>
                        <div class="brand-title">ELECTROTEC</div>
                        <div class="brand-subtitle">Certificación Eléctrica</div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="d-flex align-items-center" style="gap: 2rem;">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#servicios" class="nav-link">Servicios</a>
                    <a href="#caracteristicas" class="nav-link">Características</a>
                    <a href="#contacto" class="nav-link">Contacto</a>
                    <a href="login.php" class="btn btn-primary btn-sm">Acceder al Sistema</a>
                </div>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn d-none" id="mobileMenuBtn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="mobile-menu glass d-none" id="mobileMenu">
        <div class="mobile-menu-content">
            <a href="#inicio" class="mobile-nav-link">Inicio</a>
            <a href="#servicios" class="mobile-nav-link">Servicios</a>
            <a href="#caracteristicas" class="mobile-nav-link">Características</a>
            <a href="#contacto" class="mobile-nav-link">Contacto</a>
            <a href="login.php" class="btn btn-primary btn-block">Acceder al Sistema</a>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">
                    Certificación Eléctrica
                    <span style="display: block; background: linear-gradient(135deg, #5C66CC, #2A2F6C); background-clip: text; -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Profesional
                    </span>
                </h1>
                <p class="hero-subtitle">
                    Garantizamos la seguridad y calidad de tus instalaciones eléctricas con nuestro sistema avanzado de certificación digital y gestión de equipos.
                </p>
                <div class="d-flex justify-content-center" style="gap: 1rem; flex-wrap: wrap;">
                    <a href="login.php" class="btn btn-primary btn-xl">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12l2 2 4-4"></path>
                            <path d="M21 12c0 1.66-1.34 3-3 3h-7l-4-4 4-4h7c1.66 0 3 1.34 3 3z"></path>
                        </svg>
                        Comenzar Ahora
                    </a>
                    <a href="#servicios" class="btn btn-secondary btn-xl">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polygon points="10,8 16,12 10,16 10,8"></polygon>
                        </svg>
                        Ver Demo
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Características Principales -->
    <section id="caracteristicas" style="padding: 6rem 0;">
        <div class="container">
            <div class="text-center" style="margin-bottom: 4rem;">
                <h2>¿Por qué elegir ELECTROTEC?</h2>
                <p class="lead">Innovación, seguridad y eficiencia en cada certificación</p>
            </div>
            
            <div class="row">
                <div class="col col-4">
                    <div class="feature-card glass">
                        <div class="feature-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 12l2 2 4-4"></path>
                                <path d="M21 12c0 1.66-1.34 3-3 3h-7l-4-4 4-4h7c1.66 0 3 1.34 3 3z"></path>
                            </svg>
                        </div>
                        <h3>Certificación Digital</h3>
                        <p class="text-muted">
                            Sistema completamente digitalizado que garantiza certificados válidos, seguros y de fácil verificación.
                        </p>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="feature-card glass">
                        <div class="feature-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                <line x1="8" y1="21" x2="16" y2="21"></line>
                                <line x1="12" y1="17" x2="12" y2="21"></line>
                            </svg>
                        </div>
                        <h3>Gestión Inteligente</h3>
                        <p class="text-muted">
                            Plataforma web intuitiva para gestionar clientes, equipos y certificados desde cualquier dispositivo.
                        </p>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="feature-card glass">
                        <div class="feature-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1 1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                        </div>
                        <h3>Cumplimiento Normativo</h3>
                        <p class="text-muted">
                            Cumplimos con todas las normas técnicas y regulaciones vigentes para garantizar la validez legal.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="row" style="margin-top: 2rem;">
                <div class="col col-4">
                    <div class="feature-card glass">
                        <div class="feature-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"></polyline>
                            </svg>
                        </div>
                        <h3>Monitoreo en Tiempo Real</h3>
                        <p class="text-muted">
                            Supervisa el estado de tus certificados y recibe alertas automáticas antes del vencimiento.
                        </p>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="feature-card glass">
                        <div class="feature-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <h3>Programación Flexible</h3>
                        <p class="text-muted">
                            Agenda inspecciones y certificaciones de manera eficiente con nuestro sistema de citas integrado.
                        </p>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="feature-card glass">
                        <div class="feature-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3>Soporte Especializado</h3>
                        <p class="text-muted">
                            Equipo de técnicos especializados disponible para resolver cualquier consulta o inconveniente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section id="servicios" style="padding: 6rem 0; background: linear-gradient(135deg, rgba(92, 102, 204, 0.05), rgba(42, 47, 108, 0.05));">
        <div class="container">
            <div class="text-center" style="margin-bottom: 4rem;">
                <h2>Nuestros Servicios</h2>
                <p class="lead">Soluciones completas para tus necesidades de certificación eléctrica</p>
            </div>
            
            <div class="row">
                <div class="col col-6">
                    <div class="glass card-lg" style="height: 100%;">
                        <div class="d-flex align-items-start" style="gap: 1.5rem;">
                            <div class="feature-icon" style="min-width: 80px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14,2 14,8 20,8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10,9 9,9 8,9"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h3>Certificación de Instalaciones</h3>
                                <p class="text-muted">
                                    Inspección completa y certificación de instalaciones eléctricas residenciales, comerciales e industriales. 
                                    Garantizamos el cumplimiento de todas las normas de seguridad.
                                </p>
                                <ul style="color: var(--text-muted); margin-top: 1rem;">
                                    <li>Instalaciones residenciales</li>
                                    <li>Complejos comerciales</li>
                                    <li>Plantas industriales</li>
                                    <li>Certificados con validez legal</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-6">
                    <div class="glass card-lg" style="height: 100%;">
                        <div class="d-flex align-items-start" style="gap: 1.5rem;">
                            <div class="feature-icon" style="min-width: 80px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                            <div>
                                <h3>Gestión Digital de Equipos</h3>
                                <p class="text-muted">
                                    Sistema completo para el registro, seguimiento y mantenimiento de equipos eléctricos. 
                                    Control total de tu inventario desde una plataforma centralizada.
                                </p>
                                <ul style="color: var(--text-muted); margin-top: 1rem;">
                                    <li>Registro de equipos</li>
                                    <li>Historial de mantenimientos</li>
                                    <li>Alertas de vencimiento</li>
                                    <li>Reportes automatizados</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row" style="margin-top: 2rem;">
                <div class="col col-6">
                    <div class="glass card-lg" style="height: 100%;">
                        <div class="d-flex align-items-start" style="gap: 1.5rem;">
                            <div class="feature-icon" style="min-width: 80px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11H5a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h4l-1-1v-1a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1l-1 1h4a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2h-4"></path>
                                    <path d="M12 2v9"></path>
                                    <path d="M8 6l4-4 4 4"></path>
                                </svg>
                            </div>
                            <div>
                                <h3>Consultoría Técnica</h3>
                                <p class="text-muted">
                                    Asesoramiento especializado para optimizar tus instalaciones eléctricas y cumplir con las normativas vigentes. 
                                    Nuestros expertos te guían en cada paso.
                                </p>
                                <ul style="color: var(--text-muted); margin-top: 1rem;">
                                    <li>Análisis de normativas</li>
                                    <li>Optimización de instalaciones</li>
                                    <li>Planes de mantenimiento</li>
                                    <li>Capacitación técnica</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-6">
                    <div class="glass card-lg" style="height: 100%;">
                        <div class="d-flex align-items-start" style="gap: 1.5rem;">
                            <div class="feature-icon" style="min-width: 80px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="6" x2="12" y2="12"></line>
                                    <line x1="16.24" y1="16.24" x2="12" y2="12"></line>
                                </svg>
                            </div>
                            <div>
                                <h3>Soporte 24/7</h3>
                                <p class="text-muted">
                                    Atención continua para resolver emergencias y consultas urgentes. 
                                    Nuestro equipo está disponible cuando más lo necesitas.
                                </p>
                                <ul style="color: var(--text-muted); margin-top: 1rem;">
                                    <li>Atención telefónica 24/7</li>
                                    <li>Chat en línea</li>
                                    <li>Soporte técnico remoto</li>
                                    <li>Respuesta garantizada</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas -->
    <section style="padding: 4rem 0;">
        <div class="container">
            <div class="row">
                <div class="col col-3">
                    <div class="glass text-center card-lg">
                        <div style="font-size: 3rem; font-weight: 800; color: var(--secondary-blue); margin-bottom: 0.5rem;">
                            500+
                        </div>
                        <h4>Certificados Emitidos</h4>
                        <p class="text-muted">Certificaciones realizadas con éxito</p>
                    </div>
                </div>
                <div class="col col-3">
                    <div class="glass text-center card-lg">
                        <div style="font-size: 3rem; font-weight: 800; color: var(--success); margin-bottom: 0.5rem;">
                            150+
                        </div>
                        <h4>Clientes Satisfechos</h4>
                        <p class="text-muted">Empresas que confían en nosotros</p>
                    </div>
                </div>
                <div class="col col-3">
                    <div class="glass text-center card-lg">
                        <div style="font-size: 3rem; font-weight: 800; color: var(--warning); margin-bottom: 0.5rem;">
                            99.8%
                        </div>
                        <h4>Confiabilidad</h4>
                        <p class="text-muted">Disponibilidad del sistema</p>
                    </div>
                </div>
                <div class="col col-3">
                    <div class="glass text-center card-lg">
                        <div style="font-size: 3rem; font-weight: 800; color: var(--info); margin-bottom: 0.5rem;">
                            5+
                        </div>
                        <h4>Años de Experiencia</h4>
                        <p class="text-muted">Liderando el mercado</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" style="padding: 6rem 0; background: linear-gradient(135deg, rgba(42, 47, 108, 0.1), rgba(92, 102, 204, 0.1));">
        <div class="container">
            <div class="text-center" style="margin-bottom: 4rem;">
                <h2>Contáctanos</h2>
                <p class="lead">¿Tienes dudas? Estamos aquí para ayudarte</p>
            </div>
            
            <div class="row">
                <div class="col col-6">
                    <div class="glass card-lg">
                        <h3>Información de Contacto</h3>
                        <div style="margin-top: 2rem;">
                            <div class="d-flex align-items-center" style="margin-bottom: 1.5rem; gap: 1rem;">
                                <div class="feature-icon" style="width: 50px; height: 50px; min-width: 50px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <h5 style="margin-bottom: 0.25rem;">Dirección</h5>
                                    <p class="text-muted" style="margin: 0;">Av. Principal 123, Lima, Perú</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center" style="margin-bottom: 1.5rem; gap: 1rem;">
                                <div class="feature-icon" style="width: 50px; height: 50px; min-width: 50px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h5 style="margin-bottom: 0.25rem;">Teléfono</h5>
                                    <p class="text-muted" style="margin: 0;">+51 999 888 777</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center" style="margin-bottom: 1.5rem; gap: 1rem;">
                                <div class="feature-icon" style="width: 50px; height: 50px; min-width: 50px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <h5 style="margin-bottom: 0.25rem;">Email</h5>
                                    <p class="text-muted" style="margin: 0;">contacto@electrotec.com</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center" style="gap: 1rem;">
                                <div class="feature-icon" style="width: 50px; height: 50px; min-width: 50px;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12,6 12,12 16,14"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <h5 style="margin-bottom: 0.25rem;">Horario</h5>
                                    <p class="text-muted" style="margin: 0;">Lun - Vie: 8:00 AM - 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-6">
                    <div class="glass card-lg">
                        <h3>Envíanos un Mensaje</h3>
                        <form style="margin-top: 2rem;">
                            <div class="form-group">
                                <label class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" placeholder="Tu nombre completo" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="tu@email.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Asunto</label>
                                <select class="form-control form-select" required>
                                    <option value="">Selecciona un asunto</option>
                                    <option value="certificacion">Consulta sobre Certificación</option>
                                    <option value="soporte">Soporte Técnico</option>
                                    <option value="comercial">Información Comercial</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Mensaje</label>
                                <textarea class="form-control" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                                </svg>
                                Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: linear-gradient(135deg, rgba(42, 47, 108, 0.2), rgba(15, 18, 41, 0.2)); padding: 3rem 0 1rem; border-top: 1px solid var(--border-glass-subtle);">
        <div class="container">
            <div class="row">
                <div class="col col-4">
                    <div class="brand" style="margin-bottom: 1.5rem;">
                        <div class="brand-logo">
                            <!-- AQUÍ DEBERÁS COLOCAR TU LOGO -->
                            <span style="font-size: 1.5rem; font-weight: 800;">E</span>
                        </div>
                        <div>
                            <div class="brand-title">ELECTROTEC</div>
                            <div class="brand-subtitle">Certificación Eléctrica</div>
                        </div>
                    </div>
                    <p class="text-muted">
                        Líderes en certificación eléctrica con tecnología de vanguardia y compromiso con la excelencia.
                    </p>
                </div>
                
                <div class="col col-2">
                    <h5 style="margin-bottom: 1rem;">Servicios</h5>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="#servicios" class="text-muted" style="text-decoration: none;">Certificación</a>
                        <a href="#servicios" class="text-muted" style="text-decoration: none;">Inspección</a>
                        <a href="#servicios" class="text-muted" style="text-decoration: none;">Consultoría</a>
                        <a href="#servicios" class="text-muted" style="text-decoration: none;">Soporte</a>
                    </div>
                </div>
                
                <div class="col col-2">
                    <h5 style="margin-bottom: 1rem;">Empresa</h5>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="#inicio" class="text-muted" style="text-decoration: none;">Inicio</a>
                        <a href="#caracteristicas" class="text-muted" style="text-decoration: none;">Características</a>
                        <a href="#contacto" class="text-muted" style="text-decoration: none;">Contacto</a>
                        <a href="index.php" class="text-muted" style="text-decoration: none;">Sistema</a>
                    </div>
                </div>
                
                <div class="col col-4">
                    <h5 style="margin-bottom: 1rem;">Mantente Conectado</h5>
                    <p class="text-muted" style="margin-bottom: 1rem;">
                        Suscríbete a nuestro boletín para recibir actualizaciones y noticias.
                    </p>
                    <div class="d-flex" style="gap: 0.5rem;">
                        <input type="email" class="form-control" placeholder="tu@email.com" style="flex: 1;">
                        <button class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid var(--border-glass-subtle); margin-top: 2rem; padding-top: 1rem; text-align: center;">
                <p class="text-muted" style="margin: 0;">
                    © 2025 ELECTROTEC. Todos los derechos reservados. | Diseñado con 
                    <span style="color: var(--error);">❤</span> para la excelencia técnica.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Smooth scrolling para los enlaces de navegación
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.35)';
                navbar.style.backdropFilter = 'blur(16px)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.25)';
                navbar.style.backdropFilter = 'blur(12px)';
            }
        });

        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        // Show mobile menu button on small screens
        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                mobileMenuBtn.classList.remove('d-none');
                document.querySelector('.navbar .d-flex:last-child > .d-flex').classList.add('d-none');
            } else {
                mobileMenuBtn.classList.add('d-none');
                mobileMenu.classList.add('d-none');
                document.querySelector('.navbar .d-flex:last-child > .d-flex').classList.remove('d-none');
            }
        }

        // Toggle mobile menu
        mobileMenuBtn?.addEventListener('click', function() {
            mobileMenu.classList.toggle('d-none');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('d-none');
            });
        });

        // Check screen size on load and resize
        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);

        // Contact form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Simple form validation and submission simulation
            const formData = new FormData(this);
            
            // Show success message (you can implement actual form submission here)
            alert('¡Gracias por tu mensaje! Nos pondremos en contacto contigo pronto.');
            
            // Reset form
            this.reset();
        });

        // Add active class to navigation items based on scroll position
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (window.scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>

    <style>
        /* Estilos específicos adicionales para la landing page */
        .navbar {
            transition: all 0.3s ease;
            padding: 1rem 0;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color var(--transition-fast);
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--text-primary);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary-blue);
            transition: width var(--transition-fast);
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        /* Mobile menu styles */
        .mobile-menu-btn {
            display: flex;
            flex-direction: column;
            gap: 4px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
        }

        .mobile-menu-btn span {
            width: 25px;
            height: 3px;
            background: var(--text-primary);
            border-radius: 2px;
            transition: all var(--transition-fast);
        }

        .mobile-menu {
            position: fixed;
            top: 80px;
            left: 0;
            right: 0;
            z-index: 99;
            margin: 1rem;
            border-radius: var(--radius-lg);
        }

        .mobile-menu-content {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mobile-nav-link {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-glass-subtle);
        }

        .mobile-nav-link:last-of-type {
            border-bottom: none;
        }

        /* Hero animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content > * {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }

        .hero-content > *:nth-child(1) { animation-delay: 0.2s; }
        .hero-content > *:nth-child(2) { animation-delay: 0.4s; }
        .hero-content > *:nth-child(3) { animation-delay: 0.6s; }

        /* Feature card hover effects */
        .feature-card {
            transition: all var(--transition-normal);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 24px rgba(42, 47, 108, 0.4);
        }

        /* Form styles */
        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(42, 47, 108, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary-blue);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-blue);
        }
    </style>
</body>
</html>
