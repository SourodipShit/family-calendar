<?php
$path_prefix = "";
$page_title = "Payment Status - Family Calendar";
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/GlobalSettings.php';

$status = isset($_GET['status']) ? $_GET['status'] : '';

$is_success = ($status === 'success');
$is_failed = ($status === 'failed');

?>
<style>
    .status-icon {
        font-size: 5rem;
    }
</style>
<div class="container-fluid min-vh-100 d-flex flex-column bg-light">
    <div class="row flex-grow-1 justify-content-center align-items-center">
        <div class="col-12 col-md-6 col-lg-4 text-center">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <?php if ($is_success): ?>
                    <div class="mb-4 text-success">
                        <i class="fa-solid fa-circle-check status-icon"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Payment Successful!</h2>
                    <p class="text-muted mb-4">Thank you for your payment. Your subscription has been processed successfully.</p>
                <?php elseif ($is_failed): ?>
                    <div class="mb-4 text-danger">
                        <i class="fa-solid fa-circle-xmark status-icon"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Payment Failed</h2>
                    <p class="text-muted mb-4">Unfortunately, your payment could not be processed. Please try again or contact support.</p>
                <?php else: ?>
                    <div class="mb-4 text-secondary">
                        <i class="fa-solid fa-circle-info status-icon"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Unknown Status</h2>
                    <p class="text-muted mb-4">We could not determine your payment status.</p>
                <?php endif; ?>

                <a href="login.php" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                    Return
                </a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/components/footer.php'; ?>