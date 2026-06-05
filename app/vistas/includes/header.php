<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $datos['titulo'] ?? 'OPTICCOM'; ?> - OPTICCOM</title>

    <script>
        (function () {
            const savedTheme = localStorage.getItem('opticcom_theme') || 
                              (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@300;400;700;900&family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="<?php echo RUTA_URL; ?>/css/style.css?v=3.0">

    <style>
        /* Estilos base del Navbar */
        body {
            font-family: 'Inter', sans-serif !important;
        }
        .navbar-opticcom {
            background: var(--nav-bg, rgba(255,255,255,0.85));
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--glass-border, rgba(0,0,0,0.1));
            padding-top: 14px;
            padding-bottom: 14px;
            transition: .25s ease;
            z-index: 9999;
        }
        .brand-logo {
            height: 48px;
            transition: .2s ease;
        }
        .brand-logo:hover {
            transform: scale(1.03);
        }
        .navbar-nav { gap: 10px; }
        
        .nav-link-modern {
            color: var(--bs-body-color) !important;
            font-weight: 600;
            font-size: .97rem;
            padding: 12px 18px !important;
            border-radius: 14px;
            transition: .25s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-link-modern i { font-size: .95rem; }
        .nav-link-modern:hover {
            background: var(--bs-tertiary-bg);
            color: var(--bs-primary) !important;
            transform: translateY(-1px);
        }
        .nav-active {
            background: rgba(37, 99, 235, 0.1);
            border: 1px solid rgba(37, 99, 235, 0.2);
            color: var(--bs-primary) !important;
        }
        
        .user-menu {
            background: var(--bs-tertiary-bg);
            border: 1px solid var(--glass-border, rgba(0,0,0,0.1));
            border-radius: 18px;
            padding: 8px 14px !important;
            transition: .25s ease;
        }
        .user-menu:hover { background: var(--bs-secondary-bg); }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg,#2563eb,#7c3aed);
            color: white;
            font-size: .95rem;
            box-shadow: 0 0 20px rgba(47,107,255,.25);
        }
        .user-name {
            font-weight: 700;
            color: var(--bs-body-color);
            font-size: .95rem;
        }
        
        .dropdown-menu {
            background: var(--bs-body-bg) !important;
            border: 1px solid var(--glass-border, rgba(0,0,0,0.1)) !important;
            border-radius: 18px !important;
            overflow: hidden;
            min-width: 240px;
            box-shadow: 0 20px 40px rgba(0,0,0,.15);
        }
        .dropdown-header-modern {
            padding: 18px;
            border-bottom: 1px solid var(--glass-border, rgba(0,0,0,0.1));
        }
        .dropdown-user { color: var(--bs-body-color); font-weight: 700; }
        .dropdown-role { color: var(--bs-secondary-color); font-size: .85rem; }
        .dropdown-item {
            color: var(--bs-body-color) !important;
            padding: 14px 18px !important;
            transition: .2s ease;
        }
        .dropdown-item:hover {
            background: var(--bs-tertiary-bg) !important;
            color: var(--bs-primary) !important;
        }
        .dropdown-logout { color: #dc3545 !important; font-weight: 700; }
        
        .btn-whatsapp-modern {
            background: rgba(34,197,94,.15);
            border: 1px solid rgba(34,197,94,.30);
            color: #198754 !important;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 700;
            transition: .25s ease;
        }
        [data-bs-theme="dark"] .btn-whatsapp-modern { color: #4ade80 !important; }
        .btn-whatsapp-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(34,197,94,.18);
        }
        
        .btn-portal-modern {
            background: linear-gradient(135deg,#2563eb,#4f46e5);
            border: none;
            color: white !important;
            border-radius: 50px;
            padding: 10px 22px;
            font-weight: 700;
            transition: .25s ease;
            box-shadow: 0 0 25px rgba(37,99,235,.25);
        }
        .btn-portal-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 35px rgba(37,99,235,.35);
        }
        
        .theme-toggle-nav-btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid var(--glass-border, rgba(0,0,0,0.1));
            background: var(--bs-tertiary-bg);
            color: var(--bs-body-color);
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .theme-toggle-nav-btn:hover { background: var(--bs-secondary-bg); }
        
        @media(max-width:991px){
            .navbar-collapse {
                margin-top: 18px;
                background: var(--bs-body-bg);
                border: 1px solid var(--glass-border, rgba(0,0,0,0.1));
                border-radius: 22px;
                padding: 18px;
            }
        }
    </style>
</head>

<?php
$es_admin = (isLoggedIn() && currentRole() !== 'Cliente');
$clase_body = $es_admin ? 'bg-admin-panel' : 'bg-public';
?>

<body class="<?php echo $clase_body; ?> d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-opticcom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand me-4" href="<?php echo RUTA_URL; ?>">
            <img src="<?php echo RUTA_URL; ?>/img/logo.png" alt="OPTICCOM Logo" class="brand-logo">
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <?php if(isLoggedIn()): ?>
                <ul class="navbar-nav me-auto">
                    <?php if (currentRole() === 'Cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link nav-link-modern nav-active" href="<?php echo RUTA_URL; ?>/portal/inicio">
                                <i class="fas fa-th-large text-primary"></i> Mi Portal
                            </a>
                        </li>
                    <?php else: ?>
                        <?php if (hasRole(['Administrador'])): ?>
                            <li class="nav-item">
                                <a class="nav-link nav-link-modern <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false) ? 'nav-active' : ''; ?>" href="<?php echo RUTA_URL; ?>/dashboard">
                                    <i class="fas fa-chart-pie text-info"></i> Dashboard
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (hasRole(['Administrador','Ventas'])): ?>
                            <li class="nav-item">
                                <a class="nav-link nav-link-modern <?php echo (strpos($_SERVER['REQUEST_URI'], 'ventas') !== false) ? 'nav-active' : ''; ?>" href="<?php echo RUTA_URL; ?>/ventas">
                                    <i class="fas fa-handshake text-success"></i> Ventas
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a class="nav-link nav-link-modern <?php echo (strpos($_SERVER['REQUEST_URI'], 'clientes') !== false) ? 'nav-active' : ''; ?>" href="<?php echo RUTA_URL; ?>/clientes">
                                <i class="fas fa-users text-warning"></i> Clientes
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link nav-link-modern <?php echo (strpos($_SERVER['REQUEST_URI'], 'ordenes') !== false) ? 'nav-active' : ''; ?>" href="<?php echo RUTA_URL; ?>/ordenes">
                                <i class="fas fa-satellite-dish text-danger"></i> Seguimiento
                            </a>
                        </li>

                        <?php if (hasRole(['Administrador'])): ?>
                            <li class="nav-item">
                                <a class="nav-link nav-link-modern <?php echo (strpos($_SERVER['REQUEST_URI'], 'planes') !== false) ? 'nav-active' : ''; ?>" href="<?php echo RUTA_URL; ?>/planes">
                                    <i class="fas fa-box-open text-primary"></i> Planes
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <button type="button" class="theme-toggle-nav-btn" id="globalThemeToggler" title="Cambiar Tema (Claro/Oscuro)">
                            <i class="fas fa-moon icon-dark-mode"></i>
                            <i class="fas fa-sun icon-light-mode d-none text-warning"></i>
                        </button>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-menu d-flex align-items-center gap-3" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar"><i class="fas fa-user"></i></div>
                            <div class="d-none d-md-block">
                                <div class="user-name"><?php echo htmlspecialchars(currentUser()); ?></div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li class="dropdown-header-modern">
                                <div class="dropdown-user"><?php echo htmlspecialchars(currentUser()); ?></div>
                                <div class="dropdown-role">Rol: <?php echo htmlspecialchars(currentRole()); ?></div>
                            </li>
                            <li>
                                <a class="dropdown-item dropdown-logout" href="<?php echo RUTA_URL; ?>/portal/logout">
                                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link nav-link-modern" href="<?php echo RUTA_URL; ?>/">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-modern" href="<?php echo RUTA_URL; ?>/paginas/hogar">Servicios Hogar</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-modern" href="<?php echo RUTA_URL; ?>/paginas/empresas">Empresas</a></li>
                    <li class="nav-item"><a class="nav-link nav-link-modern" href="<?php echo RUTA_URL; ?>/paginas/nosotros">Nosotros</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <button type="button" class="theme-toggle-nav-btn" id="globalThemeToggler">
                        <i class="fas fa-moon icon-dark-mode"></i>
                        <i class="fas fa-sun icon-light-mode d-none text-warning"></i>
                    </button>
                    <a href="https://wa.me/51918845960" target="_blank" class="btn btn-whatsapp-modern"><i class="fab fa-whatsapp me-2"></i> WhatsApp</a>
                    <a href="<?php echo RUTA_URL; ?>/portal/login" class="btn btn-portal-modern"><i class="fas fa-user-circle me-2"></i> Mi Portal</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4 flex-grow-1">
<?php
flash('mensaje_error');
flash('mensaje_exito');
flash('venta_mensaje');
flash('orden_mensaje');
flash('usuario_mensaje');
flash('plan_mensaje');
flash('cliente_mensaje');
?>