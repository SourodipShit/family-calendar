<?php
$path_prefix = "../";
$page_title = "Recipes";
include $path_prefix . 'components/header.php';
?>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/recipes.css">

<?php include $path_prefix . 'components/sidebar.php'; ?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4">
        
        <!-- Header & Add Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold mb-1">Recipe Discover</h2>
                <p class="text-muted small mb-0">Explore and share family favorites</p>
            </div>
            <a href="add-recipe.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                <i class="fa-solid fa-plus me-2"></i> Add Recipe
            </a>
        </div>

        <!-- Search & Filter Bar -->
        <div class="search-filter-bar shadow-sm">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" class="form-control form-control-custom ps-5" placeholder="Search for recipes, ingredients...">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select class="form-select form-control-custom">
                        <option selected>All Categories</option>
                        <option>Breakfast</option>
                        <option>Lunch</option>
                        <option>Dinner</option>
                        <option>Dessert</option>
                        <option>Snacks</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-select form-control-custom">
                        <option selected>Difficulty: All</option>
                        <option>Easy</option>
                        <option>Medium</option>
                        <option>Hard</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Recipe Grid -->
        <div class="row g-4">
            <!-- Recipe Card 1 -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="recipe-card">
                    <div class="recipe-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?auto=format&fit=crop&q=80&w=800" alt="Recipe">
                        <div class="recipe-badge-group">
                            <span class="recipe-badge difficulty-easy">Easy</span>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h5 class="recipe-title">Grilled Salmon with Lemon</h5>
                        <div class="recipe-meta">
                            <span><i class="fa-regular fa-clock me-1"></i> 25 mins</span>
                            <span><i class="fa-solid fa-fire me-1"></i> 350 kcal</span>
                        </div>
                        <div class="recipe-footer">
                            <div class="author-info">
                                <img src="<?php echo $path_prefix; ?>public/img/dad.png" alt="User">
                                <span>Sarah Miller</span>
                            </div>
                            <a href="recipe-details.php" class="text-primary fw-bold text-decoration-none small">View <i class="fa-solid fa-chevron-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipe Card 2 -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="recipe-card">
                    <div class="recipe-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&q=80&w=800" alt="Recipe">
                        <div class="recipe-badge-group">
                            <span class="recipe-badge difficulty-medium">Medium</span>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h5 class="recipe-title">Garden Fresh Avocado Salad</h5>
                        <div class="recipe-meta">
                            <span><i class="fa-regular fa-clock me-1"></i> 15 mins</span>
                            <span><i class="fa-solid fa-fire me-1"></i> 210 kcal</span>
                        </div>
                        <div class="recipe-footer">
                            <div class="author-info">
                                <img src="<?php echo $path_prefix; ?>public/img/mom.png" alt="User">
                                <span>Mike Chef</span>
                            </div>
                            <a href="recipe-details.php" class="text-primary fw-bold text-decoration-none small">View <i class="fa-solid fa-chevron-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipe Card 3 -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="recipe-card">
                    <div class="recipe-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&q=80&w=800" alt="Recipe">
                        <div class="recipe-badge-group">
                            <span class="recipe-badge difficulty-easy">Easy</span>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h5 class="recipe-title">Fluffy Blueberry Pancakes</h5>
                        <div class="recipe-meta">
                            <span><i class="fa-regular fa-clock me-1"></i> 20 mins</span>
                            <span><i class="fa-solid fa-fire me-1"></i> 420 kcal</span>
                        </div>
                        <div class="recipe-footer">
                            <div class="author-info">
                                <img src="<?php echo $path_prefix; ?>public/img/emma.png" alt="User">
                                <span>Emma Watson</span>
                            </div>
                            <a href="recipe-details.php" class="text-primary fw-bold text-decoration-none small">View <i class="fa-solid fa-chevron-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipe Card 4 -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="recipe-card">
                    <div class="recipe-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1551183053-bf91a1d81141?auto=format&fit=crop&q=80&w=800" alt="Recipe">
                        <div class="recipe-badge-group">
                            <span class="recipe-badge difficulty-hard">Hard</span>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h5 class="recipe-title">Traditional Beef Lasagna</h5>
                        <div class="recipe-meta">
                            <span><i class="fa-regular fa-clock me-1"></i> 90 mins</span>
                            <span><i class="fa-solid fa-fire me-1"></i> 650 kcal</span>
                        </div>
                        <div class="recipe-footer">
                            <div class="author-info">
                                <img src="<?php echo $path_prefix; ?>public/img/ava.png" alt="User">
                                <span>John Doe</span>
                            </div>
                            <a href="recipe-details.php" class="text-primary fw-bold text-decoration-none small">View <i class="fa-solid fa-chevron-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipe Card 5 (Private Recipe Example) -->
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="recipe-card">
                    <div class="recipe-image-wrapper">
                        <img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&q=80&w=800" alt="Recipe">
                        <div class="recipe-badge-group">
                            <span class="recipe-badge bg-dark text-white border-0">Private</span>
                        </div>
                    </div>
                    <div class="recipe-content">
                        <h5 class="recipe-title">Secret Family BBQ Ribs</h5>
                        <div class="recipe-footer">
                            <div class="author-info">
                                <img src="<?php echo $path_prefix; ?>public/img/lina.png" alt="User">
                                <span>Uncle Bob</span>
                            </div>
                            <button class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3">Request</button>
                        </div>
                    </div>
                </div>
            </div>

        </div> <!-- End Grid -->

    </div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>
