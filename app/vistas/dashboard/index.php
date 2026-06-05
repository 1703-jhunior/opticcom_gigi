<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>

:root{
    --bg:#081120;
    --bg2:#0b172a;
    --card:#101c33;
    --border:rgba(255,255,255,.06);
    --primary:#2f6bff;
    --success:#22c55e;
    --danger:#ef4444;
    --warning:#f59e0b;
    --purple:#8b5cf6;
    --text:#ffffff;
    --muted:#9aa4b2;
    --radius:24px;
}

body{
    background:
    radial-gradient(circle at top left,#12254b 0%,#081120 35%),
    linear-gradient(to bottom,#081120,#0b172a) !important;
    font-family:'Inter',sans-serif !important;
    color:white;
}

.container-fluid{
    padding-left:30px !important;
    padding-right:30px !important;
}

.dashboard-hero{
    background:
    linear-gradient(135deg,
    rgba(47,107,255,.22),
    rgba(139,92,246,.18));
    border:1px solid rgba(255,255,255,.06);
    border-radius:30px;
    padding:40px;
    position:relative;
    overflow:hidden;
    box-shadow:
    0 0 40px rgba(47,107,255,.10);
}

.dashboard-hero::before{
    content:'';
    position:absolute;
    width:420px;
    height:420px;
    background:
    radial-gradient(circle,
    rgba(47,107,255,.20),
    transparent 70%);
    top:-180px;
    right:-150px;
}

.dashboard-title{
    font-size:2.3rem;
    font-weight:800;
    color:white;
}

.dashboard-subtitle{
    color:#c2cfde;
    font-size:1rem;
    margin-top:10px;
}

.hero-badge{
    margin-top:18px;
    display:inline-flex;
    align-items:center;
    gap:10px;
    background:rgba(47,107,255,.15);
    border:1px solid rgba(47,107,255,.35);
    color:#dce7ff;
    padding:10px 18px;
    border-radius:50px;
    font-size:.85rem;
    font-weight:700;
}

.section-title{
    color:#95a7c0;
    font-size:.85rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:1.5px;
    margin-bottom:20px;
}

.kpi-box{
    background:
    linear-gradient(145deg,
    rgba(16,28,51,.95),
    rgba(10,18,35,.98));
    border-radius:24px;
    padding:28px;
    position:relative;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.06);
    min-height:170px;
    transition:.25s ease;
}

.kpi-box:hover{
    transform:translateY(-6px);
    box-shadow:
    0 0 25px rgba(47,107,255,.14);
}

.border-blue{
    border-top:4px solid var(--primary);
}

.border-green{
    border-top:4px solid var(--success);
}

.border-red{
    border-top:4px solid var(--danger);
}

.border-orange{
    border-top:4px solid var(--warning);
}

.kpi-title{
    color:#9fb0c9;
    text-transform:uppercase;
    letter-spacing:1px;
    font-size:.78rem;
    font-weight:700;
    margin-bottom:15px;
}

.kpi-value{
    font-size:2.6rem;
    font-weight:800;
    color:white;
    line-height:1;
}

.kpi-sub{
    margin-top:12px;
    color:#8294ac;
    font-size:.92rem;
}

.kpi-icon{
    position:absolute;
    right:18px;
    bottom:12px;
    font-size:4.3rem;
    opacity:.08;
}

.card-dashboard{
    background:
    linear-gradient(145deg,
    rgba(16,28,51,.95),
    rgba(10,18,35,.98));
    border-radius:28px;
    border:1px solid rgba(255,255,255,.06);
    overflow:hidden;
    transition:.25s ease;
}

.card-dashboard:hover{
    border-color:rgba(255,255,255,.10);
}

.module-title{
    font-size:1.45rem;
    font-weight:800;
    color:white;
    margin-bottom:28px;
    display:flex;
    align-items:center;
    gap:14px;
}

.module-title i{
    color:var(--primary);
}

.btn-action-panel{
    background:
    linear-gradient(145deg,
    rgba(255,255,255,.03),
    rgba(255,255,255,.01)) !important;
    border:1px solid rgba(255,255,255,.05) !important;
    border-radius:24px !important;
    padding:30px 24px !important;
    color:white !important;
    transition:.25s ease;
    position:relative;
    overflow:hidden;
    min-height:170px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:flex-start;
    text-align:left !important;
}

.btn-action-panel:hover{
    transform:translateY(-5px);
    border-color:rgba(47,107,255,.35) !important;
    box-shadow:
    0 0 25px rgba(47,107,255,.15);
}

.btn-action-panel i{
    font-size:2.2rem;
    margin-bottom:18px;
}

.action-title{
    font-weight:700;
    font-size:1.15rem;
}

.action-sub{
    color:#90a1ba;
    font-size:.92rem;
    margin-top:8px;
}

.client-item{
    padding:20px 24px;
    border-bottom:1px solid rgba(255,255,255,.04);
    transition:.2s ease;
}

.client-item:hover{
    background:rgba(255,255,255,.02);
}

.client-avatar{
    width:48px;
    height:48px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    color:white;
    background:
    linear-gradient(135deg,#2563eb,#7c3aed);
}

.client-name{
    font-weight:700;
    color:white;
}

.client-address{
    color:#94a3b8;
    font-size:.88rem;
}

.client-date{
    color:#8da0ba;
    font-size:.8rem;
}

.client-status{
    background:rgba(34,197,94,.15);
    color:#4ade80;
    padding:6px 12px;
    border-radius:50px;
    font-size:.75rem;
    font-weight:700;
}

.view-all{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:24px;
    color:white;
    text-decoration:none;
    font-weight:700;
}

.view-all:hover{
    color:#4d7dff;
}

::-webkit-scrollbar{
    width:10px;
}

::-webkit-scrollbar-thumb{
    background:#1d3359;
    border-radius:50px;
}

@media(max-width:768px){

    .dashboard-title{
        font-size:1.7rem;
    }

    .kpi-value{
        font-size:2rem;
    }

    .btn-action-panel{
        min-height:140px;
    }
}

</style>

<div class="container-fluid py-4">

    <div class="row mb-4">

        <div class="col-12">

            <div class="dashboard-hero d-flex justify-content-between align-items-center">

                <div>

                    <div class="dashboard-title">
                        Bienvenido de vuelta,
                        <?php echo htmlspecialchars($_SESSION['nombre_usuario']); ?>
                    </div>

                    <div class="dashboard-subtitle">
                        Aquí tienes el resumen general de tu operación hoy.
                    </div>

                    <div class="hero-badge">
                        <i class="fas fa-user-shield"></i>
                        <?php echo htmlspecialchars($datos['rol']); ?>
                    </div>

                </div>

                <div class="d-none d-md-block">
                    <i class="fas fa-satellite-dish fa-5x text-white opacity-10"></i>
                </div>

            </div>

        </div>

    </div>

    <div class="section-title">
        Resumen Operacional
    </div>

    <div class="row g-4 mb-5">

        <div class="col-xl-3 col-md-6">

            <div class="kpi-box border-blue">

                <div class="kpi-title">
                    Base de Clientes
                </div>

                <div class="kpi-value">
                    <?php echo $datos['total_clientes']; ?>
                </div>

                <div class="kpi-sub">
                    Total registrados
                </div>

                <i class="fas fa-users kpi-icon text-primary"></i>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="kpi-box border-green">

                <div class="kpi-title">
                    Servicios Activos
                </div>

                <div class="kpi-value">
                    <?php echo $datos['total_activos']; ?>
                </div>

                <div class="kpi-sub">
                    Clientes conectados
                </div>

                <i class="fas fa-wifi kpi-icon text-success"></i>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="kpi-box border-red">

                <div class="kpi-title">
                    Suspendidos
                </div>

                <div class="kpi-value">
                    <?php echo $datos['total_suspendidos']; ?>
                </div>

                <div class="kpi-sub">
                    Clientes morosos
                </div>

                <i class="fas fa-user-slash kpi-icon text-danger"></i>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="kpi-box border-orange">

                <div class="kpi-title">
                    Planes Ofertados
                </div>

                <div class="kpi-value">
                    <?php echo $datos['total_planes']; ?>
                </div>

                <div class="kpi-sub">
                    Planes disponibles
                </div>

                <i class="fas fa-layer-group kpi-icon text-warning"></i>

            </div>

        </div>

    </div>

    <div class="row g-4">

        <div class="col-lg-8">

            <?php if (in_array($_SESSION['rol_usuario'], ['Administrador', 'Asesor de ventas', 'Área de pagos'])): ?>

            <div class="card-dashboard mb-4">

                <div class="p-4">

                    <h5 class="module-title">
                        <i class="fas fa-briefcase"></i>
                        Gestión Comercial
                    </h5>

                    <div class="row g-4">

                        <div class="col-md-6">

                            <a href="<?php echo RUTA_URL; ?>/clientes/agregar"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-user-plus text-primary"></i>

                                <div class="action-title">
                                    Alta de Cliente
                                </div>

                                <div class="action-sub">
                                    Registrar nuevo cliente
                                </div>

                            </a>

                        </div>

                        <div class="col-md-6">

                            <a href="<?php echo RUTA_URL; ?>/planes/agregar"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-signal text-primary"></i>

                                <div class="action-title">
                                    Registrar Plan
                                </div>

                                <div class="action-sub">
                                    Crear nuevo plan
                                </div>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <?php endif; ?>

            <?php if ($_SESSION['rol_usuario'] === 'Administrador'): ?>

            <div class="card-dashboard mb-4">

                <div class="p-4">

                    <h5 class="module-title">
                        <i class="fas fa-file-excel text-success"></i>
                        Reportes y Auditoría
                    </h5>

                    <div class="row g-4">

                        <div class="col-md-4">

                            <a href="<?php echo RUTA_URL; ?>/dashboard/exportarExcelClientes"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-users text-success"></i>

                                <div class="action-title">
                                    Base Clientes
                                </div>

                                <div class="action-sub">
                                    Exportar Excel
                                </div>

                            </a>

                        </div>

                        <div class="col-md-4">

                            <a href="<?php echo RUTA_URL; ?>/dashboard/exportarExcelPagos"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-hand-holding-usd text-success"></i>

                                <div class="action-title">
                                    Finanzas / Pagos
                                </div>

                                <div class="action-sub">
                                    Exportar Excel
                                </div>

                            </a>

                        </div>

                        <div class="col-md-4">

                            <a href="<?php echo RUTA_URL; ?>/dashboard/exportarExcelSolicitudes"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-headset text-success"></i>

                                <div class="action-title">
                                    Solicitudes Web
                                </div>

                                <div class="action-sub">
                                    Exportar Excel
                                </div>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <div class="card-dashboard">

                <div class="p-4">

                    <h5 class="module-title">
                        <i class="fas fa-shield-alt text-purple"></i>
                        Administración del Sistema
                    </h5>

                    <div class="row g-4">

                        <div class="col-md-6">

                            <a href="<?php echo RUTA_URL; ?>/usuarios"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-users-cog text-info"></i>

                                <div class="action-title">
                                    Usuarios y Roles
                                </div>

                                <div class="action-sub">
                                    Gestionar accesos
                                </div>

                            </a>

                        </div>

                        <div class="col-md-6">

                            <a href="<?php echo RUTA_URL; ?>/ordenes"
                               class="btn btn-action-panel w-100">

                                <i class="fas fa-truck-pickup text-warning"></i>

                                <div class="action-title">
                                    Monitor de Despachos
                                </div>

                                <div class="action-sub">
                                    Seguimiento de órdenes
                                </div>

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <?php endif; ?>

        </div>

        <div class="col-lg-4">

            <div class="card-dashboard h-100">

                <div class="p-4 border-bottom border-secondary border-opacity-10">

                    <h5 class="module-title mb-0">
                        <i class="fas fa-history text-warning"></i>
                        Clientes Recientes
                    </h5>

                </div>

                <?php if(!empty($datos['ultimos_clientes'])): ?>

                    <?php foreach($datos['ultimos_clientes'] as $cliente): ?>

                        <div class="client-item">

                            <div class="d-flex justify-content-between align-items-start">

                                <div class="d-flex gap-3">

                                    <div class="client-avatar">

                                        <?php echo strtoupper(substr($cliente->nombre,0,1)); ?>

                                    </div>

                                    <div>

                                        <div class="client-name">

                                            <?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellido); ?>

                                        </div>

                                        <div class="client-address">

                                            <?php echo htmlspecialchars($cliente->direccion_calle ?? 'Sin dirección'); ?>

                                        </div>

                                    </div>

                                </div>

                                <div class="client-date">

                                    <?php echo date('d/m/Y', strtotime($cliente->fecha_instalacion ?? date('Y-m-d'))); ?>

                                </div>

                            </div>

                            <div class="mt-3">

                                <span class="client-status">

                                    Alta completa

                                </span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="text-center p-5">

                        <i class="fas fa-folder-open fa-4x opacity-10 mb-4"></i>

                        <div class="text-secondary">
                            No hay clientes recientes.
                        </div>

                    </div>

                <?php endif; ?>

                <a href="<?php echo RUTA_URL; ?>/clientes"
                   class="view-all">

                    Ver Directorio Completo

                    <i class="fas fa-arrow-right"></i>

                </a>

            </div>

        </div>

    </div>

</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>
