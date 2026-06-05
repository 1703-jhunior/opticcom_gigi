<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<section class="py-5" style="background: var(--deep-dark);">
    <div class="container py-5">
        <div class="row align-items-center g-5 mb-5">
            <div class="col-lg-6">
                <div class="rounded-5 overflow-hidden shadow-2xl">
                    <img src="<?php echo RUTA_URL; ?>/img/nosotros-equipo.jpg" class="img-fluid w-100" style="object-fit: cover; height: 500px;" alt="Equipo técnico">
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="display-4 fw-black text-white mb-4">MÁS QUE INTERNET, <span class="text-primary">CONEXIÓN HUMANA.</span></h2>
                <p class="fs-5 text-white-50">En OPTICCOM S.A.C., nacimos en Huancayo con una visión clara: llevar la verdadera velocidad de la fibra óptica a cada hogar de nuestra región. No somos solo un proveedor, somos tus vecinos tecnológicos.</p>
            </div>
        </div>

        <div class="row g-4 mt-5">
            <div class="col-md-6">
                <div class="p-5 rounded-5" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="icon-box mb-4"><i class="fas fa-bullseye fa-3x text-primary"></i></div>
                    <h2 class="text-white fw-bold mb-3">NUESTRA MISIÓN</h2>
                    <p class="text-white-50 fs-5">Proveer acceso a internet de ultra velocidad con tecnología 100% fibra óptica, garantizando un servicio transparente, estable y con el soporte técnico más rápido del mercado local.</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-5 rounded-5" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="icon-box mb-4"><i class="fas fa-eye fa-3x text-primary"></i></div>
                    <h2 class="text-white fw-bold mb-3">NUESTRA VISIÓN</h2>
                    <p class="text-white-50 fs-5">Liderar la transformación digital del Perú central para el 2028, siendo reconocidos como el operador de internet más confiable y tecnológicamente avanzado de la región Junín.</p>
                </div>
            </div>
        </div>

        <div class="row align-items-center g-5 mt-5">
            <div class="col-lg-6 order-2 order-lg-1">
                <h2 class="display-5 fw-bold text-white mb-4">TECNOLOGÍA DE <span class="text-primary">CLASE MUNDIAL.</span></h2>
                <p class="fs-5 text-white-50">Nuestra central de monitoreo y racks de servidores cuentan con redundancia total. Esto significa que tu internet nunca se detiene. Invertimos en hardware de última generación para darte la latencia más baja del país.</p>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <div class="rounded-5 overflow-hidden shadow-2xl border border-secondary">
                    <img src="<?php echo RUTA_URL; ?>/img/nosotros-infra.jpg" class="img-fluid w-100" style="object-fit: cover; height: 400px;" alt="Nuestros servidores">
                </div>
            </div>
        </div>
    </div>
</section>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>