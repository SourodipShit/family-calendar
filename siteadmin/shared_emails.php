<?php
$path_prefix = "../";
$page_title = "Manage Shared Emails";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/SharedEmails.php';
require_once $path_prefix . 'classes/Family.php';

$allFamilies = Family::getAllFamilies();
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Shared Emails Management</h4>
            <div>
                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadCsvModal"><i class="ri-file-upload-line me-1"></i> Upload CSV</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmailModal"><i class="ri-add-line me-1"></i> Add New Email</button>
            </div>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="emailSearch" class="form-control bg-light border-0" placeholder="Search emails...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="overflow: visible;">
                    <table id="emailsTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">ID</th>
                                <th class="border-0">Email Address</th>
                                <th class="border-0">Password</th>
                                <th class="border-0">Family ID</th>
                                <th class="border-0">Allocated At</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $emails = SharedEmails::getAll();
                            foreach ($emails as $email):
                            ?>
                                <tr>
                                    <td class="ps-4"><?php echo htmlspecialchars($email['id']); ?></td>
                                    <td><span class="fw-bold"><?php echo htmlspecialchars($email['email_address']); ?></span></td>
                                    <td><span class="text-muted"><?php echo htmlspecialchars($email['password']); ?></span></td>
                                    <td><?php echo $email['family_id'] ? htmlspecialchars($email['family_id']) : '<span class="badge bg-secondary">Unallocated</span>'; ?></td>
                                    <td><?php echo $email['allocated_at'] ? date('M d, Y H:i', strtotime($email['allocated_at'])) : '-'; ?></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-icon edit-btn" data-id="<?php echo $email['id']; ?>" data-email="<?php echo htmlspecialchars($email['email_address']); ?>" data-password="<?php echo htmlspecialchars($email['password']); ?>" data-family_id="<?php echo htmlspecialchars($email['family_id'] ?? ''); ?>"><i class="ri-edit-line"></i></button>
                                        <button class="btn btn-sm btn-icon text-danger delete-btn" data-id="<?php echo $email['id']; ?>"><i class="ri-delete-bin-line"></i></button>
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

<!-- Add Email Modal -->
<div class="modal fade" id="addEmailModal" tabindex="-1" aria-labelledby="addEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold" id="addEmailModalLabel">Add Shared Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addEmailForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="add_email_address" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="add_email_address" name="email_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_password" class="form-label fw-bold">Password</label>
                        <input type="text" class="form-control" id="add_password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Add Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Email Modal -->
<div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold" id="editEmailModalLabel">Edit Shared Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEmailForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_email_address" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="edit_email_address" name="email_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label fw-bold">Password</label>
                        <input type="text" class="form-control" id="edit_password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_family_id" class="form-label fw-bold">Allocate to Family</label>
                        <select class="form-select" id="edit_family_id" name="family_id">
                            <option value="">-- Unallocated --</option>
                            <?php foreach ($allFamilies as $fam): ?>
                                <option value="<?php echo $fam['id']; ?>"><?php echo htmlspecialchars($fam['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload CSV Modal -->
<div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold" id="uploadCsvModalLabel">Upload Shared Emails via CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadCsvForm" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="action" value="upload_csv">
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i> CSV should have 2 columns: <strong>Email Address</strong> and <strong>Password</strong>. The first row (headers) will be skipped.
                    </div>
                    <div class="mb-3">
                        <label for="csv_file" class="form-label fw-bold">CSV File</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Upload CSV</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>
<script src="<?php echo $path_prefix; ?>public/js/alert.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#edit_family_id').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editEmailModal'),
            placeholder: '-- Unallocated --',
            allowClear: true,
            width: '100%'
        });

        const table = $('#emailsTable').DataTable({
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
                "targets": 5
            }]
        });

        $('#emailSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Add form submission
        $('#addEmailForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '../api/admin/shared_emails.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#addEmailModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('An error occurred. Please try again.', 'error');
                }
            });
        });

        // Edit button click
        $('.edit-btn').on('click', function() {
            const id = $(this).data('id');
            const email = $(this).data('email');
            const password = $(this).data('password');
            const family_id = $(this).data('family_id');

            $('#edit_id').val(id);
            $('#edit_email_address').val(email);
            $('#edit_password').val(password);
            $('#edit_family_id').val(family_id).trigger('change');

            $('#editEmailModal').modal('show');
        });

        // Edit form submission
        $('#editEmailForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '../api/admin/shared_emails.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#editEmailModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('An error occurred. Please try again.', 'error');
                }
            });
        });

        // Delete button click
        $('.delete-btn').on('click', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this shared email?')) {
                $.ajax({
                    url: '../api/admin/shared_emails.php',
                    type: 'POST',
                    data: { action: 'delete', id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            showAlert(response.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showAlert(response.message, 'error');
                        }
                    },
                    error: function() {
                        showAlert('An error occurred. Please try again.', 'error');
                    }
                });
            }
        });

        // Upload CSV form submission
        $('#uploadCsvForm').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: '../api/admin/shared_emails.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#uploadCsvModal').modal('hide');
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('An error occurred during upload. Please try again.', 'error');
                }
            });
        });
    });
</script>
