    </div><!-- .main-content -->
</div><!-- .admin-layout -->

<!-- Footer del Admin -->
<footer class="admin-footer">
    <div class="container-fluid">
        <div class="footer-content">
            <div class="footer-left">
                <span>&copy; <?= date('Y') ?> Coordicanarias. Todos los derechos reservados.</span>
            </div>
            <div class="footer-right">
                <a href="<?= url('index.php') ?>" target="_blank">Ver sitio público</a>
                <span class="separator">|</span>
                <a href="https://coordicanarias.com" target="_blank">coordicanarias.com</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<!-- jQuery -->
<script src="<?= url('js/jquery-3.7.1.min.js') ?>"></script>

<!-- Bootstrap 5 -->
<script src="<?= url('js/bootstrap.bundle.min.js') ?>"></script>

<!-- Script del Admin Panel -->
<script>
// Toggle Sidebar en móviles
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('adminSidebar').classList.toggle('active');
});

// Cerrar sidebar al hacer click fuera en móviles
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('sidebarToggle');

    if (window.innerWidth <= 768) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});

// Auto-cerrar alertas después de 5 segundos
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

<?php if (isset($extra_scripts)): ?>
    <?= $extra_scripts ?>
<?php endif; ?>

</body>
</html>
