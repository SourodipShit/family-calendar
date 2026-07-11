<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
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

    // Fetch and update navbar weather
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?php echo $path_prefix; ?>api/weatherApi.php')
            .then(response => response.json())
            .then(data => {
                if (data && !data.error) {
                    const locEl = document.getElementById('navbar-weather-location');
                    const tempEl = document.getElementById('navbar-weather-temp');
                    const iconEl = document.getElementById('navbar-weather-icon');

                    if (locEl) locEl.textContent = data.location;
                    if (tempEl) tempEl.textContent = data.current_temperature;
                    if (iconEl) iconEl.innerHTML = `<img src="${data.condition_icon_url}" alt="${data.condition_text}" style="width: 40px; height: 40px; object-fit: contain;">`;

                    // Update page widget if it exists (index.php, event-details.php)
                    const wLocEl = document.getElementById('widget-weather-location');
                    const wTempEl = document.getElementById('widget-weather-temp');
                    const wIconEl = document.getElementById('widget-weather-icon');
                    const wDescEl = document.getElementById('widget-weather-desc');
                    const wHighEl = document.getElementById('widget-weather-high');
                    const wLowEl = document.getElementById('widget-weather-low');

                    if (wLocEl) wLocEl.textContent = data.location;
                    if (wTempEl) wTempEl.textContent = data.current_temperature;
                    if (wIconEl) wIconEl.innerHTML = `<img src="${data.condition_icon_url}" alt="${data.condition_text}" style="width: 100px; height: 100px; object-fit: contain;">`;
                    if (wDescEl) wDescEl.textContent = data.condition_text;
                    if (wHighEl) wHighEl.textContent = data.high_temperature;
                    if (wLowEl) wLowEl.textContent = data.low_temperature;
                }
            })
            .catch(error => console.error('Error fetching weather:', error));
    });
</script>
</body>
</html>
