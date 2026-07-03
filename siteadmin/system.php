<?php
$path_prefix = "../";
$page_title = "System Health Detailed";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 fw-bold">System Health & Metrics</h4>
            <button class="btn btn-primary btn-sm"><i class="ri-refresh-line me-1"></i> Refresh Data</button>
        </div>

        <!-- High-Level Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="ri-server-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">API Status</h6>
                            <h4 class="fw-bold mb-0 text-success">Online</h4>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Uptime: 45 days, 12 hrs</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon bg-info-subtle text-info me-3">
                            <i class="ri-database-2-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">DB Load</h6>
                            <h4 class="fw-bold mb-0">34%</h4>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">124 Active Connections</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="ri-timer-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Avg Response</h6>
                            <h4 class="fw-bold mb-0">112ms</h4>
                        </div>
                    </div>
                    <div class="mt-2 text-success small"><i class="ri-arrow-down-line"></i> 5ms from yesterday</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="ri-mail-send-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Email Queue</h6>
                            <h4 class="fw-bold mb-0">0</h4>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small">Last processed: 2 mins ago</div>
                </div>
            </div>
        </div>

        <!-- Detailed Metrics -->
        <div class="row g-4 mb-4">
            <!-- Server Resources -->
            <div class="col-lg-12">
                <div class="card admin-card border-0 h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0">Server Resources</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">CPU Usage</span>
                                <span class="badge bg-success">18%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 18%;"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">RAM Usage (4GB / 8GB)</span>
                                <span class="badge bg-warning">50%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 50%;"></div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">Disk Space (42GB / 100GB)</span>
                                <span class="badge bg-info">42%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: 42%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>
