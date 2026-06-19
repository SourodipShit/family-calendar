<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "";
require_once __DIR__ . '/../classes/GlobalSettings.php';
$logoData = GlobalSettings::getSetting('site_logo')['data'] ?? [];
$globalSettingsLogo = $logoData['setting_value'] ?? null;

if ($globalSettingsLogo && file_exists($globalSettingsLogo)) {
    // Keep as is
} else {
    $globalSettingsLogo = $path_prefix . "public/img/logo-fmly.png";
}
?>
<!-- Sidebar -->
<div class="sidebar offcanvas-lg offcanvas-start d-flex flex-column" tabindex="-1" id="sidebar-wrapper"
    aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header border-bottom d-lg-none">
        <a href="<?php echo $path_prefix; ?>users/index.php"
            class="sidebar-heading text-primary fw-bold fs-4 d-none d-lg-flex align-items-center m-0 p-0">
            <img src="<?php echo $globalSettingsLogo; ?>" alt="logo" style="max-height: 60px;" />
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar-wrapper"
            aria-label="Close"></button>
    </div>
    <div class="offcanvas-body flex-column p-0 flex-grow-1">
        <div class="sidebar-heading text-primary fw-bold fs-4 d-none d-lg-flex align-items-center mb-4 mt-4">
            <a href="<?php echo $path_prefix; ?>users/index.php">
                <img src="<?php echo $globalSettingsLogo; ?>" alt="logo" style="max-height: 90px;" />
            </a>

        </div>
        <div class="list-group list-group-flush flex-grow-1 w-100">
            <a href="<?php echo $path_prefix; ?>users/index.php"
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-calendar-2-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Calendar</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Calendar</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/chores.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'chores.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-cloud-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Chores</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Chores</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/rewards.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'rewards.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-medal-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Rewards</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Rewards</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/meals.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'meals.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-bowl-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Meals</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Meals</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/recipes.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'recipes.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-bread-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Recipes</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Recipes</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/photos.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'photos.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-folder-image-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Photos</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Photos</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/coaches.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'coaches.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-group-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Coaches</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Coaches</small>
            </a>
            <a href="<?php echo $path_prefix; ?>users/settings.php" 
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2 d-flex flex-column flex-lg-row align-items-center text-center text-lg-start">
                <i class="ri-settings-2-line me-lg-3 fs-4 fs-lg-auto"></i>
                <span class="d-none d-lg-inline">Settings</span>
                <small class="d-block d-lg-none" style="font-size: 10px;">Settings</small>
            </a>
            <a href="mailto:<?php echo $_SESSION['user']['active_family']['email'] ?>" 
                class="list-group-item list-group-item-action text-secondary rounded mb-2 d-lg-none d-flex flex-column align-items-center text-center">
                <i class="ri-mail-line fs-4"></i>
                <small class="d-block mt-1" style="font-size: 10px;">Email</small>
            </a>
        </div>
        <div class="sidebar-footer mt-auto p-3 w-100">
            <div class="bg-light rounded p-3 mb-3 d-none d-lg-block">
                <p class="mb-1 fw-bold fs-6">Family Email</p>
                <a href="mailto:<?php echo $_SESSION['user']['active_family']['email'] ?>"
                    class="text-decoration-none text-primary small"><?php echo $_SESSION['user']['active_family']['email'] ?></a>
            </div>

        </div>
    </div>
</div>
<!-- /#sidebar-wrapper -->