<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Solicitud de Cotización Empresarial (B2B)</h2>
                </div>
                <div class="card-body">
                    <p class="card-text">Complete los datos de su empresa. Un asesor se pondrá en contacto con usted.</p>
                    <?php flash('mensaje_error'); ?>
                    <?php flash('plan_mensaje'); // Para el mensaje de "Éxito" ?>

                    <form action="<?php echo RUTA_URL; ?>/paginas/cotizacion" method="POST" novalidate>

                        <h5 class="mt-4">Datos de la Empresa</h5><hr>
                        <div class="mb-3">
                            <label for="razon_social" class="form-label">Razón Social: <sup>*</sup></label>
                            <input type="text" name="razon_social" class="form-control <?php echo (!empty($datos['razon_social_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['razon_social'] ?? ''); ?>" required>
                            <div class="invalid-feedback"><?php echo $datos['razon_social_error'] ?? ''; ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="ruc" class="form-label">RUC: <sup>*</sup></label>
                            <input type="text" name="ruc" class="form-control <?php echo (!empty($datos['ruc_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['ruc'] ?? ''); ?>" required>
                            <div class="invalid-feedback"><?php echo $datos['ruc_error'] ?? ''; ?></div>
                        </div>

                        <h5 class="mt-4">Datos de Contacto</h5><hr>
                        <div class="mb-3">
                            <label for="persona_contacto" class="form-label">Persona de Contacto: <sup>*</sup></label>
                            <input type="text" name="persona_contacto" class="form-control <?php echo (!empty($datos['contacto_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['persona_contacto'] ?? ''); ?>" required>
                            <div class="invalid-feedback"><?php echo $datos['contacto_error'] ?? ''; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono_contacto" class="form-label">Teléfono: <sup>*</sup></label>
                                <input type="tel" name="telefono_contacto" class="form-control <?php echo (!empty($datos['telefono_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['telefono_contacto'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['telefono_error'] ?? ''; ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email_contacto" class="form-label">Email: <sup>*</sup></label>
                                <input type="email" name="email_contacto" class="form-control <?php echo (!empty($datos['email_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['email_contacto'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['email_error'] ?? ''; ?></div>
                            </div>
                        </div>

                        <!-- ================== CAMPOS DE UBICACIÓN (ACTUALIZADOS) ================== -->
                        <h5 class="mt-4">Datos de Ubicación del Proyecto</h5><hr>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="departamento" class="form-label">Departamento: <sup>*</sup></label>
                                <input type="text" name="departamento" class="form-control <?php echo (!empty($datos['departamento_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['departamento'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['departamento_error'] ?? ''; ?></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="provincia" class="form-label">Provincia: <sup>*</sup></label>
                                <input type="text" name="provincia" class="form-control <?php echo (!empty($datos['provincia_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['provincia'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['provincia_error'] ?? ''; ?></div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="distrito" class="form-label">Distrito: E<sup>*</sup></label>
                                <input type="text" name="distrito" class="form-control <?php echo (!empty($datos['distrito_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['distrito'] ?? ''); ?>" required>
                                <div class="invalid-feedback"><?php echo $datos['distrito_error'] ?? ''; ?></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="direccion_calle" class="form-label">Dirección (Calle, Nro, etc.): <sup>*</sup></label>
                            <input type="text" name="direccion_calle" class="form-control <?php echo (!empty($datos['direccion_calle_error'] ?? '')) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['direccion_calle'] ?? ''); ?>" required>
                            <div class="invalid-feedback"><?php echo $datos['direccion_calle_error'] ?? ''; ?></div>
                        </div>
                         <div class="mb-3">
                            <label for="referencia" class="form-label">Referencia (Opcional):</label>
                            <input type="text" name="referencia" class="form-control" value="<?php echo htmlspecialchars($datos['referencia'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="location_link" class="form-label">Link de Ubicación (Opcional):</label>
                            <input type="url" name="location_link" class="form-control" value="<?php echo htmlspecialchars($datos['location_link'] ?? ''); ?>" placeholder="Pegue aquí el link de Google Maps...">
                        </div>
                        <!-- ================== FIN NUEVOS CAMPOS ================== -->

                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Mensaje (Opcional):</label>
                            <textarea name="mensaje" class="form-control" rows="3"><?php echo htmlspecialchars($datos['mensaje'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Enviar Cotización</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>

