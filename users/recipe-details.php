<?php
$path_prefix = "../";
require_once $path_prefix . 'classes/Recipe.php';

$recipeId = $_GET['id'] ?? null;
if (!$recipeId) {
    header("Location: recipes.php");
    exit;
}

$userFamilyId = $_SESSION['user']['families'][0]['family_id'] ?? 0;
$response = Recipe::getById($recipeId, $userFamilyId);
if ($response['status'] === 'error') {
    header("Location: recipes.php");
    exit;
}

$recipe = $response['data'];
$page_title = $recipe['name'];
include $path_prefix . 'components/header.php';

// Prepare variables
$mainImage = $recipe['image_url'] ? $path_prefix . $recipe['image_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&q=80&w=1600';
$totalTime = (int)($recipe['prep_time_min'] ?? 0) + (int)($recipe['cook_time_min'] ?? 0);
$difficultyClass = "difficulty-" . strtolower($recipe['difficulty'] ?? 'easy');
$userImage = $recipe['user_image'] ? (str_contains($recipe['user_image'], 'http') ? $recipe['user_image'] : $path_prefix . str_replace('../', '', $recipe['user_image'])) : $path_prefix . 'public/img/default-user.png';
?>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/recipes.css">

<?php include $path_prefix . 'components/sidebar.php'; ?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4">
        
        <div class="row g-4 d-flex align-items-stretch">
            <!-- Left Column -->
            <div class="col-lg-4 d-flex flex-column">
                <!-- Main Info Card -->
                <div class="recipe-details-card flex-grow-1">
                    <div class="position-relative" style="height: 280px; overflow: hidden;">
                        <img src="<?php echo $mainImage; ?>" alt="Header" class="w-100 h-100 object-fit-cover hover-scale">
                        <div class="hero-gradient-overlay"></div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-4">
                            <div class="d-flex justify-content-between align-items-end">
                                <div>
                                    <div class="d-flex gap-2 mb-2">
                                        <span class="recipe-badge <?php echo $difficultyClass; ?> border-0"><?php echo $recipe['difficulty']; ?></span>
                                        <span class="recipe-badge recipe-category border-0"><?php echo $recipe['category']; ?></span>
                                    </div>
                                    <h2 class="text-white fw-bold mb-0"><?php echo $recipe['name']; ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <!-- Author Info -->
                        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-primary-soft rounded-4">
                            <img src="<?php echo $userImage; ?>" class="rounded-circle border border-2 border-white shadow-sm" style="width: 42px; height: 42px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-primary-deep small"><?php echo $recipe['user_name']; ?></div>
                                <div class="extra-small text-muted opacity-75">Posted on <?php echo date('M d, Y', strtotime($recipe['created_at'])); ?></div>
                            </div>
                        </div>

                        <!-- Stats Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-4 text-center">
                                <div class="info-pill">
                                    <i class="ri-time-line text-indigo mb-1 d-block fs-5"></i>
                                    <div class="fw-bold text-dark small"><?php echo $totalTime; ?>m</div>
                                    <div class="extra-small text-muted text-uppercase fw-semibold" style="font-size: 0.6rem;">Time</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="info-pill">
                                    <i class="ri-restaurant-line text-emerald mb-1 d-block fs-5"></i>
                                    <div class="fw-bold text-dark small"><?php echo $recipe['servings']; ?></div>
                                    <div class="extra-small text-muted text-uppercase fw-semibold" style="font-size: 0.6rem;">Servings</div>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="info-pill">
                                    <i class="ri-earth-line text-amber mb-1 d-block fs-5"></i>
                                    <div class="fw-bold text-dark text-truncate small" title="<?php echo $recipe['cuisine']; ?>"><?php echo $recipe['cuisine']; ?></div>
                                    <div class="extra-small text-muted text-uppercase fw-semibold" style="font-size: 0.6rem;">Cuisine</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ingredients -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold mb-0 d-flex align-items-center">
                                    <span class="p-2 bg-primary-soft text-primary rounded-3 me-2">
                                        <i class="ri-shopping-basket-2-line"></i>
                                    </span>
                                    Ingredients
                                </h6>
                                <span class="badge bg-light text-muted fw-normal rounded-pill"><?php echo count($recipe['ingredients']); ?> items</span>
                            </div>
                            <div class="ingredients-list">
                                <?php foreach($recipe['ingredients'] as $ing): ?>
                                <div class="ingredient-item py-2 d-flex justify-content-between align-items-center border-bottom border-light">
                                    <span class="text-dark small fw-medium">
                                        <?php echo $ing['ingredient_name']; ?>
                                        <?php if (!empty($ing['prep_notes'])): ?>
                                            <span class="text-muted fw-normal extra-small">(<?php echo $ing['prep_notes']; ?>)</span>
                                        <?php endif; ?>
                                    </span>
                                    <span class="text-primary small fw-bold text-end">
                                        <?php echo $ing['quantity']; ?> <?php echo $ing['unit']; ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Nutrition -->
                        <?php if ($recipe['nutrition']): ?>
                        <div>
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                <span class="p-2 bg-success-subtle text-success rounded-3 me-2">
                                    <i class="ri-pulse-line"></i>
                                </span>
                                Nutrition <small class="text-muted fw-normal ms-1">(per serving)</small>
                            </h6>
                            <div class="row g-2 text-center">
                                <div class="col-3">
                                    <div class="nutrition-pill">
                                        <div class="fw-bold text-primary small"><?php echo (int)$recipe['nutrition']['calories']; ?></div>
                                        <div class="extra-small text-muted fw-bold" style="font-size: 0.55rem;">CAL</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="nutrition-pill">
                                        <div class="fw-bold text-success small"><?php echo (int)$recipe['nutrition']['protein']; ?>g</div>
                                        <div class="extra-small text-muted fw-bold" style="font-size: 0.55rem;">PRO</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="nutrition-pill">
                                        <div class="fw-bold text-warning small"><?php echo (int)$recipe['nutrition']['carbs']; ?>g</div>
                                        <div class="extra-small text-muted fw-bold" style="font-size: 0.55rem;">CARB</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="nutrition-pill">
                                        <div class="fw-bold text-danger small"><?php echo (int)$recipe['nutrition']['fat']; ?>g</div>
                                        <div class="extra-small text-muted fw-bold" style="font-size: 0.55rem;">FAT</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-8 d-flex flex-column">
                <!-- Instructions -->
                <div class="recipe-details-card p-4 p-md-5 h-100 flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <h4 class="fw-bold mb-0">Preparation Steps</h4>
                        <div class="text-muted small">
                            <i class="ri-list-check-2 me-1"></i> <?php echo count($recipe['steps']); ?> Steps
                        </div>
                    </div>

                    <div class="instructions-list">
                        <?php foreach($recipe['steps'] as $step): ?>
                        <div class="instruction-step">
                            <div class="d-flex gap-4">
                                <div class="step-number text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.85rem; font-weight: bold; z-index: 1;">
                                    <?php echo $step['step_number']; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-3"><?php echo $step['instruction']; ?></p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php if($step['equipment']): ?>
                                        <div class="extra-small px-2 py-1 bg-light rounded-2 text-muted">
                                            <i class="ri-tools-line me-1 text-primary"></i> <?php echo $step['equipment']; ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if($step['temperature']): ?>
                                        <div class="extra-small px-2 py-1 bg-light rounded-2 text-muted">
                                            <i class="ri-temp-hot-line me-1 text-danger"></i> <?php echo $step['temperature']; ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if($recipe['variations_notes']): ?>
                    <div class="mt-5 p-4 rounded-4 bg-amber-light border border-amber-subtle">
                        <h6 class="fw-bold text-amber mb-2 d-flex align-items-center">
                            <i class="ri-lightbulb-flash-line me-2 fs-5"></i> Chef's Notes & Variations
                        </h6>
                        <p class="text-dark opacity-75 small mb-0 lh-lg">
                            <?php echo nl2br($recipe['variations_notes']); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Gallery Section -->
        <?php if (count($recipe['images']) > 1): ?>
        <div class="mt-4">
            <div class="recipe-details-card p-4">
                <h6 class="fw-bold mb-4 d-flex align-items-center">
                    <span class="p-2 bg-primary-soft text-primary rounded-3 me-2">
                        <i class="ri-gallery-line"></i>
                    </span>
                    Recipe Gallery
                </h6>
                <div class="row g-3">
                    <?php foreach($recipe['images'] as $img): ?>
                        <?php if(!$img['is_main']): ?>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-6">
                            <div class="gallery-item shadow-sm">
                                <img src="<?php echo $path_prefix . $img['image_path']; ?>" class="w-100 object-fit-cover" style="height: 140px;" data-bs-toggle="modal" data-bs-target="#imageModal" onclick="document.getElementById('modalImage').src=this.src">
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>


<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-body p-0 text-center">
                <img id="modalImage" src="" class="img-fluid rounded-4 shadow-lg border border-4 border-white">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-4" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>
