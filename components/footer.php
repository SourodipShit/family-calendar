<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "";
?>
    <?php if (!isset($no_wrapper) || !$no_wrapper): ?>
    </div>
    <?php endif; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Datepicker -->
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <!-- Custom JS -->
    <script src="<?php echo $path_prefix; ?>public/js/alert.js"></script>
    <script src="<?php echo $path_prefix; ?>public/js/script.js?v=<?php echo time(); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const msg = urlParams.get('msg');

            if (status && msg) {
                if (typeof showAlert === 'function') {
                    showAlert(decodeURIComponent(msg), status);
                }
                
                // Clean up URL parameters without refreshing
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
            }
        });
    </script>
</body>

</html>
