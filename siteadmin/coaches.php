<?php
$path_prefix = "../";
$page_title = "Manage Coaches";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Coach.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Coach Management</h4>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="coachSearch" class="form-control bg-light border-0" placeholder="Search coaches...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="overflow: visible;">
                    <table id="coachesTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">Coach Name</th>
                                <th class="border-0">Email</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Status</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $coachesResponse = Coach::getAll();
                            $coaches = [];
                            if ($coachesResponse['status'] === 'success' && isset($coachesResponse['data'])) {
                                $coaches = $coachesResponse['data'];
                            }

                            foreach ($coaches as $coach):
                                $statusBadgeClass = 'secondary';
                                if ($coach['approval_status'] === 'approved') {
                                    $statusBadgeClass = 'success';
                                } elseif ($coach['approval_status'] === 'rejected') {
                                    $statusBadgeClass = 'danger';
                                } elseif ($coach['approval_status'] === 'pending') {
                                    $statusBadgeClass = 'warning';
                                }
                            ?>
                                <tr id="coach-row-<?php echo $coach['id']; ?>">
                                    <td class="ps-4 fw-bold">
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($coach['profile_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($coach['profile_image']); ?>" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <i class="ri-user-3-line text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($coach['user_name'] ?? 'Unknown'); ?>
                                        </div>
                                    </td>
                                    <td class="small"><?php echo htmlspecialchars($coach['email'] ?? ''); ?></td>
                                    <td class="small"><?php echo htmlspecialchars($coach['category_name'] ?? 'None'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $statusBadgeClass; ?>"><?php echo htmlspecialchars(ucfirst($coach['approval_status'])); ?></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown" data-bs-boundary="window"><i class="ri-more-2-fill"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                <li><a class="dropdown-item" href="#" onclick="viewCoachModal(<?php echo $coach['id']; ?>); return false;"><i class="ri-eye-line me-2"></i>View Profile</a></li>
                                                <?php if ($coach['approval_status'] !== 'approved'): ?>
                                                    <li>
                                                        <a class="dropdown-item text-success" href="#" onclick="updateCoachStatus(<?php echo $coach['id']; ?>, 'approve'); return false;">
                                                            <i class="ri-check-line me-2"></i>Approve
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($coach['approval_status'] !== 'rejected'): ?>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="updateCoachStatus(<?php echo $coach['id']; ?>, 'reject'); return false;">
                                                            <i class="ri-close-line me-2"></i>Reject
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
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

<!-- Coach Modal -->
<div class="modal fade" id="coachModal" tabindex="-1" aria-labelledby="coachModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="coachModalLabel">Coach Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="coachModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
    $(document).ready(function() {
        const table = $('#coachesTable').DataTable({
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
            }],
            "order": [
                [3, "asc"]
            ]
        });

        // Custom search logic
        $('#coachSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });

    function updateCoachStatus(id, action) {
        let actionText = action === 'approve' ? 'approve' : 'reject';
        if (confirm(`Are you sure you want to ${actionText} this coach?`)) {
            fetch(`../api/admin/coaches.php?action=${action}&id=${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (typeof showAlert === 'function') {
                            showAlert(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            alert(data.message);
                            window.location.reload();
                        }
                    } else {
                        if (typeof showAlert === 'function') {
                            showAlert(data.message, 'error');
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert(`An error occurred while trying to ${actionText} the coach.`);
                });
        }
    }

    function viewCoachModal(id) {
        const modalBody = document.getElementById('coachModalBody');
        modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

        const myModal = new bootstrap.Modal(document.getElementById('coachModal'));
        myModal.show();

        fetch(`../api/admin/coaches.php?action=get&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    modalBody.innerHTML = data.html;
                } else {
                    modalBody.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                modalBody.innerHTML = `<div class="alert alert-danger">Error loading coach details.</div>`;
            });
    }
</script>