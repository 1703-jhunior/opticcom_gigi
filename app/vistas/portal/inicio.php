<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container mt-4 mb-5">

    <h2 class="mb-4 text-center text-dark fw-bold">
        Hola, <?php echo htmlspecialchars($_SESSION['nombre_cliente'] ?? 'Cliente'); ?>
    </h2>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fas fa-satellite-dish me-2"></i> Mi Plan
        </div>
        <div class="card-body text-dark">
            <?php if (isset($datos['plan']) && $datos['plan']): ?>
                <p><strong>Plan Actual:</strong> <?php echo htmlspecialchars($datos['plan']->nombre_plan ?? 'N/A'); ?></p>
                <p><strong>Velocidad:</strong> <?php echo htmlspecialchars($datos['plan']->velocidad ?? 'N/A'); ?> Mbps</p>
                <p><strong>Precio Mensual:</strong> S/ <?php echo number_format($datos['plan']->precio_mensual ?? 0, 2); ?></p>
                <p><strong>Estado del Servicio:</strong>
                    <span class="badge bg-<?php echo ($datos['cliente']->estado_servicio ?? '') === 'Activo' ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($datos['cliente']->estado_servicio ?? 'Desconocido'); ?>
                    </span>
                </p>
            <?php else: ?>
                <p class="text-muted">No se pudo cargar la información de tu plan.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-success text-white fw-bold">
            <i class="fas fa-money-bill-wave me-2"></i> Mis Pagos Recientes
        </div>
        <div class="card-body">
            <?php if (!empty($datos['pagos'])): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle text-center text-dark">
                        <thead class="table-light">
                            <tr class="text-dark">
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Mes Correspondiente</th>
                                <th>Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($datos['pagos'] as $pago): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($pago->fecha_pago)); ?></td>
                                    <td class="fw-bold">S/ <?php echo number_format($pago->monto_pagado, 2); ?></td>
                                    <td><?php echo htmlspecialchars($pago->mes_correspondiente ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($pago->metodo_pago ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">Aún no hay pagos registrados.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-dark text-white fw-bold">
        <i class="fas fa-file-pdf me-2"></i> Mis Recibos (PDF)
      </div>
      <div class="card-body">
        <?php if (!empty($datos['recibos'])): ?>
          <ul class="list-group">
            <?php foreach($datos['recibos'] as $r): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center text-dark">
                <span class="fw-medium"><?php echo htmlspecialchars($r->nombre_archivo ?? 'Recibo'); ?></span>
                
                <a
                  href="<?php echo RUTA_URL; ?>/recibos/descargar/<?php echo rawurlencode($r->nombre_archivo); ?>"
                  target="_blank"
                  class="btn btn-danger btn-sm shadow-sm">
                  <i class="fas fa-download me-1"></i> Descargar
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-muted mb-0">No hay recibos disponibles aún.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body bg-light text-center rounded-3">
            <h5 class="fw-bold text-dark mb-3">Gestionar mi Servicio</h5>
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="<?php echo RUTA_URL; ?>/portal/cambiarPlan" class="btn btn-primary btn-lg px-4 shadow-sm">
                    <i class="fas fa-exchange-alt me-1"></i> Cambiar de Plan
                </a>
                <a href="https://wa.me/51918845960?text=Hola%20OPTICCOM,%20quisiera%20consultar%20sobre%20mi%20servicio." 
                   target="_blank" class="btn btn-success btn-lg px-4 shadow-sm">
                    <i class="fab fa-whatsapp me-1"></i> Contactar Soporte
                </a>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="<?php echo RUTA_URL; ?>/portal/logout" class="btn btn-outline-danger px-4 rounded-pill">
            <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
        </a>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>