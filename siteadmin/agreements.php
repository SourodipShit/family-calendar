<?php
$path_prefix = "../";
$page_title = "Legal Agreements";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-3 p-md-4" style="max-width: 1200px;">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-0">Legal Agreements</h4>
                <p class="text-muted small mb-0">Manage terms of service, privacy policy, and opt-in/opt-out agreements.</p>
            </div>
        </div>

        <div class="row g-3">
            <!-- Settings Navigation -->
            <div class="col-lg-3 mb-3">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="list-group list-group-flush settings-nav" role="tablist">
                        <a class="list-group-item list-group-item-action active py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list" href="#terms-of-service" role="tab">
                            <i class="ri-file-text-line me-3 fs-5 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block small">Terms of Service</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Main terms agreement</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list" href="#privacy-policy" role="tab">
                            <i class="ri-shield-keyhole-line me-3 fs-5 text-success"></i>
                            <div>
                                <span class="fw-bold d-block small">Privacy Policy</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Data protection policy</small>
                            </div>
                        </a>
                        <a class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list" href="#opt-in-agreement" role="tab">
                            <i class="ri-checkbox-circle-line me-3 fs-5 text-warning"></i>
                            <div>
                                <span class="fw-bold d-block small">Opt-In/Out</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Marketing & comms consent</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Terms of Service -->
                    <div class="tab-pane fade show active" id="terms-of-service" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                    <i class="ri-file-text-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Terms of Service</h5>
                                    <p class="text-muted small mb-0">Update the terms of service content. You can use HTML tags.</p>
                                </div>
                            </div>
                            <form id="termsForm">
                                <div class="mb-3">
                                    <div class="bg-light border-light" id="terms_of_service" style="height: 400px;"></div>
                                </div>
                                <div class="mt-4 pt-3 border-top text-end">
                                    <button type="button" class="btn btn-primary px-4" onclick="saveAgreement('terms_of_service', event)">
                                        <i class="ri-save-line me-1"></i> Save Terms
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Privacy Policy -->
                    <div class="tab-pane fade" id="privacy-policy" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                    <i class="ri-shield-keyhole-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Privacy Policy</h5>
                                    <p class="text-muted small mb-0">Update the privacy policy content. You can use HTML tags.</p>
                                </div>
                            </div>
                            <form id="privacyForm">
                                <div class="mb-3">
                                    <div class="bg-light border-light" id="privacy_policy" style="height: 400px;"></div>
                                </div>
                                <div class="mt-4 pt-3 border-top text-end">
                                    <button type="button" class="btn btn-primary px-4" onclick="saveAgreement('privacy_policy', event)">
                                        <i class="ri-save-line me-1"></i> Save Privacy Policy
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Opt-In/Opt-Out Agreement -->
                    <div class="tab-pane fade" id="opt-in-agreement" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                    <i class="ri-checkbox-circle-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Opt-In/Opt-Out Agreement</h5>
                                    <p class="text-muted small mb-0">Update the opt-in/opt-out agreement content. You can use HTML tags.</p>
                                </div>
                            </div>
                            <form id="optInForm">
                                <div class="mb-3">
                                    <div class="bg-light border-light" id="opt_in_agreement" style="height: 400px;"></div>
                                </div>
                                <div class="mt-4 pt-3 border-top text-end">
                                    <button type="button" class="btn btn-primary px-4" onclick="saveAgreement('opt_in_agreement', event)">
                                        <i class="ri-save-line me-1"></i> Save Agreement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .settings-nav .list-group-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent !important;
    }

    .settings-nav .list-group-item.active {
        background-color: rgba(13, 110, 253, 0.05);
        color: #0d6efd;
        border-left-color: #0d6efd !important;
    }

    .settings-nav .list-group-item:not(.active):hover:not(.disabled) {
        background-color: #f8f9fa;
    }
</style>

<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const SETTINGS_API_PATH = '<?php echo $path_prefix; ?>api/admin/settings.php';
    const editors = {};

    document.addEventListener('DOMContentLoaded', () => {
        const toolbarOptions = [
            ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],               // custom button values
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
            [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
            [{ 'direction': 'rtl' }],                         // text direction
            [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['clean']                                         // remove formatting button
        ];

        editors['terms_of_service'] = new Quill('#terms_of_service', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });

        editors['privacy_policy'] = new Quill('#privacy_policy', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });

        editors['opt_in_agreement'] = new Quill('#opt_in_agreement', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });

        loadAgreements();
    });

    async function loadAgreements() {
        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=fetch_all`);
            const result = await response.json();

            if (result.status === 'success') {
                const settings = result.data;
                settings.forEach(setting => {
                    if (editors[setting.setting_key]) {
                        editors[setting.setting_key].root.innerHTML = setting.setting_value;
                    }
                });
            } else {
                if (typeof showAlert === 'function') showAlert('Failed to load agreements', 'error');
                else alert('Failed to load agreements');
            }
        } catch (error) {
            console.error('Error loading agreements:', error);
            if (typeof showAlert === 'function') showAlert('Failed to connect to server', 'error');
            else alert('Failed to connect to server');
        }
    }

    async function saveAgreement(key, event) {
        const editor = editors[key];
        const value = editor ? editor.root.innerHTML : '';
        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        btn.disabled = true;

        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=update_setting`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    key: key,
                    value: value
                })
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message, 'success');
                else alert(result.message);
            } else {
                if (typeof showAlert === 'function') showAlert(result.message || 'Error saving agreement', 'error');
                else alert(result.message || 'Error saving agreement');
            }
        } catch (error) {
            console.error('Error saving agreement:', error);
            if (typeof showAlert === 'function') showAlert('Failed to connect to server', 'error');
            else alert('Failed to connect to server');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
</script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>
