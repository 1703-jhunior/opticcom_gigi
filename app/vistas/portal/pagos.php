<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>
<h1>Mis Pagos</h1>
<div class="card shadow-sm">
  <div class="card-body table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-light">
        <tr><th>#</th><th>Fecha</th><th>Monto (S/)</th><th>Mes</th><th>Método</th></tr>
      </thead>
      <tbody>
        <?php if (!empty($datos['pagos'])): $i=1; foreach($datos['pagos'] as $p): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><?php echo isset($p->fecha_pago) ? date('d/m/Y', strtotime($p->fecha_pago)) : 'N/A'; ?></td>
          <td><?php echo isset($p->monto_pagado) ? number_format($p->monto_pagado, 2) : '0.00'; ?></td>
          <td><?php echo htmlspecialchars($p->mes_correspondiente ?? ''); ?></td>
          <td><?php echo htmlspecialchars($p->metodo_pago ?? '-'); ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="5" class="text-center text-muted py-3">Aún no hay pagos registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>
