<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$flash_msg = $_SESSION['flash_msg'] ?? $_GET['msg'] ?? null;
$flash_type = $_SESSION['flash_type'] ?? $_GET['type'] ?? 'success';

if (isset($_SESSION['flash_msg'])) {
    unset($_SESSION['flash_msg']);
    unset($_SESSION['flash_type']);
}

$path_prefix = "../";
$page_title = "Coaches";
require_once $path_prefix . 'config/Database.php';
require_once $path_prefix . 'classes/Coach.php';

$coachesRes = Coach::getAll(['approval_status' => 'approved']);
$allCoaches = $coachesRes['status'] === 'success' ? $coachesRes['data'] : [];

$categoriesRes = Coach::getAllCategories();
$allCategories = $categoriesRes['status'] === 'success' ? $categoriesRes['data'] : [];

$familyId = $_SESSION['user']['family_id'] ?? null;
if (!$familyId && isset($_SESSION['user']['id'])) {
    $stmt = Database::runPrepared("SELECT family_id FROM user_family WHERE user_id = ?", [$_SESSION['user']['id']]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    $familyId = $res ? $res['family_id'] : null;
}
$myCoaches = [];
$hiredCoachStatuses = [];
if ($familyId) {
    $myRes = Coach::getFamilyCoaches($familyId);
    $myCoaches = $myRes['status'] === 'success' ? $myRes['data'] : [];
    foreach ($myCoaches as $hire) {
        if (!isset($hiredCoachStatuses[$hire['coach_id']])) {
            $hiredCoachStatuses[$hire['coach_id']] = $hire['status'];
        }
    }
}

include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<style>
    .category-pill {
        background-color: white;
        border: 1px solid #e0e0e0;
        color: #555;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .category-pill:hover {
        border-color: #0d6efd;
        color: #0d6efd;
    }

    .coach-card {
        border: 1px solid #eef0f2;
        border-radius: 16px;
        transition: all 0.3s;
        height: 100%;
    }

    .coach-card:hover {
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        border-color: #dee2e6;
    }

    .status-badge {
        background-color: #e8f5e9;
        color: #4caf50;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .coach-img {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 50%;
    }

    .tag-pill {
        background-color: #f1f5f9;
        color: #64748b;
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 8px;
        font-weight: 500;
    }

    .info-card {
        border: 1px solid #eef0f2;
        border-radius: 16px;
        background-color: white;
    }

    .tip-card {
        background-color: #f8faff;
        border: 1px solid #eef2ff;
    }

    .step-circle {
        width: 28px;
        height: 28px;
        background-color: #e0e7ff;
        color: #4f46e5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .nav-tabs .nav-link {
        color: #64748b;
        font-weight: 500;
        border: none;
        padding: 1rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 3px solid #0d6efd;
        background: transparent;
    }

    .verification-badge {
        color: #0d6efd;
        font-size: 1rem;
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #f8f9fa 0%, #f3f0ff 100%);
    }

    .search-icon {
        color: #94a3b8;
    }

    .plan-card-label input:checked+.plan-card {
        border-color: #0d6efd !important;
        background-color: #f8fbff !important;
        box-shadow: 0 0 0 1px #0d6efd;
    }

    .plan-card {
        transition: all 0.2s ease;
    }
</style>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4" style="max-width: 1400px; margin: 0 auto;">

        <div class="row g-4">
            <!-- Main Content Area -->
            <div class="col-lg-8 col-xl-9">
                <p class="text-muted fs-6 mb-4">Find and manage coaches to support your family's goals.</p>

                <!-- Tabs and Actions -->
                <div class="d-lg-flex justify-content-between align-items-end mb-4 border-bottom">
                    <ul class="nav nav-tabs border-0" id="coachesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="find-tab" data-bs-toggle="tab"
                                data-bs-target="#find" type="button" role="tab">Find a Coach</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="my-coaches-tab" data-bs-toggle="tab"
                                data-bs-target="#my-coaches" type="button" role="tab">My Coaches</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="applications-tab" data-bs-toggle="tab"
                                data-bs-target="#applications" type="button" role="tab">Applications</button>
                        </li>
                    </ul>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'family-head'): ?>
                        <button class="btn btn-primary rounded-3 px-4 py-2 mt-2 mt-lg-0 fw-medium mb-2 shadow-sm">
                            <i class="fa-solid fa-plus me-2"></i> Hire a Coach
                        </button>
                    <?php endif; ?>
                </div>

                <div class="tab-content" id="coachesTabsContent">
                    <div class="tab-pane fade show active" id="find" role="tabpanel" aria-labelledby="find-tab">

                        <!-- Search and Filters -->
                        <div class="d-flex flex-column flex-md-row gap-3 mb-4">
                            <div class="position-relative flex-grow-1">
                                <i
                                    class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 search-icon"></i>
                                <input type="text" id="coachSearchInput"
                                    class="form-control border rounded-3 py-2 ps-5"
                                    placeholder="Search coaches by name, specialty, or keyword...">
                            </div>
                            <div class="dropdown">
                                <button
                                    class="btn btn-white border rounded-3 py-2 px-3 text-dark d-flex align-items-center justify-content-between bg-white"
                                    style="min-width: 160px;" type="button" data-bs-toggle="dropdown">
                                    <span id="categoryDropdownLabel">All Categories</span>
                                    <i class="fa-solid fa-chevron-down text-muted ms-2 fs-7"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                    <li><a class="dropdown-item active category-filter" href="#"
                                            data-category="all">All Categories</a></li>
                                    <?php foreach ($allCategories as $cat): ?>
                                        <li><a class="dropdown-item category-filter" href="#"
                                                data-category="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="dropdown">
                                <button
                                    class="btn btn-white border rounded-3 py-2 px-3 text-dark d-flex align-items-center justify-content-between bg-white"
                                    style="min-width: 140px;" type="button" data-bs-toggle="dropdown">
                                    <span>Availability</span>
                                    <i class="fa-solid fa-chevron-down text-muted ms-2 fs-7"></i>
                                </button>
                                <ul class="dropdown-menu shadow-sm border-0 mt-2">
                                    <li><a class="dropdown-item active" href="#">Any Availability</a></li>
                                    <li><a class="dropdown-item" href="#">Available Now</a></li>
                                    <li><a class="dropdown-item" href="#">Evenings</a></li>
                                    <li><a class="dropdown-item" href="#">Weekends</a></li>
                                </ul>
                            </div>
                            <button
                                class="btn btn-white border rounded-3 py-2 px-4 text-dark bg-white d-flex align-items-center">
                                <i class="fa-solid fa-filter text-muted me-2"></i> Filters
                            </button>
                        </div>

                        <!-- Category Pills -->
                        <div class="d-flex flex-wrap gap-3 mb-5" id="categoryPills">
                            <?php
                            $categoryIcons = [
                                'Academic' => ['icon' => 'fa-book-open', 'color' => 'text-primary'],
                                'Sports & Fitness' => ['icon' => 'fa-futbol', 'color' => 'text-dark'],
                                'Sports' => ['icon' => 'fa-futbol', 'color' => 'text-dark'],
                                'Music & Arts' => ['icon' => 'fa-music', 'color' => 'text-info', 'style' => 'color: #ba68c8 !important;'],
                                'Music' => ['icon' => 'fa-music', 'color' => 'text-info', 'style' => 'color: #ba68c8 !important;'],
                                'Life Skills' => ['icon' => 'fa-leaf', 'color' => 'text-success'],
                                'Wellness' => ['icon' => 'fa-heart', 'color' => 'text-danger'],
                            ];
                            foreach ($allCategories as $cat):
                                $name = $cat['name'];
                                $iconData = $categoryIcons[$name] ?? ['icon' => 'fa-tag', 'color' => 'text-secondary', 'style' => ''];
                            ?>
                                <a href="#" class="category-pill shadow-sm category-filter" data-category="<?= htmlspecialchars($name) ?>">
                                    <i class="fa-solid <?= $iconData['icon'] ?> <?= $iconData['color'] ?>" <?= !empty($iconData['style']) ? 'style="' . $iconData['style'] . '"' : '' ?>></i> <?= htmlspecialchars($name) ?>
                                </a>
                            <?php endforeach; ?>
                            <a href="#" class="category-pill shadow-sm text-primary category-filter"
                                data-category="all" style="border-color: #0d6efd;">
                                <i class="fa-solid fa-border-all"></i> View All
                            </a>
                        </div>

                        <!-- Featured Coaches -->
                        <h5 class="fw-bold mb-4">Available Coaches</h5>



                        <div class="row g-4 mb-4">
                            <?php foreach ($allCoaches as $coach): ?>
                                <?php
                                $cId = $coach['user_id'];
                                $cStatus = $hiredCoachStatuses[$cId] ?? null;
                                ?>
                                <!-- Coach Card -->
                                <div class="col-md-6 col-xl-4">
                                    <div class="card coach-card bg-white p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <img src="<?= htmlspecialchars($coach['profile_image'] ?: 'default.jpg') ?>"
                                                class="coach-img shadow-sm" alt="<?= htmlspecialchars($coach['user_name']) ?>" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($coach['user_name']) ?>&background=random'">
                                            <?php if ($cStatus === 'pending_admin_approval' || $cStatus === 'pending'): ?>
                                                <span class="badge bg-warning text-dark" style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Pending</span>
                                            <?php elseif ($cStatus === 'approved' || $cStatus === 'active'): ?>
                                                <span class="badge bg-success" style="padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;">Hired</span>
                                            <?php else: ?>
                                                <span class="status-badge">Available</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-1 d-flex align-items-center">
                                            <h5 class="fw-bold mb-0 me-2"><?= htmlspecialchars($coach['user_name']) ?></h5>
                                            <i class="fa-solid fa-circle-check verification-badge me-2"></i>

                                        </div>
                                        <div class="d-flex align-items-center mb-3 fs-7">
                                            <?php if (!empty($coach['phone'])): ?>
                                                <a href="tel:<?= htmlspecialchars($coach['phone']) ?>" class="text-muted text-decoration-none fs-7" title="<?= htmlspecialchars($coach['phone']) ?>">
                                                    <i class="fa-solid fa-phone fs-7 me-2"></i><?= htmlspecialchars($coach['phone']) ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-muted fs-7 mb-2"><?= htmlspecialchars($coach['category_name'] ?? 'Coach') ?></div>
                                        <div class="d-flex align-items-center mb-3 fs-7">
                                            <i class="fa-solid fa-star text-warning me-1"></i>
                                            <span class="fw-bold me-1">5.0</span>
                                            <span class="text-muted">(0 reviews)</span>
                                        </div>
                                        <p class="text-muted fs-7 mb-4 flex-grow-1"><?= htmlspecialchars(substr($coach['description'], 0, 100)) ?>...</p>
                                        <div class="d-flex flex-wrap gap-2 mb-4">
                                            <span class="tag-pill"><?= htmlspecialchars($coach['category_name'] ?? 'General') ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3">
                                            <?php if ($cStatus === 'pending_admin_approval' || $cStatus === 'pending'): ?>
                                                <button class="btn btn-warning rounded-3 px-3 py-2 fw-medium w-100" disabled>Pending Approval</button>
                                            <?php elseif ($cStatus === 'approved' || $cStatus === 'active'): ?>
                                                <button class="btn btn-success rounded-3 px-3 py-2 fw-medium w-100" disabled>Hired</button>
                                            <?php else: ?>
                                                <?php $isHead = (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'family-head'); ?>
                                                <button class="btn btn-outline-primary rounded-3 px-3 py-2 fw-medium w-100" data-bs-toggle="modal" data-bs-target="#hireModal<?= $coach['id'] ?>"><?= $isHead ? 'View Profile & Hire' : 'View Profile' ?></button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hire Modal -->
                                <div class="modal fade" id="hireModal<?= $coach['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-0 bg-light pb-4 position-relative">
                                                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                                                <div class="d-flex align-items-center mt-3">
                                                    <img src="<?= htmlspecialchars($coach['profile_image'] ?: 'default.jpg') ?>" class="rounded-circle shadow-sm me-3 bg-white p-1" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($coach['user_name']) ?>&background=random'">
                                                    <div>
                                                        <?php $isHead = (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'family-head'); ?>
                                                        <h4 class="modal-title fw-bold mb-1"><?= $isHead ? 'Hire ' . htmlspecialchars($coach['user_name']) : htmlspecialchars($coach['user_name']) . "'s Profile" ?></h4>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill"><i class="fa-solid fa-tag me-1"></i><?= htmlspecialchars($coach['category_name'] ?? 'General') ?></span>
                                                            <?php if (!empty($coach['phone'])): ?>
                                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-pill"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($coach['phone']) ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <form action="<?= $path_prefix ?>api/hire_coach.php" method="POST">
                                                <div class="modal-body p-4">
                                                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">About the Coach</h6>
                                                    <p class="text-secondary lh-lg mb-4"><?= nl2br(htmlspecialchars($coach['description'])) ?></p>

                                                    <?php
                                                    $certifications = Database::runPrepared("SELECT * FROM coach_certifications WHERE coach_id = ?", [$coach['user_id']])->fetchAll(PDO::FETCH_ASSOC);
                                                    if (!empty($certifications)):
                                                    ?>
                                                        <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Certifications</h6>
                                                        <div class="row g-3 mb-4">
                                                            <?php foreach ($certifications as $cert): ?>
                                                                <div class="col-6 col-md-4">
                                                                    <div class="card border-0 shadow-sm h-100 overflow-hidden"
                                                                        style="cursor: pointer; transition: transform 0.2s;"
                                                                        onmouseover="this.style.transform='scale(1.03)'"
                                                                        onmouseout="this.style.transform='scale(1)'"
                                                                        onclick="<?php if (!empty($cert['image'])) echo "window.open('" . htmlspecialchars($cert['image']) . "', '_blank')"; ?>">
                                                                        <div style="padding-top: 75%; position: relative; background-color: #f8f9fa;">
                                                                            <?php if (!empty($cert['image'])): ?>
                                                                                <img src="<?= htmlspecialchars($cert['image']) ?>" alt="cert" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;">
                                                                            <?php else: ?>
                                                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center text-primary opacity-50">
                                                                                    <i class="fa-solid fa-certificate fa-3x"></i>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="card-body p-3 bg-white border-top">
                                                                            <h6 class="card-title fw-semibold fs-7 mb-1 text-truncate" title="<?= htmlspecialchars($cert['name']) ?>"><?= htmlspecialchars($cert['name']) ?></h6>
                                                                            <?php if (!empty($cert['description'])): ?>
                                                                                <p class="card-text text-muted mb-0" style="font-size: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="<?= htmlspecialchars($cert['description']) ?>"><?= htmlspecialchars($cert['description']) ?></p>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <hr class="text-muted opacity-25 my-4">

                                                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.85rem; letter-spacing: 0.5px;">Select a Plan</h6>
                                                    <?php
                                                    // Fetch plans for this coach
                                                    $plansRes = Database::runPrepared("SELECT * FROM coach_plans WHERE coach_id = ?", [$coach['user_id']])->fetchAll(PDO::FETCH_ASSOC);
                                                    if (empty($plansRes)):
                                                    ?>
                                                        <div class="alert alert-light border text-danger d-flex align-items-center">
                                                            <i class="fa-solid fa-circle-exclamation me-2"></i> This coach has no plans available.
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="row g-3">
                                                            <?php foreach ($plansRes as $plan): ?>
                                                                <div class="col-4 col-sm-3 col-lg-2">
                                                                    <label class="w-100 position-relative plan-card-label" style="<?= $isHead ? 'cursor: pointer;' : 'cursor: default;' ?>">
                                                                        <?php if ($isHead): ?>
                                                                            <input class="form-check-input position-absolute opacity-0" type="radio" name="plan_id" value="<?= $plan['id'] ?>" required>
                                                                        <?php endif; ?>
                                                                        <div class="plan-card border rounded-4 d-flex flex-column justify-content-center align-items-center text-center p-2 bg-white shadow-sm h-100" style="aspect-ratio: 4/3;">
                                                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.75rem;"><?= $plan['duration_days'] ?> Days</h6>
                                                                            <div class="text-primary fw-bold" style="font-size: 1rem;">$<?= number_format($plan['price'], 2) ?></div>
                                                                        </div>
                                                                        </label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <input type="hidden" name="coach_id" value="<?= $coach['user_id'] ?>">
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0 bg-white">
                                                    <button type="button" class="btn btn-light rounded-3 px-4 py-2 fw-medium" data-bs-dismiss="modal">Close</button>
                                                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'family-head'): ?>
                                                        <button type="submit" class="btn btn-primary rounded-3 px-4 py-2 fw-medium shadow-sm" <?= empty($plansRes) ? 'disabled' : '' ?>>
                                                            <i class="fa-solid fa-check me-2"></i> Hire Coach
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted bg-light px-3 py-2 rounded-3 fs-7 border">Only Family Head can hire.</span>
                                                    <?php endif; ?>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($allCoaches)): ?>
                                <p class="text-muted">No coaches available at the moment.</p>
                            <?php endif; ?>
                        </div>

                    </div> <!-- End #find tab-pane -->

                    <!-- My Coaches Tab Pane -->
                    <div class="tab-pane fade" id="my-coaches" role="tabpanel" aria-labelledby="my-coaches-tab">
                        <?php if (empty($myCoaches)): ?>
                            <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm mb-4 mt-2">
                                <i class="fa-solid fa-users fa-3x mb-3 text-primary opacity-50"></i>
                                <h4>Your Coaches</h4>
                                <p>You haven't hired any coaches yet. Browse coaches to find the right fit for your
                                    family.</p>
                                <button class="btn btn-outline-primary mt-2"
                                    onclick="document.getElementById('find-tab').click()">Browse Coaches</button>
                            </div>
                        <?php else: ?>
                            <div class="row g-4 mb-4 mt-2">
                                <?php foreach ($myCoaches as $hire): ?>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="card coach-card bg-white p-4 d-flex flex-column">
                                            <div class="mb-1 d-flex align-items-center">
                                                <h5 class="fw-bold mb-0 me-2"><?= htmlspecialchars($hire['coach_name']) ?></h5>
                                                <span class="badge bg-<?= $hire['status'] == 'approved' ? 'success' : ($hire['status'] == 'rejected' ? 'danger' : 'warning') ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $hire['status']))) ?></span>
                                            </div>
                                            <div class="text-muted fs-7 mb-2"><?= $hire['duration_days'] ?? 0 ?> Days Plan</div>
                                            <p class="text-muted fs-7 mb-4 flex-grow-1">Hired for $<?= number_format($hire['price_at_hire'] ?? 0, 2) ?> on <?= date('M d, Y', strtotime($hire['created_at'])) ?></p>

                                            <?php if ($hire['status'] == 'approved' && !empty($hire['csv_link'])): ?>
                                                <div class="alert alert-info py-2">
                                                    <p class="mb-2"><small>Coach has uploaded calendar events.</small></p>
                                                    <a href="<?= htmlspecialchars($hire['csv_link']) ?>" class="btn btn-sm btn-primary w-100" target="_blank">Download Events (CSV)</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Applications Tab Pane -->
                    <div class="tab-pane fade" id="applications" role="tabpanel"
                        aria-labelledby="applications-tab">
                        <div class="p-5 text-center text-muted bg-white border rounded-4 shadow-sm mb-4 mt-2">
                            <i class="fa-regular fa-file-lines fa-3x mb-3 text-info opacity-50"></i>
                            <h4>Applications</h4>
                            <p>You can check the status of your applications in the "My Coaches" tab.</p>
                        </div>
                    </div>
                </div> <!-- End #coachesTabsContent -->

            </div>

            <!-- Right Sidebar (Widgets) -->
            <div class="col-lg-4 col-xl-3">

                <!-- Why hire a coach -->
                <div class="info-card p-4 mb-4 bg-gradient-purple text-center">
                    <div class="d-flex justify-content-center mb-3 position-relative">
                        <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px; border: 4px solid #f3f0ff;">
                            <i class="fa-solid fa-user text-primary"
                                style="font-size: 2.5rem; color: #c4b5fd !important;"></i>
                        </div>
                        <div class="position-absolute bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-2"
                            style="width: 28px; height: 28px; bottom: 0; right: 50%; transform: translate(35px, 5px);">
                            <i class="fa-solid fa-star fs-8"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-4 text-start">Why hire a coach?</h5>
                    <ul class="list-unstyled text-start fs-7 text-dark mb-0 d-flex flex-column gap-3">
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Personalized guidance for your family's goals
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Build skills and confidence
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Flexible scheduling to fit your routine
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fa-solid fa-circle-check text-success mt-1 me-2"></i>
                            Track progress and achievements
                        </li>
                    </ul>
                </div>

                <!-- Tip Card -->
                <div class="info-card tip-card p-4 mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-regular fa-lightbulb text-primary me-2 fs-5"></i>
                        <h6 class="fw-bold mb-0">Tip</h6>
                    </div>
                    <p class="text-muted fs-7 mb-0">Once hired, coaches can be added to your calendar and
                        assigned to family members.</p>
                </div>

                <!-- How it works -->
                <div class="info-card p-4">
                    <h5 class="fw-bold mb-4">How it works</h5>

                    <div class="d-flex mb-4">
                        <div class="step-circle me-3">1</div>
                        <div>
                            <p class="mb-0 fs-7 text-dark">Find a coach that fits your needs</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="step-circle me-3 bg-primary text-white">2</div>
                        <div>
                            <p class="mb-0 fs-7 text-dark">Review profile and send request</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="step-circle me-3">3</div>
                        <div>
                            <p class="mb-0 fs-7 text-dark">Coach accepts and you can start scheduling</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Expand/Collapse functionality ---
        const moreCoaches = document.getElementById('moreCoaches');
        const viewAllBtn = document.getElementById('viewAllBtn');

        if (moreCoaches && viewAllBtn) {
            moreCoaches.addEventListener('show.bs.collapse', event => {
                viewAllBtn.innerHTML = 'Show Less Coaches';
            });
            moreCoaches.addEventListener('hide.bs.collapse', event => {
                viewAllBtn.innerHTML = 'View All Coaches';
            });
        }

        // --- Search and Category Filtering ---
        const searchInput = document.getElementById('coachSearchInput');
        const categoryFilters = document.querySelectorAll('.category-filter');
        const allCoachCards = document.querySelectorAll('.coach-card');

        let currentCategory = 'all';
        let searchQuery = '';

        function filterCoaches() {
            let activeCount = 0;
            allCoachCards.forEach(card => {
                const col = card.parentElement;
                const text = card.innerText.toLowerCase();
                const matchesSearch = text.includes(searchQuery);
                const matchesCategory = currentCategory === 'all' || text.includes(currentCategory.toLowerCase());

                if (matchesSearch && matchesCategory) {
                    col.style.display = '';
                    activeCount++;
                } else {
                    col.style.display = 'none';
                }
            });

            // Auto-expand the "more coaches" section if we are actively searching or filtering
            if ((searchQuery.length > 0 || currentCategory !== 'all') && moreCoaches) {
                if (!moreCoaches.classList.contains('show')) {
                    moreCoaches.classList.add('show');
                    if (viewAllBtn) viewAllBtn.innerHTML = 'Show Less Coaches';
                }
            }
        }

        // Search Input Event
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                filterCoaches();
            });
        }

        // Category Filter Event (handles both pills and dropdown items)
        categoryFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                e.preventDefault();
                currentCategory = e.currentTarget.getAttribute('data-category');

                // Update dropdown label if a dropdown item was clicked
                if (e.currentTarget.classList.contains('dropdown-item')) {
                    const label = document.getElementById('categoryDropdownLabel');
                    if (label) label.innerText = e.currentTarget.innerText;
                }

                // Reset active styling on all filters
                categoryFilters.forEach(f => {
                    if (f.classList.contains('category-pill')) {
                        f.style.borderColor = '#e0e0e0';
                        f.classList.remove('text-primary');
                    } else {
                        f.classList.remove('active');
                    }
                });

                // Set active styling on the clicked filter
                if (e.currentTarget.classList.contains('category-pill')) {
                    e.currentTarget.style.borderColor = '#0d6efd';
                    e.currentTarget.classList.add('text-primary');
                } else {
                    e.currentTarget.classList.add('active');
                }

                filterCoaches();
            });
        });
    });
</script>

<?php if ($flash_msg): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AlertSystem !== 'undefined') {
                AlertSystem.show(<?= json_encode($flash_msg) ?>, <?= json_encode($flash_type) ?>, 5000);
            }
        });
    </script>
<?php endif; ?>

<?php include $path_prefix . 'components/footer.php'; ?>