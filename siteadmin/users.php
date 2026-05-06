<?php
$path_prefix = "../";
$page_title = "Manage Users";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/User.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">User Management</h4>
            <a href="add-user.php" class="btn btn-primary"><i class="ri-user-add-line me-1"></i> Add New User</a>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="userSearch" class="form-control bg-light border-0" placeholder="Search users by name or email...">
                        </div>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <select class="form-select form-select-sm d-inline-block w-auto bg-light border-0 ms-2">
                            <option>All Roles</option>
                            <option>Admin</option>
                            <option>User</option>
                        </select>
                        <select class="form-select form-select-sm d-inline-block w-auto bg-light border-0 ms-2">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Pending</option>
                            <option>Suspended</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">User Info</th>
                                <th class="border-0">Phone</th>
                                <th class="border-0">Family</th>
                                <th class="border-0">Role</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $users = User::getAllUsersWithFamily();

                            foreach ($users as $user):
                                // Generate initials
                                $words = explode(" ", $user['name']);
                                $initials = "";
                                foreach ($words as $w) {
                                    $initials .= mb_substr($w, 0, 1);
                                }
                                $initials = strtoupper(mb_substr($initials, 0, 2));

                                // Determine color based on ID
                                $colors = ['primary', 'info', 'success', 'warning', 'danger'];
                                $color = $colors[$user['id'] % count($colors)];
                            ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            $user_image = !empty($user['image']) ? $user['image'] : '';
                                            // Clean the path to be root-relative
                                            $clean_image_path = preg_replace('/^(\.\.\/)+/', '', $user_image);
                                            $full_image_path = !empty($clean_image_path) ? $path_prefix . $clean_image_path : '';
                                            
                                            if (!empty($full_image_path) && file_exists($full_image_path)): 
                                            ?>
                                                <img src="<?php echo $full_image_path; ?>" class="rounded-circle me-3" width="40" height="40" style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-<?php echo $color; ?>-subtle text-<?php echo $color; ?> rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                    <?php echo $initials; ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <span class="fw-bold d-block"><?php echo htmlspecialchars($user['name']); ?></span>
                                                <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small"><?php echo htmlspecialchars($user['phone']); ?></span></td>
                                    <td><?php echo htmlspecialchars($user['family_name'] ?? 'No Family'); ?></td>
                                    <td><span class="badge bg-light text-dark border"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span></td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="ri-more-2-fill"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                <li><a class="dropdown-item" href="edit-user.php?id=<?php echo $user['id']; ?>"><i class="ri-edit-line me-2"></i>Edit User</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="ri-key-2-line me-2"></i>Reset Password</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="ri-shield-user-line me-2"></i>Change Role</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="../helpers/admin/deleteUser.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($user['name']); ?>?')"><i class="ri-delete-bin-line me-2"></i>Delete User</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
    $(document).ready(function() {
        const table = $('#usersTable').DataTable({
            "dom": '<"d-none"f>rt<"d-flex justify-content-between align-items-center p-3"ip>',
            "pageLength": 10,
            "language": {
                "search": "",
                "paginate": {
                    "next": '<i class="ri-arrow-right-s-line"></i>',
                    "previous": '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            "columnDefs": [{
                    "orderable": false,
                    "targets": 4
                } // Disable ordering on Actions column
            ]
        });

        // Custom search logic
        $('#userSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Role filter
        $('select:eq(0)').on('change', function() {
            const val = $(this).val();
            table.column(3).search(val === 'All Roles' ? '' : val).draw();
        });

        // Remove status filter as it doesn't exist
        $('select:eq(1)').hide();
    });
</script>