<?php
$path_prefix = "../";
$page_title = "Global Settings";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-3 p-md-4" style="max-width: 1200px;">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-0">Global Settings</h4>
                <p class="text-muted small mb-0">Manage system-wide configurations and defaults.</p>
            </div>
        </div>

        <div class="row g-3">
            <!-- Settings Navigation -->
            <div class="col-lg-3 mb-3">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="list-group list-group-flush settings-nav">
                        <a href="#event-types" class="list-group-item list-group-item-action active py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-calendar-event-line me-3 fs-5 text-warning"></i>
                            <div>
                                <span class="fw-bold d-block small">Event Types</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Default categories & colors</small>
                            </div>
                        </a>
                        <!-- Future tabs can go here -->
                        <a href="#grocery-categories" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-shopping-basket-2-line me-3 fs-5 text-success"></i>
                            <div>
                                <span class="fw-bold d-block small">Grocery Categories</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Default shopping list labels</small>
                            </div>
                        </a>
                        <a href="#general-settings" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-settings-line me-3 fs-5 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block small">General</span>
                                <small class="text-muted" style="font-size: 0.7rem;">System name & logo</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Event Types -->
                    <div class="tab-pane fade show active" id="event-types">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                        <i class="ri-calendar-event-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Global Event Types</h5>
                                        <p class="text-muted small mb-0">These types will be available to all families by default.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm" style="width: 200px;">
                                        <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                                        <input type="text" id="eventTypeSearch" class="form-control bg-light border-0" placeholder="Search...">
                                    </div>
                                    <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#eventTypeModal" onclick="prepareEventTypeModal('add')">
                                        <i class="ri-add-line me-1"></i> Add Global Type
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="eventTypesTable" class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Label</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted text-center">Color</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Scope</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Family</th>
                                            <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="event-types-table-body" class="border-0">
                                        <!-- Populated via AJAX -->
                                        <tr id="loading-row">
                                            <td colspan="4" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                <span class="small text-muted">Loading event types...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Grocery Categories -->
                    <div class="tab-pane fade" id="grocery-categories">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                        <i class="ri-shopping-basket-2-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Global Grocery Categories</h5>
                                        <p class="text-muted small mb-0">These categories will be available to all families by default.</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm" style="width: 200px;">
                                        <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                                        <input type="text" id="groceryCategorySearch" class="form-control bg-light border-0" placeholder="Search...">
                                    </div>
                                    <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#groceryCategoryModal" onclick="prepareGroceryCategoryModal('add')">
                                        <i class="ri-add-line me-1"></i> Add Global Category
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="groceryCategoriesTable" class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Name</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Scope</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Family</th>
                                            <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="grocery-categories-table-body" class="border-0">
                                        <tr id="grocery-loading-row">
                                            <td colspan="4" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                <span class="small text-muted">Loading categories...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="tab-pane fade" id="general-settings">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                    <i class="ri-settings-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">General Settings</h5>
                                    <p class="text-muted small mb-0">Update system logo and general information.</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <form id="logoForm" enctype="multipart/form-data">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">System Logo</label>
                                            <div class="d-flex align-items-center gap-4 mt-2">
                                                <div id="logoPreviewContainer" class="bg-light rounded-3 d-flex align-items-center justify-content-center border-dashed border-2 overflow-hidden" style="width: 120px; height: 120px; border: 2px dashed #dee2e6; cursor: pointer;" onclick="document.getElementById('logoInput').click();">
                                                    <div id="logoPlaceholder" class="text-center">
                                                        <i class="ri-image-add-line fs-1 text-muted"></i>
                                                        <p class="extra-small text-muted mb-0">Upload Logo</p>
                                                    </div>
                                                    <img id="logoPreview" src="" class="w-100 h-100 d-none" style="object-fit: contain;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*" onchange="previewLogo(this)">
                                                    <button type="button" class="btn btn-outline-primary btn-sm mb-2 px-3" onclick="document.getElementById('logoInput').click();">
                                                        <i class="ri-upload-2-line me-1"></i> Choose New Logo
                                                    </button>
                                                    <p class="extra-small text-muted mb-0">Recommended: Transparent PNG or SVG.<br>Max size: 2MB.</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 pt-3 border-top">
                                            <button type="button" id="saveLogoBtn" class="btn btn-primary px-4">
                                                <i class="ri-save-line me-1"></i> Save Changes
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

    .ls-1 {
        letter-spacing: 0.05rem;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-1px);
    }
</style>

<!-- Event Type Modal -->
<div class="modal fade" id="eventTypeModal" tabindex="-1" aria-labelledby="eventTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="eventTypeModalLabel">Add Global Event Type</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="eventTypeForm">
                    <input type="hidden" id="event_type_id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Label Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="event_type_name" name="name" placeholder="e.g. Work, School" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold text-dark small">Color Theme <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2 bg-light p-2 rounded-3">
                            <input type="color" class="form-control form-control-color border-0 p-0 bg-transparent" id="event_type_colour" name="colour" value="#0d6efd" style="width: 40px; height: 30px;">
                            <span class="small text-muted" id="color-hex-label">#0D6EFD</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveEventTypeBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm">Save Type</button>
            </div>
        </div>
    </div>
</div>

<!-- Grocery Category Modal -->
<div class="modal fade" id="groceryCategoryModal" tabindex="-1" aria-labelledby="groceryCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="groceryCategoryModalLabel">Add Global Grocery Category</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="groceryCategoryForm">
                    <input type="hidden" id="grocery_category_id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Category Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="grocery_category_name" name="name" placeholder="e.g. Dairy, Produce, Bakery" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveGroceryCategoryBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm">Save Category</button>
            </div>
        </div>
    </div>
</div>

<script>
    const ADMIN_API_PATH = '<?php echo $path_prefix; ?>api/admin/event_types.php';
    const SETTINGS_API_PATH = '<?php echo $path_prefix; ?>api/admin/settings.php';

    document.addEventListener('DOMContentLoaded', () => {
        loadEventTypes();
        loadGroceryCategories();
        loadGeneralSettings();

        document.getElementById('event_type_colour').addEventListener('input', (e) => {
            document.getElementById('color-hex-label').innerText = e.target.value.toUpperCase();
        });
    });

    async function loadEventTypes() {
        const tableBody = document.getElementById('event-types-table-body');
        try {
            const response = await fetch(`${ADMIN_API_PATH}?action=list`);
            const result = await response.json();

            if (result.status === 'success') {
                renderEventTypesTable(result.data);
            } else {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">${result.message}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading event types:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderEventTypesTable(types) {
        const tableBody = document.getElementById('event-types-table-body');

        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#eventTypesTable')) {
            $('#eventTypesTable').DataTable().destroy();
        }

        tableBody.innerHTML = '';

        if (types.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted small">No global event types found.</td></tr>';
            return;
        }

        types.forEach(type => {
            const tr = document.createElement('tr');
            let badge = '';
            tr.className = 'border-bottom';
            if (type.is_default == 1 && type.family_id == null) {
                badge = `<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill extra-small">Global</span>`;
            } else {
                badge = `<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill extra-small">Family</span>`;
            }

            let familyContent = type.family_name 
                ? `<span class="small fw-semibold text-dark">${type.family_name}</span>`
                : `<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small border">N/A</span>`;

            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${type.name}</td>
                <td class="py-2 text-center">
                    <div class="rounded-circle border border-2 border-white shadow-sm d-inline-block" style="width: 18px; height: 18px; background-color: ${type.colour};"></div>
                </td>
                <td class="py-2">
                    ${badge}
                </td>
                <td class="py-2">
                    ${familyContent}
                </td>
                <td class="px-3 py-2 text-end">
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editEventType(${type.id})" title="Edit">
                        <i class="ri-pencil-line fs-6"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteEventType(${type.id})" title="Delete">
                        <i class="ri-delete-bin-line fs-6"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        // Re-initialize DataTable
        const table = $('#eventTypesTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center p-3"ip>',
            "pageLength": 10,
            "destroy": true,
            "language": {
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": 4 }
            ]
        });

        // Custom search
        document.getElementById('eventTypeSearch').addEventListener('keyup', function() {
            table.search(this.value).draw();
        });
    }

    function prepareEventTypeModal(action, id = null) {
        const modalTitle = document.getElementById('eventTypeModalLabel');
        const form = document.getElementById('eventTypeForm');
        form.reset();
        document.getElementById('event_type_id').value = '';
        document.getElementById('color-hex-label').innerText = '#0D6EFD';

        if (action === 'add') {
            modalTitle.innerText = 'Add Global Event Type';
        } else {
            modalTitle.innerText = 'Edit Global Event Type';
        }
    }

    async function editEventType(id) {
        try {
            const response = await fetch(`${ADMIN_API_PATH}?action=get&id=${id}`);
            const result = await response.json();
            if (result.status === 'success') {
                prepareEventTypeModal('edit', id);
                document.getElementById('event_type_id').value = result.data.id;
                document.getElementById('event_type_name').value = result.data.name;
                document.getElementById('event_type_colour').value = result.data.colour;
                document.getElementById('color-hex-label').innerText = result.data.colour.toUpperCase();

                const modal = new bootstrap.Modal(document.getElementById('eventTypeModal'));
                modal.show();
            }
        } catch (error) {
            console.error('Error fetching event type:', error);
            if (typeof showAlert === 'function') showAlert('Failed to fetch event type details', 'error');
            else alert('Failed to fetch event type details');
        }
    }

    async function deleteEventType(id) {
        if (confirm('Are you sure you want to delete this global event type? This will affect all families.')) {
            try {
                const response = await fetch(`${ADMIN_API_PATH}?action=delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    if (typeof showAlert === 'function') showAlert('Event type deleted successfully', 'success');
                    else alert('Event type deleted successfully');
                    loadEventTypes();
                } else {
                    if (typeof showAlert === 'function') showAlert(result.message, 'error');
                    else alert(result.message);
                }
            } catch (error) {
                console.error('Error deleting event type:', error);
                if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
                else alert('Network error occurred');
            }
        }
    }

    document.getElementById('saveEventTypeBtn').addEventListener('click', async () => {
        const form = document.getElementById('eventTypeForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const action = data.id ? 'update' : 'create';

        const btn = document.getElementById('saveEventTypeBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${ADMIN_API_PATH}?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message, 'success');
                else alert(result.message);

                const modalEl = document.getElementById('eventTypeModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                loadEventTypes();
            } else {
                if (typeof showAlert === 'function') showAlert(result.message, 'error');
                else alert(result.message);
            }
        } catch (error) {
            console.error('Error saving event type:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    // --- General Settings Logic ---

    async function loadGeneralSettings() {
        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=fetch_all`);
            const result = await response.json();

            if (result.status === 'success') {
                const settings = result.data;
                const logoSetting = settings.find(s => s.setting_key === 'site_logo');
                
                if (logoSetting && logoSetting.setting_value) {
                    const logoPreview = document.getElementById('logoPreview');
                    const logoPlaceholder = document.getElementById('logoPlaceholder');
                    
                    // The path is stored as ../public/uploads/logo/...
                    // We need to adjust it if it's already a relative path from the root
                    let logoPath = logoSetting.setting_value;
                    if (logoPath.startsWith('../')) {
                        logoPath = '<?php echo $path_prefix; ?>' + logoPath.replace('../', '');
                    }

                    logoPreview.src = logoPath;
                    logoPreview.classList.remove('d-none');
                    logoPlaceholder.classList.add('d-none');
                }
            }
        } catch (error) {
            console.error('Error loading settings:', error);
        }
    }

    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const logoPreview = document.getElementById('logoPreview');
                const logoPlaceholder = document.getElementById('logoPlaceholder');
                
                logoPreview.src = e.target.result;
                logoPreview.classList.remove('d-none');
                logoPlaceholder.classList.add('d-none');
                document.getElementById('logoPreviewContainer').style.border = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.getElementById('saveLogoBtn').addEventListener('click', async () => {
        const fileInput = document.getElementById('logoInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            if (typeof showAlert === 'function') showAlert('Please select a logo to upload', 'warning');
            else alert('Please select a logo to upload');
            return;
        }

        const formData = new FormData();
        formData.append('logo', fileInput.files[0]);

        const btn = document.getElementById('saveLogoBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=update_logo`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message, 'success');
                else alert(result.message);
                loadGeneralSettings();
            } else {
                if (typeof showAlert === 'function') showAlert(result.message, 'error');
                else alert(result.message);
            }
        } catch (error) {
            console.error('Error updating logo:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    // --- Grocery Categories Logic ---

    const GROCERY_API_PATH = '<?php echo $path_prefix; ?>api/admin/grocery_categories.php';

    async function loadGroceryCategories() {
        const tableBody = document.getElementById('grocery-categories-table-body');
        try {
            const response = await fetch(`${GROCERY_API_PATH}?action=list`);
            const result = await response.json();

            if (result.status === 'success' || result.status === true) {
                renderGroceryCategoriesTable(result.data);
            } else {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">${result.message || 'No categories found'}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading grocery categories:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderGroceryCategoriesTable(categories) {
        const tableBody = document.getElementById('grocery-categories-table-body');

        if ($.fn.DataTable.isDataTable('#groceryCategoriesTable')) {
            $('#groceryCategoriesTable').DataTable().destroy();
        }

        tableBody.innerHTML = '';

        if (categories.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted small">No global grocery categories found.</td></tr>';
            return;
        }

        categories.forEach(category => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';
            
            let badge = category.is_default == 1 && category.family_id == null
                ? `<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill extra-small">Global</span>`
                : `<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill extra-small">Family</span>`;

            let familyContent = category.family_name 
                ? `<span class="small fw-semibold text-dark">${category.family_name}</span>`
                : `<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small border">N/A</span>`;

            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${category.name}</td>
                <td class="py-2">${badge}</td>
                <td class="py-2">${familyContent}</td>
                <td class="px-3 py-2 text-end">
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editGroceryCategory(${category.id})" title="Edit">
                        <i class="ri-pencil-line fs-6"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteGroceryCategory(${category.id})" title="Delete">
                        <i class="ri-delete-bin-line fs-6"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        const table = $('#groceryCategoriesTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center p-3"ip>',
            "pageLength": 10,
            "destroy": true,
            "language": {
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": 3 }
            ]
        });

        document.getElementById('groceryCategorySearch').addEventListener('keyup', function() {
            table.search(this.value).draw();
        });
    }

    function prepareGroceryCategoryModal(action) {
        const modalTitle = document.getElementById('groceryCategoryModalLabel');
        const form = document.getElementById('groceryCategoryForm');
        form.reset();
        document.getElementById('grocery_category_id').value = '';

        if (action === 'add') {
            modalTitle.innerText = 'Add Global Grocery Category';
        } else {
            modalTitle.innerText = 'Edit Global Grocery Category';
        }
    }

    async function editGroceryCategory(id) {
        try {
            const response = await fetch(`${GROCERY_API_PATH}?action=list`);
            const result = await response.json();
            if (result.status === 'success' || result.status === true) {
                const category = result.data.find(c => c.id == id);
                if (category) {
                    prepareGroceryCategoryModal('edit');
                    document.getElementById('grocery_category_id').value = category.id;
                    document.getElementById('grocery_category_name').value = category.name;

                    const modal = new bootstrap.Modal(document.getElementById('groceryCategoryModal'));
                    modal.show();
                }
            }
        } catch (error) {
            console.error('Error fetching grocery category:', error);
            if (typeof showAlert === 'function') showAlert('Failed to fetch category details', 'error');
            else alert('Failed to fetch category details');
        }
    }

    async function deleteGroceryCategory(id) {
        if (confirm('Are you sure you want to delete this global grocery category? This will affect all families.')) {
            try {
                const response = await fetch(`${GROCERY_API_PATH}?action=delete&id=${id}`, {
                    method: 'POST'
                });
                const result = await response.json();
                if (result.status === 'success' || result.status === true) {
                    if (typeof showAlert === 'function') showAlert('Category deleted successfully', 'success');
                    else alert('Category deleted successfully');
                    loadGroceryCategories();
                } else {
                    if (typeof showAlert === 'function') showAlert(result.message || 'Failed to delete category', 'error');
                    else alert(result.message || 'Failed to delete category');
                }
            } catch (error) {
                console.error('Error deleting grocery category:', error);
                if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
                else alert('Network error occurred');
            }
        }
    }

    document.getElementById('saveGroceryCategoryBtn').addEventListener('click', async () => {
        const form = document.getElementById('groceryCategoryForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const action = data.id ? 'update' : 'create';

        const btn = document.getElementById('saveGroceryCategoryBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const url = `${GROCERY_API_PATH}?action=${action}${data.id ? `&id=${data.id}` : ''}`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success' || result.status === true) {
                if (typeof showAlert === 'function') showAlert(result.message || 'Category saved successfully', 'success');
                else alert(result.message || 'Category saved successfully');
                
                const modalEl = document.getElementById('groceryCategoryModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                
                loadGroceryCategories();
            } else {
                if (typeof showAlert === 'function') showAlert(result.message || 'Failed to save category', 'error');
                else alert(result.message || 'Failed to save category');
            }
        } catch (error) {
            console.error('Error saving grocery category:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
</script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>