<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Portal - OPTICCOM</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/> <?php // Añadido para el ícono ?>
<style>
  body{background:#ff6600;font-family:system-ui,-apple-system,Segoe UI,Roboto}
  .login-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center}
  .box{background:#fff;border-radius:16px;padding:36px 28px;box-shadow:0 10px 30px rgba(0,0,0,.2);width:100%;max-width:380px}
  .brand{font-weight:800;color:#ff6600;text-align:center;margin-bottom:10px;font-size:24px}
  .btn-login{background:#ff6600;border:none;font-weight:700}
  .btn-login:hover{background:#e25800}
  .back-link a { text-decoration: none; color: #6c757d; }
  .back-link a:hover { color: #343a40; }
</style>
</head>
<body>
<div class="login-wrap">
  <div class="box">
    <div class="brand">Mi Portal OPTICCOM</div>
    <?php flash('mensaje_error'); ?>
    <form method="POST" action="<?php echo RUTA_URL; ?>/portal/login" novalidate>
      <div class="mb-3">
        <label class="form-label">Usuario (DNI o Email)</label>
        <input type="text" name="usuario" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button class="btn btn-login w-100">Iniciar sesión</button>
    </form>
    
    <?php // --- ❗ INICIO DE LA CORRECCIÓN (Enlace "Volver") --- ?>
    <p class="text-center mt-3 back-link">
        <a href="<?php echo RUTA_URL; ?>/">
            <i class="fas fa-arrow-left me-1"></i> Volver a la página principal
        </a>
    </p>
    <?php // --- ❗ FIN DE LA CORRECCIÓN --- ?>

    <p class="text-center mt-3 mb-0"><small>© OPTICCOM S.A.C.</small></p>
  </div>
</div>
</body>
</html>
