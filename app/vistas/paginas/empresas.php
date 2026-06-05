<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<section class="py-5" style="background: linear-gradient(135deg, #020617 0%, #0f172a 100%);">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary px-3 py-2 mb-3 shadow-sm">SOLUCIONES CORPORATIVAS B2B</span>
                <h1 class="display-3 fw-black text-white mt-2">TU EMPRESA EN <br><span class="text-primary">MANOS EXPERTAS.</span></h1>
                <p class="fs-5 text-white-50 mt-4">
                    En **OPTICCOM S.A.C.** contamos con una trayectoria sólida liderando proyectos de infraestructura. 
                    Hemos sido aliados estratégicos y contratistas para empresas como **Claro, Movistar y Gigared**.
                </p>
                <p class="fs-5 text-white-50">
                    Brindamos servicios de alta disponibilidad a **Municipalidades, Gobiernos Regionales e Hidroeléctricas** en todo el Perú central, garantizando estándares de certificación internacional.
                </p>
                
                <div class="row g-4 mt-4">
                    <div class="col-6">
                        <div class="p-3 border border-secondary border-opacity-50 rounded-4">
                            <h4 class="text-white fw-bold mb-0">100%</h4>
                            <small class="text-primary fw-bold text-uppercase">Garantía de Red</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border border-secondary border-opacity-50 rounded-4">
                            <h4 class="text-white fw-bold mb-0">24/7</h4>
                            <small class="text-primary fw-bold text-uppercase">Soporte Élite</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card bg-dark border-secondary p-4 p-md-5 rounded-5 shadow-2xl">
                    <div class="text-center mb-4">
                        <h3 class="text-white fw-bold">Solicitar Cotización</h3>
                        <p class="text-white-50 small">Un consultor corporativo le responderá en breve.</p>
                    </div>

                    <?php flash('mensaje_error'); ?>
                    <?php flash('plan_mensaje'); ?>

                    <form action="<?php echo RUTA_URL; ?>/paginas/cotizacion" method="POST" novalidate>
                        
                        <div class="mb-4">
                            <label class="text-white-50 fw-bold mb-2 small text-uppercase">Razón Social *</label>
                            <input type="text" name="razon_social" class="form-control bg-transparent text-white border-secondary p-3 <?php echo (!empty($datos['razon_social_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['razon_social'] ?? ''; ?>" placeholder="Nombre de la empresa">
                            <div class="invalid-feedback"><?php echo $datos['razon_social_error'] ?? ''; ?></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="text-white-50 fw-bold mb-2 small text-uppercase">RUC *</label>
                                <input type="text" name="ruc" class="form-control bg-transparent text-white border-secondary p-3 <?php echo (!empty($datos['ruc_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['ruc'] ?? ''; ?>" placeholder="11 dígitos" maxlength="11">
                                <div class="invalid-feedback"><?php echo $datos['ruc_error'] ?? ''; ?></div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="text-white-50 fw-bold mb-2 small text-uppercase">Teléfono de contacto *</label>
                                <input type="tel" name="telefono_contacto" class="form-control bg-transparent text-white border-secondary p-3 <?php echo (!empty($datos['telefono_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo $datos['telefono_contacto'] ?? ''; ?>">
                                <div class="invalid-feedback"><?php echo $datos['telefono_error'] ?? ''; ?></div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-white-50 fw-bold mb-2 small text-uppercase">Tipo de Servicio Requerido *</label>
                            <select name="tipo_servicio" class="form-select bg-dark text-white border-secondary p-3" required>
                                <option value="" selected disabled>Seleccione un servicio...</option>
                                <option value="INTERNET DEDICADO">SERVICIO DE INTERNET DEDICADO</option>
                                <option value="CABLEADO ESTRUCTURADO">CABLEADO ESTRUCTURADO</option>
                                <option value="FUSION DE FIBRA">FUSIÓN DE FIBRA ÓPTICA</option>
                                <option value="TENDIDOS">TENDIDOS DE RED</option>
                                <option value="PUNTO A PUNTO">ENLACES PUNTO A PUNTO</option>
                                <option value="CERTIFICACION">CERTIFICACIÓN DE REDES</option>
                                <option value="CONFIGURACION">CONFIGURACIÓN Y OTROS</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="text-white-50 fw-bold mb-2 small text-uppercase">Ubicación del Proyecto *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-secondary text-white-50"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="distrito" class="form-control bg-transparent text-white border-secondary p-3" placeholder="Distrito / Provincia" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-white-50 fw-bold mb-2 small text-uppercase">Mensaje Adicional</label>
                            <textarea name="mensaje" class="form-control bg-transparent text-white border-secondary" rows="3" placeholder="Detalles técnicos del proyecto..."><?php echo $datos['mensaje'] ?? ''; ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 shadow-lg text-uppercase">
                                Enviar Solicitud de Propuesta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container py-5 text-center text-dark">
        <h2 class="display-5 fw-bold mb-5">NUESTROS SERVICIOS <span class="text-primary">ESPECIALIZADOS</span></h2>
        <div class="row g-4">
            <?php 
            $servicios_extra = [
                ['icon' => 'rocket.png', 'title' => 'Internet Dedicado', 'desc' => 'Ancho de banda garantizado al 100% con IP fija.'],
                ['icon' => 'shield.png', 'title' => 'Certificación', 'desc' => 'Certificación de puntos de red categoría 6, 6A y Fibra.'],
                ['icon' => 'tools.png', 'title' => 'Fusión y Tendidos', 'desc' => 'Equipamiento propio para empalmes por fusión y despliegue.']
            ];
            foreach($servicios_extra as $s): ?>
            <div class="col-md-4">
                <div class="p-4 rounded-4 shadow-sm border border-light">
                    <img src="<?php echo RUTA_URL; ?>/img/icons/<?php echo $s['icon']; ?>" width="60" class="mb-3" alt="Icono">
                    <h4 class="fw-bold"><?php echo $s['title']; ?></h4>
                    <p class="text-muted small"><?php echo $s['desc']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>