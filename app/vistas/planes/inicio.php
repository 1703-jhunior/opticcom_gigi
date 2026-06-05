<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container-fluid px-0">
    <?php flash('plan_mensaje'); ?>
    <?php flash('mensaje_error'); ?>

    <div class="row mb-4 align-items-center mt-2">
        <div class="col-md-6">
            <h1 class="fw-bold text-body-emphasis mb-0">
                <i class="fas fa-box-open text-primary me-2"></i> <?php echo htmlspecialchars($datos['titulo']); ?>
            </h1>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <?php if (hasRole(['Administrador','Ventas'])): ?>
                <a href="<?php echo RUTA_URL; ?>/planes/agregar" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
                    <i class="fas fa-plus me-1"></i> Crear Nuevo Plan
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card card-glass-modern border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-body-secondary">
                        <tr>
                            <th class="ps-4 py-3" style="width: 60px;">#</th>
                            <th class="py-3">Nombre del Plan</th>
                            <th class="py-3">Velocidad</th>
                            <th class="py-3">Precio Mensual</th>
                            <th class="py-3">Descripción</th>
                            <th class="py-3">Estado</th>
                            <th class="text-center pe-4 py-3" style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($datos['planes']) && is_array($datos['planes'])): ?>
                            <?php $i = 1; foreach($datos['planes'] as $plan): ?>
                                <tr>
                                    <td class="ps-4 font-monospace text-body-secondary"><?php echo $i++; ?></td>
                                    <td><div class="fw-bold text-body-emphasis"><?php echo htmlspecialchars($plan->nombre_plan); ?></div></td>
                                    <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-bolt me-1"></i> <?php echo htmlspecialchars($plan->velocidad); ?></span></td>
                                    <td><div class="fw-bold text-success fs-6">S/ <?php echo number_format($plan->precio_mensual, 2); ?></div></td>
                                    <td>
                                        <div class="text-body-secondary text-truncate small" style="max-width: 250px;" title="<?php echo htmlspecialchars($plan->descripcion); ?>">
                                            <?php echo !empty($plan->descripcion) ? htmlspecialchars($plan->descripcion) : '<span class="text-muted/50 italic">Sin descripción</span>'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (($plan->estado ?? 'activo') === 'activo'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 border-0 rounded-pill">
                                                <i class="fas fa-circle me-1" style="font-size: 7px; vertical-align: middle;"></i> Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1.5 border-0 rounded-pill">
                                                <i class="fas fa-circle me-1" style="font-size: 7px; vertical-align: middle;"></i> Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden border" role="group">
                                            
                                            <?php if (hasRole(['Administrador','Ventas'])): ?>
                                                <a href="<?php echo RUTA_URL; ?>/planes/editar/<?php echo $plan->id_plan; ?>" class="btn btn-sm btn-body text-warning border-0 px-3" title="Editar Parámetros">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (($plan->estado ?? 'activo') === 'activo'): ?>
                                                <?php if (hasRole(['Administrador','Ventas'])): ?>
                                                    <a href="<?php echo RUTA_URL; ?>/planes/desactivar/<?php echo $plan->id_plan; ?>"
                                                       class="btn btn-sm btn-body text-body-secondary border-0 px-3"
                                                       title="Desactivar / Ocultar al público"
                                                       onclick="return confirm('¿Desactivar este plan? ya no se mostrará al público en los catálogos.');">
                                                        <i class="fas fa-eye-slash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if (hasRole(['Administrador','Ventas'])): ?>
                                                    <a href="<?php echo RUTA_URL; ?>/planes/activar/<?php echo $plan->id_plan; ?>"
                                                       class="btn btn-sm btn-body text-success border-0 px-3"
                                                       title="Activar / Mostrar al público"
                                                       onclick="return confirm('¿Activar este plan para que se muestre en los catálogos públicos?');">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php if (hasRole(['Administrador'])): ?>
                                                <a href="<?php echo RUTA_URL; ?>/planes/eliminar/<?php echo $plan->id_plan; ?>"
                                                   class="btn btn-sm btn-body text-danger border-0 px-3"
                                                   title="Eliminar de la base de datos"
                                                   onclick="return confirm('¿Eliminar este plan definitivamente del sistema? Esta acción no se puede deshacer.');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                            
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-body-secondary py-5">
                                    <i class="fas fa-box-open fa-2x mb-2 text-muted"></i><br>No hay planes de servicio registrados en la plataforma.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>