<?php
require_once __DIR__ . '/../config/Database.php';

$path_prefix = isset($path_prefix) ? $path_prefix : "";
$page_title = isset($page_title) ? $page_title : "Family Calendar";
$logoData = GlobalSettings::getSetting('site_logo')['data'] ?? [];
$globalSettingsLogo = $logoData['setting_value'] ?? null;

if ($globalSettingsLogo && file_exists($globalSettingsLogo)) {
    // Keep as is
} else {
    $globalSettingsLogo = $path_prefix . "public/img/logo-fmly.png";
}

$userFamilies = [];
if (isset($_SESSION['user']['id'])) {
    $userFamilies = Database::runPrepared(
        "SELECT f.id, f.name FROM families f 
         JOIN user_family uf ON f.id = uf.family_id 
         WHERE uf.user_id = ?",
        [$_SESSION['user']['id']]
    )->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3">
    <div class="d-flex align-items-center">
        <button class="btn btn-link border d-lg-none text-dark me-3 fs-4" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#sidebar-wrapper" aria-controls="sidebar-wrapper">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="<?php echo $path_prefix; ?>users/index.php" class="d-block d-lg-none">
            <img src="<?php echo $globalSettingsLogo; ?>" alt="logo" style="max-height: 50px;" /> </a>

        <h2 class="mb-0 fw-bold fs-3 fs-md-2 d-none d-lg-inline-block"><?php echo $page_title; ?></h2>
    </div>

    <div class="ms-auto rights-menus d-flex align-items-center gap-3 gap-md-4">
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
                    3
                </span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0 mt-2" style="width: 320px;">
                <li class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Notifications</h6>
                    <a href="#" class="text-primary text-decoration-none fs-8">Mark all as read</a>
                </li>
                <li>
                    <a class="dropdown-item p-3 border-bottom d-flex align-items-start" href="#">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3 d-flex justify-content-center align-items-center"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-7">Dentist Appointment</div>
                            <div class="text-muted fs-8">Tomorrow at 10:00 AM</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item p-3 border-bottom d-flex align-items-start" href="#">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2 me-3 d-flex justify-content-center align-items-center"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-broom"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-7">Chore Reminder</div>
                            <div class="text-muted fs-8">Liam hasn't taken out the trash</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item p-3 d-flex align-items-start" href="#">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3 d-flex justify-content-center align-items-center"
                            style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-7">Dinner Ready</div>
                            <div class="text-muted fs-8">Spaghetti is ready in 10 mins</div>
                        </div>
                    </a>
                </li>
                <li class="p-2 border-top text-center">
                    <a href="#" class="text-primary text-decoration-none fs-8 fw-medium">View all
                        notifications</a>
                </li>
            </ul>
        </div>

        <?php
        $avatar = $_SESSION['user']['image'] ?? 'https://ui-avatars.com/api/?name=' . $_SESSION['user']['name'] . '&background=random';
        ?>

        <div class="dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo $avatar; ?>" class="rounded-circle me-2"
                    alt="<?php echo $_SESSION['user']['name']; ?>" width="32" height="32">
                <span class="fw-bold d-none d-sm-block"><?php echo $_SESSION['user']['name']; ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3" style="min-width: 180px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2" href="#">
                        <i class="fa-regular fa-user me-3 text-primary fs-5"></i>
                        <span class="fw-medium">Profile</span>
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider opacity-50">
                </li>
                <li><h6 class="dropdown-header px-3">Switch Account</h6></li>
                <?php if (isset($_SESSION['accounts'])): ?>
                    <?php foreach ($_SESSION['accounts'] as $acc_id => $acc): ?>
                        <li>
                            <form method="POST" action="<?php echo $path_prefix; ?>helpers/switch-account.php" class="m-0">
                                <input type="hidden" name="target_user_id" value="<?php echo $acc_id; ?>">
                                <button type="submit" class="dropdown-item d-flex align-items-center py-2 rounded-2 <?php echo ($acc_id == $_SESSION['active_account_id']) ? 'bg-light' : ''; ?>">
                                    <img src="<?php echo $acc['image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($acc['name']) . '&background=random'; ?>" class="rounded-circle me-2" width="20" height="20">
                                    <span class="fw-medium"><?php echo htmlspecialchars($acc['name']); ?></span>
                                    <?php if ($acc_id == $_SESSION['active_account_id']): ?>
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
                <?php if (count($userFamilies) > 1): ?>
                <li>
                    <hr class="dropdown-divider opacity-50">
                </li>
                <li><h6 class="dropdown-header px-3">Switch Family</h6></li>
                <?php foreach ($userFamilies as $family): ?>
                    <li>
                        <form method="POST" action="<?php echo $path_prefix; ?>helpers/switch-family.php" class="m-0">
                            <input type="hidden" name="target_family_id" value="<?php echo $family['id']; ?>">
                            <button type="submit" class="dropdown-item d-flex align-items-center py-2 rounded-2 <?php echo ($family['id'] == $_SESSION['user']['active_family_id']) ? 'bg-light' : ''; ?>">
                                <i class="fa-solid fa-users me-2 text-secondary fs-6"></i>
                                <span class="fw-medium"><?php echo htmlspecialchars($family['name']); ?></span>
                                <?php if ($family['id'] == $_SESSION['user']['active_family_id']): ?>
                                    <i class="fa-solid fa-check ms-auto text-success"></i>
                                <?php endif; ?>
                            </button>
                        </form>
                    </li>
                <?php endforeach; ?>
                <?php endif; ?>
                <li>
                    <hr class="dropdown-divider opacity-50">
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2 text-danger" href="<?php echo $path_prefix; ?>logout.php">
                        <i class="fa-solid fa-arrow-right-from-bracket me-3 fs-5"></i>
                        <span class="fw-medium">Logout</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2 rounded-2 text-danger" href="<?php echo $path_prefix; ?>logout.php?all=1">
                        <i class="fa-solid fa-power-off me-3 fs-5"></i>
                        <span class="fw-medium">Logout All</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>