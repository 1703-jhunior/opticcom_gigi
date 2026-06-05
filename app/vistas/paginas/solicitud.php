<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<?php
// 🔹 LECTURA DINÁMICA DE LA BASE DE DATOS PARA LA COBERTURA
$cobertura_db = [];
try {
    $db = new Conexion();
    $conn = $db->conectar();
    
    $sql = "SELECT dep.nombre as departamento, prov.nombre as provincia, dist.nombre as distrito
            FROM departamentos dep
            INNER JOIN provincias prov ON dep.id_departamento = prov.id_departamento_fk
            INNER JOIN distritos dist ON prov.id_provincia = dist.id_provincia_fk
            WHERE dep.estado_registro = '1' AND prov.estado_registro = '1' AND dist.estado_registro = '1'
            ORDER BY dep.nombre, prov.nombre, dist.nombre ASC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $ubicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($ubicaciones as $row) {
        $dep = trim($row['departamento']);
        $prov = trim($row['provincia']);
        $dist = trim($row['distrito']);

        if(!isset($cobertura_db[$dep])) $cobertura_db[$dep] = [];
        if(!isset($cobertura_db[$dep][$prov])) $cobertura_db[$dep][$prov] = [];
        if (!in_array($dist, $cobertura_db[$dep][$prov])) {
            $cobertura_db[$dep][$prov][] = $dist;
        }
    }
} catch (Exception $e) {
    error_log("Error cargando cobertura: " . $e->getMessage());
}
$json_cobertura = json_encode($cobertura_db, JSON_UNESCAPED_UNICODE);
?>

<div class="bg-primary text-white py-5 mb-5 shadow-sm">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Únete a la Red de Fibra</h1>
        <p class="lead fs-4 opacity-75">Estás a pocos pasos de navegar a la velocidad de la luz en tu ciudad.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card border-0 shadow-lg overflow-hidden rounded-4">
                <div class="row g-0">
                    <div class="col-md-4 bg-dark text-white p-4 d-none d-md-flex flex-column justify-content-between">
                        <div>
                            <h4 class="mb-4 text-warning fw-bold">¿Por qué OPTICCOM?</h4>
                            <ul class="list-unstyled">
                                <li class="mb-4 d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-3 mt-1 fs-5"></i>
                                    <span><strong>Fibra Óptica Pura:</strong> Sin cables de cobre, conexión directa FTTH.</span>
                                </li>
                                <li class="mb-4 d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-3 mt-1 fs-5"></i>
                                    <span><strong>Velocidad Simétrica:</strong> Sube archivos tan rápido como los descargas.</span>
                                </li>
                                <li class="mb-4 d-flex align-items-start">
                                    <i class="fas fa-check-circle text-success me-3 mt-1 fs-5"></i>
                                    <span><strong>Soporte Local:</strong> Atención inmediata por técnicos en tu propia ciudad.</span>
                                </li>
                            </ul>
                        </div>
                        <div class="text-center opacity-50">
                            <img src="<?php echo RUTA_URL; ?>/img/logo.png" alt="Logo Opticcom" class="img-fluid mb-3" style="max-width: 140px;">
                            <p class="small mb-0">© <?php echo date('Y'); ?> Opticcom SAC</p>
                        </div>
                    </div>

                    <div class="col-md-8 p-4 p-lg-5 bg-white">
                        <?php flash('mensaje_error'); ?>

                        <form action="<?php echo RUTA_URL; ?>/paginas/solicitud" method="POST" id="formSolicitud" class="needs-validation" novalidate>
                            
                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="badge bg-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">1</span>
                                    <h5 class="mb-0 fw-bold text-dark">Información Personal</h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nombres" class="form-label small fw-bold text-muted">Nombres <sup>*</sup></label>
                                        <input type="text" name="nombres" id="nombres" class="form-control form-control-lg border-2 <?php echo (!empty($datos['nombres_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['nombres'] ?? ''); ?>" placeholder="Ej: Juan" required minlength="3">
                                        <div class="invalid-feedback"><?php echo $datos['nombres_error'] ?: 'Ingrese al menos 3 caracteres.'; ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellidos" class="form-label small fw-bold text-muted">Apellidos <sup>*</sup></label>
                                        <input type="text" name="apellidos" id="apellidos" class="form-control form-control-lg border-2 <?php echo (!empty($datos['apellidos_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['apellidos'] ?? ''); ?>" placeholder="Ej: Pérez Ramos" required minlength="4">
                                        <div class="invalid-feedback"><?php echo $datos['apellidos_error'] ?: 'Ingrese al menos 4 caracteres.'; ?></div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="tipo_documento" class="form-label small fw-bold text-muted">Tipo Doc. <sup>*</sup></label>
                                        <div class="input-group">
                                            <select name="tipo_documento" id="tipo_documento" class="form-select border-2" style="max-width: 90px;" required>
                                                <option value="DNI">DNI</option>
                                                <option value="CE">CE/Pasaporte</option>
                                            </select>
                                            <input type="text" name="documento_identidad" id="documento_identidad" class="form-control form-control-lg border-2 <?php echo (!empty($datos['documento_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['documento_identidad'] ?? ''); ?>" placeholder="Número" required>
                                            <div class="invalid-feedback" id="doc_feedback"><?php echo $datos['documento_error'] ?: 'Documento inválido.'; ?></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label small fw-bold text-muted">Celular de Contacto <sup>*</sup></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-2 border-end-0 text-muted">+51</span>
                                            <input type="tel" name="telefono" id="telefono" class="form-control form-control-lg border-2 border-start-0 <?php echo (!empty($datos['telefono_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['telefono'] ?? ''); ?>" placeholder="9XX XXX XXX" pattern="^9\d{8}$" required>
                                            <div class="invalid-feedback"><?php echo $datos['telefono_error'] ?: 'Debe ser un celular de 9 dígitos empezando con 9.'; ?></div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label small fw-bold text-muted">Correo Electrónico <sup>*</sup></label>
                                        <input type="email" name="email" id="email" class="form-control form-control-lg border-2 <?php echo (!empty($datos['email_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['email'] ?? ''); ?>" placeholder="usuario@ejemplo.com" required>
                                        <div class="invalid-feedback"><?php echo $datos['email_error'] ?: 'Ingrese un correo electrónico válido.'; ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="badge bg-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">2</span>
                                    <h5 class="mb-0 fw-bold text-dark">Lugar de Instalación</h5>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Departamento <sup>*</sup></label>
                                        <select name="departamento" id="departamento" class="form-select border-2 shadow-none" required>
                                            <option value="">Seleccione...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Provincia <sup>*</sup></label>
                                        <select name="provincia" id="provincia" class="form-select border-2 shadow-none" required disabled>
                                            <option value="">Seleccione...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Distrito <sup>*</sup></label>
                                        <select name="distrito" id="distrito" class="form-select border-2 <?php echo (!empty($datos['distrito_error'])) ? 'is-invalid' : ''; ?>" required disabled>
                                            <option value="">Seleccione...</option>
                                        </select>
                                        <div class="invalid-feedback"><?php echo $datos['distrito_error'] ?: 'Requerido.'; ?></div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <label for="direccion_calle" class="form-label small fw-bold text-muted">Dirección Exacta (Av/Calle/Jr) <sup>*</sup></label>
                                        <input type="text" name="direccion_calle" id="direccion_calle" class="form-control border-2 <?php echo (!empty($datos['direccion_calle_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['direccion_calle'] ?? ''); ?>" placeholder="Ej: Jr. Lima 123, Urb. San Carlos" required minlength="10">
                                        <div class="invalid-feedback" id="dir_feedback"><?php echo $datos['direccion_calle_error'] ?: 'Detalle la dirección completa (mín. 10 caracteres y 2 palabras).'; ?></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="referencia" class="form-label small fw-bold text-muted">Referencia Cercana <sup>*</sup></label>
                                        <input type="text" name="referencia" id="referencia" class="form-control border-2 <?php echo (!empty($datos['referencia_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['referencia'] ?? ''); ?>" placeholder="Ej: Frente al parque infantil, casa de 3 pisos" required minlength="10">
                                        <div class="invalid-feedback"><?php echo $datos['referencia_error'] ?: 'Sea descriptivo para que el técnico lo encuentre (mín. 10 caracteres).'; ?></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="location_link" class="form-label small fw-bold text-muted">Ubicación Google Maps <sup>*</sup></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-2 text-danger"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="url" name="location_link" id="location_link" class="form-control border-2 <?php echo (!empty($datos['location_link_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['location_link'] ?? ''); ?>" placeholder="Ej: https://maps.app.goo.gl/..." required>
                                            <div class="invalid-feedback" id="link_feedback"><?php echo $datos['location_link_error'] ?: 'Debe pegar un enlace válido de Google Maps (maps.app.goo.gl o similar).'; ?></div>
                                        </div>
                                        <small class="text-muted"><i class="fas fa-info-circle"></i> Abra Google Maps en su celular, busque su ubicación y toque "Compartir".</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-4">
                                    <span class="badge bg-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">3</span>
                                    <h5 class="mb-0 fw-bold text-dark">Plan de Internet</h5>
                                </div>

                                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                    <label for="id_plan_interesado" class="form-label small fw-bold text-primary text-uppercase">Confirma tu elección <sup>*</sup></label>
                                    <select name="id_plan_interesado" id="id_plan_interesado" class="form-select form-select-lg border-0 bg-transparent fw-bold <?php echo (!empty($datos['plan_error'])) ? 'is-invalid' : ''; ?>" required>
                                        <option value="">-- Selecciona un Plan --</option>
                                        <?php foreach($datos['planes'] as $plan): ?>
                                            <option value="<?php echo $plan->id_plan; ?>" <?php echo (($datos['plan_seleccionado'] ?? '') == $plan->id_plan) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($plan->nombre_plan); ?> | <?php echo htmlspecialchars($plan->velocidad); ?> | S/ <?php echo number_format($plan->precio_mensual, 2); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback"><?php echo $datos['plan_error'] ?: 'Seleccione un plan de la lista.'; ?></div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow border-0 transition-all" id="btnSubmit">
                                    <i class="fas fa-paper-plane me-2"></i> ENVIAR MI SOLICITUD
                                </button>
                                <p class="text-center text-muted small mt-3">
                                    <i class="fas fa-shield-alt me-1"></i> Tus datos están protegidos por nuestra política de privacidad.
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 🔹 LÓGICA DE COBERTURA DINÁMICA (Departamentos, Provincias, Distritos)
    const cobertura = <?php echo $json_cobertura; ?>;
    const deptoSelect = document.getElementById('departamento');
    const provSelect = document.getElementById('provincia');
    const distSelect = document.getElementById('distrito');

    function llenarSelect(selectElement, items) {
        selectElement.innerHTML = '<option value="">Seleccione...</option>';
        items.sort().forEach(item => {
            selectElement.add(new Option(item, item));
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        llenarSelect(deptoSelect, Object.keys(cobertura));
    });

    deptoSelect.addEventListener('change', () => {
        const val = deptoSelect.value;
        if (val && cobertura[val]) {
            llenarSelect(provSelect, Object.keys(cobertura[val]));
            provSelect.disabled = false;
        } else {
            provSelect.disabled = true; distSelect.disabled = true;
        }
        llenarSelect(distSelect, []);
    });

    provSelect.addEventListener('change', () => {
        const d = deptoSelect.value, p = provSelect.value;
        if (p && cobertura[d] && cobertura[d][p]) {
            llenarSelect(distSelect, cobertura[d][p]);
            distSelect.disabled = false;
        } else {
            distSelect.disabled = true;
        }
    });

    // 🔹 VALIDACIONES FRONTEND (JavaScript) Y BLOQUEO DE DOBLE CLIC
    document.getElementById('formSolicitud').addEventListener('submit', function(event) {
        let isValid = true;

        // Validar DNI/CE dinámicamente
        const tipoDoc = document.getElementById('tipo_documento').value;
        const docInput = document.getElementById('documento_identidad');
        const docFeedback = document.getElementById('doc_feedback');
        
        docInput.classList.remove('is-invalid');
        if (tipoDoc === 'DNI') {
            if (!/^\d{8}$/.test(docInput.value)) {
                docInput.classList.add('is-invalid');
                docFeedback.innerText = 'El DNI debe tener exactamente 8 números.';
                isValid = false;
            }
        } else {
            if (docInput.value.length < 9) {
                docInput.classList.add('is-invalid');
                docFeedback.innerText = 'El CE/Pasaporte debe tener mínimo 9 caracteres.';
                isValid = false;
            }
        }

        // Validar Dirección (Mínimo 2 palabras)
        const dirInput = document.getElementById('direccion_calle');
        const dirFeedback = document.getElementById('dir_feedback');
        dirInput.classList.remove('is-invalid');
        if (dirInput.value.trim().split(/\s+/).length < 2) {
            dirInput.classList.add('is-invalid');
            dirFeedback.innerText = 'Por favor, escriba una dirección más completa.';
            isValid = false;
        }

        // Validar Link de Google Maps
        const linkInput = document.getElementById('location_link');
        const linkFeedback = document.getElementById('link_feedback');
        // Acepta links cortos, largos, con www o sin www de Google Maps
        const mapsRegex = /(maps\.app\.goo\.gl|goo\.gl\/maps|google\.com\/maps)/i;
        
        linkInput.classList.remove('is-invalid');
        if (!mapsRegex.test(linkInput.value)) {
            linkInput.classList.add('is-invalid');
            linkFeedback.innerText = 'El enlace ingresado no parece ser de Google Maps.';
            isValid = false;
        }

        // Validaciones genéricas de Bootstrap 5
        if (!this.checkValidity()) {
            isValid = false;
        }

        // Evaluación final antes del envío
        if (!isValid) {
            // Si hay errores, detenemos el envío
            event.preventDefault(); 
            event.stopPropagation();
        } else {
            // 🚀 SI TODO ESTÁ PERFECTO: BLOQUEAMOS EL BOTÓN Y PONEMOS EFECTO DE CARGA
            const btnSubmit = document.getElementById('btnSubmit');
            
            // Cambiamos el texto y agregamos el icono de cargando (Spinner de Bootstrap)
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> PROCESANDO SOLICITUD...';
            
            // Evitamos que el botón se vea clickeable o pueda recibir eventos
            btnSubmit.classList.add('disabled');
            btnSubmit.style.pointerEvents = 'none';
            
            // Lo desactivamos milisegundos después para asegurar que el submit SÍ se dispare hacia PHP
            setTimeout(() => {
                btnSubmit.disabled = true;
            }, 50);
        }
        
        // Aplica estilos de Bootstrap para mostrar los feedbacks verdes/rojos
        this.classList.add('was-validated');
    }, false);
</script>

<style>
    .form-control, .form-select { transition: all 0.2s ease-in-out; }
    .form-control:focus, .form-select:focus { border-color: var(--bs-primary); box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15); background-color: #fff; }
    .transition-all:hover { transform: translateY(-2px); filter: brightness(110%); }
    .rounded-4 { border-radius: 1rem !important; }
</style>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>