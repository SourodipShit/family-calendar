<?php
$path_prefix = "../";
$page_title = "My Families";
require_once $path_prefix . 'config/Database.php';
require_once $path_prefix . 'classes/Coach.php';

require_once $path_prefix . 'components/coach-header.php';
require_once $path_prefix . 'components/coach-sidebar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'coach') {
    die("Unauthorized.");
}

$coachId = $_SESSION['user']['id'];
$familiesRes = Coach::getCoachFamilies($coachId);
$families = $familiesRes['status'] === 'success' ? $familiesRes['data'] : [];
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/coach-navbar.php'; ?>

    <div class="container-fluid p-4">
        <h2 class="fw-bold mb-4">My Families</h2>



        <?php if (empty($families)): ?>
            <div class="card p-5 text-center text-muted border-0 bg-light shadow-sm" style="border-radius: 16px;">
                <i class="fa-solid fa-users-slash fa-3x mb-3 text-secondary"></i>
                <h5>No families have hired you yet.</h5>
                <p>Keep your profile updated and share it to attract clients.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($families as $fam): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 16px;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($fam['family_name']) ?></h5>
                                    <small class="text-muted"><?= htmlspecialchars($fam['family_email']) ?></small>
                                </div>
                                <span class="badge bg-success rounded-pill px-3 py-2">Active</span>
                            </div>
                            
                            <hr class="text-muted opacity-25 my-3">
                            
                            <div class="mb-3">
                                <span class="text-muted fs-7">Plan:</span>
                                <p class="mb-0 fw-medium"><?= $fam['duration_days'] ?> Days Plan</p>
                            </div>

                            <form action="<?= $path_prefix ?>api/upload_coach_csv.php" method="POST" enctype="multipart/form-data" class="mt-auto">
                                <label class="form-label text-muted fs-7">Upload Calendar Events (CSV)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" name="calendar_csv" accept=".csv" required>
                                    <input type="hidden" name="family_coach_id" value="<?= $fam['id'] ?>">
                                    <button class="btn btn-primary" type="submit">Upload</button>
                                </div>
                                <?php if (!empty($fam['csv_link'])): ?>
                                    <div class="mt-2 text-success fs-7">
                                        <i class="fa-solid fa-check-circle"></i> File uploaded. Families can now import it.
                                    </div>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof AlertSystem !== 'undefined') {
            AlertSystem.show(<?= json_encode($_GET['msg']) ?>, 'success', 5000);
        }
    });
</script>
<?php endif; ?>

<?php require_once $path_prefix . 'components/footer.php'; ?>
