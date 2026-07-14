<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
$current_page = basename($_SERVER['PHP_SELF']);
require_once __DIR__ . '/../classes/GlobalSettings.php';
$logoData = GlobalSettings::getSetting('site_logo')['data'] ?? [];
$globalSettingsLogo = $logoData['setting_value'] ?? null;

if ($globalSettingsLogo && file_exists(__DIR__ . '/../' . ltrim($globalSettingsLogo, './'))) {
    $globalSettingsLogo = $path_prefix . ltrim($globalSettingsLogo, './');
} else {
    $globalSettingsLogo = $path_prefix . "public/img/logo-fmly.png";
}
?>
<!-- Sidebar -->
<div class="sidebar offcanvas-lg offcanvas-start d-flex flex-column" tabindex="-1" id="sidebar-wrapper"
    aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header border-bottom d-lg-none">
        <a href="<?php echo $path_prefix; ?>coach/index.php"
            class="sidebar-heading text-primary fw-bold fs-4 d-none d-lg-flex align-items-center m-0 p-0">
            <img src="<?php echo htmlspecialchars($globalSettingsLogo); ?>" alt="logo" style="max-height: 60px;" />
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar-wrapper"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-column p-0 flex-grow-1">
        <div class="sidebar-heading text-primary fw-bold fs-4 d-none d-lg-flex align-items-center mb-4 mt-4">
            <a href="<?php echo $path_prefix; ?>coach/index.php">
                <img src="<?php echo htmlspecialchars($globalSettingsLogo); ?>" alt="logo" style="max-height: 90px;" />
            </a>
        </div>
        
        <div class="list-group list-group-flush flex-grow-1 w-100">
            <a href="<?php echo $path_prefix; ?>coach/index.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'index.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-dashboard-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Dashboard</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Dashboard</small>
            </a>

            <a href="<?php echo $path_prefix; ?>coach/profile.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'profile.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-user-settings-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">My Profile</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Profile</small>
            </a>

            <a href="<?php echo $path_prefix; ?>coach/plans.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'plans.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-list-check-3 me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">My Plans</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Plans</small>
            </a>

            <a href="<?php echo $path_prefix; ?>coach/clients.php"
                class="list-group-item list-group-item-action <?php echo $current_page == 'clients.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-group-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Clients</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Clients</small>
            </a>
            <a href="mailto:support@familycalendar.com" 
                class="list-group-item list-group-item-action text-secondary rounded mb-2 d-lg-none d-flex flex-column align-items-center text-center">
                <i class="ri-mail-line fs-4"></i>
                <small class="d-block mt-1" style="font-size: 10px;">Email</small>
            </a>
        </div>
        <div class="sidebar-footer mt-auto p-3 w-100">
            <div class="bg-light rounded p-3 mb-3 d-none d-lg-block">
                <p class="mb-1 fw-bold fs-6">Coach Email</p>
                <a href="mailto:<?php echo $_SESSION['user']['email'] ?? 'support@familycalendar.com' ?>"
                    class="text-decoration-none text-primary small"><?php echo $_SESSION['user']['email'] ?? 'support@familycalendar.com' ?></a>
            </div>
        </div>
    </div>
</div>
<!-- /#sidebar-wrapper -->
