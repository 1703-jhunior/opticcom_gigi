<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        
        <div class="card card-glass-modern border-0 mt-4 mb-5">
            <div class="card-header bg-dark text-white border-0 py-3 d-flex align-items-center">
                <h4 class="mb-0 fw-bold"><i class="fas fa-user-edit text-warning me-2"></i> <?php echo htmlspecialchars($datos['titulo'] ?? 'Editar Usuario Admin'); ?></h4>
            </div>
            
            <div class="card-body p-4 bg-body-tertiary">
                <a href="<?php echo RUTA_URL; ?>/usuarios" class="btn btn-outline-secondary btn-sm bg-body fw-bold rounded-pill px-3 mb-4 shadow-sm">
                    <i class="fas fa-backward me-1"></i> Volver a Usuarios
                </a>
                
                <p class="text-body-secondary mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <i class="fas fa-info-circle text-primary me-1"></i> Modifique los datos corporativos y credenciales de acceso del usuario del sistema.
                </p>
                
                <form action="<?php echo RUTA_URL; ?>/usuarios/editar/<?php echo $datos['id_usuario']; ?>" method="POST">
                    
                    <div class="row g-3">
                        <div class="col-md-12 mb-2">
                            <label for="nombre" class="form-label fw-bold text-body-emphasis small">Nombre Completo: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" 
                                   id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>" required>
                            <span class="invalid-feedback fw-bold"><?php echo $datos['nombre_error'] ?? ''; ?></span>
                        </div>
                        
                        <div class="col-md-6 mb-2">
                            <label for="email" class="form-label fw-bold text-body-emphasis small">Email (Usuario): <span class="text-danger">*</span></label>
                            <div class="input-group rounded-3 overflow-hidden has-validation">
                                <span class="input-group-text bg-body border-end-0 text-muted"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control bg-body border shadow-none border-start-0 ps-0 <?php echo (!empty($datos['email_error'])) ? 'is-invalid' : ''; ?>"
                                       id="email" name="email" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" required>
                                <span class="invalid-feedback fw-bold"><?php echo $datos['email_error'] ?? ''; ?></span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-2">
                            <label for="rol" class="form-label fw-bold text-body-emphasis small">Rol en el Sistema: <span class="text-danger">*</span></label>
                            <select class="form-select bg-body border shadow-none rounded-3 <?php echo (!empty($datos['rol_error'])) ? 'is-invalid' : ''; ?>" id="rol" name="rol" required>
                                <option value="" <?php echo (empty($datos['rol'])) ? 'selected' : ''; ?>>-- Seleccione un rol --</option>
                                <option value="Administrador" <?php echo (($datos['rol'] ?? '') == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="Ventas" <?php echo (($datos['rol'] ?? '') == 'Ventas') ? 'selected' : ''; ?>>Ventas</option>
                                <option value="Pagos" <?php echo (($datos['rol'] ?? '') == 'Pagos') ? 'selected' : ''; ?>>Pagos</option>
                                <option value="Soporte" <?php echo (($datos['rol'] ?? '') == 'Soporte') ? 'selected' : ''; ?>>Soporte / Técnico</option>
                            </select>
                            <span class="invalid-feedback fw-bold"><?php echo $datos['rol_error'] ?? ''; ?></span>
                        </div>
                    </div>

                    <div class="bg-body border rounded-3 p-3 mt-4 mb-4 shadow-sm">
                        <h6 class="text-body-emphasis fw-bold border-bottom border-secondary border-opacity-10 pb-2 mb-3">
                            <i class="fas fa-key text-secondary me-1"></i> Cambio de Contraseña
                        </h6>
                        <p class="text-body-secondary small mb-3">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i> Deje los campos de contraseña <strong>en blanco</strong> si no desea cambiar la credencial actual del usuario.
                        </p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold text-body-emphasis small">Nueva Contraseña:</label>
                                <input type="password" class="form-control bg-body-tertiary border shadow-none rounded-3 <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>"
                                       id="password" name="password" minlength="6" autocomplete="new-password">
                                <span class="invalid-feedback fw-bold"><?php echo $datos['password_error'] ?? ''; ?></span>
                            </div>
                            <div class="col-md-6">
                                <label for="confirmar_password" class="form-label fw-bold text-body-emphasis small">Confirmar Nueva Contraseña:</label>
                                <input type="password" class="form-control bg-body-tertiary border shadow-none rounded-3 <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>"
                                       id="confirmar_password" name="confirmar_password" minlength="6" autocomplete="new-password">
                                <span class="invalid-feedback fw-bold"><?php echo $datos['password_error'] ?? ''; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4">
                        <a href="<?php echo RUTA_URL; ?>/usuarios" class="btn btn-outline-secondary fw-bold rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Actualizar Usuario
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
        
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>