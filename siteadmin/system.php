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
                            <h4 class="fw-bold mb-0" id="db-load">--</h4>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small" id="db-connections">-- Active Connections</div>
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
                            <h4 class="fw-bold mb-0" id="email-queue">--</h4>
                        </div>
                    </div>
                    <div class="mt-2 text-muted small" id="email-queue-time">Last processed: --</div>
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
                                <span class="fw-medium">OS Version</span>
                                <span class="badge bg-success" id="server-os">--</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">PHP Version</span>
                                <span class="badge bg-warning" id="php-version">--</span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium" id="disk-space-label">Disk Space (-- / --)</span>
                                <span class="badge bg-info" id="disk-space-percent">--%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" id="disk-space-bar" style="width: 0%;"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">Uploads Folder Size</span>
                                <span class="badge bg-primary" id="uploads-size">-- MB</span>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-medium">Database Size</span>
                                <span class="badge bg-secondary" id="db-size">-- MB</span>
                            </div>
                            <div class="text-muted small mt-1">Version: <span id="db-version">--</span></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    function fetchSystemHealth() {
        fetch('../api/admin/system_health.php')
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    const data = res.data;
                    document.getElementById('db-load').textContent = data.db_connections > 0 ? "Normal" : "Low";
                    document.getElementById('db-connections').textContent = data.db_connections + " Active Connections";
                    document.getElementById('email-queue').textContent = data.email_queue;
                    document.getElementById('email-queue-time').textContent = "Real-time";
                    
                    document.getElementById('server-os').textContent = data.server_os;
                    document.getElementById('php-version').textContent = data.php_version;
                    
                    document.getElementById('disk-space-label').textContent = `Disk Space (${data.disk_used_gb}GB / ${data.disk_total_gb}GB)`;
                    document.getElementById('disk-space-percent').textContent = data.disk_used_percent + '%';
                    document.getElementById('disk-space-bar').style.width = data.disk_used_percent + '%';
                    
                    document.getElementById('uploads-size').textContent = data.uploads_size_mb + ' MB';
                    document.getElementById('db-size').textContent = data.db_size_mb + ' MB';
                    document.getElementById('db-version').textContent = data.db_version;
                }
            })
            .catch(error => console.error('Error fetching system health:', error));
    }

    // Fetch immediately on load
    fetchSystemHealth();

    // Attach to refresh button if needed
    const refreshBtn = document.querySelector('.btn-primary');
    if(refreshBtn) {
        refreshBtn.addEventListener('click', fetchSystemHealth);
    }
});
</script>
