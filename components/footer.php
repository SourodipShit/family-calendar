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
<!-- Family View Modal -->
<div class="modal fade" id="familyViewModal" tabindex="-1" aria-labelledby="familyViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="familyViewModalLabel">Enter PIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pb-4 text-center">
                <form id="familyViewAuthForm" method="post" action="<?php echo $path_prefix; ?>api/family.php?action=verify_family_view">
                    <p class="text-muted small mb-4">Please enter the Family View PIN to proceed.</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <input type="password" class="form-control text-center fw-bold fs-3 p-0 pin-input" style="width: 45px; height: 50px;" maxlength="1" data-index="1" required autofocus>
                        <input type="password" class="form-control text-center fw-bold fs-3 p-0 pin-input" style="width: 45px; height: 50px;" maxlength="1" data-index="2" required>
                        <input type="password" class="form-control text-center fw-bold fs-3 p-0 pin-input" style="width: 45px; height: 50px;" maxlength="1" data-index="3" required>
                        <input type="password" class="form-control text-center fw-bold fs-3 p-0 pin-input" style="width: 45px; height: 50px;" maxlength="1" data-index="4" required>
                    </div>
                    <input type="hidden" id="familyViewPinInput" name="pin" value="">
                    <button type="submit" class="btn btn-primary w-100 fw-medium rounded-pill" id="familyViewSubmitBtn">Unlock</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const familyViewForm = document.getElementById('familyViewAuthForm');
    if (familyViewForm) {
        const pinInputs = document.querySelectorAll('.pin-input');
        const hiddenPinInput = document.getElementById('familyViewPinInput');
        
        pinInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                if (e.target.value.length === 1) {
                    if (index < pinInputs.length - 1) {
                        pinInputs[index + 1].focus();
                    }
                }
                updateHiddenPin();
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && e.target.value === '') {
                    if (index > 0) {
                        pinInputs[index - 1].focus();
                        pinInputs[index - 1].value = '';
                    }
                }
                updateHiddenPin();
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').slice(0, 4);
                if (/^\d+$/.test(pasteData)) {
                    for (let i = 0; i < pasteData.length; i++) {
                        if (pinInputs[i]) {
                            pinInputs[i].value = pasteData[i];
                        }
                    }
                    if (pasteData.length < 4) {
                        pinInputs[pasteData.length].focus();
                    } else {
                        pinInputs[3].focus();
                    }
                    updateHiddenPin();
                }
            });
            
            input.addEventListener('focus', function(e) {
                const emptyInputIndex = Array.from(pinInputs).findIndex(inp => inp.value === '');
                if (emptyInputIndex !== -1) {
                    if (index !== emptyInputIndex) {
                        pinInputs[emptyInputIndex].focus();
                    }
                } else {
                    if (index !== 3) {
                        pinInputs[3].focus();
                    }
                }
            });
        });

        function updateHiddenPin() {
            let pin = '';
            pinInputs.forEach(input => pin += input.value);
            hiddenPinInput.value = pin;
            if (pin.length === 4) {
                familyViewForm.dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }

        familyViewForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('familyViewSubmitBtn');
            const pin = hiddenPinInput.value;
            
            if (pin.length < 4) return;
            
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';
            
            try {
                const response = await fetch(familyViewForm.action, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ pin: pin })
                });
                const result = await response.json();
                
                if (result.status === 'success') {
                    if (typeof showAlert === 'function') showAlert('PIN verified! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = '<?php echo $path_prefix; ?>family/';
                    }, 800);
                } else {
                    if (typeof showAlert === 'function') showAlert(result.message, 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    
                    pinInputs.forEach(input => input.value = '');
                    hiddenPinInput.value = '';
                    pinInputs[0].focus();
                }
            } catch (err) {
                if (typeof showAlert === 'function') showAlert('Network error. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
        
        const modal = document.getElementById('familyViewModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function () {
                pinInputs[0].focus();
            });
            modal.addEventListener('hidden.bs.modal', function () {
                pinInputs.forEach(input => input.value = '');
                hiddenPinInput.value = '';
            });
        }
    }
});
</script>
</body>
</html>