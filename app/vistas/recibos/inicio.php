<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1>Recibos del Cliente #<?php echo (int)$datos['id_cliente']; ?></h1>
  <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<?php if (hasRole(['Administrador','Pagos'])): ?>
<form action="<?php echo RUTA_URL; ?>/recibos/subir/<?php echo (int)$datos['id_cliente']; ?>" method="POST" enctype="multipart/form-data" class="card card-body shadow-sm mb-3">
  <div class="row g-2 align-items-center">
    <div class="col-md-8">
      <input type="file" name="pdf" accept="application/pdf" class="form-control" required>
    </div>
    <div class="col-md-4 text-end">
      <button class="btn btn-primary w-100"><i class="fas fa-upload me-1"></i> Subir PDF</button>
    </div>
  </div>
</form>
<?php endif; ?>

<div class="list-group">
  <?php if (!empty($datos['recibos'])): foreach($datos['recibos'] as $r): ?>
  <div class="list-group-item d-flex justify-content-between align-items-center">
    <a href="<?php echo $r['url']; ?>" target="_blank"><i class="fas fa-file-pdf me-2"></i><?php echo htmlspecialchars($r['name']); ?></a>
    <?php if (hasRole(['Administrador','Pagos'])): ?>
      <form action="<?php echo RUTA_URL; ?>/recibos/borrar/<?php echo (int)$datos['id_cliente']; ?>" method="POST" onsubmit="return confirm('¿Eliminar este recibo?');">
        <input type="hidden" name="file" value="<?php echo htmlspecialchars($r['name']); ?>">
        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
      </form>
    <?php endif; ?>
  </div>
  <?php endforeach; else: ?>
  <div class="text-muted">Sin recibos.</div>
  <?php endif; ?>
</div>
<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>
