<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row mb-3 align-items-center">
    <div class="col-md-9">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo RUTA_URL; ?>/clientes">Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($datos['titulo'] ?? 'Historial de Pagos'); ?></li>
            </ol>
        </nav>
        <h1 class="mb-0"><?php echo htmlspecialchars($datos['titulo'] ?? 'Historial de Pagos'); ?></h1>
        <?php if (isset($datos['cliente']) && is_object($datos['cliente'])): ?>
            <p class="lead text-muted">
                Cliente: <?php echo htmlspecialchars($datos['cliente']->nombre ?? ''); ?> 
                <?php echo htmlspecialchars($datos['cliente']->apellido ?? ''); ?> 
                (DNI/RUC: <?php echo htmlspecialchars($datos['cliente']->dni ?? 'N/A'); ?>)
            </p>
        <?php endif; ?>
    </div>
    <div class="col-md-3 text-end">
        <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver a Clientes
        </a>
    </div>
</div>

<?php flash('pago_mensaje'); ?>
<?php flash('cliente_mensaje'); ?>
<?php flash('mensaje_error'); ?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fas fa-list-ul me-2"></i> Registros de Pagos del Sistema</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Fecha de Pago</th>
                        <th>Monto Pagado (S/)</th>
                        <th>Mes Correspondiente</th>
                        <th>Método Pago</th>
                        <th>Estado</th>
                        <th>Registrado Por</th>
                        <th>Fecha Sistema</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($datos['pagos']) && is_array($datos['pagos']) && !empty($datos['pagos'])):
                        $contador_pagos = 1;
                        foreach ($datos['pagos'] as $pago):
                            if (!is_object($pago)) continue;
                    ?>
                    <tr>
                        <td><?php echo $contador_pagos++; ?></td>
                        <td>
                            <i class="fas fa-calendar-day text-secondary me-1"></i>
                            <?php echo isset($pago->fecha_pago) ? date('d/m/Y', strtotime($pago->fecha_pago)) : 'N/A'; ?>
                        </td>
                        <td class="fw-bold text-success">S/ <?php echo isset($pago->monto_pagado) ? number_format($pago->monto_pagado, 2) : '0.00'; ?></td>
                        <td><strong><?php echo htmlspecialchars($pago->mes_correspondiente ?? 'N/A'); ?></strong></td>
                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($pago->metodo_pago ?? '-'); ?></span></td>
                        <td>
                            <span class="badge <?php echo ($pago->estado_pago == 'Aprobado') ? 'bg-success' : 'bg-warning'; ?>">
                                <?php echo htmlspecialchars($pago->estado_pago ?? '-'); ?>
                            </span>
                        </td>
                        <td><i class="fas fa-user-circle text-muted me-1"></i><?php echo htmlspecialchars($pago->nombre_usuario_registro ?? 'Sistema'); ?></td>
                        <td>
                            <?php
                            // En la BD v5 se llama fecha_sistema, no fecha_registro
                            if (isset($pago->fecha_sistema)) {
                                try {
                                    $fechaUTC = new DateTime($pago->fecha_sistema, new DateTimeZone('UTC'));
                                    $fechaUTC->setTimezone(new DateTimeZone('America/Lima'));
                                    echo '<small class="text-muted">' . $fechaUTC->format('d/m/Y H:i') . '</small>';
                                } catch (Exception $e) {
                                    echo 'N/A';
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-2 text-warning"></i><br>
                                No hay pagos registrados para este cliente todavía.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-file-pdf me-2"></i> Recibos y Contratos Digitales</h5>
        
        <a href="<?php echo RUTA_URL; ?>/clientes/subirRecibo/<?php echo $datos['cliente']->id_cliente; ?>" class="btn btn-light btn-sm fw-bold text-danger">
            <i class="fas fa-upload me-1"></i> Subir Nuevo PDF
        </a>
    </div>
    <div class="card-body bg-light">
        <?php if (isset($datos['recibos']) && is_array($datos['recibos']) && !empty($datos['recibos'])): ?>
            <div class="row">
                <?php foreach ($datos['recibos'] as $r): ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <a href="<?php echo RUTA_URL . '/public/uploads/' . htmlspecialchars($r->nombre_archivo); ?>" 
                           target="_blank" class="text-decoration-none">
                            <div class="card h-100 border-danger shadow-sm border-start border-4 border-end-0 border-top-0 border-bottom-0">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center overflow-hidden">
                                        <i class="fas fa-file-pdf fa-3x text-danger me-3"></i>
                                        <div class="overflow-hidden">
                                            <h6 class="mb-1 text-dark text-truncate" title="<?php echo htmlspecialchars(basename($r->nombre_archivo)); ?>">
                                                <?php 
                                                // Mostrar solo el nombre final del archivo, no toda la ruta 'clientes/ID/recibos/...'
                                                echo htmlspecialchars(basename($r->nombre_archivo)); 
                                                ?>
                                            </h6>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo date('d/m/Y', strtotime($r->fecha_subida)); ?></small>
                                        </div>
                                    </div>
                                    <i class="fas fa-external-link-alt text-secondary ms-2"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <p class="text-muted mb-0"><i class="fas fa-folder-open fa-2x mb-2 text-secondary"></i><br> No hay documentos PDF guardados para este cliente.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>