<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h2 class="fw-bold text-body-emphasis mb-0">
        <i class="fas fa-handshake text-primary me-2"></i> <?php echo htmlspecialchars($datos['titulo'] ?? 'Gestión de Ventas'); ?>
    </h2>
</div>

<?php flash('venta_mensaje'); ?>
<?php flash('cliente_mensaje'); ?>
<?php flash('mensaje_error'); ?>

<ul class="nav nav-tabs border-bottom-0 gap-1" id="ventasTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold border-top-3 rounded-top-4 px-4 py-2.5 shadow-sm" id="solicitudes-tab" data-bs-toggle="tab" data-bs-target="#solicitudes" type="button" role="tab">
            <i class="fas fa-home text-primary me-2"></i> B2C (Residencial)
            <span class="badge bg-primary ms-2 rounded-pill"><?php echo count($datos['solicitudes'] ?? []); ?></span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold border-top-3 rounded-top-4 px-4 py-2.5 shadow-sm" id="cotizaciones-tab" data-bs-toggle="tab" data-bs-target="#cotizaciones" type="button" role="tab">
            <i class="fas fa-building text-secondary me-2"></i> B2B (Empresas)
            <span class="badge bg-secondary ms-2 rounded-pill"><?php echo count($datos['cotizaciones'] ?? []); ?></span>
        </button>
    </li>
</ul>

<div class="tab-content" id="ventasTabContent">
    
    <div class="tab-pane fade show active" id="solicitudes" role="tabpanel">
        <div class="card card-glass-modern border-0 mb-5 rounded-0 rounded-bottom rounded-end">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-body-secondary">
                            <tr>
                                <th class="ps-4 py-3">Fecha / Tipo</th>
                                <th class="py-3">Cliente</th>
                                <th class="py-3">Estado</th>
                                <th class="text-center pe-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($datos['solicitudes'])): ?>
                                <?php foreach($datos['solicitudes'] as $solicitud):
                                    $estado_sol = $solicitud->estado_solicitud ?? 'desconocido';
                                    $estado_sol_norm = strtolower(trim($estado_sol));
                                    $tipo_solicitud = $solicitud->tipo_solicitud ?? 'Instalacion';
                                    
                                    // Cambio de clase estática 'table-light text-muted' a una semántica y segura para Dark Mode
                                    $clase_fila = ($estado_sol_norm === 'instalado') ? 'text-body-secondary bg-body-tertiary' : '';
                                    $opacidad = ($estado_sol_norm === 'instalado') ? 'style="opacity: 0.65;"' : '';
                                ?>
                                    <tr class="<?php echo $clase_fila; ?>" <?php echo $opacidad; ?>>
                                        <td class="ps-4">
                                            <div class="small text-body-secondary"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y H:i', strtotime($solicitud->fecha_solicitud ?? 'now')); ?></div>
                                            <div class="mt-1">
                                                <?php if ($tipo_solicitud === 'Cambio de Plan'): ?>
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="fas fa-exchange-alt me-1"></i>Cambio de Plan</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="fas fa-plus me-1"></i>Nueva Instalación</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold <?php echo ($estado_sol_norm === 'instalado') ? 'text-body-secondary' : 'text-body-emphasis'; ?>">
                                                <?php echo htmlspecialchars(($solicitud->nombres ?? '') . ' ' . ($solicitud->apellidos ?? '')); ?>
                                            </div>
                                            <div class="small text-body-secondary"><i class="fas fa-wifi text-primary me-1"></i> <?php echo htmlspecialchars($solicitud->nombre_plan ?? 'N/A'); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-1.5 
                                                <?php if($estado_sol_norm == 'pendiente') echo 'bg-warning text-dark';
                                                      elseif($estado_sol_norm == 'contactado') echo 'bg-info text-dark';
                                                      elseif($estado_sol_norm == 'con_cobertura') echo 'bg-success';
                                                      elseif($estado_sol_norm == 'sin_cobertura') echo 'bg-danger';
                                                      elseif($estado_sol_norm == 'instalado') echo 'bg-secondary';
                                                      else echo 'bg-dark'; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $estado_sol)); ?>
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill shadow-sm bg-body" data-bs-toggle="modal" data-bs-target="#modalSol_<?php echo $solicitud->id_solicitud; ?>">
                                                <i class="fas fa-eye me-1"></i> <?php echo ($estado_sol_norm === 'instalado') ? 'Ver Histórico' : 'Ver Detalles'; ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-body-secondary py-5">No hay solicitudes residenciales registradas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="cotizaciones" role="tabpanel">
        <div class="card card-glass-modern border-0 mb-5 rounded-0 rounded-bottom rounded-end">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-body-secondary">
                            <tr>
                                <th class="ps-4 py-3">Fecha</th>
                                <th class="py-3">Empresa / RUC</th>
                                <th class="py-3">Estado</th>
                                <th class="text-center pe-4 py-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($datos['cotizaciones'])): ?>
                                <?php foreach($datos['cotizaciones'] as $cotizacion):
                                    $estado_cot = $cotizacion->estado_cotizacion ?? 'pendiente';
                                    $estado_cot_norm = strtolower(trim($estado_cot));
                                    $es_final = in_array($estado_cot_norm, ['instalado', 'instalada', 'convertida', 'convertido']);
                                    
                                    $clase_fila = ($es_final) ? 'text-body-secondary bg-body-tertiary' : '';
                                    $opacidad = ($es_final) ? 'style="opacity: 0.65;"' : '';
                                ?>
                                    <tr class="<?php echo $clase_fila; ?>" <?php echo $opacidad; ?>>
                                        <td class="ps-4">
                                            <div class="small text-body-secondary"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($cotizacion->fecha_solicitud ?? 'now')); ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold <?php echo ($es_final) ? 'text-body-secondary' : 'text-body-emphasis'; ?>">
                                                <?php echo htmlspecialchars($cotizacion->razon_social ?? ''); ?>
                                            </div>
                                            <div class="small text-body-secondary">RUC: <?php echo htmlspecialchars($cotizacion->ruc ?? 'N/A'); ?></div>
                                        </td>
                                        <td>
                                            <span class="badge rounded-pill px-3 py-1.5 <?php if($estado_cot_norm == 'pendiente') echo 'bg-warning text-dark'; elseif($estado_cot_norm == 'contactado') echo 'bg-info text-dark'; elseif($estado_cot_norm == 'enviada') echo 'bg-primary'; elseif($estado_cot_norm == 'ganada') echo 'bg-success'; elseif($estado_cot_norm == 'perdida') echo 'bg-danger'; else echo 'bg-secondary'; ?>">
                                                <?php echo ucfirst($estado_cot); ?>
                                            </span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <button type="button" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill shadow-sm bg-body" data-bs-toggle="modal" data-bs-target="#modalCot_<?php echo $cotizacion->id_cotizacion; ?>">
                                                <i class="fas fa-eye me-1"></i> <?php echo ($es_final) ? 'Ver Histórico' : 'Ver Detalles'; ?>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-body-secondary py-5">No hay cotizaciones B2B registradas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($datos['solicitudes'])): ?>
    <?php foreach($datos['solicitudes'] as $solicitud):
        $estado_sol = $solicitud->estado_solicitud ?? 'desconocido';
        $estado_sol_norm = strtolower(trim($estado_sol));
        $tipo_solicitud = $solicitud->tipo_solicitud ?? 'Instalacion';
    ?>
        <div class="modal fade" id="modalSol_<?php echo $solicitud->id_solicitud; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="fas fa-file-alt me-2"></i> Detalles de la Solicitud</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-body-tertiary p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="bg-body p-3 rounded-3 border h-100 shadow-2xs">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-user me-1"></i> Datos del Cliente</h6>
                                    <p class="mb-2 text-body-emphasis"><strong>Nombre:</strong> <?php echo htmlspecialchars(($solicitud->nombres ?? '') . ' ' . ($solicitud->apellidos ?? '')); ?></p>
                                    <p class="mb-2 text-body-emphasis"><strong>Teléfono:</strong> <?php echo htmlspecialchars($solicitud->telefono ?? 'N/A'); ?></p>
                                    <p class="mb-0 text-body-emphasis"><strong>DNI:</strong> <?php echo htmlspecialchars($solicitud->documento_identidad ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-body p-3 rounded-3 border h-100 shadow-2xs">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-cog me-1"></i> Servicio Requerido</h6>
                                    <p class="mb-2 text-primary fw-bold fs-5"><i class="fas fa-wifi me-1"></i> <?php echo htmlspecialchars($solicitud->nombre_plan ?? 'N/A'); ?></p>
                                    <p class="mb-0 text-body-emphasis"><strong>Tipo:</strong> <?php echo htmlspecialchars($tipo_solicitud); ?></p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="bg-body p-3 rounded-3 border shadow-2xs">
                                    <h6 class="text-primary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-map-marked-alt me-1"></i> Ubicación</h6>
                                    <p class="mb-2 text-body-emphasis"><strong>Dirección:</strong> <?php echo htmlspecialchars($solicitud->direccion_calle ?? 'N/A'); ?></p>
                                    <p class="mb-2 text-body-emphasis"><strong>Distrito / Provincia:</strong> <?php echo htmlspecialchars($solicitud->distrito ?? ''); ?>, <?php echo htmlspecialchars($solicitud->provincia ?? ''); ?></p>
                                    <p class="mb-0 text-body-emphasis"><strong>Referencia:</strong> <?php echo htmlspecialchars($solicitud->referencia ?? 'N/A'); ?></p>
                                    <?php if(!empty($solicitud->location_link)): ?>
                                        <a href="<?php echo htmlspecialchars($solicitud->location_link); ?>" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 mt-3 fw-bold shadow-sm"><i class="fas fa-map-marker-alt me-1"></i> Abrir Mapa Real</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-body border-0 px-4 pb-4 pt-0 d-flex justify-content-between flex-wrap gap-2">
                        <?php if ($estado_sol_norm !== 'instalado'): ?>
                            <form action="<?php echo RUTA_URL; ?>/ventas/cambiarEstadoSolicitud/<?php echo $solicitud->id_solicitud; ?>" method="POST" class="d-flex align-items-center gap-2">
                                <select name="estado_solicitud" class="form-select form-select-sm fw-bold bg-body border shadow-none" style="min-width: 160px;">
                                    <option value="pendiente"     <?php if($estado_sol_norm=='pendiente') echo 'selected'; ?>>Pendiente</option>
                                    <option value="contactado"    <?php if($estado_sol_norm=='contactado') echo 'selected'; ?>>Contactado</option>
                                    <option value="con_cobertura" <?php if($estado_sol_norm=='con_cobertura') echo 'selected'; ?>>Con Cobertura</option>
                                    <option value="sin_cobertura" <?php if($estado_sol_norm=='sin_cobertura') echo 'selected'; ?>>Sin Cobertura</option>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm rounded-pill px-3 fw-bold shadow-sm">Actualizar</button>
                            </form>

                            <?php if ($estado_sol_norm == 'con_cobertura'): ?>
                                <form action="<?php echo RUTA_URL; ?>/ventas/convertir/<?php echo $solicitud->id_solicitud; ?>" method="POST" class="m-0">
                                    <?php if ($tipo_solicitud === 'Cambio de Plan'): ?>
                                        <button type="submit" class="btn btn-info fw-bold text-dark rounded-pill px-4 shadow-sm" onclick="return confirm('¿Aprobar el CAMBIO DE PLAN para este cliente?');">
                                            <i class="fas fa-check me-1"></i> Aprobar Cambio de Plan
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm" onclick="return confirm('¿Convertir esta solicitud en cliente oficial e iniciar instalación?');">
                                            <i class="fas fa-user-plus me-1"></i> Convertir a Cliente
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="w-100 text-center"><span class="badge bg-secondary-subtle text-body border px-4 py-2 fs-7 rounded-pill"><i class="fas fa-check-double text-success me-2"></i> Gestionado / Instalado en Sistema</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($datos['cotizaciones'])): ?>
    <?php foreach($datos['cotizaciones'] as $cotizacion):
        $estado_cot = $cotizacion->estado_cotizacion ?? 'pendiente';
        $estado_cot_norm = strtolower(trim($estado_cot));
        $es_final = in_array($estado_cot_norm, ['instalado', 'instalada', 'convertida', 'convertido']);
    ?>
        <div class="modal fade" id="modalCot_<?php echo $cotizacion->id_cotizacion; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-secondary text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="fas fa-building me-2"></i> Cotización Corporativa B2B</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-body-tertiary p-4">
                        <div class="bg-body p-4 rounded-3 border shadow-2xs">
                            <h6 class="text-secondary fw-bold border-bottom pb-2 mb-3"><i class="fas fa-server me-1"></i> Información Comercial</h6>
                            <p class="mb-2 text-body-emphasis fs-5"><strong>Empresa:</strong> <?php echo htmlspecialchars($cotizacion->razon_social ?? ''); ?></p>
                            <p class="mb-2 text-body-emphasis"><strong>RUC corporativo:</strong> <span class="badge bg-secondary-subtle text-body font-monospace px-3"><?php echo htmlspecialchars($cotizacion->ruc ?? 'N/A'); ?></span></p>
                            <hr class="my-3 text-secondary border-opacity-10">
                            <p class="mb-2 text-body-emphasis"><strong>Persona de Contacto:</strong> <?php echo htmlspecialchars($cotizacion->persona_contacto ?? 'N/A'); ?></p>
                            <p class="mb-2 text-body-emphasis"><strong>Teléfono Directo:</strong> <?php echo htmlspecialchars($cotizacion->telefono_contacto ?? 'N/A'); ?></p>
                            <p class="mb-0 text-body-emphasis"><strong>Dirección Fiscal / Sede:</strong> <?php echo htmlspecialchars($cotizacion->direccion_calle ?? ''); ?>, <?php echo htmlspecialchars($cotizacion->distrito ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="modal-footer bg-body border-0 px-4 pb-4 pt-0 d-flex justify-content-between flex-wrap gap-2">
                        <?php if (!$es_final): ?>
                            <form action="<?php echo RUTA_URL; ?>/ventas/cambiarEstadoCotizacion/<?php echo $cotizacion->id_cotizacion; ?>" method="POST" class="d-flex align-items-center gap-2">
                                <select name="estado_cotizacion" class="form-select form-select-sm fw-bold bg-body border shadow-none" style="min-width: 160px;">
                                    <option value="pendiente" <?php if($estado_cot_norm=='pendiente') echo 'selected'; ?>>Pendiente</option>
                                    <option value="contactado" <?php if($estado_cot_norm=='contactado') echo 'selected'; ?>>Contactado</option>
                                    <option value="enviada" <?php if($estado_cot_norm=='enviada') echo 'selected'; ?>>Enviada</option>
                                    <option value="ganada" <?php if($estado_cot_norm=='ganada') echo 'selected'; ?>>Ganada</option>
                                    <option value="perdida" <?php if($estado_cot_norm=='perdida') echo 'selected'; ?>>Perdida</option>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm rounded-pill px-3 fw-bold shadow-sm">Actualizar</button>
                            </form>
                            
                            <?php if ($estado_cot_norm == 'ganada'): ?>
                                <form action="<?php echo RUTA_URL; ?>/ventas/convertirCotizacion/<?php echo $cotizacion->id_cotizacion; ?>" method="POST" class="m-0">
                                    <button type="submit" class="btn btn-success fw-bold rounded-pill px-4 shadow-sm" onclick="return confirm('¿Convertir esta cotización en Cliente Corporativo?');">
                                        <i class="fas fa-building me-1"></i> Convertir a Cliente B2B
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="w-100 text-center"><span class="badge bg-secondary-subtle text-body border px-4 py-2 fs-7 rounded-pill"><i class="fas fa-check-double text-success me-2"></i> Lead Convertido / Portafolio Corporativo Activo</span></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>