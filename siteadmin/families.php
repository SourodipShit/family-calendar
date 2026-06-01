<?php
$path_prefix = "../";
$page_title = "Manage Families";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Family.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Family Management</h4>
            <a href="add-family.php" class="btn btn-primary"><i class="ri-add-line me-1"></i> Add New Family</a>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="familySearch" class="form-control bg-light border-0" placeholder="Search families...">
                        </div>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <button class="btn btn-light btn-sm"><i class="ri-filter-line me-1"></i> Filter</button>
                        <button class="btn btn-light btn-sm ms-2"><i class="ri-download-line me-1"></i> Export</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="familiesTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">Family Name</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Location</th>
                                <th class="border-0">Timezone</th>
                                <th class="border-0">Members</th>
                                <th class="border-0">Status</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $families = Family::getAllFamilies();

                            foreach ($families as $family):
                                $members_count = $family['member_count'];
                            ?>
                            <tr id="family-row-<?php echo $family['id']; ?>">
                                <td class="ps-4 fw-bold"><?php echo htmlspecialchars(!empty($family['name']) ? $family['name'] : 'N/A'); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars(!empty($family['email']) ? $family['email'] : 'N/A'); ?></td>
                                <td class="small"><?php echo htmlspecialchars(!empty($family['location']) ? $family['location'] : 'N/A'); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars(!empty($family['timezone']) ? $family['timezone'] : 'N/A'); ?></td>
                                <td>
                                    <div class="avatar-group d-flex">
                                        <?php for($i=0; $i<min($members_count, 3); $i++): ?>
                                            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; margin-left: -8px; font-size: 10px; z-index: <?php echo 10-$i; ?>;">
                                                <i class="ri-user-line"></i>
                                            </div>
                                        <?php endfor; ?>
                                        <?php if($members_count > 3): ?>
                                            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; margin-left: -8px; font-size: 10px; z-index: 1;">
                                                +<?php echo $members_count - 3; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($members_count == 0): ?>
                                            <small class="text-muted">No members</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $family['approved'] ? 'success' : 'danger'; ?>"><?php echo htmlspecialchars($family['approved'] ? 'Approved' : 'Pending'); ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="ri-more-2-fill"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="edit-family.php?id=<?php echo $family['id']; ?>"><i class="ri-edit-line me-2"></i>Edit Family</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="ri-group-line me-2"></i>Manage Members</a></li>
                                            <li>
                                                <a class="dropdown-item <?php echo !empty($family['approved']) ? 'disabled' : ''; ?>" href="#" onclick="approveFamily(<?php echo $family['id']; ?>); return false;" <?php echo !empty($family['approved']) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                                    <i class="ri-check-line me-2"></i>
                                                    <?php echo !empty($family['approved']) ? 'Approved' : 'Approve'; ?>
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="../helpers/admin/deleteFamily.php?id=<?php echo $family['id']; ?>" onclick="return confirm('Are you sure you want to delete the family \'<?php echo htmlspecialchars(!empty($family['name']) ? $family['name'] : 'N/A'); ?>\'? This will also remove all associated members.')"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
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
    const table = $('#familiesTable').DataTable({
        "dom": '<"d-none"f>rt<"d-flex justify-content-between align-items-center p-3"ip>',
        "pageLength": 10,
        "language": {
            "search": "",
            "paginate": {
                "next": '<i class="ri-arrow-right-s-line"></i>',
                "previous": '<i class="ri-arrow-left-s-line"></i>'
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": 5 } // Disable ordering on Actions column
        ]
    });

    // Custom search logic
    $('#familySearch').on('keyup', function() {
        table.search(this.value).draw();
    });

});

function approveFamily(id) {
    if(confirm('Are you sure you want to approve this family?')) {
        fetch(`../api/admin/family.php?action=approve&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if(typeof showAlert === 'function') {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                if(typeof showAlert === 'function') {
                    showAlert(data.message, 'error');
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while approving the family.');
        });
    }
}
</script>
