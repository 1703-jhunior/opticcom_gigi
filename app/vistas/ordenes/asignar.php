<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-glass-modern border-0 mt-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center border-0 py-3">
                <h4 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i> <?php echo htmlspecialchars($datos['titulo']); ?></h4>
                <a href="<?php echo RUTA_URL; ?>/ordenes" class="btn btn-sm btn-light rounded-pill px-3fw-bold shadow-sm"><i class="fas fa-arrow-left me-1"></i> Volver</a>
            </div>
            <div class="card-body p-4 bg-body-tertiary">
                <form action="<?php echo RUTA_URL; ?>/ordenes/asignar" method="POST">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-body-emphasis">1. Seleccionar Cliente: <span class="text-danger">*</span></label>
                        <select name="id_cliente" class="form-select bg-body border shadow-none rounded-3" required>
                            <option value="">-- Buscar cliente pendiente --</option>
                            <?php foreach($datos['clientes'] as $cliente): ?>
                                <option value="<?php echo $cliente->id_cliente; ?>">
                                    <?php echo htmlspecialchars($cliente->nombre . ' ' . $cliente->apellido . ' (DNI: ' . $cliente->dni . ') - ' . $cliente->direccion_calle); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-body-emphasis">2. Asignar Técnico: <span class="text-danger">*</span></label>
                            <select name="id_tecnico" class="form-select bg-body border shadow-none rounded-3" required>
                                <option value="">-- Seleccione un Técnico --</option>
                                <?php foreach($datos['tecnicos'] as $tecnico): ?>
                                    <?php if($tecnico->id_usuario != 1): ?>
                                        <option value="<?php echo $tecnico->id_usuario; ?>">
                                            <?php echo htmlspecialchars($tecnico->nombre); ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-body-emphasis">3. Tipo de Trabajo: <span class="text-danger">*</span></label>
                            <select name="id_tipo_orden" class="form-select bg-body border shadow-none rounded-3" required>
                                <option value="">-- Seleccione Tipo --</option>
                                <?php foreach($datos['tipos_orden'] as $tipo): ?>
                                    <option value="<?php echo $tipo->id_tipo_orden; ?>">
                                        <?php echo htmlspecialchars($tipo->nombre_tipo); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-body-emphasis">4. Día Estimado de Visita: <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_programada" class="form-control bg-body border shadow-none rounded-3" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-bold text-body-emphasis">5. Prioridad:</label>
                            <select name="prioridad" class="form-select bg-body border shadow-none rounded-3">
                                <option value="Media" selected>Media (Normal)</option>
                                <option value="Alta">Alta (Urgente)</option>
                                <option value="Baja">Baja</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-body-emphasis">Observaciones para el Técnico (Opcional):</label>
                        <textarea name="observaciones" class="form-control bg-body border shadow-none rounded-3" rows="2" placeholder="Ej: Llevar escalera larga, llamar antes de llegar..."></textarea>
                        <small class="text-body-secondary">Este mensaje aparecerá en la App Móvil del técnico.</small>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-3 shadow rounded-pill text-uppercase tracking-wider mt-2">
                        <i class="fas fa-paper-plane me-2"></i> DESPACHAR ORDEN A LA APP
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>