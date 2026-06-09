<?php
$path_prefix = isset($path_prefix) ? $path_prefix : "../";
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-3">
    <div class="container-fluid p-0">
        <button class="btn btn-light d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar-wrapper">
            <i class="ri-menu-2-line"></i>
        </button>

        <div class="d-none d-md-flex align-items-center">
            <h5 class="mb-0 fw-bold"><?php echo isset($page_title) ? $page_title : "Dashboard"; ?></h5>
        </div>

        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="ri-notification-3-line"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                        3
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0" style="width: 300px;">
                    <div class="px-3 py-2 border-bottom">
                        <h6 class="mb-0 fw-bold">Notifications</h6>
                    </div>
                    <a class="dropdown-item py-2 border-bottom" href="#">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 me-3">
                                <i class="ri-user-add-line"></i>
                            </div>
                            <div>
                                <p class="mb-0 small fw-bold">New user registered</p>
                                <small class="text-muted">2 minutes ago</small>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item py-2 text-center text-primary small" href="#">View all notifications</a>
                </div>
            </div>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark nav-link-profile" data-bs-toggle="dropdown">
                    <div class="bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: 600;">
                        SA
                    </div>
                    <div class="d-none d-sm-block">
                        <span class="fw-bold d-block small"><?php echo $_SESSION['user']['first_name'] ?? 'Admin'; ?></span>
                        <small class="text-muted" style="font-size: 0.7rem;">Site Administrator</small>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item" href="#"><i class="ri-user-line me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="ri-settings-line me-2"></i>Settings</a></li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <h6 class="dropdown-header">Switch Account</h6>
                    </li>
                    <?php if (isset($_SESSION['accounts'])): ?>
                        <?php foreach ($_SESSION['accounts'] as $acc_id => $acc): ?>
                            <li>
                                <form method="POST" action="<?php echo $path_prefix; ?>helpers/switch-account.php" class="m-0">
                                    <input type="hidden" name="target_user_id" value="<?php echo $acc_id; ?>">
                                    <button type="submit" class="dropdown-item <?php echo ($acc_id == $_SESSION['active_account_id']) ? 'bg-light' : ''; ?>">
                                        <img src="<?php echo $acc['image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($acc['name']) . '&background=random'; ?>" class="rounded-circle me-2" width="20" height="20"><?php echo htmlspecialchars($acc['name']); ?>
                                        <?php if ($acc_id == $_SESSION['active_account_id']): ?>
                                            <i class="ri-check-line ms-auto text-success float-end"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?php echo $path_prefix; ?>login.php?add_account=1"><i class="ri-user-add-line me-2"></i>Add Account</a></li>

                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="<?php echo $path_prefix; ?>logout.php"><i class="ri-logout-box-r-line me-2"></i>Logout</a></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo $path_prefix; ?>logout.php?all=1"><i class="ri-shut-down-line me-2"></i>Logout All</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>