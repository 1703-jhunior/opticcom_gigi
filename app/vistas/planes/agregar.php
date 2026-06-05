<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-glass-modern border-0 mt-5">
            <div class="card-body p-4 bg-body-tertiary">
                <a href="<?php echo RUTA_URL; ?>/planes" class="btn btn-outline-secondary btn-sm bg-body fw-bold rounded-pill px-3 mb-4 shadow-sm">
                    <i class="fa fa-backward me-1"></i> Volver
                </a>
                
                <h2 class="mb-2 fw-bold text-body-emphasis"><?php echo htmlspecialchars($datos['titulo']); ?></h2>
                <p class="text-body-secondary mb-4">Complete los datos para crear un nuevo plan de servicio de fibra óptica.</p>
                
                <form action="<?php echo RUTA_URL; ?>/planes/agregar" method="POST">
                    
                    <div class="mb-3">
                        <label for="nombre_plan" class="form-label fw-bold text-body-emphasis small">Nombre del Plan: <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_plan" id="nombre_plan" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombre_plan']); ?>" required>
                        <div class="invalid-feedback fw-bold"><?php echo $datos['nombre_error']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="velocidad" class="form-label fw-bold text-body-emphasis small">Velocidad (Ej: 100 Mbps): <span class="text-danger">*</span></label>
                        <input type="text" name="velocidad" id="velocidad" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['velocidad_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['velocidad']); ?>" required>
                        <div class="invalid-feedback fw-bold"><?php echo $datos['velocidad_error']; ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="precio_mensual" class="form-label fw-bold text-body-emphasis small">Precio Mensual (S/): <span class="text-danger">*</span></label>
                        <div class="input-group has-validation rounded-3 overflow-hidden">
                            <span class="input-group-text bg-body border-end-0">S/</span>
                            <input type="text" name="precio_mensual" id="precio_mensual" class="form-control bg-body border shadow-none ps-1 <?php echo (!empty($datos['precio_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['precio_mensual']); ?>" required>
                            <div class="invalid-feedback fw-bold"><?php echo $datos['precio_error']; ?></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-bold text-body-emphasis small">Descripción (Opcional):</label>
                        <textarea name="descripcion" id="descripcion" class="form-control bg-body border shadow-none rounded-3" rows="3" placeholder="Ej: Ideal para streaming 4K, teletrabajo, incluye Wifi 6..."><?php echo htmlspecialchars($datos['descripcion']); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 fw-bold py-2.5 rounded-pill shadow text-uppercase tracking-wider">
                        <i class="fas fa-save me-1"></i> Crear Plan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>