<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-glass-modern border-0 mt-4 mb-5">
            <div class="card-header bg-dark text-white border-0 py-3">
                <h4 class="mb-0 fw-bold"><i class="fas fa-user-plus text-primary me-2"></i> <?php echo htmlspecialchars($datos['titulo']); ?></h4>
            </div>
            
            <div class="card-body p-4 bg-body-tertiary">
                <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-outline-secondary btn-sm bg-body fw-bold rounded-pill px-3 mb-4 shadow-sm">
                    <i class="fa fa-backward me-1"></i> Volver al Listado
                </a>
                
                <p class="text-body-secondary mb-4 pb-2 border-bottom border-secondary border-opacity-10">
                    <i class="fas fa-info-circle text-primary me-1"></i> Registre los parámetros de identidad, geolocalización y facturación para dar de alta al nuevo abonado en la red.
                </p>
                
                <form action="<?php echo RUTA_URL; ?>/clientes/agregar" method="POST" novalidate>
                    
                    <div class="bg-body border rounded-3 p-3 mb-4 shadow-sm">
                        <h6 class="text-primary fw-bold border-bottom border-secondary border-opacity-10 pb-2 mb-3">
                            <i class="fas fa-id-card me-1"></i> Datos Personales y de Contacto
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-bold text-body-emphasis small">Nombres: <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['nombre_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombre']); ?>" required>
                                <div class="invalid-feedback fw-bold"><?php echo $datos['nombre_error']; ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="apellido" class="form-label fw-bold text-body-emphasis small">Apellidos:</label>
                                <input type="text" name="apellido" id="apellido" class="form-control bg-body border shadow-none rounded-3" value="<?php echo htmlspecialchars($datos['apellido']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="documento_identidad" class="form-label fw-bold text-body-emphasis small">Documento (DNI/RUC): <span class="text-danger">*</span></label>
                                <input type="text" name="documento_identidad" id="documento_identidad" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['documento_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['documento_identidad']); ?>" required>
                                <div class="invalid-feedback fw-bold"><?php echo $datos['documento_error']; ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-bold text-body-emphasis small">Celular de Contacto: <span class="text-danger">*</span></label>
                                <div class="input-group has-validation rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-body border-end-0 text-muted"><i class="fas fa-phone small"></i></span>
                                    <input type="text" name="telefono" id="telefono" class="form-control bg-body border shadow-none border-start-0 ps-0 <?php echo (!empty($datos['telefono_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['telefono']); ?>" required>
                                    <div class="invalid-feedback fw-bold"><?php echo $datos['telefono_error']; ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label fw-bold text-body-emphasis small">Correo Electrónico (Opcional):</label>
                                <input type="email" name="email" id="email" class="form-control bg-body border shadow-none rounded-3" value="<?php echo htmlspecialchars($datos['email']); ?>" placeholder="ejemplo@correo.com">
                            </div>
                        </div>
                    </div>

                    <div class="bg-body border rounded-3 p-3 mb-4 shadow-sm">
                        <h6 class="text-primary fw-bold border-bottom border-secondary border-opacity-10 pb-2 mb-3">
                            <i class="fas fa-map-marked-alt me-1"></i> Datos de Ubicación e Instalación
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="distrito" class="form-label fw-bold text-body-emphasis small">Distrito de Cobertura: <span class="text-danger">*</span></label>
                                <select name="distrito" id="distrito" class="form-select bg-body border shadow-none rounded-3 <?php echo (!empty($datos['distrito_error'])) ? 'is-invalid' : ''; ?>" required>
                                    <option value="">-- Seleccione un Distrito --</option>
                                    <?php foreach($datos['distritos'] as $d): ?>
                                        <option value="<?php echo $d->id_distrito; ?>" <?php echo ($datos['distrito'] == $d->id_distrito) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($d->distrito . ' (' . $d->provincia . ', ' . $d->departamento . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback fw-bold"><?php echo $datos['distrito_error']; ?></div>
                            </div>
                            <div class="col-md-12">
                                <label for="direccion_calle" class="form-label fw-bold text-body-emphasis small">Dirección (Calle, Avenida, Nro., Mz., Lt.): <span class="text-danger">*</span></label>
                                <input type="text" name="direccion_calle" id="direccion_calle" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['direccion_calle_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['direccion_calle']); ?>" required>
                                <div class="invalid-feedback fw-bold"><?php echo $datos['direccion_calle_error']; ?></div>
                            </div>
                            <div class="col-md-12">
                                <label for="referencia" class="form-label fw-bold text-body-emphasis small">Referencia de Fachada:</label>
                                <input type="text" name="referencia" id="referencia" class="form-control bg-body border shadow-none rounded-3" value="<?php echo htmlspecialchars($datos['referencia']); ?>" placeholder="Ej: Frente al parque infantil, portón marrón...">
                            </div>
                            <div class="col-md-12">
                                <label for="location_link" class="form-label fw-bold text-body-emphasis small">Enlace Georreferenciado GPS (Opcional):</label>
                                <div class="input-group rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-body border-end-0 text-danger"><i class="fas fa-map-pin"></i></span>
                                    <input type="url" name="location_link" id="location_link" class="form-control bg-body border shadow-none border-start-0 ps-0" value="<?php echo htmlspecialchars($datos['location_link']); ?>" placeholder="Pegue aquí la URL compartida de Google Maps...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-body border rounded-3 p-3 mb-4 shadow-sm">
                        <h6 class="text-primary fw-bold border-bottom border-secondary border-opacity-10 pb-2 mb-3">
                            <i class="fas fa-satellite-dish me-1"></i> Asignación del Plan y del Servicio
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="id_plan" class="form-label fw-bold text-body-emphasis small">Plan Contratado: <span class="text-danger">*</span></label>
                                <select name="id_plan" id="id_plan" class="form-select bg-body border shadow-none rounded-3 <?php echo (!empty($datos['plan_error'])) ? 'is-invalid' : ''; ?>" required>
                                    <option value="">-- Seleccione un Plan Comercial --</option>
                                    <?php foreach($datos['planes'] as $plan): ?>
                                        <option value="<?php echo $plan->id_plan; ?>" <?php echo ($datos['id_plan'] == $plan->id_plan) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($plan->nombre_plan); ?> (<?php echo htmlspecialchars($plan->velocidad); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback fw-bold"><?php echo $datos['plan_error']; ?></div>
                            </div>
                            <div class="col-md-6">
                                <label for="estado_servicio" class="form-label fw-bold text-body-emphasis small">Estado Inicial en el Sistema: <span class="text-danger">*</span></label>
                                <select name="estado_servicio" id="estado_servicio" class="form-select bg-body border shadow-none rounded-3">
                                    <option value="Pendiente Instalacion" <?php echo ($datos['estado_servicio'] == 'Pendiente Instalacion') ? 'selected' : ''; ?>>Pendiente Instalación</option>
                                    <option value="Activo" <?php echo ($datos['estado_servicio'] == 'Activo') ? 'selected' : ''; ?>>Activo</option>
                                    <option value="Suspendido" <?php echo ($datos['estado_servicio'] == 'Suspendido') ? 'selected' : ''; ?>>Suspendido</option>
                                    <option value="Cancelado" <?php echo ($datos['estado_servicio'] == 'Cancelado') ? 'selected' : ''; ?>>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_instalacion" class="form-label fw-bold text-body-emphasis small">Fecha de Alta / Solicitud: <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_instalacion" id="fecha_instalacion" class="form-control bg-body border shadow-none rounded-3 <?php echo (!empty($datos['fecha_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['fecha_instalacion']); ?>" required>
                                <div class="invalid-feedback fw-bold"><?php echo $datos['fecha_error']; ?></div>
                            </div>
                            <div class="col-12">
                                <label for="detalles" class="form-label fw-bold text-body-emphasis small">Detalles Internos o Notas:</label>
                                <textarea name="detalles" id="detalles" class="form-control bg-body border shadow-none rounded-3" rows="3" placeholder="Información contractual o de red adicional relevante..."><?php echo htmlspecialchars($datos['detalles'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top border-secondary border-opacity-10 pt-4">
                        <a href="<?php echo RUTA_URL; ?>/clientes" class="btn btn-outline-secondary fw-bold rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                            <i class="fas fa-save me-1"></i> Guardar Nuevo Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>