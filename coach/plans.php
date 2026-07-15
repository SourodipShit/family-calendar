<?php
$path_prefix = "../";
$page_title = "My Plans";
require_once $path_prefix . 'components/coach-header.php';
require_once $path_prefix . 'components/coach-sidebar.php';
require_once $path_prefix . 'classes/Coach.php';
require_once $path_prefix . 'config/Database.php';

$userId = $_SESSION['user']['id'];
$successMsg = '';
$errorMsg = '';

// Handle CSV upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['plans_csv'])) {
    $file = $_FILES['plans_csv'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmpName = $file['tmp_name'];
        if (($handle = fopen($tmpName, 'r')) !== false) {
            // Read headers
            $header = fgetcsv($handle, 0, ',', '"', '\\');
            if ($header) {
                // Clean headers (trim whitespace)
                $header = array_map('trim', $header);

                $plansToInsert = [];
                while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                    if (count($header) === count($row)) {
                        $plan = array_combine($header, $row);
                        // Make sure we have some data
                        if (array_filter($plan)) {
                            $plansToInsert[] = $plan;
                        }
                    }
                }

                if (!empty($plansToInsert)) {
                    try {
                        Database::getInstance()->beginTransaction();
                        Coach::addPlans($userId, $plansToInsert);
                        Database::getInstance()->commit();
                        $successMsg = "Successfully uploaded " . count($plansToInsert) . " plans!";
                    } catch (Exception $e) {
                        Database::getInstance()->rollBack();
                        $errorMsg = "Error uploading plans: " . $e->getMessage() . ". Please check your CSV columns match the database schema.";
                    }
                } else {
                    $errorMsg = "No valid data found in the CSV.";
                }
            } else {
                $errorMsg = "CSV is empty or missing headers.";
            }
            fclose($handle);
        } else {
            $errorMsg = "Failed to open the uploaded file.";
        }
    } else {
        $errorMsg = "Error uploading the file.";
    }
}

// Fetch existing plans
$plans = [];
try {
    $stmt = Database::runPrepared("SELECT * FROM coach_plans WHERE coach_id = ? ORDER BY id DESC", [$userId]);
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If table doesn't exist or query fails, just keep $plans empty
    $errorMsg = "Could not fetch plans: " . $e->getMessage();
}
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/coach-navbar.php'; ?>

    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1">My Plans</h2>
                <p class="text-muted mb-0">Manage your coaching plans and offerings.</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <button type="button" class="btn btn-outline-primary shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#addPlanModal">
                    <i class="ri-add-line me-1"></i> Add Plan
                </button>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="collapse" data-bs-target="#uploadCsvCard">
                    <i class="ri-file-excel-line me-2"></i> Upload CSV
                </button>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($successMsg): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="ri-checkbox-circle-fill me-2"></i> <?php echo htmlspecialchars($successMsg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="ri-error-warning-fill me-2"></i> <?php echo htmlspecialchars($errorMsg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Upload CSV Section -->
        <div class="collapse mb-4" id="uploadCsvCard">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="ri-upload-cloud-2-line text-primary me-2"></i> Bulk Upload Plans</h5>
                    <p class="text-muted small mb-4">
                        Upload a CSV file containing your plans. Ensure the header row exactly matches the database column names
                        (e.g., <code>name, description, price, duration_days</code>).
                    </p>

                    <form method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-3">
                        <div class="flex-grow-1" style="max-width: 400px;">
                            <input type="file" name="plans_csv" accept=".csv" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success fw-bold px-4">
                            Upload & Import
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Plans Grid -->
        <div class="row g-4 mt-2">
            <?php if (empty($plans) && empty($errorMsg)): ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted">
                        <i class="ri-file-list-3-line mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5>No Plans Found</h5>
                        <p>You haven't uploaded any plans yet.</p>
                        <button class="btn btn-outline-primary mt-2" data-bs-toggle="collapse" data-bs-target="#uploadCsvCard">
                            Upload CSV Now
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($plans as $plan): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm hover-lift" style="border-radius: 16px; transition: transform 0.2s;">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($plan['duration_days']); ?> Days Plan</h5>
                                    <?php if (isset($plan['price']) && $plan['price'] !== ''): ?>
                                        <span class="badge bg-success-subtle text-success fs-6 fw-bold px-3 py-2 rounded-pill">
                                            $<?php echo htmlspecialchars($plan['price']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-4 pt-0 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#editPlanModal"
                                    data-id="<?php echo $plan['id']; ?>"
                                    data-price="<?php echo htmlspecialchars($plan['price'] ?? ''); ?>"
                                    data-duration="<?php echo htmlspecialchars($plan['duration_days'] ?? ''); ?>">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePlan(<?php echo $plan['id']; ?>)"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addPlanModalLabel">Add Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPlanForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_plan">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Duration (Days)</label>
                        <input type="number" class="form-control" name="duration_days" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveAddPlanBtn">Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editPlanModalLabel">Edit Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPlanForm">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_plan">
                    <input type="hidden" name="plan_id" id="editPlanId">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Duration (Days)</label>
                        <input type="number" class="form-control" name="duration_days" id="editPlanDuration" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Price</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="editPlanPrice">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveEditPlanBtn">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo $path_prefix; ?>public/js/alert.js"></script>
<script>
    // Handle Add Plan Form Submission
    const addPlanForm = document.getElementById('addPlanForm');
    if (addPlanForm) {
        addPlanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveAddPlanBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
            btn.disabled = true;

            fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.href = window.location.href, 1500);
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showAlert('An unexpected error occurred.', 'error');
            });
        });
    }

    // Populate Edit Plan Modal
    const editPlanModal = document.getElementById('editPlanModal');
    if (editPlanModal) {
        editPlanModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            document.getElementById('editPlanId').value = button.getAttribute('data-id');
            document.getElementById('editPlanPrice').value = button.getAttribute('data-price');
            document.getElementById('editPlanDuration').value = button.getAttribute('data-duration');
        });
    }

    // Handle Edit Plan Form Submission
    const editPlanForm = document.getElementById('editPlanForm');
    if (editPlanForm) {
        editPlanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveEditPlanBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            btn.disabled = true;

            fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.href = window.location.href, 1500);
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showAlert('An unexpected error occurred.', 'error');
            });
        });
    }

    // Handle Delete Plan
    function deletePlan(planId) {
        if (confirm("Are you sure you want to delete this plan?")) {
            const formData = new FormData();
            formData.append('action', 'delete_plan');
            formData.append('plan_id', planId);

            fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.href = window.location.href, 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An unexpected error occurred.', 'error');
            });
        }
    }
</script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>