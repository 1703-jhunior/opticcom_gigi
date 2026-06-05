<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 mt-2">
    <h1 class="fw-bold text-body-emphasis mb-0">
        <i class="fas fa-users-cog text-primary me-2"></i> <?php echo htmlspecialchars($datos['titulo']); ?>
    </h1>
</div>

<?php flash('usuario_mensaje'); ?>
<?php flash('mensaje_error'); ?>

<div class="card card-glass-modern border-0 mb-5">
    <div class="card-header bg-body-tertiary border-0 d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold text-body-emphasis">
            <i class="fas fa-user-shield text-primary me-2"></i> Usuarios del Sistema (Personal)
        </h5>
        <a href="<?php echo RUTA_URL; ?>/usuarios/agregar" class="btn btn-primary btn-sm fw-bold rounded-pill px-3 shadow-sm">
            <i class="fas fa-plus me-1"></i> Agregar Usuario
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 80px;">ID</th>
                        <th class="py-3">Nombre</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Rol</th>
                        <th class="py-3">Fecha Creación</th>
                        <th class="text-center pe-4 py-3" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($datos['usuarios_admin'])): ?>
                        <?php foreach($datos['usuarios_admin'] as $usuario): ?>
                        <tr>
                            <td class="ps-4 font-monospace text-body-secondary">#<?php echo $usuario->id_usuario; ?></td>
                            <td><div class="fw-bold text-body-emphasis"><?php echo htmlspecialchars($usuario->nombre); ?></div></td>
                            <td><div class="text-body-secondary small"><?php echo htmlspecialchars($usuario->email); ?></div></td>
                            <td>
                                <?php 
                                    // Inserta distintivos de color semánticos dependiendo del rol asignado
                                    $rol_clase = 'bg-primary';
                                    if(htmlspecialchars($usuario->rol) === 'Administrador') $rol_clase = 'bg-danger';
                                    if(htmlspecialchars($usuario->rol) === 'Ventas') $rol_clase = 'bg-success';
                                    if(htmlspecialchars($usuario->rol) === 'Técnico') $rol_clase = 'bg-warning text-dark';
                                ?>
                                <span class="badge <?php echo $rol_clase; ?> rounded-pill px-3 py-1.5"><?php echo htmlspecialchars($usuario->rol); ?></span>
                            </td>
                            <td><div class="text-body-secondary small"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($usuario->fecha_creacion)); ?></div></td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden border" role="group">
                                    <a href="<?php echo RUTA_URL; ?>/usuarios/editar/<?php echo $usuario->id_usuario; ?>" class="btn btn-sm btn-body text-warning border-0 px-3" title="Editar Parámetros">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    
                                    <?php if ($usuario->id_usuario != $_SESSION['id_usuario']): ?>
                                        <form action="<?php echo RUTA_URL; ?>/usuarios/borrar/<?php echo $usuario->id_usuario; ?>" method="POST" class="d-inline m-0" onsubmit="return confirm('¿Está seguro de que desea eliminar a <?php echo htmlspecialchars(addslashes($usuario->nombre)); ?>? Esta acción no se puede deshacer.');">
                                            <button type="submit" class="btn btn-sm btn-body text-danger border-0 px-3" title="Eliminar definitivamente">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-body-secondary py-5">
                                <i class="fas fa-user-slash fa-2x mb-2 text-muted"></i><br>No hay usuarios del sistema registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="card card-glass-modern border-0 mb-5">
    <div class="card-header bg-body-tertiary border-0 py-3">
        <h5 class="mb-0 fw-bold text-body-emphasis">
            <i class="fas fa-user-lock text-secondary me-2"></i> Accesos de Clientes (Portal Cliente)
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="p-4 border-bottom bg-body-tertiary bg-opacity-25">
            <p class="text-body-secondary mb-0 small">
                <i class="fas fa-info-circle text-primary me-1"></i> Aquí puede restablecer o generar contraseñas seguras para que los clientes ingresen a consultar su información en sus respectivos portales. La contraseña ingresada debe tener un mínimo de <strong>6 caracteres</strong> de longitud.
            </p>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-body-secondary">
                    <tr>
                        <th class="ps-4 py-3">Cód. Cliente (DNI)</th>
                        <th class="py-3">Nombre Cliente</th>
                        <th class="py-3">Teléfono</th>
                        <th class="py-3">¿Tiene Acceso?</th>
                        <th class="pe-4 py-3" style="min-width: 360px;">Nueva Contraseña del Portal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($datos['clientes'])): ?>
                        <?php foreach($datos['clientes'] as $cliente): ?>
                        <tr>
                            <td class="ps-4 font-monospace text-body-emphasis fw-bold"><?php echo htmlspecialchars($cliente->dni ?? 'N/A'); ?></td>
                            <td><div class="fw-bold text-body-emphasis"><?php echo htmlspecialchars(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? '')); ?></div></td>
                            <td><div class="text-body-secondary small"><i class="fas fa-phone-alt me-1"></i> <?php echo htmlspecialchars($cliente->telefono ?? 'N/A'); ?></div></td>
                            <td>
                                <?php if (!empty($cliente->password)): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill border-0 fw-bold">
                                        <i class="fas fa-check-circle me-1"></i> Habilitado
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1.5 rounded-pill border-0 fw-bold">
                                        <i class="fas fa-times-circle me-1"></i> Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4">
                                <form action="<?php echo RUTA_URL; ?>/usuarios/crearAccAccessoCliente/<?php echo $cliente->id_cliente; ?>" method="POST" class="d-flex gap-2 align-items-center m-0 py-1">
                                    <div class="input-group input-group-sm rounded-3 overflow-hidden border bg-body">
                                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-key small"></i></span>
                                        <input type="password" name="password" class="form-control border-0 bg-transparent shadow-none" placeholder="Contraseña nueva" autocomplete="new-password" required>
                                    </div>
                                    <div class="input-group input-group-sm rounded-3 overflow-hidden border bg-body">
                                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="fas fa-lock small"></i></span>
                                        <input type="password" name="confirmar_password" class="form-control border-0 bg-transparent shadow-none" placeholder="Confirmar" autocomplete="new-password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm flex-shrink-0">
                                        <i class="fas fa-save me-1"></i> Guardar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-body-secondary py-5">
                                <i class="fas fa-id-badge fa-2x mb-2 text-muted"></i><br>No hay registros de clientes vinculados actualmente.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>