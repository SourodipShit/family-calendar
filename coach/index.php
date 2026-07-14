<?php
$path_prefix = "../";
$page_title = "Coach Dashboard";
require_once $path_prefix . 'components/coach-header.php';
require_once $path_prefix . 'components/coach-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/coach-navbar.php'; ?>

    <div class="container-fluid p-4">
        <!-- Welcome Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 bg-primary text-white shadow-sm" style="border-radius: 16px; overflow: hidden; position: relative;">
                    <div class="card-body p-5 position-relative z-1">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="fw-bold mb-2">Welcome back, <?php echo htmlspecialchars(explode(' ', $_SESSION['user']['name'])[0]); ?>! 👋</h2>
                                <p class="lead mb-0 opacity-75">Here is what's happening with your clients and plans today.</p>
                            </div>
                            <div class="col-md-4 text-md-end mt-4 mt-md-0 d-none d-md-block">
                                <i class="ri-rocket-2-line" style="font-size: 5rem; opacity: 0.2;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card admin-card p-4 border-0 h-100 shadow-sm" style="border-radius: 16px;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-primary-subtle text-primary me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-group-line fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Active Clients</h6>
                            <h3 class="fw-bold mb-0">--</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card admin-card p-4 border-0 h-100 shadow-sm" style="border-radius: 16px;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-success-subtle text-success me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-list-check-3 fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Active Plans</h6>
                            <h3 class="fw-bold mb-0">--</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card admin-card p-4 border-0 h-100 shadow-sm" style="border-radius: 16px;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon bg-warning-subtle text-warning me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-wallet-3-line fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Earnings (Monthly)</h6>
                            <h3 class="fw-bold mb-0">$--</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card admin-card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body p-4 text-center text-muted">
                        <div class="py-5">
                            <i class="ri-ghost-line fs-1 mb-2 text-light"></i>
                            <p>No recent activity found.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card admin-card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-3">
                            <a href="#" class="btn btn-light text-start p-3 rounded text-primary fw-medium" style="background-color: var(--bs-primary-bg-subtle);">
                                <i class="ri-add-circle-line me-2"></i> Create New Plan
                            </a>
                            <a href="#" class="btn btn-light text-start p-3 rounded fw-medium text-secondary">
                                <i class="ri-user-add-line me-2"></i> Invite Client
                            </a>
                            <a href="<?php echo $path_prefix; ?>coach/profile.php" class="btn btn-light text-start p-3 rounded fw-medium text-secondary">
                                <i class="ri-settings-3-line me-2"></i> Update Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>
