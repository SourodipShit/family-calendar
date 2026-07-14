<?php
$path_prefix = "../";
$page_title = "Coach Approvals";
require_once $path_prefix . 'config/Database.php';
require_once $path_prefix . 'classes/Coach.php';

require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    die("Unauthorized.");
}

$pendingRes = Coach::getPendingCoachApprovals();
$pending = $pendingRes['status'] === 'success' ? $pendingRes['data'] : [];
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <h2 class="fw-bold mb-4">Pending Coach Hires</h2>



        <?php if (empty($pending)): ?>
            <div class="card p-5 text-center text-muted border-0 bg-white shadow-sm rounded-4">
                <i class="fa-solid fa-check-circle fa-3x mb-3 text-success"></i>
                <h5>All caught up!</h5>
                <p>There are no pending coach hire approvals right now.</p>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Family</th>
                                <th>Coach</th>
                                <th>Plan</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending as $req): ?>
                                <tr>
                                    <td><?= htmlspecialchars($req['family_name']) ?></td>
                                    <td><?= htmlspecialchars($req['coach_name']) ?></td>
                                    <td><?= $req['plan_duration'] ?> Days</td>
                                    <td>$<?= number_format($req['price'], 2) ?></td>
                                    <td>
                                        <form action="<?= $path_prefix ?>api/approve_coach.php" method="POST" class="d-inline">
                                            <input type="hidden" name="family_coach_id" value="<?= $req['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
