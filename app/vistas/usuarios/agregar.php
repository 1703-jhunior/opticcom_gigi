<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-glass-modern border-0 mt-4 mb-5">
            <div class="card-body p-4 bg-body-tertiary">
                <a href="<?php echo RUTA_URL; ?>/usuarios" class="btn btn-outline-secondary btn-sm bg-body fw-bold rounded-pill px-3 mb-4 shadow-sm">
                    <i class="fas fa-backward me-1"></i> Volver a la lista
                </a>
                
                <h2 class="mb-2 fw-bold text-body-emphasis"><?php echo htmlspecialchars($datos['titulo'] ?? 'Agregar Usuario Admin'); ?></h2>
                <p class="text-body-secondary border-bottom border-secondary border-opacity-10 pb-3 mb-4">Complete el formulario para registrar un nuevo integrante del personal en el sistema.</p>
                
                <form action="<?php echo RUTA_URL; ?>/usuarios/agregar" method="POST">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold text-body-emphasis small">Nombre Completo: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" 
                               id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>" required>
                        <span class="invalid-feedback fw-bold"><?php echo $datos['nombre_error'] ?? ''; ?></span>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-body-emphasis small">Email (Usuario de Acceso): <span class="text-danger">*</span></label>
                        <input type="email" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['email_error'])) ? 'is-invalid' : ''; ?>"
                               id="email" name="email" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" required>
                        <span class="invalid-feedback fw-bold"><?php echo $datos['email_error'] ?? ''; ?></span>
                    </div>
                    
                    <div class="mb-4">
                        <label for="rol" class="form-label fw-bold text-body-emphasis small">Rol del Sistema: <span class="text-danger">*</span></label>
                        <select class="form-select bg-body border shadow-none rounded-3 <?php echo (!empty($datos['rol_error'])) ? 'is-invalid' : ''; ?>" id="rol" name="rol" required>
                            <option value="" <?php echo (empty($datos['rol'])) ? 'selected' : ''; ?>>-- Seleccione un rol --</option>
                            <?php foreach($datos['roles'] as $rol) : ?>
                                <option value="<?php echo $rol->id_rol; ?>" <?php echo (($datos['rol'] ?? '') == $rol->id_rol) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rol->nombre_rol); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="invalid-feedback fw-bold"><?php echo $datos['rol_error'] ?? ''; ?></span>
                    </div>

                    <div class="bg-body p-4 rounded-3 border shadow-2xs mb-4">
                        <h6 class="fw-bold text-body-emphasis border-bottom border-secondary border-opacity-10 pb-2 mb-3"><i class="fas fa-lock text-primary me-2"></i> Configuración de Seguridad</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="password" class="form-label fw-bold text-body-emphasis small">Contraseña: <span class="text-danger">*</span></label>
                                <input type="password" class="form-control bg-body-tertiary border shadow-none rounded-3 <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>"
                                       id="password" name="password" required minlength="6" autocomplete="new-password">
                                <span class="invalid-feedback fw-bold"><?php echo $datos['password_error'] ?? ''; ?></span>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="confirmar_password" class="form-label fw-bold text-body-emphasis small">Confirmar Contraseña: <span class="text-danger">*</span></label>
                                <input type="password" class="form-control bg-body-tertiary border shadow-none rounded-3 <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>"
                                       id="confirmar_password" name="confirmar_password" required minlength="6" autocomplete="new-password">
                                <span class="invalid-feedback fw-bold"><?php echo $datos['password_error'] ?? ''; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-3 rounded-pill shadow-sm text-uppercase tracking-wider">
                        <i class="fas fa-save me-2"></i> Guardar Nuevo Usuario
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>