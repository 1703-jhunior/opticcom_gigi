<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<section id="vista-exito" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 text-center">
                <div class="card border-0 shadow-lg p-4 p-md-5 rounded-4 animate__animated animate__zoomIn">
                    
                    <div class="mb-4">
                        <div class="success-checkmark mb-3">
                            <i class="fas fa-check-circle display-1 text-success shadow-sm"></i>
                        </div>
                        <h2 class="fw-bold text-dark">¡Solicitud Enviada!</h2>
                        <div class="separador mx-auto"></div>
                    </div>
                    
                    <p class="lead text-secondary mb-4">
                        Gracias por confiar en <span class="text-primary fw-bold">OPTICCOM S.A.C.</span>
                    </p>

                    <div class="info-box p-3 rounded-3 mb-4 text-start">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            <span class="fw-bold text-dark small text-uppercase">Próximos pasos</span>
                        </div>
                        <p class="mb-0 text-muted small">
                            Nuestros asesores revisarán la factibilidad técnica en su zona y se comunicarán al número proporcionado a la brevedad posible.
                        </p>
                    </div>

                    <div class="d-grid">
                        <a href="<?php echo RUTA_URL; ?>" class="btn btn-primary btn-lg shadow rounded-pill fw-bold py-3 transition-all">
                            <i class="fas fa-arrow-left me-2"></i> Volver al Inicio
                        </a>
                    </div>

                    <div class="mt-4 opacity-50">
                        <img src="<?php echo RUTA_URL; ?>/img/logo.png" alt="Opticcom" style="max-height: 40px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* 1. ELIMINAR EL FONDO BLANCO DETRÁS (Sobrescribimos el estilo del body solo aquí) */
    body {
        background-color: #f0f2f5 !important; /* Gris muy claro tipo Facebook/Instagram */
    }

    #vista-exito {
        min-height: 80vh;
        display: flex;
        align-items: center;
        /* Graduado sutil para dar profundidad */
        background: radial-gradient(circle at center, #ffffff 0%, #f0f2f5 100%);
    }

    /* 2. ESTILO DE LA TARJETA */
    .rounded-4 {
        border-radius: 20px !important;
    }

    .separador {
        width: 60px;
        height: 4px;
        background: var(--bs-primary);
        border-radius: 2px;
        margin-top: 10px;
    }

    .info-box {
        background-color: rgba(13, 110, 253, 0.05);
        border: 1px dashed rgba(13, 110, 253, 0.2);
    }

    /* 3. EFECTOS Y TRANSICIONES */
    .transition-all {
        transition: all 0.3s ease;
    }

    .transition-all:hover {
        transform: translateY(-3px);
        filter: brightness(110%);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    /* Ajuste de color para iconos */
    .text-success {
        color: #28a745 !important;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>