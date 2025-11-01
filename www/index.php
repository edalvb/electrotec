<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Calibración y Mantenimiento de Equipos Topográficos</title>
    <meta name="description" content="ELECTROTEC Consulting SAC - Servicios especializados en calibración y mantenimiento de equipos topográficos: Estaciones Totales, Teodolitos y Niveles Automáticos. Mantenimiento preventivo y correctivo.">
    <meta name="keywords" content="calibración estación total, mantenimiento teodolito, calibración nivel automático, equipos topográficos, servicios topografía, Electrotec Consulting">
    
    <link rel="icon" type="image/x-icon" href="./assets/images/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon-16x16.png">

    <!-- CSS Global -->
    <link href="assets/css/global.css" rel="stylesheet">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://electrotec.com/">
    <meta property="og:title" content="ELECTROTEC | Calibración y Mantenimiento de Equipos Topográficos">
    <meta property="og:description" content="Servicios especializados en calibración y mantenimiento de equipos topográficos: Estaciones Totales, Teodolitos y Niveles Automáticos.">
    <meta property="og:image" content="https://electrotec.com/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://electrotec.com/">
    <meta property="twitter:title" content="ELECTROTEC | Calibración y Mantenimiento de Equipos Topográficos">
    <meta property="twitter:description" content="Servicios especializados en calibración y mantenimiento de equipos topográficos: Estaciones Totales, Teodolitos y Niveles Automáticos.">
    <meta property="twitter:image" content="https://electrotec.com/og-image.jpg">
    
    <style>
        /* Estilos personalizados adicionales para el rediseño */
        
        /* Hero Section Mejorado */
        .hero-redesigned {
            min-height: 90vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 8rem 0 4rem;
        }
        
        .hero-redesigned::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(2, 157, 228, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(3, 103, 154, 0.15) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: rgba(2, 157, 228, 0.1);
            border: 1px solid rgba(2, 157, 228, 0.3);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--primary-blue);
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        
        .hero-badge svg {
            width: 16px;
            height: 16px;
        }
        
        .hero-title-redesigned {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }
        
        .hero-gradient-text {
            background: linear-gradient(135deg, #B3E5FC 0%, #4FC3F7 50%, #029DE4 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }
        
        .hero-subtitle-redesigned {
            font-size: clamp(1.125rem, 2vw, 1.375rem);
            line-height: 1.6;
            color: var(--text-muted);
            max-width: 700px;
            margin: 0 auto 2.5rem;
        }
        
        .hero-cta-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .btn-large {
            padding: 1rem 2.5rem;
            font-size: 1.125rem;
            font-weight: 600;
            border-radius: var(--radius-lg);
            transition: all var(--transition-normal);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-large:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary-redesigned {
            background: linear-gradient(135deg, #029DE4 0%, #03679A 100%);
            color: white;
            border: none;
        }
        
        .btn-primary-redesigned:hover {
            background: linear-gradient(135deg, #0388c4 0%, #025f7a 100%);
        }
        
        .btn-outline-redesigned {
            background: transparent;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
        }
        
        .btn-outline-redesigned:hover {
            background: var(--primary-blue);
            color: white;
        }
        
        /* Cards Mejoradas */
        .equipment-card {
            position: relative;
            height: 100%;
            padding: 2.5rem 2rem;
            border-radius: var(--radius-xl);
            transition: all var(--transition-normal);
            cursor: pointer;
            overflow: hidden;
        }
        
        .equipment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #029DE4 0%, #03679A 100%);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform var(--transition-normal);
        }
        
        .equipment-card:hover::before {
            transform: scaleX(1);
        }
        
        .equipment-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(2, 157, 228, 0.2);
        }
        
        .equipment-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, rgba(2, 157, 228, 0.1), rgba(3, 103, 154, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-normal);
        }
        
        .equipment-card:hover .equipment-icon {
            background: linear-gradient(135deg, #029DE4, #03679A);
            transform: rotate(5deg) scale(1.1);
        }
        
        .equipment-icon svg {
            transition: all var(--transition-normal);
        }
        
        .equipment-card:hover .equipment-icon svg {
            stroke: white;
        }
        
        .equipment-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        
        /* Section Headers Mejorados */
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }
        
        .section-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(2, 157, 228, 0.1);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .section-title {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .section-description {
            font-size: 1.125rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Service Detail Cards Mejoradas */
        .service-detail-card {
            position: relative;
            padding: 2.5rem;
            border-radius: var(--radius-xl);
            margin-bottom: 2rem;
            transition: all var(--transition-normal);
        }
        
        .service-detail-card:hover {
            transform: translateX(8px);
        }
        
        .service-type-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, #029DE4, #03679A);
            color: white;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }
        
        .service-type-badge svg {
            width: 18px;
            height: 18px;
        }
        
        .service-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .service-list li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1rem;
            line-height: 1.8;
            color: var(--text-muted);
        }
        
        .service-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 0;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #029DE4, #03679A);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
        }
        
        /* Stats Section Mejorada */
        .stat-card {
            position: relative;
            text-align: center;
            padding: 2.5rem 2rem;
            border-radius: var(--radius-xl);
            transition: all var(--transition-normal);
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(2, 157, 228, 0.1) 0%, transparent 70%);
            transform: scale(0);
            transition: transform var(--transition-normal);
        }
        
        .stat-card:hover::before {
            transform: scale(1);
        }
        
        .stat-number {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, #029DE4, #03679A);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-label {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        
        /* Contact Section Mejorada */
        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            background: rgba(2, 157, 228, 0.05);
            margin-bottom: 1.5rem;
            transition: all var(--transition-fast);
        }
        
        .contact-info-item:hover {
            background: rgba(2, 157, 228, 0.1);
            transform: translateX(8px);
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            min-width: 50px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #029DE4, #03679A);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .contact-icon svg {
            stroke: white;
        }
        
        .form-control-redesigned {
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            border: 2px solid rgba(2, 157, 228, 0.2);
            background: rgba(255, 255, 255, 0.05);
            transition: all var(--transition-fast);
        }
        
        .form-control-redesigned:focus {
            border-color: var(--primary-blue);
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        /* Service Type Cards */
        .service-type-card {
            position: relative;
            padding: 2.5rem 2rem;
            border-radius: var(--radius-xl);
            transition: all var(--transition-normal);
            height: 100%;
        }
        
        .service-type-card:hover {
            transform: translateY(-8px);
        }
        
        .service-type-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, rgba(2, 157, 228, 0.1), rgba(3, 103, 154, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all var(--transition-normal);
        }
        
        .service-type-card:hover .service-type-icon {
            background: linear-gradient(135deg, #029DE4, #03679A);
            transform: rotate(-5deg) scale(1.1);
        }
        
        .service-type-card:hover .service-type-icon svg {
            stroke: white;
        }
        
        /* Footer Mejorado */
        .footer-redesigned {
            background: linear-gradient(180deg, rgba(3, 103, 154, 0.1) 0%, rgba(15, 18, 41, 0.2) 100%);
            border-top: 1px solid var(--border-glass-subtle);
            padding: 4rem 0 2rem;
        }
        
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .footer-logo {
            width: 50px;
            height: 50px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #029DE4, #03679A);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
        }
        
        .footer-links a {
            display: block;
            padding: 0.5rem 0;
            color: var(--text-muted);
            text-decoration: none;
            transition: all var(--transition-fast);
        }
        
        .footer-links a:hover {
            color: var(--primary-blue);
            padding-left: 0.5rem;
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
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
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        
        /* Map Container */
        .map-container {
            position: relative;
            width: 100%;
            height: 450px;
            border-radius: var(--radius-xl);
            overflow: hidden;
            transition: all var(--transition-normal);
        }
        
        .map-container:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(2, 157, 228, 0.2);
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
            filter: grayscale(0.1);
            transition: filter var(--transition-normal);
        }
        
        .map-container:hover iframe {
            filter: grayscale(0);
        }
        
        /* Facebook Container */
        .facebook-container {
            position: relative;
            width: 100%;
            min-height: 240px;
            background: rgba(2, 157, 228, 0.03);
            border-radius: var(--radius-md);
            padding: 0.5rem;
            overflow: hidden;
        }
        
        .facebook-container .fb-page {
            width: 100%;
        }
        
        .facebook-container iframe {
            border-radius: var(--radius-md);
        }
        
        /* ========================================
           RESPONSIVE DESIGN - MOBILE FIRST
           ======================================== */
        
        /* Tablets en portrait y móviles en landscape (768px - 1024px) */
        @media (max-width: 1024px) {
            .container {
                padding: 0 1.5rem;
            }
            
            .section-header {
                margin-bottom: 3rem;
            }
            
            .section-title {
                font-size: clamp(1.75rem, 5vw, 2.5rem);
            }
            
            /* Grid adjustments */
            .col-4, .col-3, .col-6 {
                flex: 0 0 50%;
                max-width: 50%;
                margin-bottom: 2rem;
            }
            
            .col-2 {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            /* Equipment cards */
            .equipment-card {
                padding: 2rem 1.5rem;
            }
            
            /* Service cards */
            .service-type-card {
                padding: 2rem 1.5rem;
            }
            
            /* Stats */
            .stat-number {
                font-size: 3.5rem;
            }
        }
        
        /* Móviles en landscape y tablets pequeñas (768px - 991px) */
        @media (max-width: 991px) {
            /* Navigation */
            .navbar .d-flex:nth-child(2) {
                display: none !important;
            }
            
            .mobile-menu-btn {
                display: flex !important;
            }
            
            /* Hero */
            .hero-redesigned {
                min-height: 80vh;
                padding: 7rem 0 4rem;
            }
            
            .hero-title-redesigned {
                font-size: clamp(2rem, 7vw, 3rem);
            }
            
            .hero-subtitle-redesigned {
                font-size: 1.125rem;
            }
            
            .hero-cta-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .btn-large {
                width: 100%;
                justify-content: center;
            }
            
            /* Service detail cards */
            .service-detail-card {
                padding: 2rem 1.5rem;
            }
            
            .service-detail-card .col-6 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        /* Móviles en portrait (hasta 767px) */
        @media (max-width: 767px) {
            /* Container */
            .container {
                padding: 0 1rem;
            }
            
            /* Hero */
            .hero-redesigned {
                min-height: 70vh;
                padding: 6rem 0 3rem;
            }
            
            .hero-badge {
                font-size: 0.8rem;
                padding: 0.4rem 1rem;
                margin-bottom: 1.5rem;
            }
            
            .hero-title-redesigned {
                font-size: 1.875rem;
                margin-bottom: 1.25rem;
            }
            
            .hero-subtitle-redesigned {
                font-size: 1rem;
                margin-bottom: 2rem;
            }
            
            .hero-cta-group {
                gap: 0.75rem;
            }
            
            .btn-large {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
            
            /* Sections */
            section {
                padding: 4rem 0 !important;
            }
            
            .section-header {
                margin-bottom: 2.5rem;
            }
            
            .section-badge {
                font-size: 0.75rem;
                padding: 0.4rem 0.875rem;
            }
            
            .section-title {
                font-size: 1.75rem;
                margin-bottom: 0.75rem;
            }
            
            .section-description {
                font-size: 1rem;
            }
            
            /* Grid - Todo a columna única */
            .row {
                margin: 0;
            }
            
            .col, .col-2, .col-3, .col-4, .col-6, .col-12 {
                flex: 0 0 100%;
                max-width: 100%;
                padding: 0 0.5rem;
                margin-bottom: 1.5rem;
            }
            
            /* Equipment cards */
            .equipment-card {
                padding: 2rem 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .equipment-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 1.25rem;
            }
            
            .equipment-icon svg {
                width: 30px;
                height: 30px;
            }
            
            .equipment-title {
                font-size: 1.25rem;
                margin-bottom: 0.75rem;
            }
            
            /* Service detail cards */
            .service-detail-card {
                padding: 1.5rem 1rem;
                margin-bottom: 1.5rem;
            }
            
            .service-type-badge {
                font-size: 0.75rem;
                padding: 0.4rem 1rem;
                margin-bottom: 1rem;
            }
            
            .service-type-badge svg {
                width: 14px;
                height: 14px;
            }
            
            .service-list li {
                font-size: 0.9rem;
                padding-left: 1.75rem;
                margin-bottom: 0.75rem;
            }
            
            .service-list li::before {
                width: 20px;
                height: 20px;
                font-size: 0.75rem;
            }
            
            /* Service type cards */
            .service-type-card {
                padding: 2rem 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .service-type-icon {
                width: 60px;
                height: 60px;
                margin-bottom: 1.25rem;
            }
            
            .service-type-icon svg {
                width: 30px;
                height: 30px;
            }
            
            /* Stats */
            .stat-card {
                padding: 2rem 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .stat-number {
                font-size: 2.5rem;
                margin-bottom: 0.75rem;
            }
            
            .stat-label {
                font-size: 1.125rem;
            }
            
            /* Contact section */
            .contact-info-item {
                flex-direction: row;
                text-align: left;
                padding: 1.25rem;
                margin-bottom: 1rem;
            }
            
            .contact-icon {
                width: 45px;
                height: 45px;
                min-width: 45px;
            }
            
            .contact-icon svg {
                width: 20px;
                height: 20px;
            }
            
            /* Forms */
            .form-control-redesigned {
                padding: 0.875rem 1rem;
                font-size: 1rem;
            }
            
            textarea.form-control-redesigned {
                min-height: 120px;
            }
            
            /* Map */
            .map-container {
                height: 350px;
            }
            
            .map-container:hover {
                transform: none;
                box-shadow: none;
            }
            
            /* Footer */
            .footer-redesigned {
                padding: 3rem 0 1.5rem;
            }
            
            .footer-brand {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 1.25rem;
            }
            
            .footer-logo {
                width: 45px;
                height: 45px;
                font-size: 1.25rem;
            }
            
            .footer-links a {
                padding: 0.4rem 0;
                font-size: 0.9rem;
            }
            
            /* Navbar responsive */
            .navbar {
                padding: 0.6rem 0 !important;
            }
            
            .navbar .brand-logo {
                width: 35px !important;
                height: 35px !important;
            }
            
            .navbar .brand-logo img {
                width: 35px !important;
                height: 35px !important;
            }
            
            .navbar .brand-title {
                font-size: 1.125rem !important;
            }
            
            .navbar .brand-subtitle {
                font-size: 0.65rem !important;
            }
            
            /* Facebook */
            .facebook-container {
                min-height: 200px;
            }
        }
        
        /* Móviles pequeños (hasta 480px) */
        @media (max-width: 480px) {
            /* Hero */
            .hero-redesigned {
                min-height: 65vh;
                padding: 5rem 0 2.5rem;
            }
            
            .hero-badge {
                font-size: 0.75rem;
                padding: 0.35rem 0.875rem;
            }
            
            .hero-badge svg {
                width: 14px;
                height: 14px;
            }
            
            .hero-title-redesigned {
                font-size: 1.625rem;
            }
            
            .hero-subtitle-redesigned {
                font-size: 0.95rem;
                margin-bottom: 1.75rem;
            }
            
            .btn-large {
                padding: 0.8rem 1.25rem;
                font-size: 0.95rem;
            }
            
            /* Sections */
            section {
                padding: 3rem 0 !important;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
            
            .section-description {
                font-size: 0.95rem;
            }
            
            /* Cards */
            .equipment-card,
            .service-type-card,
            .stat-card {
                padding: 1.75rem 1.25rem;
            }
            
            .service-detail-card {
                padding: 1.25rem 1rem;
            }
            
            /* Stats */
            .stat-number {
                font-size: 2.25rem;
            }
            
            .stat-label {
                font-size: 1rem;
            }
            
            /* Contact */
            .contact-info-item {
                flex-direction: column;
                text-align: center;
                align-items: center;
                padding: 1rem;
            }
            
            .contact-icon {
                margin-bottom: 0.75rem;
            }
            
            /* Forms */
            .form-group {
                margin-bottom: 1rem;
            }
            
            /* Map */
            .map-container {
                height: 300px;
            }
            
            /* Footer */
            .footer-brand {
                text-align: center;
                align-items: center;
            }
            
            .footer-brand .footer-logo {
                margin: 0 auto 0.75rem;
            }
            
            /* Mobile menu */
            .mobile-menu {
                margin: 0.5rem;
            }
            
            .mobile-menu-content {
                padding: 1.5rem;
            }
            
            /* Facebook */
            .facebook-container {
                min-height: 180px;
                padding: 0.25rem;
            }
        }
        
        /* Móviles muy pequeños (hasta 375px) */
        @media (max-width: 375px) {
            .container {
                padding: 0 0.875rem;
            }
            
            .hero-title-redesigned {
                font-size: 1.5rem;
            }
            
            .hero-subtitle-redesigned {
                font-size: 0.9rem;
            }
            
            .btn-large {
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
            }
            
            .section-title {
                font-size: 1.375rem;
            }
            
            .equipment-card,
            .service-type-card,
            .stat-card,
            .service-detail-card {
                padding: 1.5rem 1rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            /* Map */
            .map-container {
                height: 250px;
            }
            
            /* Facebook */
            .facebook-container {
                min-height: 150px;
                padding: 0.25rem;
            }
        }
        
        /* Landscape móvil (orientación horizontal) */
        @media (max-height: 500px) and (orientation: landscape) {
            .hero-redesigned {
                min-height: auto;
                padding: 5rem 0 2rem;
            }
            
            .hero-title-redesigned {
                font-size: 1.75rem;
                margin-bottom: 1rem;
            }
            
            .hero-subtitle-redesigned {
                font-size: 0.95rem;
                margin-bottom: 1.5rem;
            }
            
            .hero-badge {
                margin-bottom: 1rem;
            }
        }
        
        /* Print styles */
        @media print {
            .navbar,
            .mobile-menu,
            .mobile-menu-btn,
            .hero-cta-group,
            footer {
                display: none !important;
            }
            
            .hero-redesigned {
                min-height: auto;
                padding: 2rem 0;
            }
            
            section {
                padding: 2rem 0 !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Facebook SDK -->
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v24.0&appId=1649645399255189"></script>
    
    <!-- Navigation Header -->
    <nav class="navbar glass" style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: .8rem 0; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center" style="width: 100%;">
                <!-- Logo y Brand -->
                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                    <div class="brand-logo" style="width: 40px; height: 40px;">
                        <img src="assets/images/logo.png" alt="ELECTROTEC Logo" style="width: 40px; height: 40px;">
                    </div>
                    <div>
                        <div class="brand-title" style="line-height: 1.2; font-size: 1.25rem;">ELECTROTEC</div>
                        <div class="brand-subtitle" style="font-size: 0.7rem; line-height: 1;">Consulting SAC</div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="d-flex align-items-center" style="gap: 1.5rem;">
                    <a href="#inicio" class="nav-link">Inicio</a>
                    <a href="#equipos" class="nav-link">Equipos</a>
                    <a href="#servicios" class="nav-link">Servicios</a>
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
            <a href="#equipos" class="mobile-nav-link">Equipos</a>
            <a href="#servicios" class="mobile-nav-link">Servicios</a>
            <a href="#contacto" class="mobile-nav-link">Contacto</a>
            <a href="login.php" class="btn btn-primary btn-block">Acceder al Sistema</a>
        </div>
    </div>

    <!-- Hero Section REDISEÑADA -->
    <section id="inicio" class="hero-redesigned">
        <div class="container">
            <div class="hero-content" style="text-align: center;">
                <div class="hero-badge fade-in-up" style="animation-delay: 0.1s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>
                    Expertos en Equipos Topográficos
                </div>
                
                <h1 class="hero-title-redesigned fade-in-up" style="animation-delay: 0.2s;">
                    Calibración y Mantenimiento
                    <span class="hero-gradient-text">de Equipos Topográficos</span>
                </h1>
                
                <p class="hero-subtitle-redesigned fade-in-up" style="animation-delay: 0.3s;">
                    Servicios especializados en calibración y mantenimiento preventivo y correctivo para Estaciones Totales, Teodolitos y Niveles Automáticos con la más alta precisión.
                </p>
                
                <div class="hero-cta-group fade-in-up" style="animation-delay: 0.4s;">
                    <a href="#servicios" class="btn btn-large btn-primary-redesigned">
                        Ver Servicios
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: 0.5rem;">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="#contacto" class="btn btn-large btn-outline-redesigned">
                        Contáctanos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios por Tipo de Equipo REDISEÑADO -->
    <section id="equipos" style="padding: 6rem 0; background: linear-gradient(135deg, rgba(2, 157, 228, 0.03), rgba(3, 103, 154, 0.03));">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Nuestros Equipos</span>
                <h2 class="section-title">Servicios por Tipo de Equipo</h2>
                <p class="section-description">
                    Calibración y mantenimiento especializado para cada tipo de equipo topográfico con tecnología de punta
                </p>
            </div>
            
            <div class="row">
                <div class="col col-4">
                    <div class="glass equipment-card text-center">
                        <div class="equipment-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <h3 class="equipment-title">Estación Total</h3>
                        <p class="text-muted" style="margin-bottom: 2rem;">
                            Calibración completa de nivel, angular y compensadores electrónicos
                        </p>
                        <a href="#estacion-total" class="btn btn-secondary" style="width: 100%;">
                            Ver Detalles →
                        </a>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="glass equipment-card text-center">
                        <div class="equipment-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                <path d="M2 17l10 5 10-5"></path>
                                <path d="M2 12l10 5 10-5"></path>
                            </svg>
                        </div>
                        <h3 class="equipment-title">Teodolito</h3>
                        <p class="text-muted" style="margin-bottom: 2rem;">
                            Calibración angular y mantenimiento especializado de precisión
                        </p>
                        <a href="#teodolito" class="btn btn-secondary" style="width: 100%;">
                            Ver Detalles →
                        </a>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="glass equipment-card text-center">
                        <div class="equipment-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                        </div>
                        <h3 class="equipment-title">Nivel Automático</h3>
                        <p class="text-muted" style="margin-bottom: 2rem;">
                            Ajuste de nivel esférico y calibración de compensador mecánico
                        </p>
                        <a href="#nivel-automatico" class="btn btn-secondary" style="width: 100%;">
                            Ver Detalles →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ESTACIÓN TOTAL - Servicios Detallados REDISEÑADO -->
    <section id="estacion-total" style="padding: 6rem 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Estación Total</span>
                <h2 class="section-title">Servicios Especializados</h2>
                <p class="section-description">
                    Calibración completa y mantenimiento preventivo y correctivo
                </p>
            </div>
            
            <div class="row">
                <!-- Calibración -->
                <div class="col col-12">
                    <div class="glass service-detail-card">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"></path>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                            Calibración
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Ajuste de nivel tubular o nivel esférico</li>
                                    <li>Calibración angular, vertical y horizontal</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Calibración de compensador electrónico (X, Y)</li>
                                    <li>Ajuste de plomada óptica o láser</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Preventivo -->
                <div class="col col-12">
                    <div class="glass service-detail-card" style="background: linear-gradient(135deg, rgba(2, 157, 228, 0.08), rgba(3, 103, 154, 0.08));">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33"></path>
                            </svg>
                            Mantenimiento Preventivo
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Desmontaje y limpieza en general del equipo</li>
                                    <li>Limpieza de tapas laterales, puerto USB, Bluetooth</li>
                                    <li>Limpieza de limbos y sensores CCD</li>
                                    <li>Limpieza de teclado, pantalla y botones</li>
                                    <li>Lubricación de tangente vertical y horizontal</li>
                                    <li>Limpieza y lubricación de base nivelante</li>
                                    <li>Lubricación de ocular y enfoque</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Alineamiento de hilo estadimétrico</li>
                                    <li>Ajuste de plomada óptica o láser</li>
                                    <li>Ajuste de nivel tubular o nivel esférico</li>
                                    <li>Ajuste de puntero láser y EDM motor</li>
                                    <li>Calibración angular, vertical y horizontal</li>
                                    <li>Calibración de compensador electrónico (X, Y)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Correctivo -->
                <div class="col col-12">
                    <div class="glass service-detail-card">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77"></path>
                            </svg>
                            Mantenimiento Correctivo
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Reemplazo de pieza dañada</li>
                                    <li>Corrección de perpendicularidad (Vertical, Horizontal)</li>
                                    <li>Restablecimiento de fábrica del sistema</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Calibración por software de la marca</li>
                                    <li>Actualización de firmware</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TEODOLITO - Servicios Detallados -->
    <section id="teodolito" style="padding: 6rem 0; background: linear-gradient(135deg, rgba(2, 157, 228, 0.03), rgba(3, 103, 154, 0.03));">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Teodolito</span>
                <h2 class="section-title">Servicios Especializados</h2>
                <p class="section-description">
                    Calibración angular y mantenimiento de precisión
                </p>
            </div>
            
            <div class="row">
                <!-- Calibración -->
                <div class="col col-12">
                    <div class="glass service-detail-card">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"></path>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                            Calibración
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Ajuste de nivel tubular</li>
                                    <li>Calibración angular, vertical y horizontal</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Calibración de compensador electrónico (X, Y)</li>
                                    <li>Ajuste de plomada óptica o láser</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Preventivo -->
                <div class="col col-12">
                    <div class="glass service-detail-card" style="background: linear-gradient(135deg, rgba(2, 157, 228, 0.08), rgba(3, 103, 154, 0.08));">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Mantenimiento Preventivo
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Desmontaje y limpieza en general del equipo</li>
                                    <li>Limpieza de tapas laterales</li>
                                    <li>Limpieza de limbos y sensores CCD</li>
                                    <li>Limpieza de teclado, pantalla y botones</li>
                                    <li>Lubricación de tangente vertical y horizontal</li>
                                    <li>Limpieza y lubricación de base nivelante</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Lubricación de ocular y enfoque</li>
                                    <li>Alineamiento de hilo estadimétrico</li>
                                    <li>Ajuste de plomada óptica o láser</li>
                                    <li>Ajuste de nivel tubular</li>
                                    <li>Ajuste de puntero láser</li>
                                    <li>Calibración angular, vertical y horizontal</li>
                                    <li>Calibración de compensador electrónico (X, Y)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Correctivo -->
                <div class="col col-12">
                    <div class="glass service-detail-card">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6"></path>
                            </svg>
                            Mantenimiento Correctivo
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Reemplazo de pieza dañada</li>
                                    <li>Corrección de perpendicularidad (Vertical, Horizontal)</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Restablecimiento de fábrica del sistema</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NIVEL AUTOMÁTICO - Servicios Detallados -->
    <section id="nivel-automatico" style="padding: 6rem 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Nivel Automático</span>
                <h2 class="section-title">Servicios Especializados</h2>
                <p class="section-description">
                    Ajuste y calibración de máxima precisión
                </p>
            </div>
            
            <div class="row">
                <!-- Calibración -->
                <div class="col col-12">
                    <div class="glass service-detail-card">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"></path>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                            Calibración
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Ajuste de nivel esférico</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Calibración de compensador mecánico</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Preventivo -->
                <div class="col col-12">
                    <div class="glass service-detail-card" style="background: linear-gradient(135deg, rgba(2, 157, 228, 0.08), rgba(3, 103, 154, 0.08));">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            Mantenimiento Preventivo
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Desmontaje y limpieza en general del equipo</li>
                                    <li>Limpieza de anillo de grados</li>
                                    <li>Lubricación de tangente horizontal</li>
                                    <li>Limpieza y lubricación de base nivelante</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Lubricación de ocular y enfoque</li>
                                    <li>Alineamiento de hilo estadimétrico</li>
                                    <li>Ajuste de nivel esférico</li>
                                    <li>Calibración de compensador mecánico</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Correctivo -->
                <div class="col col-12">
                    <div class="glass service-detail-card">
                        <span class="service-type-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6"></path>
                            </svg>
                            Mantenimiento Correctivo
                        </span>
                        <div class="row">
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Reemplazo de pieza dañada</li>
                                </ul>
                            </div>
                            <div class="col col-6">
                                <ul class="service-list">
                                    <li>Corrección de compensador mecánico</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tipos de Servicios REDISEÑADO -->
    <section id="servicios" style="padding: 6rem 0; background: linear-gradient(135deg, rgba(2, 157, 228, 0.03), rgba(3, 103, 154, 0.03));">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Tipos de Servicios</span>
                <h2 class="section-title">Soluciones Completas</h2>
                <p class="section-description">
                    Mantenimiento integral para equipos topográficos en óptimas condiciones
                </p>
            </div>
            
            <div class="row">
                <div class="col col-4">
                    <div class="glass service-type-card text-center">
                        <div class="service-type-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 12l2 2 4-4"></path>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                        </div>
                        <h3 class="equipment-title">Calibración</h3>
                        <p class="text-muted" style="margin-bottom: 1.5rem;">
                            Ajustes de precisión para garantizar mediciones exactas y confiables
                        </p>
                        <ul class="service-list" style="text-align: left; margin-top: 1.5rem;">
                            <li>Ajuste de nivel tubular y esférico</li>
                            <li>Calibración angular</li>
                            <li>Calibración de compensadores</li>
                            <li>Ajuste de plomada óptica</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="glass service-type-card text-center" style="background: linear-gradient(135deg, rgba(2, 157, 228, 0.08), rgba(3, 103, 154, 0.08));">
                        <div class="service-type-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83"></path>
                            </svg>
                        </div>
                        <h3 class="equipment-title">Mantenimiento Preventivo</h3>
                        <p class="text-muted" style="margin-bottom: 1.5rem;">
                            Servicios programados para prevenir fallas y prolongar vida útil
                        </p>
                        <ul class="service-list" style="text-align: left; margin-top: 1.5rem;">
                            <li>Desmontaje y limpieza general</li>
                            <li>Lubricación de componentes</li>
                            <li>Limpieza de sensores y óptica</li>
                            <li>Ajustes preventivos</li>
                        </ul>
                    </div>
                </div>
                
                <div class="col col-4">
                    <div class="glass service-type-card text-center">
                        <div class="service-type-icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0"></path>
                            </svg>
                        </div>
                        <h3 class="equipment-title">Mantenimiento Correctivo</h3>
                        <p class="text-muted" style="margin-bottom: 1.5rem;">
                            Reparación especializada para restaurar funcionamiento óptimo
                        </p>
                        <ul class="service-list" style="text-align: left; margin-top: 1.5rem;">
                            <li>Reemplazo de piezas dañadas</li>
                            <li>Corrección de perpendicularidad</li>
                            <li>Restablecimiento de fábrica</li>
                            <li>Actualización de firmware</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ¿Por qué elegirnos? REDISEÑADO -->
    <section style="padding: 6rem 0;">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Por qué elegirnos</span>
                <h2 class="section-title">Experiencia y Calidad</h2>
                <p class="section-description">
                    Compromiso con la excelencia en cada servicio
                </p>
            </div>
            
            <div class="row">
                <div class="col col-3">
                    <div class="glass stat-card">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Equipos Calibrados</div>
                        <p class="text-muted">Calibraciones realizadas con éxito</p>
                    </div>
                </div>
                
                <div class="col col-3">
                    <div class="glass stat-card">
                        <div class="stat-number">150+</div>
                        <div class="stat-label">Clientes Satisfechos</div>
                        <p class="text-muted">Empresas que confían en nosotros</p>
                    </div>
                </div>
                
                <div class="col col-3">
                    <div class="glass stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Atención al Cliente</div>
                        <p class="text-muted">Soporte cuando lo necesites</p>
                    </div>
                </div>
                
                <div class="col col-3">
                    <div class="glass stat-card">
                        <div class="stat-number">5+</div>
                        <div class="stat-label">Años de Experiencia</div>
                        <p class="text-muted">Especialistas en topografía</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto REDISEÑADO -->
    <section id="contacto" style="padding: 6rem 0; background: linear-gradient(135deg, rgba(3, 103, 154, 0.05), rgba(2, 157, 228, 0.05));">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Contacto</span>
                <h2 class="section-title">¿Tienes Dudas?</h2>
                <p class="section-description">
                    Estamos aquí para ayudarte en lo que necesites
                </p>
            </div>
            
            <div class="row">
                <div class="col col-6">
                    <div class="glass card-lg">
                        <h3 style="margin-bottom: 2rem;">Información de Contacto</h3>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <h5 style="margin-bottom: 0.5rem; color: var(--text-primary);">Dirección</h5>
                                <p class="text-muted" style="margin: 0;">Lima, Perú</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 style="margin-bottom: 0.5rem; color: var(--text-primary);">Teléfono / WhatsApp</h5>
                                <p class="text-muted" style="margin: 0;">+51 930 321 872</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h5 style="margin-bottom: 0.5rem; color: var(--text-primary);">Email</h5>
                                <p class="text-muted" style="margin: 0;">contacto@electrotec.com</p>
                            </div>
                        </div>
                        
                        <div class="contact-info-item" style="margin-bottom: 0;">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12,6 12,12 16,14"></polyline>
                                </svg>
                            </div>
                            <div>
                                <h5 style="margin-bottom: 0.5rem; color: var(--text-primary);">Horario</h5>
                                <p class="text-muted" style="margin: 0;">Lun - Vie: 8:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col col-6">
                    <div class="glass card-lg">
                        <h3 style="margin-bottom: 2rem;">Envíanos un Mensaje</h3>
                        <form id="contactForm">
                            <div class="form-group">
                                <label class="form-label" for="contactName">Nombre Completo</label>
                                <input type="text" id="contactName" name="name" class="form-control form-control-redesigned" placeholder="Tu nombre completo" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="contactEmail">Email</label>
                                <input type="email" id="contactEmail" name="email" class="form-control form-control-redesigned" placeholder="tu@email.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="contactSubject">Asunto</label>
                                <select id="contactSubject" name="subject" class="form-control form-control-redesigned form-select" required>
                                    <option value="">Selecciona un asunto</option>
                                    <option value="certificacion">Consulta sobre Certificación</option>
                                    <option value="soporte">Soporte</option>
                                    <option value="comercial">Información Comercial</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="contactMessage">Mensaje</label>
                                <textarea id="contactMessage" name="message" class="form-control form-control-redesigned" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-large btn-primary-redesigned" style="width: 100%;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                    <polygon points="22,2 15,22 11,13 2,9 22,2"></polygon>
                                </svg>
                                Enviar Mensaje
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Mapa de Ubicación -->
            <div class="row" style="margin-top: 4rem;">
                <div class="col col-12">
                    <div class="section-header" style="margin-bottom: 2rem;">
                        <h3 class="section-title" style="font-size: 1.75rem;">Encuéntranos</h3>
                        <p class="section-description">
                            Visítanos en nuestra ubicación física
                        </p>
                    </div>
                    
                    <div class="glass card-lg" style="padding: 0; overflow: hidden;">
                        <div class="map-container">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d810.6819284350479!2d-77.01403402043186!3d-12.033498823347207!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105cf1d622839b9%3A0x4977bd72531a0d0d!2sElectrotec%20Consulting%20SAC!5e1!3m2!1ses-419!2spy!4v1761958835412!5m2!1ses-419!2spy" 
                                width="100%" 
                                height="450" 
                                style="border:0; display: block;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Ubicación de Electrotec Consulting SAC">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer REDISEÑADO -->
    <footer class="footer-redesigned">
        <div class="container">
            <div class="row" style="margin-bottom: 3rem;">
                <div class="col col-4">
                    <div class="footer-brand">
                        <div class="footer-logo">E</div>
                        <div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">ELECTROTEC</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">Consulting SAC</div>
                        </div>
                    </div>
                    <p class="text-muted">
                        Especialistas en calibración y mantenimiento de equipos topográficos con tecnología de vanguardia y compromiso con la excelencia.
                    </p>
                </div>
                
                <div class="col col-2">
                    <h5 style="margin-bottom: 1.5rem; color: var(--text-primary);">Servicios</h5>
                    <div class="footer-links">
                        <a href="#servicios">Calibración</a>
                        <a href="#servicios">Mant. Preventivo</a>
                        <a href="#servicios">Mant. Correctivo</a>
                        <a href="#contacto">Consultoría</a>
                    </div>
                </div>
                
                <div class="col col-2">
                    <h5 style="margin-bottom: 1.5rem; color: var(--text-primary);">Equipos</h5>
                    <div class="footer-links">
                        <a href="#estacion-total">Estación Total</a>
                        <a href="#teodolito">Teodolito</a>
                        <a href="#nivel-automatico">Nivel Automático</a>
                        <a href="login.php">Sistema</a>
                    </div>
                </div>
                
                <div class="col col-4">
                    <h5 style="margin-bottom: 1.5rem; color: var(--text-primary);">Síguenos en Facebook</h5>
                    <p class="text-muted" style="margin-bottom: 1rem;">
                        Mantente al día con nuestras últimas noticias y servicios
                    </p>
                    <div class="facebook-container">
                        <div class="fb-page" 
                            data-href="https://www.facebook.com/ElectrotecConsulting" 
                            data-tabs="timeline" 
                            data-width="" 
                            data-height="240" 
                            data-small-header="true" 
                            data-adapt-container-width="true" 
                            data-hide-cover="false" 
                            data-show-facepile="true">
                            <blockquote cite="https://www.facebook.com/ElectrotecConsulting" class="fb-xfbml-parse-ignore">
                                <a href="https://www.facebook.com/ElectrotecConsulting">Electrotec Consulting SAC</a>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="border-top: 1px solid var(--border-glass-subtle); padding-top: 2rem; text-align: center;">
                <p class="text-muted" style="margin: 0; font-size: 0.875rem;">
                    © 2025 ELECTROTEC Consulting SAC. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobileMenu').classList.add('d-none');
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.4)';
                navbar.style.backdropFilter = 'blur(20px)';
                navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.25)';
                navbar.style.backdropFilter = 'blur(12px)';
                navbar.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.05)';
            }
        });

        // Mobile menu
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        function checkScreenSize() {
            if (window.innerWidth <= 768) {
                mobileMenuBtn.classList.remove('d-none');
                document.querySelector('.navbar .d-flex:nth-child(2)').classList.add('d-none');
            } else {
                mobileMenuBtn.classList.add('d-none');
                mobileMenu.classList.add('d-none');
                document.querySelector('.navbar .d-flex:nth-child(2)').classList.remove('d-none');
            }
        }

        mobileMenuBtn?.addEventListener('click', function() {
            mobileMenu.classList.toggle('d-none');
        });

        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);

        // Contact form -> WhatsApp
        document.getElementById('contactForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('contactName')?.value?.trim() || '';
            const email = document.getElementById('contactEmail')?.value?.trim() || '';
            const subject = document.getElementById('contactSubject')?.value || '';
            const message = document.getElementById('contactMessage')?.value?.trim() || '';

            const subjectMap = {
                certificacion: 'Consulta sobre Certificación',
                soporte: 'Soporte',
                comercial: 'Información Comercial',
                otro: 'Otro'
            };

            const subjectLabel = subjectMap[subject] || 'Contacto desde web';
            const raw = `Hola ELECTROTEC\n\nAsunto: ${subjectLabel}\nNombre: ${name}\nEmail: ${email}\nMensaje:\n${message}`;
            const text = encodeURIComponent(raw);
            const phone = '51930321872';

            const isMobile = /Android|iPhone|iPad|iPod|Windows Phone/i.test(navigator.userAgent);
            const webUrl = `https://wa.me/${phone}?text=${text}`;
            const appUrl = `whatsapp://send?phone=${phone}&text=${text}`;

            if (isMobile) {
                window.location.href = appUrl;
                setTimeout(() => { window.location.href = webUrl; }, 800);
            } else {
                window.open(webUrl, '_blank', 'noopener');
            }

            this.reset();
        });

        // Active nav link
        window.addEventListener('scroll', function() {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
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

        // Fade in elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.equipment-card, .stat-card, .service-type-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease-out';
            observer.observe(el);
        });
    </script>
</body>
</html>
