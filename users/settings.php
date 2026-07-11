<?php
$path_prefix = "../";
$page_title = "Settings";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
require_once $path_prefix . 'classes/EventTypes.php';

$family_id = $_SESSION['user']['active_family_id'] ?? null;
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
                        <a href="#personal-settings" class="list-group-item list-group-item-action active py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-user-settings-line me-3 fs-5 text-info"></i>
                            <div>
                                <span class="fw-bold d-block small">Personal Settings</span>
                                <small class="opacity-75" style="font-size: 0.7rem;">Your personal preferences</small>
                            </div>
                        </a>
                        <a href="#shared-family" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-team-line me-3 fs-5 text-success"></i>
                            <div>
                                <span class="fw-bold d-block small">Shared Family</span>
                                <small class="opacity-75" style="font-size: 0.7rem;">Manage connections & invites</small>
                            </div>
                        </a>
                        <?php if ($_SESSION['user']['role'] == 'family-head'): ?>
                            <a href="#family-details" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
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
                            <a href="#theme-rewards" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                                <i class="ri-medal-line me-3 fs-5 text-info"></i>
                                <div>
                                    <span class="fw-bold d-block small">Themed Rewards</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Manage your custom rewards</small>
                                </div>
                            </a>
                            <a href="#manage-rewards" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                                <i class="ri-gift-line me-3 fs-5 text-danger"></i>
                                <div>
                                    <span class="fw-bold d-block small">Manage Rewards</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Manage redeemable rewards</small>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Personal Settings -->
                    <div class="tab-pane fade show active" id="personal-settings">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4 mb-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                    <i class="ri-user-settings-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Personal Settings</h5>
                                    <p class="text-muted small mb-0">Manage your personal preferences.</p>
                                </div>
                            </div>

                            <form id="personalSettingsForm">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                            <div>
                                                <h6 class="fw-bold mb-1 small">Default Calendar View</h6>
                                                <p class="text-muted extra-small mb-0">Choose your preferred default view for the calendar.</p>
                                            </div>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="default_view" id="view_month" value="month">
                                                    <label class="form-check-label small" for="view_month">Month</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="default_view" id="view_week" value="week" checked>
                                                    <label class="form-check-label small" for="view_week">Week</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="default_view" id="view_day" value="day">
                                                    <label class="form-check-label small" for="view_day">Day</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                            <div>
                                                <h6 class="fw-bold mb-1 small">Event Sharing</h6>
                                                <p class="text-muted extra-small mb-0">Share events across all your family calendars, or keep them separate.</p>
                                            </div>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="share_events" id="share_yes" value="yes">
                                                    <label class="form-check-label small" for="share_yes">Share Across Calendars</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="share_events" id="share_no" value="no" checked>
                                                    <label class="form-check-label small" for="share_no">Keep Separate</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="submit" id="savePersonalSettingsBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2">Save Changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                    <!-- Shared Family -->
                    <div class="tab-pane fade" id="shared-family">
                        <!-- Request to Join Family -->
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4 mb-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                    <i class="ri-user-shared-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Join a Family</h5>
                                    <p class="text-muted small mb-0">Enter the email of a user to request to join their family.</p>
                                </div>
                            </div>

                            <form id="inviteFamilyForm">
                                <div class="row g-3">
                                    <div class="col-12 col-md-10 col-lg-8">
                                        <label class="form-label fw-bold text-dark extra-small text-uppercase ls-1">User Email</label>
                                        <div class="d-flex gap-2 flex-wrap flex-sm-nowrap">
                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm bg-light border-light flex-grow-1">
                                                <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="ri-mail-line"></i></span>
                                                <input type="email" class="form-control border-0 bg-transparent py-1 px-1 small" id="invite_email" name="email" required placeholder="Enter email address">
                                            </div>
                                            <button type="submit" id="sendInviteBtn" class="btn btn-success btn-sm px-4 py-1 fw-medium rounded-2 flex-shrink-0">Send Join Request</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Connections and Invites -->
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4 mb-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                    <i class="ri-links-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">Your Connections & Invites</h5>
                                    <p class="text-muted small mb-0">Manage your external family connections and pending invites.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">User</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Status</th>
                                            <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="connections-table-body" class="border-0">
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted small">Loading connections...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Family Profile -->
                    <div class="tab-pane fade" id="family-details">
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
                                    <div class="col-12 mt-3 border-top pt-3">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 d-flex align-items-center justify-content-between gap-3 mb-2 mb-md-0">
                                                <div>
                                                    <h6 class="fw-bold mb-0 small">Family View</h6>
                                                    <p class="text-muted extra-small mb-0">Enable family view and set a PIN.</p>
                                                </div>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" id="family_view_enabled" name="family_view_enabled" value="1" style="width: 40px; height: 20px; cursor: pointer;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm bg-light border-light">
                                                    <span class="input-group-text bg-transparent border-0 text-muted ps-2"><i class="ri-lock-password-line"></i></span>
                                                    <input type="password" class="form-control border-0 bg-transparent py-1 px-1 small" id="family_view_pin" name="family_view_pin" placeholder="Set 4-digit PIN (leave blank to keep)" maxlength="4" disabled>
                                                    <button class="btn btn-light bg-transparent border-0 text-muted px-2" type="button" id="togglePinVisibility" disabled>
                                                        <i class="ri-eye-off-line" id="pinEyeIcon"></i>
                                                    </button>
                                                </div>
                                            </div>
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
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                                        <div>
                                            <h6 class="fw-bold mb-1 small">Use recipes in meal</h6>
                                            <p class="text-muted extra-small mb-0">Enable using existing recipes when adding meals.</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="use_recipes_in_meal_toggle" style="width: 40px; height: 20px;">
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

                    <!-- Theme Rewards -->
                    <div class="tab-pane fade" id="theme-rewards">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                        <i class="ri-medal-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Themed Rewards</h5>
                                        <p class="text-muted small mb-0">Manage your family's custom themed rewards.</p>
                                    </div>
                                </div>
                                <button id="addThemeRewardBtn" class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#themeRewardModal" onclick="prepareThemeRewardModal('add')">
                                    <i class="ri-add-line me-1"></i> Add Reward
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Theme Name</th>
                                            <th class="border-0 px-3 py-2 text-center text-uppercase extra-small ls-1 fw-bold text-muted">Levels</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Type</th>
                                            <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="theme-rewards-table-body" class="border-0">
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                <span class="small text-muted">Loading themed rewards...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Manage Rewards Settings -->
                    <div class="tab-pane fade" id="manage-rewards">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                        <i class="ri-gift-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Rewards Store</h5>
                                        <p class="text-muted small mb-0">Manage items members can redeem.</p>
                                    </div>
                                </div>
                                <button id="addStoreRewardBtn" class="btn btn-primary btn-sm px-3 py-2 fw-medium rounded-2 shadow-sm d-flex align-items-center" onclick="prepareRewardModal('add')" data-bs-toggle="modal" data-bs-target="#rewardModal">
                                    <i class="ri-add-line me-1"></i> Add Reward
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 rounded-start-2 text-muted small fw-semibold py-2 px-3">Image</th>
                                            <th class="border-0 text-muted small fw-semibold py-2 px-3">Title</th>
                                            <th class="border-0 text-muted small fw-semibold py-2 px-3">Price</th>
                                            <th class="border-0 rounded-end-2 text-muted small fw-semibold py-2 px-3 text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="manage-rewards-table-body" class="border-0">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">Loading rewards...</td>
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

<!-- Theme Reward Modal -->
<div class="modal fade" id="themeRewardModal" tabindex="-1" aria-labelledby="themeRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="themeRewardModalLabel">Add Custom Theme Reward</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="themeRewardForm" enctype="multipart/form-data">
                    <input type="hidden" id="theme_reward_old_name" name="old_name">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small">Theme Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="theme_reward_name" name="name" placeholder="e.g. Summer Vacation" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-dark small">Levels</h6>
                    </div>

                    <div id="themed-reward-levels-container">
                        <!-- Dynamic levels will be appended here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-3 d-flex justify-content-end gap-2 border-top mt-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveThemeRewardBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm" onclick="saveThemeReward()">Save Reward</button>
            </div>
        </div>
    </div>
</div>

<!-- Reward Modal -->
<div class="modal fade" id="rewardModal" tabindex="-1" aria-labelledby="rewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="rewardModalLabel">Add Reward</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="rewardForm">
                    <input type="hidden" id="reward_id" name="id">
                    <input type="hidden" id="reward_existing_image" name="existing_image">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control px-3 py-2 small border rounded-3" id="reward_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Price (Points) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control px-3 py-2 small border rounded-3" id="reward_price" name="price" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Image</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" class="form-control px-3 py-2 small border rounded-3" id="reward_image" name="image" accept="image/*" onchange="previewLevelImage(this, 'reward_img_preview')">
                            <img id="reward_img_preview" src="" class="rounded border d-none" style="width: 40px; height: 40px; object-fit: cover;">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveRewardBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm" onclick="saveReward()">Save Reward</button>
            </div>
        </div>
    </div>
</div>

<script>
    let familySettings = {};
    const CURRENT_USER_ID = <?php echo $_SESSION['user']['id']; ?>;
    const CURRENT_USER_EMAIL = "<?php echo $_SESSION['user']['email']; ?>";
    let themeRewardsData = [];
    let globalThemeLevels = [];
    let themedRewardLevelCount = 0;
    const THEME_LEVEL_API_PATH = '<?php echo $path_prefix; ?>api/admin/theme_level.php';

    document.addEventListener('DOMContentLoaded', () => {
        loadFamilyProfile();
        loadEventTypes();
        loadGroceryCategories();
        loadThemeLevels();
        loadThemeRewards();
        loadConnections();

        // Load personal settings from local storage and DB
        const savedView = localStorage.getItem('default_calendar_view');
        if (savedView) {
            const viewRadio = document.querySelector(`input[name="default_view"][value="${savedView}"]`);
            if (viewRadio) viewRadio.checked = true;
        }

        // Load user settings
        loadUserSettings();

        document.getElementById('event_type_colour').addEventListener('input', (e) => {
            document.getElementById('color-hex-label').innerText = e.target.value.toUpperCase();
        });
    });

    document.getElementById('personalSettingsForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const selectedView = document.querySelector('input[name="default_view"]:checked').value;
        const shareEvents = document.querySelector('input[name="share_events"]:checked').value;

        localStorage.setItem('default_calendar_view', selectedView);

        const btn = document.getElementById('savePersonalSettingsBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${API_PATH}user_settings.php?action=update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    settings: {
                        share_events: shareEvents
                    }
                })
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert('Personal settings saved successfully', 'success');
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            console.error('Error updating settings:', error);
            showAlert('Network error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    document.getElementById('inviteFamilyForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        const btn = document.getElementById('sendInviteBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';

        try {
            const response = await fetch(`${API_PATH}family_requests.php?action=create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert(result.message, 'success');
                form.reset();
                if (typeof loadConnections === 'function') loadConnections();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            console.error('Error sending invite:', error);
            showAlert('Network error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    async function loadUserSettings() {
        try {
            const response = await fetch(`${API_PATH}user_settings.php?action=get`);
            const result = await response.json();
            if (result.status === 'success') {
                const settings = result.data;
                const shareEventsSetting = settings.share_events || 'no';
                const shareRadio = document.querySelector(`input[name="share_events"][value="${shareEventsSetting}"]`);
                if (shareRadio) shareRadio.checked = true;
            }
        } catch (error) {
            console.error('Error loading user settings:', error);
        }
    }

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

                const isFamilyViewEnabled = family.family_view_enabled == 1;
                document.getElementById('family_view_enabled').checked = isFamilyViewEnabled;
                document.getElementById('family_view_pin').disabled = !isFamilyViewEnabled;
                const toggleBtn = document.getElementById('togglePinVisibility');
                if (toggleBtn) toggleBtn.disabled = !isFamilyViewEnabled;

                // Load general settings
                if (family.settings) {
                    try {
                        familySettings = typeof family.settings === 'string' ? JSON.parse(family.settings) : family.settings;
                        document.getElementById('show_nicknames_toggle').checked = familySettings.show_nicknames || false;
                        document.getElementById('use_recipes_in_meal_toggle').checked = familySettings.use_recipes_in_meal || false;
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

    document.getElementById('family_view_enabled').addEventListener('change', function() {
        document.getElementById('family_view_pin').disabled = !this.checked;
        const toggleBtn = document.getElementById('togglePinVisibility');
        if (toggleBtn) toggleBtn.disabled = !this.checked;
    });

    document.getElementById('togglePinVisibility').addEventListener('click', function() {
        const pinInput = document.getElementById('family_view_pin');
        const eyeIcon = document.getElementById('pinEyeIcon');
        if (pinInput.type === 'password') {
            pinInput.type = 'text';
            eyeIcon.classList.remove('ri-eye-off-line');
            eyeIcon.classList.add('ri-eye-line');
        } else {
            pinInput.type = 'password';
            eyeIcon.classList.remove('ri-eye-line');
            eyeIcon.classList.add('ri-eye-off-line');
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

    document.getElementById('use_recipes_in_meal_toggle').addEventListener('change', async (e) => {
        const isChecked = e.target.checked;

        // Update local state
        familySettings.use_recipes_in_meal = isChecked;

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
                familySettings.use_recipes_in_meal = !isChecked;
                e.target.checked = !isChecked;
            }
        } catch (error) {
            console.error('Error updating settings:', error);
            showAlert('Network error occurred', 'error');
            // Revert local state and UI
            familySettings.use_recipes_in_meal = !isChecked;
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

    async function loadConnections() {
        const tableBody = document.getElementById('connections-table-body');
        try {
            const response = await fetch(`${API_PATH}family_requests.php?action=getUserConnections`);
            const result = await response.json();

            if (result.status === 'success') {
                renderConnectionsTable(result.data);
            } else {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">${result.message}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading connections:', error);
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderConnectionsTable(connections) {
        const tableBody = document.getElementById('connections-table-body');
        tableBody.innerHTML = '';

        if (!connections || connections.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted small">No connections or invites found.</td></tr>';
            return;
        }

        connections.forEach(conn => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';

            let otherName = '';
            let isRequester = (conn.requester_id == CURRENT_USER_ID);

            if (isRequester) {
                otherName = conn.receiver_name ? conn.receiver_name : conn.email;
            } else {
                otherName = conn.requester_name;
            }

            let statusBadge = '';
            if (conn.status === 'approved') {
                statusBadge = '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill extra-small">Approved</span>';
            } else if (conn.status === 'pending') {
                statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill extra-small">Pending</span>';
            } else {
                statusBadge = `<span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1 rounded-pill extra-small">${conn.status}</span>`;
            }

            let actions = '';
            if (conn.status === 'pending') {
                if (isRequester) {
                    actions = `<button class="btn btn-outline-danger btn-sm rounded-2 py-0 px-2 extra-small" onclick="cancelConnection(${conn.id})">Cancel</button>`;
                } else {
                    actions = `<span class="text-muted extra-small">Pending</span>`;
                }
            } else if (conn.status === 'approved') {
                actions = `<button class="btn btn-outline-danger btn-sm rounded-2 py-0 px-2 extra-small" onclick="cancelConnection(${conn.id}, true)">Remove</button>`;
            }

            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${otherName}</td>
                <td class="py-2">${statusBadge}</td>
                <td class="px-3 py-2 text-end">${actions}</td>
            `;
            tableBody.appendChild(tr);
        });
    }

    async function cancelConnection(id, isRemove = false) {
        if (!confirm(isRemove ? 'Are you sure you want to remove this connection?' : 'Are you sure you want to cancel this invite?')) return;

        try {
            const response = await fetch(`${API_PATH}family_requests.php?action=delete`, {
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
                showAlert(result.message, 'success');
                loadConnections();
            } else {
                showAlert(result.message, 'error');
            }
        } catch (error) {
            console.error('Error cancelling connection:', error);
            showAlert('Network error occurred', 'error');
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

    async function loadThemeLevels() {
        try {
            const response = await fetch(`${THEME_LEVEL_API_PATH}?action=list`);
            const result = await response.json();

            if (result.status === 'success') {
                globalThemeLevels = result.data;
            }
        } catch (error) {
            console.error('Error loading theme levels:', error);
        }
    }

    async function loadThemeRewards() {
        const tableBody = document.getElementById('theme-rewards-table-body');
        try {
            const response = await fetch(`${API_PATH}theme_rewards.php?action=list`);
            const result = await response.json();

            if (result.status === 'success' || result.status === true) {
                themeRewardsData = result.data || [];
                renderThemeRewardsTable();
            } else {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">${result.message || 'No rewards found'}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading theme rewards:', error);
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderThemeRewardsTable() {
        const tableBody = document.getElementById('theme-rewards-table-body');
        tableBody.innerHTML = '';

        let customCount = 0;

        if (themeRewardsData.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted small">No theme rewards found.</td></tr>';
            document.getElementById('addThemeRewardBtn').style.display = 'block';
            return;
        }

        themeRewardsData.forEach(reward => {
            if (reward.is_global == 0) {
                customCount++;
            }
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';

            let levelsHtml = '<div class="d-flex flex-wrap gap-1 justify-content-center">';
            if (reward.levels && reward.levels.length > 0) {
                reward.levels.forEach(l => {
                    let imgHtml = '';
                    if (l.image) {
                        let imagePath = l.image;
                        if (imagePath.startsWith('../')) {
                            imagePath = imagePath.replace('../', '');
                        }
                        if (!imagePath.startsWith('http')) {
                            imagePath = '<?php echo $path_prefix; ?>' + imagePath;
                        }
                        imgHtml = `<img src="${imagePath}" class="rounded-circle me-1" style="width: 16px; height: 16px; object-fit: cover;">`;
                    }
                    levelsHtml += `<span class="badge bg-light text-dark border d-flex align-items-center">${imgHtml}${l.level} (${l.points} pts)</span>`;
                });
            } else {
                levelsHtml += `<span class="badge bg-light text-muted border">0 Levels</span>`;
            }
            levelsHtml += '</div>';

            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${reward.name}</td>
                <td class="px-3 py-2 text-center small text-muted">${levelsHtml}</td>
                <td class="py-2">
                    ${reward.is_global == 1 
                        ? '<span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded-pill extra-small">System</span>' 
                        : '<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small border">Custom</span>'}
                </td>
                <td class="px-3 py-2 text-end">
                    ${reward.is_global == 0 ? `
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editThemeReward('${reward.name.replace(/'/g, "\\'")}')" title="Edit">
                            <i class="ri-pencil-line fs-6"></i>
                        </button>
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteThemeReward('${reward.name.replace(/'/g, "\\'")}')" title="Delete">
                            <i class="ri-delete-bin-line fs-6"></i>
                        </button>
                    ` : `
                        <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 disabled opacity-25 border-0" style="width: 32px; height: 32px;" title="System defined">
                            <i class="ri-lock-line fs-6"></i>
                        </button>
                    `}
                </td>
            `;
            tableBody.appendChild(tr);
        });

        // Hide add button if 1 or more custom rewards are found
        if (customCount >= 1) {
            document.getElementById('addThemeRewardBtn').style.display = 'none';
        } else {
            document.getElementById('addThemeRewardBtn').style.display = 'block';
        }
    }

    function addThemedRewardLevel(levelData = null) {
        const container = document.getElementById('themed-reward-levels-container');
        const id = themedRewardLevelCount++;

        let existingImageHtml = '';
        let currentImgSrc = '';
        if (levelData && levelData.image) {
            let imagePath = levelData.image;
            if (imagePath.startsWith('../')) {
                imagePath = imagePath.replace('../', '');
            }
            if (!imagePath.startsWith('http')) {
                imagePath = '<?php echo $path_prefix; ?>' + imagePath;
            }
            currentImgSrc = imagePath;
            existingImageHtml = `<input type="hidden" name="levels[${id}][existing_image]" value="${levelData.image}">`;
        }

        const div = document.createElement('div');
        div.className = 'card bg-light border-0 mb-2 level-row shadow-none';
        div.id = `themed-reward-level-${id}`;
        div.innerHTML = `
            <div class="card-body p-2 px-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark extra-small mb-1">Level Name</label>
                        <input type="text" class="form-control form-control-sm border-0 shadow-sm bg-white" name="levels[${id}][level]" value="${levelData ? levelData.level : ''}" required readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark extra-small mb-1">Points</label>
                        <input type="number" class="form-control form-control-sm border-0 shadow-sm" name="levels[${id}][points]" value="${levelData ? levelData.points : ''}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold text-dark extra-small mb-1">Image</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="file" class="form-control form-control-sm border-0 shadow-sm level-image" name="levels_image_${id}" accept="image/*" onchange="previewLevelImage(this, 'level-img-preview-${id}')">
                            <img id="level-img-preview-${id}" src="${currentImgSrc}" class="rounded border ${currentImgSrc ? '' : 'd-none'}" style="width: 30px; height: 30px; object-fit: cover;">
                        </div>
                        ${existingImageHtml}
                    </div>
                </div>
            </div>
        `;
        container.appendChild(div);
    }

    function previewLevelImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function prepareThemeRewardModal(action) {
        const modalTitle = document.getElementById('themeRewardModalLabel');
        const form = document.getElementById('themeRewardForm');
        form.reset();
        document.getElementById('theme_reward_old_name').value = '';
        document.getElementById('themed-reward-levels-container').innerHTML = '';
        themedRewardLevelCount = 0;

        if (action === 'add') {
            modalTitle.innerText = 'Add Custom Theme Reward';
            globalThemeLevels.forEach(level => addThemedRewardLevel({
                level: level.name,
                points: ''
            }));
        } else {
            modalTitle.innerText = 'Edit Custom Theme Reward';
        }
    }

    function editThemeReward(name) {
        const reward = themeRewardsData.find(r => r.name === name && r.is_global == 0);
        if (reward) {
            prepareThemeRewardModal('edit');
            document.getElementById('theme_reward_old_name').value = reward.name;
            document.getElementById('theme_reward_name').value = reward.name;

            globalThemeLevels.forEach(predefined => {
                const existing = reward.levels ? reward.levels.find(l => l.level === predefined.name) : null;
                if (existing) {
                    addThemedRewardLevel({
                        level: existing.level,
                        points: existing.points,
                        image: existing.image
                    });
                } else {
                    addThemedRewardLevel({
                        level: predefined.name,
                        points: ''
                    });
                }
            });

            const modal = new bootstrap.Modal(document.getElementById('themeRewardModal'));
            modal.show();
        }
    }

    async function deleteThemeReward(name) {
        if (confirm('Are you sure you want to delete this custom theme reward?')) {
            try {
                const response = await fetch(`${API_PATH}theme_rewards.php?action=delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name
                    })
                });
                const result = await response.json();
                if (result.status === 'success' || result.status === true) {
                    showAlert('Reward deleted successfully', 'success');
                    loadThemeRewards();
                } else {
                    showAlert(result.message || 'Failed to delete reward', 'error');
                }
            } catch (error) {
                console.error('Error deleting reward:', error);
                showAlert('Network error occurred', 'error');
            }
        }
    }

    async function saveThemeReward() {
        const form = document.getElementById('themeRewardForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const container = document.getElementById('themed-reward-levels-container');
        if (container.children.length === 0) {
            showAlert('Please add at least one level to the theme.', 'error');
            return;
        }

        const formData = new FormData(form);
        const oldName = formData.get('old_name');
        const action = oldName ? 'update' : 'create';

        let levels = [];
        const levelsNodes = container.querySelectorAll('.level-row');
        levelsNodes.forEach(node => {
            const id = node.id.split('-').pop();
            levels.push({
                level: formData.get(`levels[${id}][level]`),
                points: formData.get(`levels[${id}][points]`),
                existing_image: formData.get(`levels[${id}][existing_image]`) || null,
                frontend_id: id
            });
            formData.delete(`levels[${id}][level]`);
            formData.delete(`levels[${id}][points]`);
            formData.delete(`levels[${id}][existing_image]`);
        });

        formData.append('levels', JSON.stringify(levels));

        const btn = document.getElementById('saveThemeRewardBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${API_PATH}theme_rewards.php?action=${action}`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.status === 'success' || result.status === true) {
                showAlert(result.message || 'Theme reward saved successfully', 'success');
                const modalEl = document.getElementById('themeRewardModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                loadThemeRewards();
            } else {
                showAlert(result.message || 'Failed to save theme reward', 'error');
            }
        } catch (error) {
            console.error('Error saving theme reward:', error);
            showAlert('Network error occurred', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }

    // --- Manage Rewards Logic ---
    let storeRewardsData = [];

    async function loadStoreRewards() {
        const tableBody = document.getElementById('manage-rewards-table-body');
        try {
            const response = await fetch(`${API_PATH}rewards.php?action=list`);
            const result = await response.json();

            if (result.status === 'success') {
                storeRewardsData = result.data || [];
                renderStoreRewardsTable();
            } else {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">${result.message || 'No rewards found'}</td></tr>`;
            }
        } catch (error) {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger small">Error loading rewards</td></tr>`;
        }
    }

    function renderStoreRewardsTable() {
        const tableBody = document.getElementById('manage-rewards-table-body');
        const addBtn = document.getElementById('addStoreRewardBtn');

        tableBody.innerHTML = '';

        if (addBtn) {
            addBtn.style.display = storeRewardsData.length >= 10 ? 'none' : 'flex';
        }

        if (storeRewardsData.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted small">No rewards found. Add one above.</td></tr>';
            return;
        }

        storeRewardsData.forEach(reward => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';
            let imgSrc = reward.image ? (reward.image.startsWith('../') ? reward.image.substring(3) : reward.image) : '';
            if (imgSrc && !imgSrc.startsWith('http')) imgSrc = '<?php echo $path_prefix; ?>' + imgSrc;
            let imgHtml = imgSrc ? `<img src="${imgSrc}" class="rounded border" style="width: 40px; height: 40px; object-fit: cover;">` : `<div class="bg-light rounded border d-flex align-items-center justify-content-center text-muted" style="width:40px;height:40px;"><i class="ri-image-line"></i></div>`;

            tr.innerHTML = `
                <td class="px-3 py-2">${imgHtml}</td>
                <td class="px-3 py-2 fw-bold text-dark small">${reward.title}</td>
                <td class="px-3 py-2 text-dark small">${reward.price} pts</td>
                <td class="px-3 py-2 text-end">
                    <button class="btn btn-light btn-sm rounded-circle p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editReward(${reward.id})" title="Edit">
                        <i class="ri-pencil-line fs-6"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteReward(${reward.id})" title="Delete">
                        <i class="ri-delete-bin-line fs-6"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });
    }

    function prepareRewardModal(action) {
        document.getElementById('rewardForm').reset();
        document.getElementById('reward_id').value = '';
        document.getElementById('reward_existing_image').value = '';
        document.getElementById('reward_img_preview').classList.add('d-none');
        document.getElementById('reward_img_preview').src = '';

        document.getElementById('rewardModalLabel').innerText = action === 'add' ? 'Add Reward' : 'Edit Reward';
    }

    function editReward(id) {
        const reward = storeRewardsData.find(r => r.id == id);
        if (reward) {
            prepareRewardModal('edit');
            document.getElementById('reward_id').value = reward.id;
            document.getElementById('reward_title').value = reward.title;
            document.getElementById('reward_price').value = reward.price;
            if (reward.image) {
                document.getElementById('reward_existing_image').value = reward.image;
                let imgSrc = reward.image.startsWith('../') ? reward.image.substring(3) : reward.image;
                if (!imgSrc.startsWith('http')) imgSrc = '<?php echo $path_prefix; ?>' + imgSrc;
                document.getElementById('reward_img_preview').src = imgSrc;
                document.getElementById('reward_img_preview').classList.remove('d-none');
            }
            new bootstrap.Modal(document.getElementById('rewardModal')).show();
        }
    }

    async function saveReward() {
        const form = document.getElementById('rewardForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const action = formData.get('id') ? 'update' : 'create';
        const btn = document.getElementById('saveRewardBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = 'Saving...';

        try {
            const response = await fetch(`${API_PATH}rewards.php?action=${action}`, {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                showAlert('Reward saved successfully', 'success');
                bootstrap.Modal.getInstance(document.getElementById('rewardModal')).hide();
                loadStoreRewards();
            } else {
                showAlert(result.message || 'Failed to save', 'error');
            }
        } catch (e) {
            showAlert('Network error', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }

    async function deleteReward(id) {
        if (confirm('Are you sure you want to delete this reward?')) {
            try {
                const response = await fetch(`${API_PATH}rewards.php?action=delete`, {
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
                    showAlert('Reward deleted', 'success');
                    loadStoreRewards();
                } else {
                    showAlert('Failed to delete', 'error');
                }
            } catch (e) {
                showAlert('Network error', 'error');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadStoreRewards();
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>