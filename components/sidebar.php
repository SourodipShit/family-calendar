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
                class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-calendar-2-line me-lg-3"></i><span class="d-none d-lg-inline">Calendar</span></a>
            <a href="<?php echo $path_prefix; ?>users/chores.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'chores.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-cloud-line me-lg-3"></i><span class="d-none d-lg-inline">Chores</span></a>
            <a href="<?php echo $path_prefix; ?>users/rewards.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'rewards.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-medal-line me-lg-3"></i><span class="d-none d-lg-inline">Rewards</span></a>
            <a href="<?php echo $path_prefix; ?>users/meals.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'meals.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-bowl-line me-lg-3"></i><span class="d-none d-lg-inline">Meals</span></a>
            <a href="<?php echo $path_prefix; ?>users/recipes.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'recipes.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-bread-line me-lg-3"></i><span class="d-none d-lg-inline">Recipes</span></a>
            <a href="#" class="list-group-item list-group-item-action text-secondary rounded mb-2"><i
                    class="ri-folder-image-line me-lg-3"></i><span class="d-none d-lg-inline">Photos</span></a>
            <a href="<?php echo $path_prefix; ?>users/coaches.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'coaches.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-group-line me-lg-3"></i><span class="d-none d-lg-inline">Coaches</span></a>
            <a href="<?php echo $path_prefix; ?>users/settings.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active bg-primary text-white' : 'text-secondary'; ?> rounded mb-2"><i
                    class="ri-settings-2-line me-lg-3"></i><span class="d-none d-lg-inline">Settings</span></a>
            <a href="mailto:<?php echo $_SESSION['user']['families'][0]['email'] ?>" class="list-group-item list-group-item-action text-secondary rounded mb-2 d-lg-none"><i
                    class="ri-mail-line me-lg-3"></i><span class="d-none d-lg-inline">Email</span></a>
        </div>
        <div class="sidebar-footer mt-auto p-3 w-100">
            <div class="bg-light rounded p-3 mb-3 d-none d-lg-block">
                <p class="mb-1 fw-bold fs-6">Family Email</p>
                <a href="mailto:<?php echo $_SESSION['user']['families'][0]['email'] ?>"
                    class="text-decoration-none text-primary small"><?php echo $_SESSION['user']['families'][0]['email'] ?></a>
            </div>
            <a href="<?php echo $path_prefix; ?>siteadmin/index.php"
                class="d-none d-lg-flex align-items-center text-decoration-none text-dark hover-bg-light p-2 rounded">
                <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center"
                    style="width: 32px; height: 32px;">
                    <i class="ri-lock-2-line"></i>
                </div>
                <div>
                    <span class="fw-bold d-block">Site Admin</span>
                    <small class="text-muted">Administrator</small>
                </div>
                <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
    </div>
</div>
<!-- /#sidebar-wrapper -->