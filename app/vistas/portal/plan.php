<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Mi Plan</h1>
  <a href="<?php echo RUTA_URL; ?>/portal/cambiarPlan" class="btn btn-warning"><i class="fas fa-exchange-alt"></i> Solicitar cambio de plan</a>
</div>

<?php if (isset($datos['cliente']) && is_object($datos['cliente'])): $c=$datos['cliente']; ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <p><strong>Plan:</strong> <?php echo htmlspecialchars($c->nombre_plan ?? 'Sin plan'); ?></p>
      <p><strong>Estado Servicio:</strong> <?php echo htmlspecialchars($c->estado_servicio ?? ''); ?></p>
      <p><strong>Estado Pago:</strong> <?php echo htmlspecialchars($c->estado_pago ?? ''); ?></p>
      <p><strong>Fecha Instalación:</strong> <?php echo htmlspecialchars($c->fecha_instalacion ?? ''); ?></p>
    </div>
  </div>
<?php endif; ?>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>
