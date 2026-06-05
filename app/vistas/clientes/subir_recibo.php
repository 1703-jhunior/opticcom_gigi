<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row mt-4 mb-3">
    <div class="col-md-8 mx-auto d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-file-upload text-danger me-2"></i> Subir Recibo / Contrato</h2>
        <a href="<?php echo RUTA_URL . '/clientes/historialPagos/' . (int)$datos['cliente']->id_cliente; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Historial
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold">
                <i class="fas fa-user text-primary me-2"></i> Cliente: <?php echo htmlspecialchars(($datos['cliente']->nombre ?? '') . ' ' . ($datos['cliente']->apellido ?? '')); ?>
            </div>
            <div class="card-body bg-light">
                
                <?php flash('mensaje_error'); ?>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Solo se permiten archivos en formato <strong>.PDF</strong> (Máximo 5MB). El archivo se guardará de forma segura en la carpeta personal del cliente.
                </div>

                <form action="<?php echo RUTA_URL . '/clientes/subirRecibo/' . (int)$datos['cliente']->id_cliente; ?>" method="post" enctype="multipart/form-data" class="mt-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Seleccionar Archivo PDF:</label>
                        <input type="file" name="recibo" accept="application/pdf" class="form-control form-control-lg" required>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 fw-bold py-2">
                        <i class="fas fa-cloud-upload-alt me-2"></i> GUARDAR PDF EN EL SISTEMA
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>