<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
$page_title = isset($page_title) ? $page_title : "Family Calendar";
$logoData = GlobalSettings::getSetting('site_logo')['data'] ?? [];
$globalSettingsLogo = $logoData['setting_value'] ?? null;

if ($globalSettingsLogo && file_exists($globalSettingsLogo)) {
    // Keep as is
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
        <a href="<?php echo $path_prefix; ?>family/index.php" class="d-block d-lg-none">
            <img src="<?php echo $globalSettingsLogo; ?>" alt="logo" style="max-height: 50px;" /> </a>

        <h2 class="mb-0 fw-bold fs-3 fs-md-2 d-none d-lg-inline-block"><?php echo $page_title; ?></h2>
    </div>

    <div class="ms-auto rights-menus d-flex align-items-center gap-3 gap-md-4">
        <!-- Weather Block -->
        <div class="d-flex align-items-center text-muted d-none d-md-flex">
            <span class="location-icons">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"
                    fill="rgba(76,76,76,1)">
                    <path
                        d="M12 20.8995L16.9497 15.9497C19.6834 13.2161 19.6834 8.78392 16.9497 6.05025C14.2161 3.31658 9.78392 3.31658 7.05025 6.05025C4.31658 8.78392 4.31658 13.2161 7.05025 15.9497L12 20.8995ZM12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.15076 5.63604 4.63604C9.15076 1.12132 14.8492 1.12132 18.364 4.63604C21.8787 8.15076 21.8787 13.8492 18.364 17.364L12 23.7279ZM12 13C13.1046 13 14 12.1046 14 11C14 9.89543 13.1046 9 12 9C10.8954 9 10 9.89543 10 11C10 12.1046 10.8954 13 12 13ZM12 15C9.79086 15 8 13.2091 8 11C8 8.79086 9.79086 7 12 7C14.2091 7 16 8.79086 16 11C16 13.2091 14.2091 15 12 15Z">
                    </path>
                </svg>
            </span> <span id="navbar-weather-location">Loading...</span>
        </div>
        <div class="d-flex align-items-center fw-bold d-none d-md-flex" id="navbar-weather-container">
            <span id="navbar-weather-icon" class="me-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" height="26" fill="rgba(254,186,9,1)">
                    <path d="M12 18C8.68629 18 6 15.3137 6 12C6 8.68629 8.68629 6 12 6C15.3137 6 18 8.68629 18 12C18 15.3137 15.3137 18 12 18ZM11 1H13V4H11V1ZM11 20H13V23H11V20ZM3.51472 4.92893L4.92893 3.51472L7.05025 5.63604L5.63604 7.05025L3.51472 4.92893ZM16.9497 18.364L18.364 16.9497L20.4853 19.0711L19.0711 20.4853L16.9497 18.364ZM19.0711 3.51472L20.4853 4.92893L18.364 7.05025L16.9497 5.63604L19.0711 3.51472ZM5.63604 16.9497L7.05025 18.364L4.92893 20.4853L3.51472 19.0711L5.63604 16.9497ZM23 11V13H20V11H23ZM4 11V13H1V11H4Z"></path>
                </svg>
            </span>
            <span id="navbar-weather-temp">--°C</span>
        </div>

        <?php if (isset($_SESSION['user']) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] !== 'family_view'): ?>
            <a href="<?php echo $path_prefix; ?>logout.php?redirect=family" class="btn btn-outline-danger fw-bold rounded-pill px-4">
                <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
            </a>
        <?php else: ?>
            <a href="<?php echo $path_prefix; ?>login.php" class="btn btn-outline-primary fw-bold rounded-pill px-4">
                <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Login
            </a>
        <?php endif; ?>
    </div>
</nav>
