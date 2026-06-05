</div> <footer class="footer-opticcom pt-5 pb-4 mt-auto bg-body-tertiary border-top" id="nosotros">
    <div class="container text-center text-md-start">
        <div class="row g-4">
            
            <div class="col-md-4">
                <img src="<?php echo RUTA_URL; ?>/img/logo.png" alt="OPTICCOM" style="height: 45px;" class="mb-4">
                <p class="text-body-secondary pe-md-4">
                    Conectamos tus sueños con la red de fibra óptica más estable del centro del país. 
                    Tecnología de última generación al servicio de Huancayo.
                </p>
                <div class="social-links mt-3">
                    <a href="#" class="text-body-secondary text-primary-hover me-3 fs-5 transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-body-secondary text-primary-hover me-3 fs-5 transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-body-secondary text-primary-hover fs-5 transition-colors"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <div class="col-md-4">
                <h6 class="text-uppercase fw-bold mb-4 text-primary">Navegación</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?php echo RUTA_URL; ?>/" class="text-body-secondary text-decoration-none">Inicio</a></li>
                    <li class="mb-2"><a href="<?php echo RUTA_URL; ?>/paginas/hogar" class="text-body-secondary text-decoration-none">Servicios Hogar</a></li>
                    <li class="mb-2"><a href="<?php echo RUTA_URL; ?>/paginas/empresas" class="text-body-secondary text-decoration-none">Servicios Empresa</a></li>
                    <li class="mb-2"><a href="<?php echo RUTA_URL; ?>/portal/login" class="text-body-secondary text-decoration-none">Portal de Clientes</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6 class="text-uppercase fw-bold mb-4 text-primary">Contacto Directo</h6>
                <p class="mb-2 text-body-secondary"><i class="fas fa-map-marker-alt text-primary me-2"></i> Pj. Rosario Nro. 582, El Tambo, Huancayo</p>
                <p class="mb-2 text-body-secondary"><i class="fas fa-envelope text-primary me-2"></i> opticcom@outlook.com</p>
                <p class="mb-2 text-body-secondary"><i class="fas fa-phone-alt text-primary me-2"></i> +51 918 845 960</p>
                
                <div class="mt-4 p-3 rounded-4 bg-body border shadow-sm">
                    <small class="d-block text-body-secondary fw-bold mb-1">Horario de Atención:</small>
                    <span class="text-body-emphasis fw-bold fs-6">Lun - Sáb: 8:00 AM - 7:00 PM</span>
                </div>
            </div>
            
        </div>
    </div>
</footer>

<div class="py-3 border-top bg-body">
    <div class="container text-center">
        <p class="mb-0 small text-body-secondary">
            &copy; <?php echo date('Y'); ?> <span class="text-body-emphasis fw-bold">OPTICCOM S.A.C.</span> - Todos los derechos reservados.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggler = document.getElementById('globalThemeToggler');
    if (!toggler) return; // Validación de seguridad
    
    const iconDark = toggler.querySelector('.icon-dark-mode');
    const iconLight = toggler.querySelector('.icon-light-mode');
    
    const syncThemeIcons = (theme) => {
        if (theme === 'dark') {
            iconDark.classList.add('d-none');
            iconLight.classList.remove('d-none');
        } else {
            iconDark.classList.remove('d-none');
            iconLight.classList.add('d-none');
        }
    };
    
    // Sincroniza al cargar la página
    syncThemeIcons(document.documentElement.getAttribute('data-bs-theme'));
    
    // Escucha el click
    toggler.addEventListener('click', () => {
        const activeTheme = document.documentElement.getAttribute('data-bs-theme');
        const targetTheme = activeTheme === 'dark' ? 'light' : 'dark';
        
        // Aplica el tema al HTML
        document.documentElement.setAttribute('data-bs-theme', targetTheme);
        // Guarda en LocalStorage
        localStorage.setItem('opticcom_theme', targetTheme);
        // Cambia el icono
        syncThemeIcons(targetTheme);
    });
});
</script>

</body>
</html>