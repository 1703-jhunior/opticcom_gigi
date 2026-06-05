<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row mt-3 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold text-body-emphasis mb-0"><i class="fas fa-check-circle text-success me-2"></i> Reporte de Campo Finalizado</h2>
        <a href="<?php echo RUTA_URL; ?>/ordenes" class="btn btn-outline-secondary bg-body fw-bold rounded-pill px-4 shadow-sm"><i class="fas fa-arrow-left me-1"></i> Volver</a>
    </div>
</div>

<?php if(empty($datos['reporte'])): ?>
    <div class="alert alert-warning border-0 shadow-sm rounded-4">El técnico finalizó la orden pero no envió los datos del formulario.</div>
<?php else: ?>
    <?php $rep = $datos['reporte']; ?>
    <div class="row">
        
        <div class="col-md-6 mb-4">
            <div class="card card-glass-modern h-100 border-0">
                <div class="card-header bg-dark text-white fw-bold border-0 py-3">
                    <i class="fas fa-network-wired me-2"></i> Parámetros Técnicos
                </div>
                <ul class="list-group list-group-flush bg-transparent">
                    <li class="list-group-item bg-transparent d-flex justify-content-between border-light border-opacity-10 py-3">
                        <span class="text-body-secondary"><strong>Serie ONU (Router):</strong></span>
                        <span class="text-primary fw-bold"><?php echo htmlspecialchars($rep->serie_onu ?? '-'); ?></span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between border-light border-opacity-10 py-3">
                        <span class="text-body-secondary"><strong>Caja NAP y Puerto:</strong></span>
                        <span class="text-body-emphasis fw-bold"><?php echo htmlspecialchars($rep->codigo_nap ?? '-'); ?> / Puerto <?php echo htmlspecialchars($rep->puerto_nap ?? '-'); ?></span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between border-light border-opacity-10 py-3">
                        <span class="text-body-secondary"><strong>Potencia Óptica:</strong></span>
                        <span class="badge bg-<?php echo (floatval($rep->potance_optica ?? $rep->potencia_optica) < -25) ? 'danger' : 'success'; ?> px-3 py-2 fs-7 rounded-pill">
                            <?php echo htmlspecialchars($rep->potance_optica ?? $rep->potencia_optica ?? '0'); ?> dBm
                        </span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between border-light border-opacity-10 py-3">
                        <span class="text-body-secondary"><strong>Cable Utilizado:</strong></span>
                        <span class="text-body-emphasis fw-bold"><?php echo htmlspecialchars($rep->metros_cable_usado ?? '0'); ?> Metros</span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between border-light border-opacity-10 py-3">
                        <span class="text-body-secondary"><strong>Conectores Usados:</strong></span>
                        <span class="text-body-emphasis fw-bold"><?php echo htmlspecialchars($rep->conectores_usados ?? '0'); ?></span>
                    </li>
                    <li class="list-group-item bg-transparent d-flex justify-content-between border-0 py-3">
                        <span class="text-body-secondary"><strong>Ubicación GPS Exacta:</strong></span>
                        <a href="https://www.google.com/maps?q=<?php echo $rep->coordenadas_lat . ',' . $rep->coordenadas_lon; ?>" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                            <i class="fas fa-map-marker-alt me-1"></i> Ver en Mapa
                        </a>
                    </li>
                </ul>
                <div class="card-body bg-body-tertiary border-top p-4">
                    <strong class="text-body-emphasis d-block mb-1">Observaciones del Técnico:</strong>
                    <p class="mb-0 text-body-secondary fs-7"><?php echo nl2br(htmlspecialchars($rep->observaciones_tecnico ?? 'Ninguna observación.')); ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card card-glass-modern h-100 border-0">
                <div class="card-header bg-dark text-white fw-bold border-0 py-3">
                    <i class="fas fa-camera me-2"></i> Evidencias y Conformidad
                </div>
                <div class="card-body p-4 bg-body-tertiary">
                    <h6 class="text-body-emphasis fw-bold mb-3 border-bottom border-secondary border-opacity-10 pb-2">Fotografías en Sitio</h6>
                    <div class="row g-3">
                        <?php if(!empty($datos['fotos']) && is_array($datos['fotos'])): ?>
                            <?php foreach($datos['fotos'] as $foto): ?>
                                <?php 
                                    // Solución Quirúrgica de Rutas de Archivos: Limpieza de prefijos recursivos
                                    $nombre_archivo = htmlspecialchars($foto->url_foto);
                                    $nombre_archivo = str_replace(['public/', 'uploads/'], '', $nombre_archivo);
                                    $ruta_final = RUTA_URL . '/public/uploads/' . $nombre_archivo;
                                ?>
                                <div class="col-6 text-center">
                                    <div class="border rounded-3 p-2 bg-body shadow-sm">
                                        <span class="badge bg-secondary rounded-pill mb-2 px-2 py-1 fs-8"><?php echo htmlspecialchars($foto->tipo_foto); ?></span><br>
                                        <a href="<?php echo $ruta_final; ?>" target="_blank" title="Ver imagen completa">
                                            <img src="<?php echo $ruta_final; ?>" class="img-fluid rounded-2 shadow-2xs" style="max-height: 140px; min-height: 140px; object-fit: cover; width: 100%;" alt="<?php echo htmlspecialchars($foto->tipo_foto); ?>" onerror="this.src='<?php echo RUTA_URL; ?>/public/img/no-image.png';">
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center text-body-secondary py-4 bg-body border rounded-3">
                                <i class="fas fa-image fa-2x mb-2 text-muted"></i><br>No se subieron o procesaron fotos de campo.
                            </div>
                        <?php endif; ?>
                    </div>

                    <h6 class="text-body-emphasis fw-bold mt-4 mb-3 border-bottom border-secondary border-opacity-10 pb-2">Firma Digital del Cliente</h6>
                    <div class="text-center border rounded-3 bg-body p-3 shadow-sm" style="height: 140px; display: flex; align-items: center; justify-content: center;">
                        <?php if (!empty($rep->firma_cliente_url)): ?>
                            <?php 
                                $archivo_firma = str_replace(['public/', 'uploads/'], '', htmlspecialchars($rep->firma_cliente_url));
                                $ruta_firma = RUTA_URL . '/public/uploads/' . $archivo_firma;
                            ?>
                            <img src="<?php echo $ruta_firma; ?>" alt="Firma de Conformidad" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                        <?php else: ?>
                            <div class="text-body-secondary"><i class="fas fa-signature fa-2x mb-1 text-muted"></i><br><small>Sin firma registrada</small></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
<?php endif; ?>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>