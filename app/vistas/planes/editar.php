<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-glass-modern border-0 mt-4">
            <div class="card-header bg-dark text-white border-0 py-3">
                <h4 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-warning"></i> <?php echo htmlspecialchars($datos['titulo'] ?? 'Editar Plan'); ?></h4>
            </div>
            <div class="card-body p-4 bg-body-tertiary">
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-body-emphasis small">Nombre del Plan: <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_plan" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombre_plan'] ?? ''); ?>" required>
                        <?php if(!empty($datos['nombre_error'])): ?>
                            <div class="text-danger small fw-bold mt-1"><i class="fas fa-exclamation-circle me-1"></i> <?php echo $datos['nombre_error']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-body-emphasis small">Velocidad (Ej: 200 Mbps): <span class="text-danger">*</span></label>
                        <input type="text" name="velocidad" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['velocidad_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['velocidad'] ?? ''); ?>" required>
                        <?php if(!empty($datos['velocidad_error'])): ?>
                            <div class="text-danger small fw-bold mt-1"><i class="fas fa-exclamation-circle me-1"></i> <?php echo $datos['velocidad_error']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-body-emphasis small">Precio Mensual (S/): <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3 overflow-hidden">
                            <span class="input-group-text bg-body border-end-0">S/</span>
                            <input type="number" step="0.01" name="precio_mensual" class="form-control bg-body border shadow-none ps-1 <?php echo (!empty($datos['precio_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['precio_mensual'] ?? ''); ?>" required>
                        </div>
                        <?php if(!empty($datos['precio_error'])): ?>
                            <div class="text-danger small fw-bold mt-1"><i class="fas fa-exclamation-circle me-1"></i> <?php echo $datos['precio_error']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-body-emphasis small">Descripción del Servicio:</label>
                        <textarea name="descripcion" class="form-control bg-body border shadow-none rounded-3" rows="3"><?php echo htmlspecialchars($datos['descripcion'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="<?php echo RUTA_URL; ?>/planes" class="btn btn-outline-secondary fw-bold rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>