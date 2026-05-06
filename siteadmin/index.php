<?php
$path_prefix = "../";
$page_title = "Admin Dashboard";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <!-- Stats Overview -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary-subtle text-primary me-3">
                            <i class="ri-community-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total Families</h6>
                            <h3 class="fw-bold mb-0">124</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-success small fw-bold"><i class="ri-arrow-up-line"></i> 12%</span>
                        <span class="text-muted small ms-1">from last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success-subtle text-success me-3">
                            <i class="ri-user-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Active Users</h6>
                            <h3 class="fw-bold mb-0">842</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-success small fw-bold"><i class="ri-arrow-up-line"></i> 5%</span>
                        <span class="text-muted small ms-1">from last month</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning-subtle text-warning me-3">
                            <i class="ri-calendar-event-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Events Today</h6>
                            <h3 class="fw-bold mb-0">45</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-danger small fw-bold"><i class="ri-arrow-down-line"></i> 2%</span>
                        <span class="text-muted small ms-1">from yesterday</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card p-3 border-0">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info-subtle text-info me-3">
                            <i class="ri-database-2-line"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">DB Storage</h6>
                            <h3 class="fw-bold mb-0">1.2GB</h3>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-muted small fw-bold">85% Capacity</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Activity Table -->
            <div class="col-lg-8">
                <div class="card admin-card border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">Recent Registrations</h6>
                        <a href="users.php" class="btn btn-sm btn-light">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 border-0">User</th>
                                        <th class="border-0">Family</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Joined</th>
                                        <th class="text-end pe-4 border-0">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">JD</div>
                                                <div>
                                                    <span class="fw-bold d-block">John Doe</span>
                                                    <small class="text-muted">john@example.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>The Johnsons</td>
                                        <td><span class="badge bg-success-subtle text-success px-2 py-1">Active</span></td>
                                        <td>May 10, 2024</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-icon"><i class="ri-more-2-fill"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-info text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">AS</div>
                                                <div>
                                                    <span class="fw-bold d-block">Alice Smith</span>
                                                    <small class="text-muted">alice@example.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Smith Household</td>
                                        <td><span class="badge bg-warning-subtle text-warning px-2 py-1">Pending</span></td>
                                        <td>May 11, 2024</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-icon"><i class="ri-more-2-fill"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">RW</div>
                                                <div>
                                                    <span class="fw-bold d-block">Robert White</span>
                                                    <small class="text-muted">robert@example.com</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>White Family</td>
                                        <td><span class="badge bg-success-subtle text-success px-2 py-1">Active</span></td>
                                        <td>May 11, 2024</td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-icon"><i class="ri-more-2-fill"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions / System Health -->
            <div class="col-lg-4">
                <div class="card admin-card border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0">System Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small">API Response Time</span>
                            <span class="badge bg-success">124ms</span>
                        </div>
                        <div class="progress mb-4" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 90%;"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small">Database Load</span>
                            <span class="badge bg-warning">42%</span>
                        </div>
                        <div class="progress mb-4" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 42%;"></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small">Email Queue</span>
                            <span class="badge bg-info">0 pending</span>
                        </div>
                        <div class="progress mb-0" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>
