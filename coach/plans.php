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
            $header = fgetcsv($handle);
            if ($header) {
                // Clean headers (trim whitespace)
                $header = array_map('trim', $header);

                $plansToInsert = [];
                while (($row = fgetcsv($handle)) !== false) {
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
                                    <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($plan['name'] ?? $plan['title'] ?? 'Untitled Plan'); ?></h5>
                                    <?php if (isset($plan['price']) && $plan['price'] !== ''): ?>
                                        <span class="badge bg-success-subtle text-success fs-6 fw-bold px-3 py-2 rounded-pill">
                                            $<?php echo htmlspecialchars($plan['price']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($plan['description'])): ?>
                                    <p class="text-secondary small mb-4 flex-grow-1">
                                        <?php echo nl2br(htmlspecialchars(substr($plan['description'], 0, 150) . (strlen($plan['description']) > 150 ? '...' : ''))); ?>
                                    </p>
                                <?php endif; ?>

                                <div class="mt-auto pt-3 border-top border-light">
                                    <ul class="list-unstyled mb-0 small text-muted">
                                        <?php
                                        $skipKeys = ['id', 'coach_id', 'name', 'title', 'description', 'price', 'created_at', 'updated_at'];
                                        foreach ($plan as $key => $value):
                                            if (!in_array($key, $skipKeys) && !empty($value)):
                                        ?>
                                                <li class="mb-2 d-flex align-items-center">
                                                    <i class="ri-arrow-right-s-line text-primary me-2"></i>
                                                    <strong class="text-capitalize me-1"><?php echo str_replace('_', ' ', $key); ?>:</strong>
                                                    <span class="text-dark"><?php echo htmlspecialchars($value); ?></span>
                                                </li>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 p-4 pt-0 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary flex-grow-1">Edit</button>
                                <button class="btn btn-sm btn-outline-danger"><i class="ri-delete-bin-line"></i></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>