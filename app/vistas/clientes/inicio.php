<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold text-body-emphasis mb-0">
            <i class="fas fa-users text-primary me-2"></i> <?php echo htmlspecialchars($datos['titulo'] ?? 'Gestión de Clientes'); ?>
        </h2>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
        <a href="<?php echo RUTA_URL; ?>/clientes/agregar" class="btn btn-primary fw-bold shadow-sm rounded-pill px-4">
            <i class="fas fa-user-plus me-1"></i> Nuevo Cliente
        </a>
        
        <button type="button" class="btn btn-outline-secondary fw-bold bg-body shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAccionesMasivas">
            <i class="fas fa-bolt text-warning me-1"></i> Acciones Masivas
        </button>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12 col-lg-6">
        <form action="<?php echo RUTA_URL; ?>/clientes" method="POST">
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-body border">
                <span class="input-group-text bg-transparent border-0 text-primary ps-4">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="busqueda" class="form-control border-0 bg-transparent shadow-none" 
                       placeholder="Buscar por Nombre, Apellido o DNI..." 
                       value="<?php echo htmlspecialchars($datos['busqueda'] ?? ''); ?>">
                
                <button class="btn btn-primary px-4 fw-bold rounded-pill m-1" type="submit">Buscar</button>
                
                <?php if(!empty($datos['busqueda'])): ?>
                    <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-danger px-3 rounded-circle m-1 d-flex align-items-center justify-content-center" style="width:38px; height:38px;" title="Limpiar búsqueda">
                        <i class="fas fa-times"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<?php flash('cliente_mensaje'); ?>
<?php flash('mensaje_error'); ?>

<div class="card card-glass-modern border-0 mb-5">
    <div class="card-body p-0">
        <div class="table-responsive"> 
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th class="ps-4 py-3">Cliente</th>
                        <th class="py-3">Ubicación y GPS</th>
                        <th class="py-3">Servicio Contratado</th>
                        <th class="py-3">Estado Pago</th>
                        <th class="text-center pe-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($datos['clientes']) && is_array($datos['clientes'])): ?>
                        <?php foreach($datos['clientes'] as $cliente): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-body-emphasis"><?php echo htmlspecialchars(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')); ?></div>
                                <div class="text-body-secondary small"><i class="far fa-id-card me-1"></i> <?php echo htmlspecialchars($cliente->dni ?? 'N/A'); ?></div>
                            </td>
                            
                            <td>
                                <div class="text-body-emphasis text-truncate fw-bold" style="max-width: 200px;" title="<?php echo htmlspecialchars($cliente->direccion_calle ?? 'N/A'); ?>">
                                    <?php echo htmlspecialchars($cliente->direccion_calle ?? 'N/A'); ?>
                                </div>
                                <div class="text-body-secondary small mb-1">
                                    <i class="fas fa-map-marker-alt text-danger me-1"></i> 
                                    <?php echo htmlspecialchars($cliente->distrito ?? 'Sin Distrito'); ?>, 
                                    <?php echo htmlspecialchars($cliente->provincia ?? 'Sin Provincia'); ?>
                                </div>
                                
                                <?php if(!empty($cliente->location_link)): ?>
                                    <?php 
                                        $link_mapa = trim($cliente->location_link);
                                        if (strpos($link_mapa, 'http') === false) {
                                            $link_mapa = "https://www.google.com/maps?q=" . urlencode($link_mapa);
                                        }
                                    ?>
                                    <a href="<?php echo htmlspecialchars($link_mapa); ?>" target="_blank" class="badge bg-danger-subtle text-danger border border-danger-subtle text-decoration-none p-1 shadow-sm">
                                        <i class="fas fa-map-marked-alt me-1"></i> Mapa Real
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-body-secondary border p-1"><i class="fas fa-map-marker-slash me-1"></i> Sin GPS</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <div class="fw-bold text-primary"><?php echo htmlspecialchars($cliente->nombre_plan ?? 'Sin Plan'); ?></div>
                                <?php $estado_servicio = $cliente->estado_servicio ?? 'Desconocido'; ?>
                                <span class="badge rounded-pill <?php echo $estado_servicio === 'Activo' ? 'bg-success bg-opacity-10 text-success' : ($estado_servicio === 'Suspendido' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-secondary bg-opacity-10 text-secondary'); ?> border-0">
                                    <i class="fas fa-circle" style="font-size: 8px; vertical-align: middle;"></i> <?php echo htmlspecialchars($estado_servicio); ?>
                                </span>
                            </td>
                            
                            <td>
                                <?php $estado_pago = $cliente->estado_pago ?? 'N/A'; ?>
                                <span class="badge rounded-pill px-3 py-2 <?php echo $estado_pago === 'Al día' ? 'bg-success' : ($estado_pago === 'Pendiente' ? 'bg-warning text-dark' : ($estado_pago === 'Vencido' ? 'bg-danger' : 'bg-secondary')); ?>">
                                    <?php echo htmlspecialchars($estado_pago); ?>
                                </span>
                            </td>
                            
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm fw-bold bg-body" data-bs-toggle="modal" data-bs-target="#modalOpciones_<?php echo $cliente->id_cliente; ?>">
                                    <i class="fas fa-cog"></i> Opciones
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-body-secondary py-5">
                                <?php echo !empty($datos['busqueda']) ? 'No se encontraron clientes con esa búsqueda.' : 'No hay clientes registrados en el sistema.'; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($datos['total_paginas']) && $datos['total_paginas'] > 1): ?>
            <div class="card-footer bg-transparent border-top-0 pt-4 pb-3 d-flex justify-content-center">
                <nav aria-label="Paginación de clientes">
                    <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden m-0">
                        <li class="page-item <?php echo ($datos['pagina_actual'] <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo RUTA_URL; ?>/clientes?pagina=<?php echo $datos['pagina_actual'] - 1; ?>&busqueda=<?php echo urlencode($datos['busqueda'] ?? ''); ?>">Anterior</a>
                        </li>
                        <?php for ($i = 1; $i <= $datos['total_paginas']; $i++): ?>
                            <li class="page-item <?php echo ($datos['pagina_actual'] == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="<?php echo RUTA_URL; ?>/clientes?pagina=<?php echo $i; ?>&busqueda=<?php echo urlencode($datos['busqueda'] ?? ''); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($datos['pagina_actual'] >= $datos['total_paginas']) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo RUTA_URL; ?>/clientes?pagina=<?php echo $datos['pagina_actual'] + 1; ?>&busqueda=<?php echo urlencode($datos['busqueda'] ?? ''); ?>">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php if (!empty($datos['clientes']) && is_array($datos['clientes'])): ?>
    <?php foreach($datos['clientes'] as $cliente): ?>
        <?php $estado_pago = $cliente->estado_pago ?? 'N/A'; ?>
        
        <div class="modal fade" id="modalOpciones_<?php echo $cliente->id_cliente; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-body-tertiary border-bottom-0 pb-0">
                        <h6 class="modal-title fw-bold text-body-emphasis text-truncate"><i class="fas fa-user-circle text-primary me-2"></i> <?php echo htmlspecialchars($cliente->nombre ?? ''); ?></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2 pb-4 px-4 d-grid gap-2">
                        
                        <button type="button" class="btn btn-outline-success fw-bold text-start mt-2" data-bs-dismiss="modal" onclick="abrirModalPago(<?php echo $cliente->id_cliente; ?>, '<?php echo htmlspecialchars(addslashes(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''))); ?>')">
                            <i class="fas fa-dollar-sign text-center me-2" style="width:20px;"></i> Registrar Pago
                        </button>
                        
                        <a class="btn btn-outline-info fw-bold text-start" href="<?php echo RUTA_URL; ?>/clientes/historialPagos/<?php echo $cliente->id_cliente; ?>">
                            <i class="fas fa-history text-center me-2" style="width:20px;"></i> Perfil / Historial
                        </a>
                        
                        <a class="btn btn-outline-danger fw-bold text-start" href="<?php echo RUTA_URL; ?>/recibos/cliente/<?php echo $cliente->id_cliente; ?>">
                            <i class="fas fa-file-pdf text-center me-2" style="width:20px;"></i> Recibos (PDF)
                        </a>
                        
                        <a class="btn btn-outline-warning fw-bold text-start" href="<?php echo RUTA_URL; ?>/clientes/editar/<?php echo $cliente->id_cliente; ?>">
                            <i class="fas fa-edit text-center me-2" style="width:20px;"></i> Editar Cliente
                        </a>

                        <hr class="my-2 text-secondary">
                        <small class="text-body-secondary fw-bold mb-1">Cambiar Estado de Pago:</small>

                        <?php if ($estado_pago == 'Al día'): ?>
                            <form action="<?php echo RUTA_URL; ?>/clientes/marcarEstadoPago/<?php echo $cliente->id_cliente; ?>/Pendiente" method="POST" onsubmit="return confirm('¿Marcar como Pendiente?')">
                                <button type="submit" class="btn btn-secondary w-100 text-start fw-bold"><i class="fas fa-exclamation-triangle text-center me-2" style="width:20px;"></i> Marcar Pendiente</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($estado_pago == 'Al día' || $estado_pago == 'Pendiente'): ?>
                            <form action="<?php echo RUTA_URL; ?>/clientes/marcarEstadoPago/<?php echo $cliente->id_cliente; ?>/Vencido" method="POST" onsubmit="return confirm('¿Marcar como VENCIDO?')">
                                <button type="submit" class="btn btn-danger w-100 text-start fw-bold"><i class="fas fa-times-circle text-center me-2" style="width:20px;"></i> Marcar Vencido</button>
                            </form>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>


<div class="modal fade" id="modalAccionesMasivas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-body-tertiary">
        <h6 class="modal-title fw-bold text-body-emphasis"><i class="fas fa-bolt text-warning me-2"></i> Acciones Masivas</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 d-grid gap-3">
        <form action="<?php echo RUTA_URL; ?>/clientes/notificarTodos" method="POST">
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm" onclick="return confirm('¿Enviar notificación a TODOS los clientes?');">
                <i class="fas fa-bullhorn mb-2 fa-2x d-block"></i> Notificar a Todos
            </button>
        </form>
        <form action="<?php echo RUTA_URL; ?>/clientes/notificarMorosos" method="POST">
            <button type="submit" class="btn btn-danger w-100 py-3 fw-bold shadow-sm" onclick="return confirm('¿Enviar alerta de deuda a todos los morosos?');">
                <i class="fas fa-exclamation-circle mb-2 fa-2x d-block"></i> Notificar Morosos
            </button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title fw-bold" id="modalRegistrarPagoLabel"><i class="fas fa-hand-holding-usd me-2"></i> Registrar Pago</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form id="formRegistrarPago" action="" method="POST">
          <div class="modal-body bg-body-tertiary">
            <h6 class="text-center text-body-secondary mb-3">Cliente: <strong id="nombreClienteModal" class="text-body-emphasis fs-5"></strong></h6>
            <div class="mb-3">
              <label class="form-label fw-bold">Fecha del Pago:</label>
              <input type="date" class="form-control border bg-body" id="fecha_pago" name="fecha_pago" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Monto Pagado (S/):</label>
              <div class="input-group">
                  <span class="input-group-text bg-body border-end-0">S/</span>
                  <input type="number" step="0.01" min="1" class="form-control bg-body border-start-0 ps-0" id="monto_pagado" name="monto_pagado" placeholder="Ej: 50.00" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Mes Correspondiente:</label>
              <select name="mes_correspondiente" class="form-select bg-body border" required>
                  <option value="">-- Seleccione el mes --</option>
                  <?php 
                  $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                  foreach($meses as $mes): ?>
                      <option value="<?php echo $mes . ' ' . date('Y'); ?>"><?php echo $mes . ' ' . date('Y'); ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Método de Pago:</label>
              <select name="id_tipo_pago" class="form-select bg-body border" required>
                  <option value="">-- Seleccione un método --</option>
                  <?php if(!empty($datos['tipos_pago'])): ?>
                      <?php foreach($datos['tipos_pago'] as $tipo): ?>
                          <option value="<?php echo $tipo->id_tipo_pago; ?>"><?php echo htmlspecialchars($tipo->nombre_tipo); ?></option>
                      <?php endforeach; ?>
                  <?php else: ?>
                      <option value="1">Efectivo</option>
                      <option value="2">Transferencia</option>
                      <option value="3">Yape / Plin</option>
                  <?php endif; ?>
              </select>
            </div>
          </div>
          <div class="modal-footer bg-body border-top-0 pt-0">
            <button type="button" class="btn btn-outline-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-save me-1"></i> Confirmar y Notificar</button>
          </div>
        </form>
      </div>
  </div>
</div>

<script>
function abrirModalPago(idCliente, nombreCliente) {
    document.getElementById('nombreClienteModal').innerText = nombreCliente;
    const form = document.getElementById('formRegistrarPago');
    form.action = '<?php echo RUTA_URL; ?>/clientes/registrarPago/' + idCliente;
    form.reset();
    document.getElementById('fecha_pago').value = new Date().toISOString().split('T')[0];
    const modal = new bootstrap.Modal(document.getElementById('modalRegistrarPago'));
    modal.show();
}
</script>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>