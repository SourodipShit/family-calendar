<?php
$path_prefix = "../";
$page_title = "Settings";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
require_once $path_prefix . 'classes/EventTypes.php';

$family_id = $_SESSION['user']['families'][0]['family_id'] ?? null;
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-3 p-md-4" style="max-width: 1200px;">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="fw-bold text-dark mb-0">Settings</h4>
                <p class="text-muted small mb-0">Manage your family account and preferences.</p>
            </div>
        </div>

        <div class="row g-3">
            <!-- Settings Navigation -->
            <div class="col-lg-3 mb-3">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                    <div class="list-group list-group-flush settings-nav">
                        <a href="#family-details" class="list-group-item list-group-item-action active py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-home-4-line me-3 fs-5 text-primary"></i>
                            <div>
                                <span class="fw-bold d-block small">Family Profile</span>
                                <small class="opacity-75" style="font-size: 0.7rem;">Basic info & location</small>
                            </div>
                        </a>
                        <a href="#general-settings" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-settings-4-line me-3 fs-5 text-secondary"></i>
                            <div>
                                <span class="fw-bold d-block small">General Settings</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Display & app preferences</small>
                            </div>
                        </a>
                        <a href="#event-types" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-calendar-event-line me-3 fs-5 text-warning"></i>
                            <div>
                                <span class="fw-bold d-block small">Event Types</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Custom categories & colors</small>
                            </div>
                        </a>
                        <a href="#grocery-categories" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-shopping-basket-2-line me-3 fs-5 text-success"></i>
                            <div>
                                <span class="fw-bold d-block small">Grocery Categories</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Organize your shopping list</small>
                            </div>
                        </a>
                        <!-- Commented out tabs as requested -->
                        <!-- 
                        <a href="#meal-types" class="list-group-item list-group-item-action py-2 px-3 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-restaurant-line me-2 fs-5 text-success"></i>
                            <div>
                                <span class="fw-bold d-block small">Meal Types</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Daily meal planning labels</small>
                            </div>
                        </a>
                        <a href="#chore-types" class="list-group-item list-group-item-action py-2 px-3 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-checkbox-circle-line me-2 fs-5 text-info"></i>
                            <div>
                                <span class="fw-bold d-block small">Chore Categories</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Organize household tasks</small>
                            </div>
                        </a>
                        <a href="#notifications" class="list-group-item list-group-item-action py-2 px-3 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-notification-3-line me-2 fs-5 text-danger"></i>
                            <div>
                                <span class="fw-bold d-block small">Notifications</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Email & push alerts</small>
                            </div>
                        </a>
                        -->
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Family Profile -->
                    <div class="tab-pane fade show active" id="family-details">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                    <i class="ri-home-4-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Family Profile</h5>
                                    <p class="text-muted small mb-0">Update your family's core information.</p>
                                </div>
                            </div>

                            <form id="familyProfileForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase ls-1">Family Name</label>
                                        <div class="input-group border rounded-3 overflow-hidden shadow-sm bg-light border-light">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="ri-user-line"></i></span>
                                            <input type="text" class="form-control border-0 bg-transparent py-1 px-1 small" id="family_name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase ls-1">Primary Email</label>
                                        <div class="input-group border rounded-3 overflow-hidden shadow-sm bg-light border-light">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="ri-mail-line"></i></span>
                                            <input type="email" class="form-control border-0 bg-transparent py-1 px-1 small" id="family_email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase ls-1">Location</label>
                                        <div class="input-group border rounded-3 overflow-hidden shadow-sm bg-light border-light">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="ri-map-pin-line"></i></span>
                                            <input type="text" class="form-control border-0 bg-transparent py-1 px-1 small" id="family_location" name="location">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase ls-1">Time Zone</label>
                                        <div class="input-group border rounded-3 overflow-hidden shadow-sm bg-light border-light">
                                            <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="ri-time-line"></i></span>
                                            <select class="form-select border-0 bg-transparent py-1 px-1 cursor-pointer small" id="family_timezone" name="timezone">
                                                <?php
                                                require_once $path_prefix . 'classes/GlobalSettings.php';
                                                $timezone_setting = GlobalSettings::getSetting('timezone');
                                                $timezones = [];
                                                if ($timezone_setting['status'] === 'success' && !empty($timezone_setting['data'])) {
                                                    $timezones = json_decode($timezone_setting['data']['setting_value'], true);
                                                }
                                                if (is_array($timezones)) {
                                                    foreach ($timezones as $tz) {
                                                        $val = htmlspecialchars($tz['timezone']);
                                                        $lbl = htmlspecialchars($tz['lable']);
                                                        echo "<option value=\"$val\">$lbl</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-light btn-sm px-3 py-1 fw-medium rounded-2 border" onclick="loadFamilyProfile()">Discard</button>
                                            <button type="submit" id="saveFamilyProfileBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2">Save Changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="tab-pane fade" id="general-settings">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-secondary bg-opacity-10 text-secondary p-2 rounded-3 me-3">
                                    <i class="ri-settings-4-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">General Settings</h5>
                                    <p class="text-muted small mb-0">Manage app-wide display preferences.</p>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                        <div>
                                            <h6 class="fw-bold mb-1 small">Show Nicknames</h6>
                                            <p class="text-muted extra-small mb-0">Display user nicknames instead of full names on the calendar.</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="show_nicknames_toggle" style="width: 40px; height: 20px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Types -->
                    <div class="tab-pane fade" id="event-types">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                        <i class="ri-calendar-event-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Event Types</h5>
                                        <p class="text-muted small mb-0">Customize categories for your family events.</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#eventTypeModal" onclick="prepareEventTypeModal('add')">
                                    <i class="ri-add-line me-1"></i> Add Type
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Label</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted text-center">Color</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Type</th>
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
                                        <h5 class="fw-bold mb-0">Grocery Categories</h5>
                                        <p class="text-muted small mb-0">Manage categories for your grocery items.</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#groceryCategoryModal" onclick="prepareGroceryCategoryModal('add')">
                                    <i class="ri-add-line me-1"></i> Add Category
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Name</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Type</th>
                                            <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="grocery-categories-table-body" class="border-0">
                                        <tr id="grocery-loading-row">
                                            <td colspan="3" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                <span class="small text-muted">Loading categories...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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

    .settings-nav .list-group-item:not(.active):hover {
        background-color: #f8f9fa;
    }

    .ls-1 {
        letter-spacing: 0.05rem;
    }

    .extra-small {
        font-size: 0.65rem;
    }

    .ls-1 {
        letter-spacing: 0.05rem;
    }

    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-1px);
    }

    .transition-all {
        transition: all 0.2s ease;
    }

    .form-label {
        margin-bottom: 0.25rem;
    }

    .input-group-text {
        padding-right: 0.5rem;
    }
</style>

<!-- Event Type Modal -->
<div class="modal fade" id="eventTypeModal" tabindex="-1" aria-labelledby="eventTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="eventTypeModalLabel">Add Event Type</h6>
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
                    <div class="mb-2 mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="allow_multiple_day" name="allow_multiple_day" value="1">
                            <label class="form-check-label fw-semibold text-dark small" for="allow_multiple_day">Allow Multi-Day Events</label>
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
                <h6 class="modal-title fw-bold" id="groceryCategoryModalLabel">Add Grocery Category</h6>
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
    let familySettings = {};

    document.addEventListener('DOMContentLoaded', () => {
        loadFamilyProfile();
        loadEventTypes();
        loadGroceryCategories();

        document.getElementById('event_type_colour').addEventListener('input', (e) => {
            document.getElementById('color-hex-label').innerText = e.target.value.toUpperCase();
        });
    });

    async function loadFamilyProfile() {
        try {
            const response = await fetch(`${API_PATH}family.php?action=get`);
            const result = await response.json();
            if (result.status === 'success') {
                const family = result.data;
                document.getElementById('family_name').value = family.name;
                document.getElementById('family_email').value = family.email;
                document.getElementById('family_location').value = family.location;
                document.getElementById('family_timezone').value = family.timezone;

                // Load general settings
                if (family.settings) {
                    try {
                        familySettings = typeof family.settings === 'string' ? JSON.parse(family.settings) : family.settings;
                        document.getElementById('show_nicknames_toggle').checked = familySettings.show_nicknames || false;
                    } catch (e) {
                        console.error('Error parsing settings:', e);
                        familySettings = {};
                    }
                } else {
                    familySettings = {};
                }
            }
        } catch (error) {
            console.error('Error loading family profile:', error);
        }
    }

    document.getElementById('familyProfileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const btn = document.getElementById('saveFamilyProfileBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${API_PATH}family.php?action=update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert(result.message, 'success');
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving family profile:', error);
            showAlert('Network error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    document.getElementById('show_nicknames_toggle').addEventListener('change', async (e) => {
        const isChecked = e.target.checked;
        
        // Update local state
        familySettings.show_nicknames = isChecked;
        
        try {
            const response = await fetch(`${API_PATH}family.php?action=updateSettings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    settings: familySettings
                })
            });
            const result = await response.json();
            if (result.status !== 'success') {
                showAlert(result.message, 'error');
                // Revert local state and UI
                familySettings.show_nicknames = !isChecked;
                e.target.checked = !isChecked;
            }
        } catch (error) {
            console.error('Error updating settings:', error);
            showAlert('Network error occurred', 'error');
            // Revert local state and UI
            familySettings.show_nicknames = !isChecked;
            e.target.checked = !isChecked;
        }
    });

    async function loadEventTypes() {
        const tableBody = document.getElementById('event-types-table-body');
        try {
            const response = await fetch(`${API_PATH}event_types.php?action=list`);
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
        tableBody.innerHTML = '';

        if (types.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted small">No event types found.</td></tr>';
            return;
        }

        types.forEach(type => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';
            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${type.name}</td>
                <td class="py-2 text-center">
                    <div class="rounded-circle border border-2 border-white shadow-sm d-inline-block" style="width: 18px; height: 18px; background-color: ${type.colour};"></div>
                </td>
                <td class="py-2">
                    ${type.is_default 
                        ? '<span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill extra-small">System</span>' 
                        : '<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small">Custom</span>'}
                </td>
                <td class="px-3 py-2 text-end">
                    ${!type.is_default ? `
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editEventType(${type.id})" title="Edit">
                            <i class="ri-pencil-line fs-6"></i>
                        </button>
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteEventType(${type.id})" title="Delete">
                            <i class="ri-delete-bin-line fs-6"></i>
                        </button>
                    ` : `
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 disabled opacity-25" style="width: 32px; height: 32px;" title="System defined">
                            <i class="ri-lock-line fs-6"></i>
                        </button>
                    `}
                </td>
            `;
            tableBody.appendChild(tr);
        });
    }

    function prepareEventTypeModal(action, id = null) {
        const modalTitle = document.getElementById('eventTypeModalLabel');
        const form = document.getElementById('eventTypeForm');
        form.reset();
        document.getElementById('event_type_id').value = '';
        document.getElementById('color-hex-label').innerText = '#0D6EFD';

        if (action === 'add') {
            modalTitle.innerText = 'Add Event Type';
        } else {
            modalTitle.innerText = 'Edit Event Type';
        }
    }

    async function editEventType(id) {
        prepareEventTypeModal('edit', id);
        try {
            const response = await fetch(`${API_PATH}event_types.php?action=get&id=${id}`);
            const result = await response.json();
            if (result.status === 'success') {
                document.getElementById('event_type_id').value = result.data.id;
                document.getElementById('event_type_name').value = result.data.name;
                document.getElementById('event_type_colour').value = result.data.colour;
                document.getElementById('color-hex-label').innerText = result.data.colour.toUpperCase();
                document.getElementById('allow_multiple_day').checked = result.data.allow_multiple_day == 1;

                const modal = new bootstrap.Modal(document.getElementById('eventTypeModal'));
                modal.show();
            }
        } catch (error) {
            console.error('Error fetching event type:', error);
            showAlert('Failed to fetch event type details', 'error');
        }
    }

    async function deleteEventType(id) {
        if (confirm('Are you sure you want to delete this event type?')) {
            try {
                const response = await fetch(`${API_PATH}event_types.php?action=delete`, {
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
                    showAlert('Event type deleted successfully', 'success');
                    loadEventTypes();
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting event type:', error);
                showAlert('Network error occurred', 'error');
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
            const response = await fetch(`${API_PATH}event_types.php?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert(result.message, 'success');

                // Hide modal using bootstrap instance
                const modalEl = document.getElementById('eventTypeModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                // Refresh table without reload
                loadEventTypes();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving event type:', error);
            showAlert('Network error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
    async function loadGroceryCategories() {
        const tableBody = document.getElementById('grocery-categories-table-body');
        try {
            const response = await fetch(`${API_PATH}grocery_categories.php?action=list`);
            const result = await response.json();

            if (result.status === 'success' || result.status === true) {
                renderGroceryCategoriesTable(result.data);
            } else {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">${result.message || 'No categories found'}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading grocery categories:', error);
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderGroceryCategoriesTable(categories) {
        const tableBody = document.getElementById('grocery-categories-table-body');
        tableBody.innerHTML = '';

        if (categories.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted small">No grocery categories found.</td></tr>';
            return;
        }

        categories.forEach(category => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';
            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${category.name}</td>
                <td class="py-2">
                    ${category.is_default == 1 
                        ? '<span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill extra-small">System</span>' 
                        : '<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small">Custom</span>'}
                </td>
                <td class="px-3 py-2 text-end">
                    ${category.is_default == 0 ? `
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editGroceryCategory(${category.id})" title="Edit">
                            <i class="ri-pencil-line fs-6"></i>
                        </button>
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteGroceryCategory(${category.id})" title="Delete">
                            <i class="ri-delete-bin-line fs-6"></i>
                        </button>
                    ` : `
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 disabled opacity-25" style="width: 32px; height: 32px;" title="System defined">
                            <i class="ri-lock-line fs-6"></i>
                        </button>
                    `}
                </td>
            `;
            tableBody.appendChild(tr);
        });
    }

    function prepareGroceryCategoryModal(action) {
        const modalTitle = document.getElementById('groceryCategoryModalLabel');
        const form = document.getElementById('groceryCategoryForm');
        form.reset();
        document.getElementById('grocery_category_id').value = '';

        if (action === 'add') {
            modalTitle.innerText = 'Add Grocery Category';
        } else {
            modalTitle.innerText = 'Edit Grocery Category';
        }
    }

    async function editGroceryCategory(id) {
        try {
            const response = await fetch(`${API_PATH}grocery_categories.php?action=list`);
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
            showAlert('Failed to fetch category details', 'error');
        }
    }

    async function deleteGroceryCategory(id) {
        if (confirm('Are you sure you want to delete this grocery category?')) {
            try {
                const response = await fetch(`${API_PATH}grocery_categories.php?action=delete&id=${id}`, {
                    method: 'POST'
                });
                const result = await response.json();
                if (result.status === 'success' || result.status === true) {
                    showAlert('Category deleted successfully', 'success');
                    loadGroceryCategories();
                } else {
                    showAlert(result.message || 'Failed to delete category', 'error');
                }
            } catch (error) {
                console.error('Error deleting grocery category:', error);
                showAlert('Network error occurred', 'error');
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
            const url = `${API_PATH}grocery_categories.php?action=${action}${data.id ? `&id=${data.id}` : ''}`;
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success' || result.status === true) {
                showAlert(result.message || 'Category saved successfully', 'success');
                const modalEl = document.getElementById('groceryCategoryModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                loadGroceryCategories();
            } else {
                showAlert(result.message || 'Failed to save category', 'error');
            }
        } catch (error) {
            console.error('Error saving grocery category:', error);
            showAlert('Network error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>