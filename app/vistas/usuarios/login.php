<?php require APP_ROOT . '/app/vistas/includes/header.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card card-body bg-light mt-5">
            <h2>Iniciar Sesión (Personal)</h2>
            <p>Por favor, ingrese sus credenciales para acceder.</p>
            
            <!-- 
                Este formulario apunta a /usuarios/login
                Tu controlador Portal.php (en portal/login) es el "unificado"
                Este es solo para personal (Admin/Ventas/Pagos)
            -->
            <form action="<?php echo RUTA_URL; ?>/usuarios/login" method="POST">
                
                <!-- ❗ CORRECCIÓN BUG #3 -->
                <!-- Se cambió el name="email" por name="usuario" para que coincida con el controlador -->
                <div class="mb-3">
                    <label for="usuario" class="form-label">Email: <sup>*</sup></label>
                    <input type="email" name="usuario" id="usuario" class="form-control <?php echo (!empty($datos['usuario_error'])) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($datos['usuario'] ?? ''); ?>">
                    <div class="invalid-feedback"><?php echo $datos['usuario_error'] ?? ''; ?></div>
                </div>
                <!-- Fin Corrección -->

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña: <sup>*</sup></label>
                    <input type="password" name="password" id="password" class="form-control <?php echo (!empty($datos['password_error'])) ? 'is-invalid' : ''; ?>" value="">
                    <div class="invalid-feedback"><?php echo $datos['password_error']; ?></div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require APP_ROOT . '/app/vistas/includes/footer.php'; ?>
