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
        <script>
            const userFamilyId = <?php echo $_SESSION['user']['families'][0]['family_id'] ?? 0; ?>;
            const currentUserId = <?php echo $_SESSION['user']['id'] ?? 0; ?>;
        </script>

        <!-- Header & Add Button -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="fw-bold mb-1">Recipe Discover</h2>
                <p class="text-muted small mb-0">Explore and share family favorites</p>
            </div>
            <div class="d-flex gap-2">
                <a href="recipe-requests.php" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                    <i class="fa-solid fa-envelope-open-text me-2"></i> Access Requests
                </a>
                <a href="add-recipe.php" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                    <i class="fa-solid fa-plus me-2"></i> Add Recipe
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="search-filter-bar shadow-sm">
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="recipe-search" class="form-control form-control-custom ps-5" placeholder="Search for recipes, ingredients...">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select id="filter-category" class="form-select form-control-custom">
                        <option value="">All Categories</option>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Lunch">Lunch</option>
                        <option value="Dinner">Dinner</option>
                        <option value="Dessert">Dessert</option>
                        <option value="Snacks">Snacks</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select id="filter-difficulty" class="form-select form-control-custom">
                        <option value="">Difficulty: All</option>
                        <option value="Easy">Easy</option>
                        <option value="Medium">Medium</option>
                        <option value="Hard">Hard</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Recipe Grid -->
        <div class="row g-4 mt-2" id="recipe-grid">
            <!-- Recipes will be loaded here via AJAX -->
        </div>

        <!-- Load More -->
        <div class="text-center mt-5 mb-4" id="load-more-container">
            <button id="load-more-btn" class="btn btn-outline-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-spinner fa-spin me-2 d-none"></i> Load More Recipes
            </button>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let offset = 0;
        const count = 8;
        const recipeGrid = document.getElementById('recipe-grid');
        const loadMoreBtn = document.getElementById('load-more-btn');
        const loadMoreSpinner = loadMoreBtn.querySelector('.fa-spinner');
        const searchInput = document.getElementById('recipe-search');
        const categoryFilter = document.getElementById('filter-category');
        const difficultyFilter = document.getElementById('filter-difficulty');

        function fetchRecipes(isNewSearch = false) {
            if (isNewSearch) {
                offset = 0;
                recipeGrid.innerHTML = '';
                loadMoreBtn.parentElement.classList.remove('d-none');
            }

            loadMoreSpinner.classList.remove('d-none');
            loadMoreBtn.disabled = true;

            const filters = {
                search: searchInput.value,
                category: categoryFilter.value,
                difficulty: difficultyFilter.value
            };

            const params = new URLSearchParams({
                action: 'getRecipies',
                count: count,
                offset: offset
            });

            // Add filters to params
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    params.append(`filter[${key}]`, filters[key]);
                }
            });

            fetch(`../api/recipe.php?${params.toString()}`)
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'success' && Array.isArray(response.data)) {
                        const recipes = response.data;
                        if (recipes.length < count) {
                            loadMoreBtn.parentElement.classList.add('d-none');
                        }

                        recipes.forEach(recipe => {
                            recipeGrid.innerHTML += createRecipeCard(recipe);
                        });

                        offset += count;
                    } else {
                        if (offset === 0) {
                            recipeGrid.innerHTML = `
                            <div class="col-12 text-center py-5">
                                <i class="fa-solid fa-utensils display-1 text-light mb-3"></i>
                                <h4 class="text-muted">No recipes found</h4>
                                <p class="text-muted small">Try adjusting your filters or search terms</p>
                            </div>
                        `;
                        }
                        loadMoreBtn.parentElement.classList.add('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error fetching recipes:', error);
                    showAlert('Failed to load recipes', 'error');
                })
                .finally(() => {
                    loadMoreSpinner.classList.add('d-none');
                    loadMoreBtn.disabled = false;
                });
        }

        function createRecipeCard(recipe) {
            const isApproved = recipe.request_status === 'approved';
            const isPending = recipe.request_status === 'pending';
            const isPrivate = recipe.visibility === 'private' && !isApproved;
            const isOwner = recipe.user_id == currentUserId;

            const difficultyClass = `difficulty-${recipe.difficulty.toLowerCase()}`;
            const kcal = recipe.calories ? `${recipe.calories} kcal` : 'N/A';
            const image = recipe.image_url ? `../${recipe.image_url}` : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&q=80&w=800';
            const userImage = recipe.user_image ? (recipe.user_image.startsWith('http') ? recipe.user_image : `../${recipe.user_image.replace('../', '')}`) : 'https://ui-avatars.com/api/?name=' + recipe.user_name.replace(' ', '+');
            const totalTime = parseInt(recipe.prep_time_min || 0) + parseInt(recipe.cook_time_min || 0);

            return `
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="recipe-card shadow-sm border-0 h-100 ${isPrivate ? 'recipe-private' : ''}">
                    <div class="recipe-image-wrapper">
                        <img src="${image}" alt="${recipe.name}" class="w-100 h-100 object-fit-cover">
                        ${!isPrivate ? `
                        <div class="recipe-badge-group">
                            <span class="recipe-badge ${difficultyClass}">${recipe.difficulty}</span>
                            <span class="recipe-badge recipe-category">${recipe.category}</span>
                            ${isApproved && recipe.visibility === 'private' ? `<span class="recipe-badge bg-success text-white">Approved</span>` : ''}
                        </div>
                        ` : `
                        <div class="recipe-badge-group">
                            <span class="recipe-badge bg-dark text-white"><i class="fa-solid fa-lock me-1"></i> Private</span>
                            ${isPending ? `<span class="recipe-badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Pending</span>` : ''}
                        </div>
                        `}
                    </div>
                    <div class="recipe-content p-3">
                        <h5 class="recipe-title fw-bold text-dark mb-2">${recipe.name}</h5>
                        ${!isPrivate ? `
                        <div class="recipe-meta d-flex gap-3 small text-muted mb-3">
                            <span><i class="fa-regular fa-clock me-1"></i> ${totalTime} mins</span>
                            <span><i class="fa-solid fa-fire me-1"></i> ${kcal}</span>
                        </div>
                        ` : `
                        <div class="recipe-meta mb-3">
                            <span class="extra-small text-muted"><i class="fa-solid fa-circle-info me-1"></i> Request access to see full recipe</span>
                        </div>
                        `}
                        <div class="recipe-footer d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                            <div class="author-info d-flex align-items-center gap-2">
                                <img src="${userImage}" alt="${recipe.user_name}" class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                                <span class="small fw-medium text-dark">${recipe.user_name}</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                ${isOwner ? `
                                <a href="edit-recipe.php?id=${recipe.id}" class="text-secondary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <button onclick="deleteRecipe(${recipe.id})" class="btn btn-link p-0 text-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                ` : ''}
                                ${!isPrivate ? `
                                <a href="recipe-details.php?id=${recipe.id}" class="btn btn-link p-0 text-primary fw-bold text-decoration-none small">View <i class="fa-solid fa-chevron-right ms-1"></i></a>
                                ` : `
                                    ${isPending ? `
                                    <button class="btn btn-link p-0 text-muted fw-bold text-decoration-none small" disabled>Requested <i class="fa-solid fa-hourglass-half ms-1"></i></button>
                                    ` : `
                                    <button onclick="requestAccess(${recipe.id})" class="btn btn-link p-0 text-warning fw-bold text-decoration-none small">Request <i class="fa-solid fa-paper-plane ms-1"></i></button>
                                    `}
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        }

        window.requestAccess = function(recipeId) {
            if (!userFamilyId) {
                showAlert('User family not found. Please log in again.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('recipeId', recipeId);
            formData.append('userFamilyId', userFamilyId);

            fetch(`../api/recipe.php?action=requestAccess`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'success') {
                        showAlert(response.message, 'success');
                        fetchRecipes(true); // Refresh grid to show pending status
                    } else {
                        showAlert(response.message || 'Failed to send request', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error sending request:', error);
                    showAlert('Failed to send request', 'error');
                });
        };

        window.deleteRecipe = function(recipeId) {
            if (confirm('Are you sure you want to delete this recipe?')) {
                const formData = new FormData();
                formData.append('recipeId', recipeId);
                
                fetch(`../api/recipe.php?action=deleteRecipe`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'success') {
                        showAlert(response.message, 'success');
                        fetchRecipes(true); // Refresh grid
                    } else {
                        showAlert(response.message || 'Failed to delete recipe', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting recipe:', error);
                    showAlert('Failed to delete recipe', 'error');
                });
            }
        };

        // Initial load
        fetchRecipes();

        // Event listeners
        loadMoreBtn.addEventListener('click', () => fetchRecipes());

        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchRecipes(true), 500);
        });

        categoryFilter.addEventListener('change', () => fetchRecipes(true));
        difficultyFilter.addEventListener('change', () => fetchRecipes(true));
    });
</script>

</div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>