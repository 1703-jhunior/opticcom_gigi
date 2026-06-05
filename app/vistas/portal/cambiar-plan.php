<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="<?php echo RUTA_URL; ?>/portal/inicio" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Volver a Mi Portal
            </a>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Solicitud de Cambio de Plan</h2>
                </div>
                <div class="card-body">
                    <?php flash('mensaje_error'); ?>
                    <?php flash('plan_mensaje'); ?>

                    <!-- 1. Plan Actual -->
                    <h5 class="text-secondary">Tu Plan Actual</h5>
                    <?php if (isset($datos['plan_actual']) && $datos['plan_actual']): ?>
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h4 class="card-title text-primary"><?php echo htmlspecialchars($datos['plan_actual']->nombre_plan ?? 'N/A'); ?></h4>
                                <p class="card-text mb-1">
                                    <strong>Velocidad:</strong> <?php echo htmlspecialchars($datos['plan_actual']->velocidad ?? 'N/A'); ?> Mbps
                                </p>
                                <p class="card-text">
                                    <strong>Precio:</strong> S/ <?php echo number_format($datos['plan_actual']->precio_mensual ?? 0, 2); ?>
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No tienes un plan activo actualmente.</p>
                    <?php endif; ?>

                    <!-- 2. Formulario de Cambio -->
                    <h5 class="text-secondary">Seleccionar Nuevo Plan</h5>
                    <form action="<?php echo RUTA_URL; ?>/portal/cambiarPlan" method="POST">
                        <div class="mb-3">
                            <label for="id_plan_nuevo" class="form-label">Planes Disponibles:</label>
                            <select name="id_plan_nuevo" id="id_plan_nuevo" class="form-select <?php echo (!empty($datos['plan_error'] ?? '')) ? 'is-invalid' : ''; ?>" required>
                                <option value="">-- Seleccione un nuevo plan --</option>
                                
                                <?php if (!empty($datos['planes_disponibles'])): ?>
                                    <?php foreach($datos['planes_disponibles'] as $plan): ?>
                                        <option value="<?php echo $plan->id_plan; ?>">
                                            <?php echo htmlspecialchars($plan->nombre_plan ?? '?'); ?>
                                            (<?php echo htmlspecialchars($plan->velocidad ?? '-'); ?> Mbps - 
                                            S/ <?php echo number_format($plan->precio_mensual ?? 0, 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay otros planes disponibles.</option>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback"><?php echo $datos['plan_error'] ?? ''; ?></div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <small>
                                <strong>Nota:</strong> Al enviar esta solicitud, un asesor se comunicará con usted para confirmar el cambio y la fecha de aplicación.
                            </small>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Enviar Solicitud de Cambio</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>

