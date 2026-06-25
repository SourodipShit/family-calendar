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
                        <a href="#manage-rewards-themes" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-medal-line me-3 fs-5 text-danger"></i>
                            <div>
                                <span class="fw-bold d-block small">Manage Rewards Themes</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Themes & points</small>
                            </div>
                        </a>
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
                        <a href="#config-settings" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-equalizer-line me-3 fs-5 text-dark"></i>
                            <div>
                                <span class="fw-bold d-block small">Config</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Mail & system configuration</small>
                            </div>
                        </a>
                        <a href="#timezone-settings" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0" data-bs-toggle="list">
                            <i class="ri-global-line me-3 fs-5 text-info"></i>
                            <div>
                                <span class="fw-bold d-block small">Time Zones</span>
                                <small class="text-muted" style="font-size: 0.7rem;">Manage system time zones</small>
                            </div>
                        </a>
                        <a href="#system-reset" class="list-group-item list-group-item-action py-3 px-4 d-flex align-items-center border-0 text-danger" data-bs-toggle="list">
                            <i class="ri-alert-line me-3 fs-5"></i>
                            <div>
                                <span class="fw-bold d-block small">System Reset</span>
                                <small class="text-danger opacity-75" style="font-size: 0.7rem;">Danger Zone</small>
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

                    <!-- Manage Rewards Themes -->
                    <div class="tab-pane fade" id="manage-rewards-themes">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                        <i class="ri-medal-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">Manage Rewards Themes</h5>
                                        <p class="text-muted small mb-0">Configure global themes and reward point levels.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Horizontal Tabs -->
                            <ul class="nav nav-tabs mb-4" id="rewardsThemesTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="themed-rewards-tab" data-bs-toggle="tab" data-bs-target="#themed-rewards" type="button" role="tab">Themed Rewards</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="levels-tab" data-bs-toggle="tab" data-bs-target="#levels" type="button" role="tab">Levels</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content" id="rewardsThemesTabContent">
                                <!-- Themed Rewards -->
                                <div class="tab-pane fade show active" id="themed-rewards" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">Global Themed Rewards</h6>
                                        <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#themedRewardModal" onclick="prepareThemedRewardModal('add')">
                                            <i class="ri-add-line me-1"></i> Add Theme
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="themedRewardsTable" class="table table-hover align-middle border-0 mb-0 w-100">
                                            <thead class="bg-light border-0">
                                                <tr>
                                                    <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Theme Name</th>
                                                    <th class="border-0 px-3 py-2 text-center text-uppercase extra-small ls-1 fw-bold text-muted">Levels</th>
                                                    <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="themed-rewards-table-body" class="border-0">
                                                <tr id="themed-rewards-loading-row">
                                                    <td colspan="3" class="text-center py-4">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                        <span class="small text-muted">Loading themed rewards...</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Levels -->
                                <div class="tab-pane fade" id="levels" role="tabpanel">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0">Theme Levels</h6>
                                        <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#themeLevelModal" onclick="prepareThemeLevelModal('add')">
                                            <i class="ri-add-line me-1"></i> Add Level
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="themeLevelsTable" class="table table-hover align-middle border-0 mb-0 w-100">
                                            <thead class="bg-light border-0">
                                                <tr>
                                                    <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Level Name</th>
                                                    <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="theme-levels-table-body" class="border-0">
                                                <tr id="theme-levels-loading-row">
                                                    <td colspan="2" class="text-center py-4">
                                                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                        <span class="small text-muted">Loading levels...</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
                                    <form id="logoForm" enctype="multipart/form-data" class="mb-4">
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
                                                    <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*" onchange="previewImage(this, 'logoPreview', 'logoPlaceholder', 'logoPreviewContainer')">
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

                                    <form id="loginImageForm" enctype="multipart/form-data">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Login Page Image</label>
                                            <div class="d-flex align-items-center gap-4 mt-2">
                                                <div id="loginPreviewContainer" class="bg-light rounded-3 d-flex align-items-center justify-content-center border-dashed border-2 overflow-hidden" style="width: 120px; height: 120px; border: 2px dashed #dee2e6; cursor: pointer;" onclick="document.getElementById('loginInput').click();">
                                                    <div id="loginPlaceholder" class="text-center">
                                                        <i class="ri-image-add-line fs-1 text-muted"></i>
                                                        <p class="extra-small text-muted mb-0">Upload Image</p>
                                                    </div>
                                                    <img id="loginPreview" src="" class="w-100 h-100 d-none" style="object-fit: cover;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" name="login_image" id="loginInput" class="d-none" accept="image/*" onchange="previewImage(this, 'loginPreview', 'loginPlaceholder', 'loginPreviewContainer')">
                                                    <button type="button" class="btn btn-outline-primary btn-sm mb-2 px-3" onclick="document.getElementById('loginInput').click();">
                                                        <i class="ri-upload-2-line me-1"></i> Choose New Image
                                                    </button>
                                                    <p class="extra-small text-muted mb-0">Recommended: High quality image.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <button type="button" id="saveLoginImageBtn" class="btn btn-primary px-4">
                                                <i class="ri-save-line me-1"></i> Save Image
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-md-6">
                                    <form id="signupImageForm" enctype="multipart/form-data">
                                        <div class="mb-4">
                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Sign Up Page Image</label>
                                            <div class="d-flex align-items-center gap-4 mt-2">
                                                <div id="signupPreviewContainer" class="bg-light rounded-3 d-flex align-items-center justify-content-center border-dashed border-2 overflow-hidden" style="width: 120px; height: 120px; border: 2px dashed #dee2e6; cursor: pointer;" onclick="document.getElementById('signupInput').click();">
                                                    <div id="signupPlaceholder" class="text-center">
                                                        <i class="ri-image-add-line fs-1 text-muted"></i>
                                                        <p class="extra-small text-muted mb-0">Upload Image</p>
                                                    </div>
                                                    <img id="signupPreview" src="" class="w-100 h-100 d-none" style="object-fit: cover;">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <input type="file" name="signup_image" id="signupInput" class="d-none" accept="image/*" onchange="previewImage(this, 'signupPreview', 'signupPlaceholder', 'signupPreviewContainer')">
                                                    <button type="button" class="btn btn-outline-primary btn-sm mb-2 px-3" onclick="document.getElementById('signupInput').click();">
                                                        <i class="ri-upload-2-line me-1"></i> Choose New Image
                                                    </button>
                                                    <p class="extra-small text-muted mb-0">Recommended: High quality image.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <button type="button" id="saveSignupImageBtn" class="btn btn-primary px-4">
                                                <i class="ri-save-line me-1"></i> Save Image
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Config Settings -->
                    <div class="tab-pane fade" id="config-settings">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-dark bg-opacity-10 text-dark p-2 rounded-3 me-3">
                                    <i class="ri-equalizer-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0">System Configuration</h5>
                                    <p class="text-muted small mb-0">Manage technical configurations like email settings.</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-10">
                                    <form id="configForm">
                                        <div class="accordion" id="configAccordion">

                                            <!-- Site Configuration -->
                                            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
                                                <h2 class="accordion-header" id="headingSiteConfig">
                                                    <button class="accordion-button collapsed fw-bold bg-light text-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSiteConfig" aria-expanded="false" aria-controls="collapseSiteConfig">
                                                        <i class="ri-settings-4-line me-2 text-primary"></i> Site Configuration
                                                    </button>
                                                </h2>
                                                <div id="collapseSiteConfig" class="accordion-collapse collapse" aria-labelledby="headingSiteConfig" data-bs-parent="#configAccordion">
                                                    <div class="accordion-body border-top border-light bg-white">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Company Name</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-building-line"></i></span>
                                                                <input type="text" class="form-control border-0 px-3 py-2 small" id="company_name" name="company_name" placeholder="e.g. Family Calendar Company">
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">The name of the company to display in system communications.</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Mail Sending Address</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-mail-line"></i></span>
                                                                <input type="email" class="form-control border-0 px-3 py-2 small" id="mail_from_address" name="mail_from_address" placeholder="e.g. noreply@familycalendar.com">
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">This address will be used as the 'From' address for all system emails.</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Base URL</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-link"></i></span>
                                                                <input type="url" class="form-control border-0 px-3 py-2 small" id="base_url" name="base_url" placeholder="e.g. http://localhost/family-calendar">
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">The base URL of the application, used for absolute links in emails and notifications.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Infobip Configuration -->
                                            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
                                                <h2 class="accordion-header" id="headingInfobip">
                                                    <button class="accordion-button collapsed fw-bold bg-light text-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInfobip" aria-expanded="false" aria-controls="collapseInfobip">
                                                        <i class="ri-message-3-line me-2 text-success"></i> Infobip SMS Integration
                                                    </button>
                                                </h2>
                                                <div id="collapseInfobip" class="accordion-collapse collapse" aria-labelledby="headingInfobip" data-bs-parent="#configAccordion">
                                                    <div class="accordion-body border-top border-light bg-white">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Infobip API Key</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-key-2-line"></i></span>
                                                                <input type="password" class="form-control border-0 px-3 py-2 small" id="infobip_api_key" name="infobip_api_key" placeholder="Enter your Infobip API key">
                                                                <button class="btn btn-white border-0 px-3" type="button" id="toggleApiKeyBtn" onclick="toggleApiKeyVisibility()">
                                                                    <i class="ri-eye-line text-muted"></i>
                                                                </button>
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">API key used for sending SMS reminders via Infobip integration.</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Infobip API Base URL</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-global-line"></i></span>
                                                                <input type="url" class="form-control border-0 px-3 py-2 small" id="infobip_api_base_url" name="infobip_api_base_url" placeholder="e.g. https://xxxxxx.api.infobip.com">
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">Base URL used for Infobip API requests (e.g., https://[your-subdomain].api.infobip.com).</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Infobip Sender ID / Number</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-phone-line"></i></span>
                                                                <input type="text" class="form-control border-0 px-3 py-2 small" id="infobip_sender" name="infobip_sender" placeholder="e.g. 447491163443 or InfoSMS">
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">The sender number or alphanumeric sender ID whitelisted on your Infobip account.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- IMAP Configuration -->
                                            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
                                                <h2 class="accordion-header" id="headingImap">
                                                    <button class="accordion-button collapsed fw-bold bg-light text-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImap" aria-expanded="false" aria-controls="collapseImap">
                                                        <i class="ri-mail-download-line me-2 text-warning"></i> IMAP Settings for Shared Family Emails
                                                    </button>
                                                </h2>
                                                <div id="collapseImap" class="accordion-collapse collapse" aria-labelledby="headingImap" data-bs-parent="#configAccordion">
                                                    <div class="accordion-body border-top border-light bg-white">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">IMAP Host</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-server-line"></i></span>
                                                                <input type="text" class="form-control border-0 px-3 py-2 small" id="imap_host" name="imap_host" placeholder="e.g. imap.gmail.com">
                                                            </div>
                                                            <p class="extra-small text-muted mt-1 mb-0">Leave blank to dynamically extract from email domain (mail.domain.com).</p>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">IMAP Port</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-plug-line"></i></span>
                                                                <input type="text" class="form-control border-0 px-3 py-2 small" id="imap_port" name="imap_port" placeholder="e.g. 993">
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">IMAP Flags</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-flag-line"></i></span>
                                                                <input type="text" class="form-control border-0 px-3 py-2 small" id="imap_flags" name="imap_flags" placeholder="e.g. /imap/ssl/novalidate-cert">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Stripe Configuration -->
                                            <div class="accordion-item border-0 mb-3 rounded-3 shadow-sm overflow-hidden">
                                                <h2 class="accordion-header" id="headingStripe">
                                                    <button class="accordion-button collapsed fw-bold bg-light text-dark border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStripe" aria-expanded="false" aria-controls="collapseStripe">
                                                        <i class="ri-bank-card-line me-2 text-primary"></i> Stripe Settings for Payments
                                                    </button>
                                                </h2>
                                                <div id="collapseStripe" class="accordion-collapse collapse" aria-labelledby="headingStripe" data-bs-parent="#configAccordion">
                                                    <div class="accordion-body border-top border-light bg-white">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Stripe Public Key</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-key-line"></i></span>
                                                                <input type="text" class="form-control border-0 px-3 py-2 small" id="stripe_public_key" name="stripe_public_key" placeholder="e.g. pk_test_...">
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Stripe Secret Key</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-key-2-line"></i></span>
                                                                <input type="password" class="form-control border-0 px-3 py-2 small" id="stripe_secret_key" name="stripe_secret_key" placeholder="e.g. sk_test_...">
                                                                <button class="btn btn-white border-0 px-3" type="button" id="toggleStripeKeyBtn" onclick="toggleStripeKeyVisibility()">
                                                                    <i class="ri-eye-line text-muted"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-muted text-uppercase ls-1">Stripe Webhook Secret</label>
                                                            <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                                                                <span class="input-group-text bg-white border-0"><i class="ri-lock-line"></i></span>
                                                                <input type="password" class="form-control border-0 px-3 py-2 small" id="stripe_webhook_secret" name="stripe_webhook_secret" placeholder="e.g. whsec_...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="mt-4 pt-3 border-top">
                                            <button type="button" id="saveConfigBtn" class="btn btn-primary px-4">

                                                <i class="ri-save-line me-1"></i> Save Config
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timezone Settings -->
                    <div class="tab-pane fade" id="timezone-settings">
                        <div class="card border-0 shadow-sm rounded-3 p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-3 me-3">
                                        <i class="ri-global-line fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">System Time Zones</h5>
                                        <p class="text-muted small mb-0">Manage time zones available for families and events.</p>
                                    </div>
                                </div>
                                <button class="btn btn-primary btn-sm rounded-2 px-3 py-1" data-bs-toggle="modal" data-bs-target="#timezoneModal" onclick="prepareTimezoneModal('add')">
                                    <i class="ri-add-line me-1"></i> Add Time Zone
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table id="timezonesTable" class="table table-hover align-middle border-0 mb-0">
                                    <thead class="bg-light border-0">
                                        <tr>
                                            <th class="border-0 rounded-start px-3 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Timezone</th>
                                            <th class="border-0 py-2 text-uppercase extra-small ls-1 fw-bold text-muted">Lable</th>
                                            <th class="border-0 rounded-end px-3 py-2 text-end text-uppercase extra-small ls-1 fw-bold text-muted">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="timezones-table-body" class="border-0">
                                        <!-- Populated via AJAX -->
                                        <tr id="timezone-loading-row">
                                            <td colspan="3" class="text-center py-4">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                <span class="small text-muted">Loading time zones...</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- System Reset -->
                    <div class="tab-pane fade" id="system-reset">
                        <div class="card border-danger shadow-sm rounded-3 p-3 p-md-4 mb-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 me-3">
                                    <i class="ri-alert-line fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold text-danger mb-0">Danger Zone</h5>
                                    <p class="text-muted small mb-0">Irreversible system reset actions.</p>
                                </div>
                            </div>
                            
                            <div class="mb-4 pb-4 border-bottom">
                                <h6 class="fw-bold">Reset All Family Assets</h6>
                                <p class="small text-muted">This will delete all events, meals, recipes, grocery lists, and uploaded photos. Users, families, and configuration will remain untouched.</p>
                                <button class="btn btn-outline-danger" onclick="triggerResetAssets()">
                                    <i class="ri-delete-bin-line me-2"></i> Reset Family Assets
                                </button>
                            </div>

                            <div>
                                <h6 class="fw-bold text-danger">Factory Reset</h6>
                                <p class="small text-muted">This will wipe the entire system clean! It deletes all families, all users (except site admins), and all assets. Only global settings and default categories are preserved.</p>
                                <button class="btn btn-danger" onclick="triggerFactoryReset()">
                                    <i class="ri-skull-line me-2"></i> Perform Factory Reset
                                </button>
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

    /* Smooth accordion animation */
    .collapsing {
        transition: height 0.35s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
    }
</style>

<!-- Password Confirmation Modal for Resets -->
<div class="modal fade" id="passwordConfirmModal" tabindex="-1" aria-labelledby="passwordConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold text-danger" id="passwordConfirmModalLabel">Security Check</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <p class="small text-muted mb-3" id="passwordConfirmMessage">Please enter your admin password to confirm this action.</p>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark small">Admin Password <span class="text-danger">*</span></label>
                    <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                        <input type="password" class="form-control border-0 px-3 py-2 small" id="confirmAdminPassword" placeholder="Enter your password" required>
                    </div>
                </div>
                <input type="hidden" id="confirmActionType">
            </div>
            <div class="modal-footer border-0 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="executeResetBtn" class="btn btn-danger btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm">Confirm Action</button>
            </div>
        </div>
    </div>
</div>

<!-- Theme Level Modal -->
<div class="modal fade" id="themeLevelModal" tabindex="-1" aria-labelledby="themeLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="themeLevelModalLabel">Add Theme Level</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="themeLevelForm">
                    <input type="hidden" id="theme_level_id" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Level Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="theme_level_name" name="name" placeholder="e.g. Platinum" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveThemeLevelBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm">Save Level</button>
            </div>
        </div>
    </div>
</div>

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

<!-- Timezone Modal -->
<div class="modal fade" id="timezoneModal" tabindex="-1" aria-labelledby="timezoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="timezoneModalLabel">Add Time Zone</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="timezoneForm">
                    <input type="hidden" id="timezone_index" name="index">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Timezone (e.g. Asia/Kolkata) <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="timezone_value" name="timezone" placeholder="Asia/Kolkata" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Lable (e.g. Kolkata GMT+5:30) <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="timezone_lable" name="lable" placeholder="Kolkata GMT+5:30" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-2 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light btn-sm border px-3 py-1 fw-medium rounded-2 text-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveTimezoneBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm">Save Time Zone</button>
            </div>
        </div>
    </div>
</div>

<!-- Themed Reward Modal -->
<div class="modal fade" id="themedRewardModal" tabindex="-1" aria-labelledby="themedRewardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow p-3">
            <div class="modal-header border-0 pb-1">
                <h6 class="modal-title fw-bold" id="themedRewardModalLabel">Add Global Theme</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-2">
                <form id="themedRewardForm" enctype="multipart/form-data">
                    <input type="hidden" id="themed_reward_old_name" name="old_name">
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small">Theme Name <span class="text-danger">*</span></label>
                        <div class="input-group border rounded-3 overflow-hidden shadow-sm border-light">
                            <input type="text" class="form-control border-0 px-3 py-2 small" id="themed_reward_name" name="name" placeholder="e.g. Medals, Badges" required>
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
                <button type="button" id="saveThemedRewardBtn" class="btn btn-primary btn-sm px-3 py-1 fw-medium rounded-2 shadow-sm">Save Theme</button>
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
        loadConfigSettings();
        loadTimezones();
        loadThemeLevels();
        loadThemedRewards();

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

            let familyContent = type.family_name ?
                `<span class="small fw-semibold text-dark">${type.family_name}</span>` :
                `<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small border">N/A</span>`;

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
            "columnDefs": [{
                "orderable": false,
                "targets": 4
            }]
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
                document.getElementById('allow_multiple_day').checked = result.data.allow_multiple_day == 1;

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

                // Load Logo
                const logoSetting = settings.find(s => s.setting_key === 'site_logo');
                if (logoSetting && logoSetting.setting_value) {
                    const logoPreview = document.getElementById('logoPreview');
                    const logoPlaceholder = document.getElementById('logoPlaceholder');

                    let logoPath = logoSetting.setting_value;
                    if (logoPath.startsWith('../')) {
                        logoPath = '<?php echo $path_prefix; ?>' + logoPath.replace('../', '');
                    }

                    logoPreview.src = logoPath;
                    logoPreview.classList.remove('d-none');
                    logoPlaceholder.classList.add('d-none');
                }

                // Load Login Image
                const loginSetting = settings.find(s => s.setting_key === 'login_page_image');
                if (loginSetting && loginSetting.setting_value) {
                    const loginPreview = document.getElementById('loginPreview');
                    const loginPlaceholder = document.getElementById('loginPlaceholder');

                    let loginPath = loginSetting.setting_value;
                    if (loginPath.startsWith('../')) {
                        loginPath = '<?php echo $path_prefix; ?>' + loginPath.replace('../', '');
                    }

                    loginPreview.src = loginPath;
                    loginPreview.classList.remove('d-none');
                    loginPlaceholder.classList.add('d-none');
                }

                // Load Signup Image
                const signupSetting = settings.find(s => s.setting_key === 'sign_up_page_image');
                if (signupSetting && signupSetting.setting_value) {
                    const signupPreview = document.getElementById('signupPreview');
                    const signupPlaceholder = document.getElementById('signupPlaceholder');

                    let signupPath = signupSetting.setting_value;
                    if (signupPath.startsWith('../')) {
                        signupPath = '<?php echo $path_prefix; ?>' + signupPath.replace('../', '');
                    }

                    signupPreview.src = signupPath;
                    signupPreview.classList.remove('d-none');
                    signupPlaceholder.classList.add('d-none');
                }

                // Load Config Settings
                const companyNameSetting = settings.find(s => s.setting_key === 'company_name');
                if (companyNameSetting) {
                    document.getElementById('company_name').value = companyNameSetting.setting_value || '';
                }

                const mailFromSetting = settings.find(s => s.setting_key === 'mail_from_address');
                if (mailFromSetting) {
                    document.getElementById('mail_from_address').value = mailFromSetting.setting_value || '';
                }

                const baseUrlSetting = settings.find(s => s.setting_key === 'base_url');
                if (baseUrlSetting) {
                    document.getElementById('base_url').value = baseUrlSetting.setting_value || '';
                }

                const infobipApiKeySetting = settings.find(s => s.setting_key === 'infobip_api_key');
                if (infobipApiKeySetting) {
                    document.getElementById('infobip_api_key').value = infobipApiKeySetting.setting_value || '';
                }

                const infobipApiBaseUrlSetting = settings.find(s => s.setting_key === 'infobip_api_base_url');
                if (infobipApiBaseUrlSetting) {
                    document.getElementById('infobip_api_base_url').value = infobipApiBaseUrlSetting.setting_value || '';
                }

                const infobipSenderSetting = settings.find(s => s.setting_key === 'infobip_sender');
                if (infobipSenderSetting) {
                    document.getElementById('infobip_sender').value = infobipSenderSetting.setting_value || '';
                }

                const imapHostSetting = settings.find(s => s.setting_key === 'imap_host');
                if (imapHostSetting) {
                    document.getElementById('imap_host').value = imapHostSetting.setting_value || '';
                }

                const imapPortSetting = settings.find(s => s.setting_key === 'imap_port');
                if (imapPortSetting) {
                    document.getElementById('imap_port').value = imapPortSetting.setting_value || '';
                }

                const imapFlagsSetting = settings.find(s => s.setting_key === 'imap_flags');
                if (imapFlagsSetting) {
                    document.getElementById('imap_flags').value = imapFlagsSetting.setting_value || '';
                }

                const stripePublicKeySetting = settings.find(s => s.setting_key === 'stripe_public_key');
                if (stripePublicKeySetting) {
                    document.getElementById('stripe_public_key').value = stripePublicKeySetting.setting_value || '';
                }
                
                const stripeSecretKeySetting = settings.find(s => s.setting_key === 'stripe_secret_key');
                if (stripeSecretKeySetting) {
                    document.getElementById('stripe_secret_key').value = stripeSecretKeySetting.setting_value || '';
                }

                const stripeWebhookSecretSetting = settings.find(s => s.setting_key === 'stripe_webhook_secret');
                if (stripeWebhookSecretSetting) {
                    document.getElementById('stripe_webhook_secret').value = stripeWebhookSecretSetting.setting_value || '';
                }
            }
        } catch (error) {
            console.error('Error loading settings:', error);
        }
    }

    function toggleApiKeyVisibility() {
        const apiKeyInput = document.getElementById('infobip_api_key');
        const toggleBtnIcon = document.querySelector('#toggleApiKeyBtn i');
        if (apiKeyInput.type === 'password') {
            apiKeyInput.type = 'text';
            toggleBtnIcon.classList.remove('ri-eye-line');
            toggleBtnIcon.classList.add('ri-eye-off-line');
        } else {
            apiKeyInput.type = 'password';
            toggleBtnIcon.classList.remove('ri-eye-off-line');
            toggleBtnIcon.classList.add('ri-eye-line');
        }
    }

    function toggleStripeKeyVisibility() {
        const stripeKeyInput = document.getElementById('stripe_secret_key');
        const toggleBtnIcon = document.querySelector('#toggleStripeKeyBtn i');
        if (stripeKeyInput.type === 'password') {
            stripeKeyInput.type = 'text';
            toggleBtnIcon.classList.remove('ri-eye-line');
            toggleBtnIcon.classList.add('ri-eye-off-line');
        } else {
            stripeKeyInput.type = 'password';
            toggleBtnIcon.classList.remove('ri-eye-off-line');
            toggleBtnIcon.classList.add('ri-eye-line');
        }
    }

    async function loadConfigSettings() {
        // This is now integrated into loadGeneralSettings to reduce API calls
        // But keeping the function call in DOMContentLoaded for clarity or future specific logic
    }

    function previewImage(input, previewId, placeholderId, containerId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);

                preview.src = e.target.result;
                preview.classList.remove('d-none');
                placeholder.classList.add('d-none');
                document.getElementById(containerId).style.border = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function handleImageUpload(btnId, fileInputId, actionName, formDataKey) {
        const fileInput = document.getElementById(fileInputId);
        if (!fileInput.files || fileInput.files.length === 0) {
            if (typeof showAlert === 'function') showAlert('Please select an image to upload', 'warning');
            else alert('Please select an image to upload');
            return;
        }

        const formData = new FormData();
        formData.append(formDataKey, fileInput.files[0]);

        const btn = document.getElementById(btnId);
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=${actionName}`, {
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
            console.error(`Error updating image (${actionName}):`, error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    document.getElementById('saveLogoBtn').addEventListener('click', () => {
        handleImageUpload('saveLogoBtn', 'logoInput', 'update_logo', 'logo');
    });

    document.getElementById('saveLoginImageBtn').addEventListener('click', () => {
        handleImageUpload('saveLoginImageBtn', 'loginInput', 'update_login_image', 'login_image');
    });

    document.getElementById('saveSignupImageBtn').addEventListener('click', () => {
        handleImageUpload('saveSignupImageBtn', 'signupInput', 'update_signup_image', 'signup_image');
    });

    document.getElementById('saveConfigBtn').addEventListener('click', async () => {
        const companyName = document.getElementById('company_name').value;
        const mailFrom = document.getElementById('mail_from_address').value;
        const baseUrl = document.getElementById('base_url').value;
        const infobipApiKey = document.getElementById('infobip_api_key').value;
        const infobipApiBaseUrl = document.getElementById('infobip_api_base_url').value;
        const infobipSender = document.getElementById('infobip_sender').value;

        const imapHost = document.getElementById('imap_host').value;
        const imapPort = document.getElementById('imap_port').value;
        const imapFlags = document.getElementById('imap_flags').value;
        
        const stripePublicKey = document.getElementById('stripe_public_key').value;
        const stripeSecretKey = document.getElementById('stripe_secret_key').value;
        const stripeWebhookSecret = document.getElementById('stripe_webhook_secret').value;

        const btn = document.getElementById('saveConfigBtn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=update_setting`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    settings: {
                        company_name: companyName,
                        mail_from_address: mailFrom,
                        base_url: baseUrl,
                        infobip_api_key: infobipApiKey,
                        infobip_api_base_url: infobipApiBaseUrl,
                        infobip_sender: infobipSender,
                        imap_host: imapHost,
                        imap_port: imapPort,
                        imap_flags: imapFlags,
                        stripe_public_key: stripePublicKey,
                        stripe_secret_key: stripeSecretKey,
                        stripe_webhook_secret: stripeWebhookSecret
                    }
                })
            });
            const result = await response.json();

            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message, 'success');
                else alert(result.message);
            } else {
                if (typeof showAlert === 'function') showAlert(result.message, 'error');
                else alert(result.message);
            }
        } catch (error) {
            console.error('Error updating config:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    // --- Theme Levels Logic ---

    const THEME_LEVEL_API_PATH = '<?php echo $path_prefix; ?>api/admin/theme_level.php';
    let globalThemeLevels = [];

    async function loadThemeLevels() {
        const tableBody = document.getElementById('theme-levels-table-body');
        try {
            const response = await fetch(`${THEME_LEVEL_API_PATH}?action=list`);
            const result = await response.json();

            if (result.status === 'success') {
                globalThemeLevels = result.data;
                renderThemeLevelsTable(result.data);
            } else {
                tableBody.innerHTML = `<tr><td colspan="2" class="text-center py-4 text-danger small">${result.message || 'No levels found'}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading theme levels:', error);
            tableBody.innerHTML = `<tr><td colspan="2" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderThemeLevelsTable(levels) {
        const tableBody = document.getElementById('theme-levels-table-body');

        if ($.fn.DataTable.isDataTable('#themeLevelsTable')) {
            $('#themeLevelsTable').DataTable().destroy();
        }

        tableBody.innerHTML = '';

        if (levels.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="2" class="text-center py-4 text-muted small">No theme levels found.</td></tr>';
            return;
        }

        levels.forEach(level => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';

            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${level.name}</td>
                <td class="px-3 py-2 text-end">
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editThemeLevel(${level.id}, '${level.name}')" title="Edit">
                        <i class="ri-pencil-line fs-6"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteThemeLevel(${level.id})" title="Delete">
                        <i class="ri-delete-bin-line fs-6"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        $('#themeLevelsTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center p-3"ip>',
            "pageLength": 10,
            "destroy": true,
            "language": {
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            "columnDefs": [{
                "orderable": false,
                "targets": 1
            }]
        });
    }

    function prepareThemeLevelModal(action) {
        const modalTitle = document.getElementById('themeLevelModalLabel');
        const form = document.getElementById('themeLevelForm');
        form.reset();
        document.getElementById('theme_level_id').value = '';

        if (action === 'add') {
            modalTitle.innerText = 'Add Theme Level';
        } else {
            modalTitle.innerText = 'Edit Theme Level';
        }
    }

    function editThemeLevel(id, name) {
        prepareThemeLevelModal('edit');
        document.getElementById('theme_level_id').value = id;
        document.getElementById('theme_level_name').value = name;

        const modal = new bootstrap.Modal(document.getElementById('themeLevelModal'));
        modal.show();
    }

    async function deleteThemeLevel(id) {
        if (confirm('Are you sure you want to delete this level?')) {
            try {
                const response = await fetch(`${THEME_LEVEL_API_PATH}?action=delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    if (typeof showAlert === 'function') showAlert('Level deleted successfully', 'success');
                    else alert('Level deleted successfully');
                    loadThemeLevels();
                } else {
                    if (typeof showAlert === 'function') showAlert(result.message || 'Failed to delete level', 'error');
                    else alert(result.message || 'Failed to delete level');
                }
            } catch (error) {
                console.error('Error deleting level:', error);
                if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
                else alert('Network error occurred');
            }
        }
    }

    document.getElementById('saveThemeLevelBtn').addEventListener('click', async () => {
        const form = document.getElementById('themeLevelForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const action = data.id ? 'update' : 'create';

        const btn = document.getElementById('saveThemeLevelBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${THEME_LEVEL_API_PATH}?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message || 'Level saved successfully', 'success');
                else alert(result.message || 'Level saved successfully');

                const modalEl = document.getElementById('themeLevelModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                loadThemeLevels();
            } else {
                if (typeof showAlert === 'function') showAlert(result.message || 'Failed to save level', 'error');
                else alert(result.message || 'Failed to save level');
            }
        } catch (error) {
            console.error('Error saving theme level:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
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

            let badge = category.is_default == 1 && category.family_id == null ?
                `<span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill extra-small">Global</span>` :
                `<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill extra-small">Family</span>`;

            let familyContent = category.family_name ?
                `<span class="small fw-semibold text-dark">${category.family_name}</span>` :
                `<span class="badge bg-light text-muted px-2 py-1 rounded-pill extra-small border">N/A</span>`;

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
            "columnDefs": [{
                "orderable": false,
                "targets": 3
            }]
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

    // --- Timezone Logic ---

    async function loadTimezones() {
        const tableBody = document.getElementById('timezones-table-body');
        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=fetch_all`);
            const result = await response.json();

            if (result.status === 'success') {
                const timezoneSetting = result.data.find(s => s.setting_key === 'timezone');
                const timezones = timezoneSetting ? JSON.parse(timezoneSetting.setting_value || '[]') : [];
                renderTimezonesTable(timezones);
            } else {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">${result.message}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading timezones:', error);
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderTimezonesTable(timezones) {
        const tableBody = document.getElementById('timezones-table-body');

        if ($.fn.DataTable.isDataTable('#timezonesTable')) {
            $('#timezonesTable').DataTable().destroy();
        }

        tableBody.innerHTML = '';

        if (timezones.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted small">No time zones found.</td></tr>';
            return;
        }

        timezones.forEach((tz, index) => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';

            tr.innerHTML = `
                <td class="px-3 py-2 fw-bold text-dark small">${tz.timezone}</td>
                <td class="py-2 text-dark small">${tz.lable}</td>
                <td class="px-3 py-2 text-end">
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick="editTimezone(${index}, '${tz.timezone}', '${tz.lable}')" title="Edit">
                        <i class="ri-pencil-line fs-6"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteTimezone(${index})" title="Delete">
                        <i class="ri-delete-bin-line fs-6"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        $('#timezonesTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center p-3"ip>',
            "pageLength": 10,
            "destroy": true,
            "language": {
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            "columnDefs": [{
                "orderable": false,
                "targets": 2
            }]
        });
    }

    function prepareTimezoneModal(action) {
        const modalTitle = document.getElementById('timezoneModalLabel');
        const form = document.getElementById('timezoneForm');
        form.reset();
        document.getElementById('timezone_index').value = '';

        if (action === 'add') {
            modalTitle.innerText = 'Add Time Zone';
        } else {
            modalTitle.innerText = 'Edit Time Zone';
        }
    }

    function editTimezone(index, timezone, lable) {
        prepareTimezoneModal('edit');
        document.getElementById('timezone_index').value = index;
        document.getElementById('timezone_value').value = timezone;
        document.getElementById('timezone_lable').value = lable;

        const modal = new bootstrap.Modal(document.getElementById('timezoneModal'));
        modal.show();
    }

    async function deleteTimezone(index) {
        if (confirm('Are you sure you want to delete this time zone?')) {
            try {
                const response = await fetch(`${SETTINGS_API_PATH}?action=delete_timezone`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        index: index
                    })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    if (typeof showAlert === 'function') showAlert('Time zone deleted successfully', 'success');
                    else alert('Time zone deleted successfully');
                    loadTimezones();
                } else {
                    if (typeof showAlert === 'function') showAlert(result.message, 'error');
                    else alert(result.message);
                }
            } catch (error) {
                console.error('Error deleting timezone:', error);
                if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
                else alert('Network error occurred');
            }
        }
    }

    document.getElementById('saveTimezoneBtn').addEventListener('click', async () => {
        const form = document.getElementById('timezoneForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        const index = data.index;
        const action = index !== '' ? 'update_timezone' : 'add_timezone';

        const btn = document.getElementById('saveTimezoneBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        const timezoneData = {
            timezone: data.timezone,
            lable: data.lable
        };

        try {
            const response = await fetch(`${SETTINGS_API_PATH}?action=${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    index: index !== '' ? parseInt(index) : null,
                    timezone_data: timezoneData
                })
            });
            const result = await response.json();
            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message, 'success');
                else alert(result.message);

                const modalEl = document.getElementById('timezoneModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                loadTimezones();
            } else {
                if (typeof showAlert === 'function') showAlert(result.message, 'error');
                else alert(result.message);
            }
        } catch (error) {
            console.error('Error saving timezone:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    // --- Themed Rewards Logic ---

    const THEMED_REWARDS_API_PATH = '<?php echo $path_prefix; ?>api/admin/theme_rewards.php';
    let themedRewardLevelCount = 0;

    async function loadThemedRewards() {
        const tableBody = document.getElementById('themed-rewards-table-body');
        try {
            const response = await fetch(`${THEMED_REWARDS_API_PATH}?action=list`);
            const result = await response.json();

            if (result.status === 'success') {
                renderThemedRewardsTable(result.data);
            } else {
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">${result.message || 'No themes found'}</td></tr>`;
            }
        } catch (error) {
            console.error('Error loading themed rewards:', error);
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-4 text-danger small">Failed to connect to server</td></tr>`;
        }
    }

    function renderThemedRewardsTable(themes) {
        const tableBody = document.getElementById('themed-rewards-table-body');

        if ($.fn.DataTable.isDataTable('#themedRewardsTable')) {
            $('#themedRewardsTable').DataTable().destroy();
        }

        tableBody.innerHTML = '';

        if (themes.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-muted small">No global themes found.</td></tr>';
            return;
        }

        themes.forEach(theme => {
            const tr = document.createElement('tr');
            tr.className = 'border-bottom';

            let levelsHtml = '<div class="d-flex flex-wrap gap-1 justify-content-center">';
            if (theme.levels && theme.levels.length > 0) {
                theme.levels.forEach(l => {
                    let imgHtml = '';
                    if (l.image) {
                        let imagePath = l.image;
                        if (!imagePath.startsWith('../') && !imagePath.startsWith('http')) {
                            imagePath = '<?php echo $path_prefix; ?>' + imagePath;
                        } else if (imagePath.startsWith('../')) {
                            imagePath = '<?php echo $path_prefix; ?>' + imagePath.replace('../', '');
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
                <td class="px-3 py-2 fw-bold text-dark small">${theme.name}</td>
                <td class="px-3 py-2 text-center small text-muted">${levelsHtml}</td>
                <td class="px-3 py-2 text-end">
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 me-1 hover-shadow" style="width: 32px; height: 32px;" onclick='editThemedReward(${JSON.stringify(theme).replace(/'/g, "&#39;")})' title="Edit">
                        <i class="ri-pencil-line fs-6"></i>
                    </button>
                    <button class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center p-0 text-danger hover-shadow" style="width: 32px; height: 32px;" onclick="deleteThemedReward('${theme.name.replace(/'/g, "\\'")}')" title="Delete">
                        <i class="ri-delete-bin-line fs-6"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        $('#themedRewardsTable').DataTable({
            "dom": 'rt<"d-flex justify-content-between align-items-center p-3"ip>',
            "pageLength": 10,
            "destroy": true,
            "language": {
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            "columnDefs": [{
                "orderable": false,
                "targets": 2
            }]
        });
    }

    function addThemedRewardLevel(levelData = null) {
        const container = document.getElementById('themed-reward-levels-container');
        const id = themedRewardLevelCount++;
        
        let existingImageHtml = '';
        let currentImgSrc = '';
        if (levelData && levelData.image) {
            let imagePath = levelData.image;
            if (!imagePath.startsWith('../') && !imagePath.startsWith('http')) {
                imagePath = '<?php echo $path_prefix; ?>' + imagePath;
            } else if (imagePath.startsWith('../')) {
                imagePath = '<?php echo $path_prefix; ?>' + imagePath.replace('../', '');
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
                            <input type="file" class="form-control form-control-sm border-0 shadow-sm" name="levels_image_${id}" accept="image/*" onchange="previewLevelImage(this, 'level-img-preview-${id}')">
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

    function prepareThemedRewardModal(action) {
        const modalTitle = document.getElementById('themedRewardModalLabel');
        const form = document.getElementById('themedRewardForm');
        form.reset();
        document.getElementById('themed_reward_old_name').value = '';
        document.getElementById('themed-reward-levels-container').innerHTML = '';
        themedRewardLevelCount = 0;

        if (action === 'add') {
            modalTitle.innerText = 'Add Global Theme';
            globalThemeLevels.forEach(level => addThemedRewardLevel({ level: level.name, points: '' }));
        } else {
            modalTitle.innerText = 'Edit Global Theme';
        }
    }

    function editThemedReward(theme) {
        prepareThemedRewardModal('edit');
        document.getElementById('themed_reward_old_name').value = theme.name;
        document.getElementById('themed_reward_name').value = theme.name;

        // Add rows based on predefined levels
        globalThemeLevels.forEach(predefined => {
            const existing = theme.levels ? theme.levels.find(l => l.level === predefined.name) : null;
            if (existing) {
                addThemedRewardLevel({ level: existing.level, points: existing.amount, image: existing.image });
            } else {
                addThemedRewardLevel({ level: predefined.name, points: '' });
            }
        });

        const modal = new bootstrap.Modal(document.getElementById('themedRewardModal'));
        modal.show();
    }

    async function deleteThemedReward(name) {
        if (confirm(`Are you sure you want to delete the theme "${name}" and all its levels?`)) {
            try {
                const response = await fetch(`${THEMED_REWARDS_API_PATH}?action=delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name: name })
                });
                const result = await response.json();
                if (result.status === 'success') {
                    if (typeof showAlert === 'function') showAlert('Theme deleted successfully', 'success');
                    else alert('Theme deleted successfully');
                    loadThemedRewards();
                } else {
                    if (typeof showAlert === 'function') showAlert(result.message || 'Failed to delete theme', 'error');
                    else alert(result.message || 'Failed to delete theme');
                }
            } catch (error) {
                console.error('Error deleting theme:', error);
                if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
                else alert('Network error occurred');
            }
        }
    }

    document.getElementById('saveThemedRewardBtn').addEventListener('click', async () => {
        const form = document.getElementById('themedRewardForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const container = document.getElementById('themed-reward-levels-container');
        if (container.children.length === 0) {
            alert('Please add at least one level to the theme.');
            return;
        }

        const formData = new FormData(form);
        const oldName = formData.get('old_name');
        const action = oldName ? 'update' : 'create';

        // Reconstruct levels array from FormData to standard JSON format for the API
        // But since we have file uploads, we'll just send the FormData directly 
        // after formatting the levels as JSON so PHP can decode it
        
        let levels = [];
        const levelsNodes = container.querySelectorAll('.level-row');
        levelsNodes.forEach(node => {
            const id = node.id.split('-').pop();
            levels.push({
                level: formData.get(`levels[${id}][level]`),
                points: formData.get(`levels[${id}][points]`),
                existing_image: formData.get(`levels[${id}][existing_image]`) || null
            });
            formData.delete(`levels[${id}][level]`);
            formData.delete(`levels[${id}][points]`);
            formData.delete(`levels[${id}][existing_image]`);
        });
        
        formData.append('levels', JSON.stringify(levels));

        const btn = document.getElementById('saveThemedRewardBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

        try {
            const response = await fetch(`${THEMED_REWARDS_API_PATH}?action=${action}`, {
                method: 'POST',
                // Don't set Content-Type header when using FormData; browser does it automatically with boundary
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message || 'Theme saved successfully', 'success');
                else alert(result.message || 'Theme saved successfully');

                const modalEl = document.getElementById('themedRewardModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                loadThemedRewards();
            } else {
                if (typeof showAlert === 'function') showAlert(result.message || 'Failed to save theme', 'error');
                else alert(result.message || 'Failed to save theme');
            }
        } catch (error) {
            console.error('Error saving theme:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });

    // Reset System Functions
    let resetModalInstance = null;

    function openPasswordModal(actionType, message) {
        document.getElementById('confirmActionType').value = actionType;
        document.getElementById('passwordConfirmMessage').innerText = message;
        document.getElementById('confirmAdminPassword').value = '';
        
        if (!resetModalInstance) {
            resetModalInstance = new bootstrap.Modal(document.getElementById('passwordConfirmModal'));
        }
        resetModalInstance.show();
    }

    function triggerResetAssets() {
        if (!confirm("WARNING: You are about to permanently delete all events, meals, recipes, grocery lists, and uploaded photos in the system! Users and Configuration will be kept.")) {
            return;
        }
        openPasswordModal('resetAssets', "Type your password to permanently delete all family assets:");
    }

    function triggerFactoryReset() {
        if (!confirm("CRITICAL WARNING: This will FACTORY RESET the entire application! All non-admin users, all families, all assets, and all data will be permanently wiped.")) {
            return;
        }
        openPasswordModal('factoryReset', "Type your password to securely wipe the entire system:");
    }

    document.getElementById('executeResetBtn').addEventListener('click', async () => {
        const actionType = document.getElementById('confirmActionType').value;
        const password = document.getElementById('confirmAdminPassword').value;

        if (!password) {
            alert('Password is required!');
            return;
        }

        const btn = document.getElementById('executeResetBtn');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';

        try {
            const response = await fetch(`../api/admin_reset.php?action=${actionType}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ password: password })
            });
            const result = await response.json();
            
            if (result.status === 'success') {
                if (typeof showAlert === 'function') showAlert(result.message, 'success');
                else alert(result.message);
                
                resetModalInstance.hide();
                if (actionType === 'factoryReset') {
                    setTimeout(() => window.location.reload(), 2000);
                }
            } else {
                if (typeof showAlert === 'function') showAlert(result.message, 'error');
                else alert(result.message);
            }
        } catch (error) {
            console.error('Error executing reset:', error);
            if (typeof showAlert === 'function') showAlert('Network error occurred', 'error');
            else alert('Network error occurred');
        } finally {
            btn.disabled = false;
            btn.innerText = originalText;
        }
    });
</script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>