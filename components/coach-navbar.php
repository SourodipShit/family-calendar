<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
$page_title = isset($page_title) ? $page_title : "Coach Dashboard";
require_once __DIR__ . '/../classes/GlobalSettings.php';
$logoData = GlobalSettings::getSetting('site_logo')['data'] ?? [];
$globalSettingsLogo = $logoData['setting_value'] ?? null;

if ($globalSettingsLogo && file_exists(__DIR__ . '/../' . ltrim($globalSettingsLogo, './'))) {
    $globalSettingsLogo = $path_prefix . ltrim($globalSettingsLogo, './');
} else {
    $globalSettingsLogo = $path_prefix . "public/img/logo-fmly.png";
}
?>
<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3">
    <div class="d-flex align-items-center">
        <button class="btn btn-link border d-lg-none text-dark me-3 fs-4" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#sidebar-wrapper" aria-controls="sidebar-wrapper">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="<?php echo $path_prefix; ?>coach/index.php" class="d-block d-lg-none">
            <img src="<?php echo htmlspecialchars($globalSettingsLogo); ?>" alt="logo" style="max-height: 50px;" /> </a>

        <h2 class="mb-0 fw-bold fs-3 fs-md-2 d-none d-lg-inline-block"><?php echo $page_title; ?></h2>
    </div>

    <div class="ms-auto rights-menus d-flex align-items-center gap-3 gap-md-4">
        <div class="dropdown">
            <div class="position-relative cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"
                    fill="rgba(75,75,75,1)">
                    <path
                        d="M20 17H22V19H2V17H4V10C4 5.58172 7.58172 2 12 2C16.4183 2 20 5.58172 20 10V17ZM18 17V10C18 6.68629 15.3137 4 12 4C8.68629 4 6 6.68629 6 10V17H18ZM9 21H15V23H9V21Z">
                    </path>
                </svg>
                <span
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    style="font-size: 0.6rem;">
                    0
                </span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0 mt-2" style="width: 320px;">
                <li class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Notifications</h6>
                </li>
                <li>
                    <div class="p-3 text-center text-muted small">
                        No new notifications
                    </div>
                </li>
            </ul>
        </div>

        <?php
        $avatar = "";
        if (!empty($_SESSION['user']['image'])) {
            $avatar = htmlspecialchars($_SESSION['user']['image']);
        } else {
            $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user']['name'] ?? 'Coach') . '&background=random';
        }
        ?>

        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo $avatar; ?>" class="rounded-circle me-2"
                    alt="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Coach'); ?>" width="32" height="32">
                <span class="fw-bold d-none d-sm-block"><?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Coach'); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3" style="min-width: 180px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="<?php echo $path_prefix; ?>coach/profile.php">
                        <i class="fa-regular fa-user me-3 text-primary fs-5"></i>
                        <span class="fw-medium">Profile</span>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider opacity-50">
                </li>
                <li>
                    <h6 class="dropdown-header px-3">Switch Account</h6>
                </li>
                <?php if (isset($_SESSION['accounts'])): ?>
                    <?php foreach ($_SESSION['accounts'] as $acc_id => $acc): ?>
                        <li>
                            <form method="POST" action="<?php echo $path_prefix; ?>helpers/switch-account.php" class="m-0">
                                <input type="hidden" name="target_user_id" value="<?php echo $acc_id; ?>">
                                <button type="submit" class="dropdown-item d-flex align-items-center py-2 rounded-2 <?php echo (isset($_SESSION['active_account_id']) && $acc_id == $_SESSION['active_account_id']) ? 'bg-light' : ''; ?>">
                                    <img src="<?php echo $acc['image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($acc['name']) . '&background=random'; ?>" class="rounded-circle me-2" width="20" height="20">
                                    <span class="fw-medium"><?php echo htmlspecialchars($acc['name']); ?></span>
                                    <?php if (isset($_SESSION['active_account_id']) && $acc_id == $_SESSION['active_account_id']): ?>
                                        <i class="fa-solid fa-check ms-auto text-success"></i>
                                    <?php endif; ?>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="<?php echo $path_prefix; ?>login.php?add_account=1">
                        <i class="fa-solid fa-plus me-3 text-primary fs-5"></i>
                        <span class="fw-medium">Add Account</span>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider opacity-50">
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2 text-danger" href="<?php echo $path_prefix; ?>logout.php">
                        <i class="fa-solid fa-arrow-right-from-bracket me-3 fs-5"></i>
                        <span class="fw-medium">Sign out</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2 text-danger" href="<?php echo $path_prefix; ?>logout.php?all=1">
                        <i class="fa-solid fa-power-off me-3 fs-5"></i>
                        <span class="fw-medium">Sign out All</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>