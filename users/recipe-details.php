<?php
$path_prefix = "../";
$page_title = "Recipe Details";
include $path_prefix . 'components/header.php';
?>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/recipes.css">

<?php include $path_prefix . 'components/sidebar.php'; ?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4">
        
        <!-- Header Image Section -->
        <div class="recipe-header-section shadow">
            <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&q=80&w=1600" alt="Header" class="w-100 h-100 object-fit-cover">
            <div class="recipe-header-overlay">
                <div class="d-flex gap-2 mb-3">
                    <span class="badge bg-success rounded-pill px-3">Easy</span>
                </div>
                <h1 class="display-5 fw-bold mb-2">Grilled Salmon with Lemon & Herbs</h1>
                <div class="d-flex align-items-center gap-4 opacity-75 small">
                    <span><i class="fa-regular fa-clock me-2"></i> 25 Minutes</span>
                    <span><i class="fa-solid fa-users me-2"></i> 4 Servings</span>
                    <span><i class="fa-solid fa-fire me-2"></i> 350 Calories</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Ingredients & Nutrition -->
            <div class="col-lg-4">
                <div class="recipe-details-card shadow-sm">
                    <h5 class="fw-bold mb-4">Ingredients</h5>
                    <div class="ingredient-item">
                        <span>Salmon Fillets</span>
                        <span class="fw-bold text-muted">4 pieces</span>
                    </div>
                    <div class="ingredient-item">
                        <span>Fresh Lemon</span>
                        <span class="fw-bold text-muted">2 large</span>
                    </div>
                    <div class="ingredient-item">
                        <span>Olive Oil</span>
                        <span class="fw-bold text-muted">3 tbsp</span>
                    </div>
                    <div class="ingredient-item">
                        <span>Garlic Cloves</span>
                        <span class="fw-bold text-muted">3 minced</span>
                    </div>
                    <div class="ingredient-item">
                        <span>Fresh Dill</span>
                        <span class="fw-bold text-muted">2 tbsp</span>
                    </div>
                    <div class="ingredient-item">
                        <span>Salt & Pepper</span>
                        <span class="fw-bold text-muted">To taste</span>
                    </div>

                    <h5 class="fw-bold mt-5 mb-4">Nutrition Info <small class="text-muted fw-normal fs-7">(per serving)</small></h5>
                    <div class="row g-3 text-center">
                        <div class="col-3">
                            <div class="bg-light rounded-3 p-2">
                                <div class="fw-bold text-primary">350</div>
                                <div class="extra-small text-muted" style="font-size: 0.6rem;">CAL</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-light rounded-3 p-2">
                                <div class="fw-bold text-success">34g</div>
                                <div class="extra-small text-muted" style="font-size: 0.6rem;">PRO</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-light rounded-3 p-2">
                                <div class="fw-bold text-warning">2g</div>
                                <div class="extra-small text-muted" style="font-size: 0.6rem;">CARB</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-light rounded-3 p-2">
                                <div class="fw-bold text-danger">22g</div>
                                <div class="extra-small text-muted" style="font-size: 0.6rem;">FAT</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="recipe-details-card shadow-sm">
                    <h5 class="fw-bold mb-3">Chef's Tips</h5>
                    <p class="text-muted small mb-0">Don't overcook the salmon! It should be flaky but still moist in the center. Let it rest for 5 minutes after taking it off the grill for the best results.</p>
                </div>
            </div>

            <!-- Right Side: Instructions -->
            <div class="col-lg-8">
                <div class="recipe-details-card shadow-sm h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Step-by-step Instructions</h5>
                        <button class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="fa-solid fa-play me-2"></i> Start Cooking Mode
                        </button>
                    </div>

                    <div class="instruction-step">
                        <div class="step-number">1</div>
                        <div>
                            <h6 class="fw-bold mb-2">Prepare the Marinade</h6>
                            <p class="text-muted">In a small bowl, whisk together the olive oil, minced garlic, fresh dill, and the juice of one lemon. Season with salt and pepper according to your preference.</p>
                            <div class="badge bg-light text-dark fw-normal border">
                                <i class="fa-solid fa-utensils me-2"></i> Small mixing bowl
                            </div>
                        </div>
                    </div>

                    <div class="instruction-step">
                        <div class="step-number">2</div>
                        <div>
                            <h6 class="fw-bold mb-2">Season the Salmon</h6>
                            <p class="text-muted">Pat the salmon fillets dry with paper towels. Place them on a tray and brush both sides generously with the prepared marinade. Let it sit for 10 minutes at room temperature.</p>
                        </div>
                    </div>

                    <div class="instruction-step">
                        <div class="step-number">3</div>
                        <div>
                            <h6 class="fw-bold mb-2">Grill to Perfection</h6>
                            <p class="text-muted">Preheat your grill to medium-high heat. Place salmon skin-side down first and grill for about 6 minutes. Flip carefully and grill for another 4-5 minutes until the internal temperature reaches 145°F.</p>
                            <div class="badge bg-light text-dark fw-normal border">
                                <i class="fa-solid fa-temperature-three-quarters me-2"></i> 400°F (200°C)
                            </div>
                        </div>
                    </div>

                    <div class="instruction-step border-0 mb-0">
                        <div class="step-number">4</div>
                        <div>
                            <h6 class="fw-bold mb-2">Garnish and Serve</h6>
                            <p class="text-muted">Remove from the grill and immediately top with fresh lemon slices. Garnish with more fresh dill if desired. Serve warm with your favorite sides like asparagus or wild rice.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>
