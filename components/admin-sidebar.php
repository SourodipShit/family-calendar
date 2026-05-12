<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
$current_page = basename($_SERVER['PHP_SELF']);
require_once __DIR__ . '/../classes/GlobalSettings.php';
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
                class="list-group-item list-group-item-action <?php echo $current_page == 'index.php' ? 'active' : ''; ?> rounded mb-1">
                <i class="ri-dashboard-3-line me-3"></i>Dashboard
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/families.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'families.php' ? 'active' : ''; ?> rounded mb-1">
                <i class="ri-community-line me-3"></i>Manage Families
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/users.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'users.php' ? 'active' : ''; ?> rounded mb-1">
                <i class="ri-user-settings-line me-3"></i>Manage Users
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/system.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'system.php' ? 'active' : ''; ?> rounded mb-1">
                <i class="ri-server-line me-3"></i>System Health
            </a>

            <div class="px-4 mt-4 mb-2">
                <small class="text-uppercase text-muted fw-bold" style="font-size: 10px;">Configuration</small>
            </div>

            <a href="<?php echo $path_prefix; ?>siteadmin/settings.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'settings.php' ? 'active' : ''; ?> rounded mb-1">
                <i class="ri-settings-4-line me-3"></i>Global Settings
            </a>

            <a href="<?php echo $path_prefix; ?>siteadmin/logs.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'logs.php' ? 'active' : ''; ?> rounded mb-1">
                <i class="ri-history-line me-3"></i>Audit Logs
            </a>
        </div>

        <div class="sidebar-footer mt-auto p-3 w-100 border-top" style="border-color: #f1f5f9 !important;">
            <a href="<?php echo $path_prefix; ?>users/index.php"
                class="d-flex align-items-center text-decoration-none text-light hover-bg-dark-opacity p-2 rounded mb-2">
                <i class="ri-arrow-left-line me-2"></i>
                <span>Back to App</span>
            </a>
            <a href="<?php echo $path_prefix; ?>logout.php"
                class="d-flex align-items-center text-decoration-none text-danger hover-bg-dark-opacity p-2 rounded">
                <i class="ri-logout-box-r-line me-2"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>
</div>
<!-- /#sidebar-wrapper -->