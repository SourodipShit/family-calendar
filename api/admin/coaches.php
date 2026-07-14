<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'siteadmin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

require_once __DIR__ . '/../../classes/Coach.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

if (!$action || !$id) {
    echo json_encode(["status" => "error", "message" => "Missing required parameters."]);
    exit;
}

try {
    if ($action === 'approve') {
        $result = Coach::updateStatus($id, 'approved');
        echo json_encode($result);
        exit;
    }

    if ($action === 'reject') {
        $result = Coach::updateStatus($id, 'rejected');
        echo json_encode($result);
        exit;
    }

    if ($action === 'get') {
        $coachResponse = Coach::getById($id);
        if ($coachResponse['status'] === 'success') {
            $coachData = $coachResponse['data'];
            $profile = $coachData['profile'];
            $certifications = $coachData['certifications'];

            // Build the HTML for the modal body
            ob_start();
?>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <?php if (!empty($profile['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($profile['profile_image']); ?>" class="img-fluid rounded-circle mb-3" style="max-height: 150px; object-fit: cover; aspect-ratio: 1/1;" alt="Profile Image">
                        <?php else: ?>
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 150px; height: 150px;">
                                <i class="ri-user-3-line fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <h4 class="mb-1 fw-bold"><?php echo htmlspecialchars($profile['user_name']); ?></h4>
                        <p class="text-muted small mb-1"><?php echo htmlspecialchars($profile['email']); ?></p>
                        <span class="badge bg-primary rounded-pill px-3 py-2 mt-2"><?php echo htmlspecialchars($profile['category_name'] ?? 'No Category'); ?></span>

                        <div class="mt-4 text-start">
                            <h6 class="fw-bold text-uppercase small text-muted">About</h6>
                            <p class="small"><?php echo nl2br(htmlspecialchars($profile['description'])); ?></p>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h6 class="fw-bold text-uppercase text-muted border-bottom pb-2 mb-3">Certifications</h6>
                        <?php if (empty($certifications)): ?>
                            <div class="alert alert-light text-center small">No certifications uploaded.</div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($certifications as $cert): ?>
                                    <div class="col-12 col-lg-6 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <?php if (!empty($cert['image'])): ?>
                                                <img src="<?php echo htmlspecialchars($cert['image']); ?>" class="card-img-top" style="max-height: 120px; object-fit: cover;" alt="Certification Image">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h6 class="card-title fw-bold m-0"><?php echo htmlspecialchars($cert['name']); ?></h6>
                                                <?php if (!empty($cert['description'])): ?>
                                                    <p class="card-text small text-muted mt-2 mb-0"><?php echo htmlspecialchars($cert['description']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
<?php
            $html = ob_get_clean();

            echo json_encode([
                "status" => "success",
                "html" => $html
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => $coachResponse['message']]);
        }
        exit;
    }

    echo json_encode(["status" => "error", "message" => "Invalid action."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $e->getMessage()]);
}
