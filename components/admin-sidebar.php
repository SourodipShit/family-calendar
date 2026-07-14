<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
$current_page = basename($_SERVER['PHP_SELF']);
require_once __DIR__ . '/../classes/GlobalSettings.php';
require_once __DIR__ . '/../classes/Recipe.php';
$logoData = GlobalSettings::getSetting('site_logo')['data'] ?? [];
$globalSettingsLogo = $logoData['setting_value'] ?? null;

if ($globalSettingsLogo && file_exists($globalSettingsLogo)) {
    // Keep as is
} else {
    $globalSettingsLogo = $path_prefix . "public/img/logo-fmly.png";
}
?>
<!-- Admin Sidebar -->
<div class="sidebar admin-sidebar offcanvas-lg offcanvas-start d-flex flex-column" tabindex="-1" id="sidebar-wrapper"
    aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header border-bottom border-secondary d-lg-none">
        <a href="<?php echo $path_prefix; ?>siteadmin/index.php"
            class="sidebar-heading fw-bold fs-4 d-flex align-items-center m-0 p-0 text-decoration-none">
            <img src="<?php echo $globalSettingsLogo; ?>" alt="logo" style="max-height: 60px;" />
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#sidebar-wrapper"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-column p-0 flex-grow-1">
        <div class="sidebar-heading fw-bold fs-4 d-none d-lg-flex align-items-center mb-3 mt-3 px-4 justify-content-center align-items-center">
            <a href="<?php echo $path_prefix; ?>siteadmin/index.php" class="text-decoration-none d-flex align-items-center">
                <img src="<?php echo $globalSettingsLogo; ?>" alt="logo" style="max-height: 90px;" />
            </a>
        </div>

        <div class="px-4 mb-2">
            <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Main Menu</small>
        </div>

        <div class="list-group list-group-flush flex-grow-1 w-100">
            <a href="<?php echo $path_prefix; ?>siteadmin/index.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'index.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-dashboard-3-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Dashboard</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Dashboard</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/families.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'families.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-community-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Manage Families</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Manage Families</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/bills.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'bills.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-file-list-3-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Bills & Invoices</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Bills & Invoices</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/users.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'users.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-user-settings-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Manage Users</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Manage Users</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/recipes.php"
                class="list-group-item list-group-item-action d-flex flex-column flex-lg-row align-items-center text-center text-lg-start <?php echo $current_page == 'recipes.php' ? 'active' : ''; ?> rounded mb-1">
                <div class="d-flex flex-column flex-lg-row align-items-center w-100">
                    <i class="ri-restaurant-line me-lg-3 fs-4 fs-lg-auto"></i>
                    <span class="d-none d-lg-inline">Manage Recipes</span>
                    <small class="d-block d-lg-none" style="font-size: 10px;">Manage Recipes</small>
                    <?php
                    $pendingRecipesRes = Recipe::getPendingRecipesCount();
                    $pendingRecipesCount = ($pendingRecipesRes['status'] === 'success') ? $pendingRecipesRes['data'] : 0;
                    if ($pendingRecipesCount > 0):
                    ?>
                        <span class="badge bg-danger rounded-circle ms-lg-auto mt-1 mt-lg-0" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; padding: 0;"><?php echo $pendingRecipesCount; ?></span>
                    <?php endif; ?>
                </div>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/coaches.php"
                class="list-group-item list-group-item-action d-flex flex-column flex-lg-row align-items-center text-center text-lg-start <?php echo $current_page == 'coaches.php' ? 'active' : ''; ?> rounded mb-1">
                <div class="d-flex flex-column flex-lg-row align-items-center w-100">
                    <i class="ri-user-star-line me-lg-3 fs-4 fs-lg-auto"></i>
                    <span class="d-none d-lg-inline">Manage Coaches</span>
                    <small class="d-block d-lg-none" style="font-size: 10px;">Manage Coaches</small>
                    <?php
                    require_once __DIR__ . '/../classes/Coach.php';
                    $pendingCoachesRes = Coach::getPendingCoachesCount();
                    $pendingCoachesCount = ($pendingCoachesRes['status'] === 'success') ? $pendingCoachesRes['data'] : 0;
                    if ($pendingCoachesCount > 0):
                    ?>
                        <span class="badge bg-danger rounded-circle ms-lg-auto mt-1 mt-lg-0" style="width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; padding: 0;"><?php echo $pendingCoachesCount; ?></span>
                    <?php endif; ?>
                </div>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/coach_approvals.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'coach_approvals.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-checkbox-circle-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Coach Approvals</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Coach Approvals</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/promocodes.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'promocodes.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-coupon-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Promo Codes</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Promo Codes</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/system.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'system.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-server-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">System Health</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">System Health</small>
            </a>

            <div class="px-4 mt-4 mb-2">
                <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Configuration</small>
            </div>

            <a href="<?php echo $path_prefix; ?>siteadmin/agreements.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'agreements.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-file-paper-2-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Legal Agreements</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Legal Agreements</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/settings.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'settings.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-settings-4-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Global Settings</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Global Settings</small>
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/loginlogs.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'loginlogs.php' ? 'active' : ''; ?> rounded mb-1 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-history-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Login Logs</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Login Logs</small>
            </a>
        </div>

        <div class="sidebar-footer mt-auto p-3 w-100 border-top" style="border-color: #f1f5f9 !important;">
            <a href="<?php echo $path_prefix; ?>users/index.php"
                class="d-flex flex-column flex-lg-row align-items-center text-center text-lg-start text-decoration-none text-light hover-bg-dark-opacity p-2 rounded mb-2">
                <i class="ri-arrow-left-line me-lg-2 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Back to App</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Back to App</small>
            </a>
            <a href="<?php echo $path_prefix; ?>logout.php"
                class="d-flex flex-column flex-lg-row align-items-center text-center text-lg-start text-decoration-none text-danger hover-bg-dark-opacity p-2 rounded">
                <i class="ri-logout-box-r-line me-lg-2 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Logout</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Logout</small>
            </a>
        </div>
    </div>
</div>
<!-- /#sidebar-wrapper -->