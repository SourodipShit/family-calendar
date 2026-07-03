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
                            <h3 class="fw-bold mb-0" id="total-families-count">--</h3>
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
                            <h3 class="fw-bold mb-0" id="active-users-count">--</h3>
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
                            <h3 class="fw-bold mb-0" id="events-today-count">--</h3>
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
                            <h3 class="fw-bold mb-0" id="db-storage-count">--</h3>
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
                                <tbody id="recent-signups-tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Logins Graph -->
            <div class="col-lg-4">
                <div class="card admin-card border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0">Daily Logins</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="loginChart" style="width: 100%; height: 250px;"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('../api/admin/stats.php')
        .then(response => response.json())
        .then(res => {
            if (res.status === 'success' && res.data) {
                const data = res.data;
                document.getElementById('total-families-count').textContent = data.total_families;
                document.getElementById('active-users-count').textContent = data.total_users;
                document.getElementById('events-today-count').textContent = data.events_today;
                document.getElementById('db-storage-count').textContent = data.db_space_mb + 'MB';

                // Populate recent signups
                if (data.recent_signups) {
                    const tbody = document.getElementById('recent-signups-tbody');
                    tbody.innerHTML = '';
                    data.recent_signups.forEach(user => {
                        const dateStr = new Date(user.created_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'});
                        const initials = user.name ? user.name.substring(0, 2).toUpperCase() : 'U';
                        const familyName = user.family_name || 'No Family';
                        const statusBadge = user.family_approved == 1 ? '<span class="badge bg-success-subtle text-success px-2 py-1">Active</span>' : '<span class="badge bg-warning-subtle text-warning px-2 py-1">Pending</span>';

                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">${initials}</div>
                                    <div>
                                        <span class="fw-bold d-block">${user.name}</span>
                                        <small class="text-muted">${user.email}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${familyName}</td>
                            <td>${statusBadge}</td>
                            <td>${dateStr}</td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-icon"><i class="ri-more-2-fill"></i></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }

                // Chart setup
                if (data.login_graph && data.login_graph.length > 0) {
                    const ctx = document.getElementById('loginChart').getContext('2d');
                    const labels = data.login_graph.map(row => row.date);
                    const counts = data.login_graph.map(row => row.count);
                    
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Logins',
                                data: counts,
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } }
                            }
                        }
                    });
                }
            }
        })
        .catch(error => console.error('Error fetching admin stats:', error));
});
</script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>