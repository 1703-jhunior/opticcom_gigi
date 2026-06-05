<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3 gap-3">
    <h2 class="fw-bold text-body-emphasis mb-0"><i class="fas fa-satellite-dish text-primary me-2"></i> Monitor de Órdenes y Despachos</h2>
    <a href="<?php echo RUTA_URL; ?>/ordenes/asignar" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
        <i class="fas fa-plus me-1"></i> Nueva Asignación Manual
    </a>
</div>

<div class="row mb-4">
    <div class="col-12 col-lg-6">
        <form action="<?php echo RUTA_URL; ?>/ordenes" method="POST">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-body border">
                <span class="input-group-text bg-transparent border-0 text-primary ps-4">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="busqueda" class="form-control border-0 bg-transparent shadow-none" 
                       placeholder="Buscar por ID, Cliente, Técnico o Estado..." 
                       value="<?php echo htmlspecialchars($datos['busqueda'] ?? ''); ?>">
                
                <button class="btn btn-primary px-4 fw-bold rounded-pill m-1" type="submit">Buscar</button>
                
                <?php if(!empty($datos['busqueda'])): ?>
                    <a href="<?php echo RUTA_URL; ?>/ordenes" class="btn btn-danger px-3 rounded-circle m-1 d-flex align-items-center justify-content-center" style="width:38px; height:38px;" title="Limpiar búsqueda">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php flash('orden_mensaje'); ?>
<?php flash('mensaje_error'); ?>

<div class="card card-glass-modern border-0 mb-5">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Cliente / Distrito</th>
                        <th class="py-3">Tipo de Trabajo</th>
                        <th class="py-3">Técnico Asignado</th>
                        <th class="py-3">Día Programado</th>
                        <th class="py-3">Estado App</th>
                        <th class="text-center pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($datos['ordenes'])): ?>
                        <?php foreach($datos['ordenes'] as $orden): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-body-emphasis">#<?php echo $orden->id_orden; ?></td>
                                <td>
                                    <div class="fw-bold text-body-emphasis"><?php echo htmlspecialchars($orden->cliente_nombre . ' ' . $orden->cliente_apellido); ?></div>
                                    <small class="text-body-secondary"><i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo htmlspecialchars($orden->distrito_nombre ?? 'N/A'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-body border-0"><?php echo htmlspecialchars($orden->tipo_orden_nombre); ?></span>
                                    <?php if($orden->prioridad == 'Alta'): ?>
                                        <span class="badge bg-danger animate-pulse"><i class="fas fa-exclamation-circle"></i> Urgente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($orden->id_tecnico_fk == 1): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border-0 fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> SIN ASIGNAR</span>
                                    <?php else: ?>
                                        <div class="text-body-emphasis"><i class="fas fa-hard-hat text-warning me-1"></i> <?php echo htmlspecialchars($orden->tecnico_nombre); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td><div class="text-body-emphasis"><i class="fas fa-calendar-day text-body-secondary me-1"></i> <?php echo date('d/m/Y', strtotime($orden->fecha_programada)); ?></div></td>
                                <td>
                                    <?php 
                                        $color = 'bg-secondary';
                                        if($orden->estado_orden == 'Pendiente') $color = 'bg-warning text-dark';
                                        if($orden->estado_orden == 'En Camino') $color = 'bg-info text-dark';
                                        if($orden->estado_orden == 'En Proceso') $color = 'bg-primary';
                                        if($orden->estado_orden == 'Finalizado') $color = 'bg-success';
                                        if($orden->estado_orden == 'Cancelado') $color = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $color; ?> px-3 py-2 rounded-pill"><?php echo $orden->estado_orden; ?></span>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if($orden->estado_orden == 'Finalizado'): ?>
                                        <a href="<?php echo RUTA_URL; ?>/ordenes/reporte/<?php echo $orden->id_orden; ?>" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold">
                                            <i class="fas fa-file-contract me-1"></i> Reporte App
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm fw-bold bg-body" data-bs-toggle="modal" data-bs-target="#reasignarModal_<?php echo $orden->id_orden; ?>">
                                            <i class="fas fa-people-arrows me-1"></i> Reasignar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-body-secondary py-5">
                            <?php echo !empty($datos['busqueda']) ? 'No se encontraron órdenes con esa búsqueda.' : 'No hay órdenes de trabajo registradas.'; ?>
                        </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(!empty($datos['ordenes'])): ?>
    <?php foreach($datos['ordenes'] as $orden): ?>
        <?php if($orden->estado_orden != 'Finalizado'): ?>
            <div class="modal fade" id="reasignarModal_<?php echo $orden->id_orden; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-body-tertiary border-0">
                            <h5 class="modal-title fw-bold text-body-emphasis"><i class="fas fa-hard-hat text-primary me-2"></i> Asignar Técnico a Orden #<?php echo $orden->id_orden; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?php echo RUTA_URL; ?>/ordenes/reasignarOrden" method="POST">
                            <div class="modal-body bg-body-tertiary text-start px-4">
                                <input type="hidden" name="id_orden" value="<?php echo $orden->id_orden; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-body-secondary">Cliente:</label>
                                    <input type="text" class="form-control bg-body border shadow-none" value="<?php echo htmlspecialchars($orden->cliente_nombre . ' ' . $orden->cliente_apellido); ?>" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary small">Técnico de Campo:</label>
                                    <select name="id_tecnico" class="form-select bg-body border shadow-none" required>
                                        <option value="">-- Seleccione un Técnico --</option>
                                        <?php if(isset($datos['tecnicos']) && !empty($datos['tecnicos'])): ?>
                                            <?php foreach($datos['tecnicos'] as $tecnico): ?>
                                                <?php if($tecnico->id_usuario != 1): ?>
                                                    <option value="<?php echo $tecnico->id_usuario; ?>" <?php if($orden->id_tecnico_fk == $tecnico->id_usuario) echo 'selected'; ?>>
                                                        <?php echo htmlspecialchars($tecnico->nombre); ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>No hay técnicos activos</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary small">Día Estimado de Instalación:</label>
                                    <input type="date" name="fecha_programada" class="form-control bg-body border shadow-none" value="<?php echo date('Y-m-d', strtotime($orden->fecha_programada)); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer bg-body-tertiary border-0 pt-0 px-4 pb-4">
                                <button type="button" class="btn btn-outline-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success fw-bold rounded-pill px-4"><i class="fas fa-save me-1"></i> Guardar Asignación</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>