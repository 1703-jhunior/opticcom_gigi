<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<section class="py-5" style="background: var(--deep-dark);">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-3 fw-black text-white">INTERNET <span class="text-primary">HOGAR</span></h1>
            <p class="fs-4 text-white-50">Fibra Óptica Pura (FTTH) para que tu familia nunca se detenga.</p>
        </div>

        <div class="row g-4 justify-content-center">
            <?php if (!empty($datos['planes'])): ?>
                <?php foreach($datos['planes'] as $plan): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="plan-card featured h-100 d-flex flex-column shadow-lg">
                        <h3 class="fw-bold mb-3 text-white"><?php echo htmlspecialchars($plan->nombre_plan); ?></h3>
                        <div class="d-flex align-items-baseline mb-4">
                            <span class="fs-2 fw-bold text-primary">S/</span>
                            <span class="price-tag"><?php echo number_format($plan->precio_mensual, 0); ?></span>
                            <span class="text-white-50 ms-2">/mes</span>
                        </div>
                        <div class="bg-primary rounded-3 p-3 text-center mb-4 fs-4 fw-bold">
                            <?php echo $plan->velocidad; ?> Mbps Simétricos
                        </div>
                        <ul class="list-unstyled mb-5 flex-grow-1 text-white-50">
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> 100% Fibra Óptica</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Router WiFi 6 Alta Gama</li>
                            <li class="mb-2"><i class="fas fa-check text-primary me-2"></i> Soporte Local 24/7</li>
                        </ul>
                        <a href="<?php echo RUTA_URL; ?>/paginas/solicitud/<?php echo $plan->id_plan; ?>" class="btn-action w-100 text-center">CONTRATAR AHORA</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-white">No hay planes disponibles en este momento.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>