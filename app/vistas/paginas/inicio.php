<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<section class="hero-master">
    <div class="container">
        <div class="row align-items-center" style="min-height: 80vh;">
            <div class="col-lg-7 py-5">
                <div class="hero-badge mb-4">🚀 COBERTURA 100% FIBRA ÓPTICA</div>
                <h1 class="display-1 fw-black mb-4 text-white">VELOCIDAD QUE <br><span class="text-primary">NO TIENE LÍMITES.</span></h1>
                <p class="fs-4 text-white-50 mb-5 pe-lg-5">Conéctate al internet más estable de Huancayo. Fibra pura hasta tu casa para que vueles mientras trabajas, estudias o juegas.</p>
                <div class="d-flex flex-wrap gap-4">
                    <a href="#planes" class="btn-action">VER PLANES PRUEBBBBBBBBBBBBBBBBBBBBA</a>
                    <a href="<?php echo RUTA_URL; ?>/paginas/cotizacion" class="btn btn-outline-light btn-lg rounded-4 px-4 py-3 fw-bold">SOLUCIONES EMPRESA</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-dark">
    <div class="container py-5">
        <div class="row g-5 text-center">
            <div class="col-md-4">
                <div class="icon-box mx-auto">
                    <img src="<?php echo RUTA_URL; ?>/img/icons/rocket.png" alt="Speed">
                </div>
                <h3 class="fw-bold">1000 Mbps</h3>
                <p class="text-secondary">Simetría total. Sube tus archivos a la misma velocidad que los descargas.</p>
            </div>
            <div class="col-md-4">
                <div class="icon-box mx-auto" style="border-color: var(--accent-blue);">
                    <img src="<?php echo RUTA_URL; ?>/img/icons/shield.png" alt="Stable">
                </div>
                <h3 class="fw-bold">Latencia Cero</h3>
                <p class="text-secondary">La red más estable de la región. Sin microcortes ni caídas inesperadas.</p>
            </div>
            <div class="col-md-4">
                <div class="icon-box mx-auto">
                    <img src="<?php echo RUTA_URL; ?>/img/icons/tools.png" alt="Support">
                </div>
                <h3 class="fw-bold">Soporte Real</h3>
                <p class="text-secondary">Atención técnica inmediata. Estamos en tu ciudad, llegamos cuando nos necesitas.</p>
            </div>
        </div>
    </div>
</section>

<section id="planes" class="py-5" style="background: #020617;">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="display-3 fw-bold text-white">PLANES <span class="text-primary">GAMER & HOGAR</span></h2>
            <p class="text-secondary fs-5">Precios fijos para siempre. Sin sorpresas en tu recibo.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php foreach($datos['planes'] as $plan): ?>
            <div class="col-lg-4 col-md-6">
                <div class="plan-card h-100 <?php echo ($plan->velocidad >= 500) ? 'featured' : ''; ?>">
                    <h4 class="fw-bold mb-4 text-primary"><?php echo htmlspecialchars($plan->nombre_plan); ?></h4>
                    <div class="d-flex align-items-start mb-4">
                        <span class="fs-2 fw-bold mt-2 text-white">S/</span>
                        <span class="price-tag"><?php echo number_format($plan->precio_mensual, 0); ?></span>
                    </div>
                    <div class="badge bg-primary rounded-3 p-3 w-100 mb-4 fs-5 fw-bold text-white">
                        <?php echo $plan->velocidad; ?> MBPS SIMÉTRICOS
                    </div>
                    <ul class="list-unstyled mb-5 text-white-50">
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Router Dual Band WiFi 6</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Instalación en 24h</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-primary me-2"></i> Fibra Pura (FTTH)</li>
                    </ul>
                    <a href="<?php echo RUTA_URL; ?>/paginas/solicitud/<?php echo $plan->id_plan; ?>" class="btn-action w-100 text-center">LO QUIERO AHORA</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5 wifi6-section overflow-hidden">
    <div class="blur-circle"></div>
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="display-4 fw-black text-white">TECNOLOGÍA <span class="text-primary">WIFI 6</span></h2>
                <p class="fs-4 text-white-50 mt-4">Nuestros routers de última generación eliminan las zonas muertas y conectan más dispositivos sin perder velocidad.</p>
                <div class="mt-4">
                    <p class="text-white"><i class="fas fa-bolt text-primary me-2"></i> <strong>Gaming:</strong> Latencia reducida en un 75%.</p>
                    <p class="text-white"><i class="fas fa-signal text-primary me-2"></i> <strong>Cobertura:</strong> Mayor alcance en todos los pisos.</p>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="<?php echo RUTA_URL; ?>/img/wifi6-router.png" class="img-fluid floating-animation" alt="WiFi 6">
            </div>
        </div>
    </div>
</section>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>