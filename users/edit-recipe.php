<?php
$path_prefix = "../";
$page_title = "Edit Recipe";

include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';

require_once $path_prefix . 'classes/Recipe.php';
require_once $path_prefix . 'classes/File.php';

$recipeIdToEdit = $_GET['id'] ?? null;
if (!$recipeIdToEdit) {
    echo "<script>window.location.href='recipes.php';</script>";
    exit;
}

$recipeFetchResult = Recipe::getById($recipeIdToEdit);
if ($recipeFetchResult['status'] === 'error') {
    echo "<script>alert('Recipe not found'); window.location.href='recipes.php';</script>";
    exit;
}
$currentRecipe = $recipeFetchResult['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $recipeData = [
            'name' => $_POST['name'],
            'category' => $_POST['category'],
            'cuisine' => $_POST['cuisine'],
            'servings' => $_POST['servings'],
            'prep_time_min' => $_POST['prep_time_min'],
            'cook_time_min' => $_POST['cook_time_min'],
            'difficulty' => $_POST['difficulty'],
            'visibility' => $_POST['visibility'],
            'variations_notes' => $_POST['variations_notes'],
            'status' => 'pending'
        ];

        $ingredients = [];
        if (isset($_POST['ingredient_name'])) {
            foreach ($_POST['ingredient_name'] as $i => $name) {
                if (!empty($name)) {
                    $ingredients[] = [
                        'quantity' => $_POST['ingredient_qty'][$i] ?? null,
                        'unit' => $_POST['ingredient_unit'][$i] ?? null,
                        'ingredient_name' => $name,
                        'prep_notes' => $_POST['ingredient_notes'][$i] ?? null
                    ];
                }
            }
        }

        $steps = [];
        if (isset($_POST['instruction'])) {
            foreach ($_POST['instruction'] as $i => $instruction) {
                if (!empty($instruction)) {
                    $steps[] = [
                        'step_number' => $i + 1,
                        'instruction' => $instruction,
                        'equipment' => $_POST['equipment'][$i] ?? null,
                        'temperature' => $_POST['temperature'][$i] ?? null
                    ];
                }
            }
        }

        $stats = [
            'calories' => $_POST['calories'] ?? 0,
            'protein' => $_POST['protein'] ?? 0,
            'carbs' => $_POST['carbs'] ?? 0,
            'fat' => $_POST['fat'] ?? 0
        ];

        $images = [];

        // Main Image using File::upload
        if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = File::upload($_FILES['recipe_image'], 'recipes');
            if ($upload_result['status'] === 'success') {
                $images[] = [
                    'image_path' => str_replace('../', '', $upload_result['filePath']),
                    'is_main' => 1,
                    'sort_order' => 0
                ];
            }
        }

        // Additional Images using File::upload
        if (isset($_FILES['additional_images'])) {
            foreach ($_FILES['additional_images']['tmp_name'] as $i => $tmpName) {
                if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileArray = [
                        'name' => $_FILES['additional_images']['name'][$i],
                        'type' => $_FILES['additional_images']['type'][$i],
                        'tmp_name' => $tmpName,
                        'error' => $_FILES['additional_images']['error'][$i],
                        'size' => $_FILES['additional_images']['size'][$i]
                    ];
                    
                    $upload_result = File::upload($fileArray, 'recipes');
                    if ($upload_result['status'] === 'success') {
                        $images[] = [
                            'image_path' => str_replace('../', '', $upload_result['filePath']),
                            'is_main' => 0,
                            'sort_order' => $i + 1
                        ];
                    }
                }
            }
        }

        $data = [
            'recipe' => $recipeData,
            'ingredients' => $ingredients,
            'steps' => $steps,
            'stats' => $stats,
            'images' => $images
        ];

        $updateSuccess = Recipe::update($recipeIdToEdit, $data);
        if ($updateSuccess) {
            $success_msg = "Recipe updated successfully and is waiting for approval!";
        }
    } catch (Exception $e) {
        $error_msg = $e->getMessage();
    }
}
?>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/recipes.css">

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4">
        <div class="d-flex align-items-center mb-4">
            <a href="recipes.php" class="btn btn-light rounded-circle me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0">Edit Recipe: <?php echo htmlspecialchars($currentRecipe['name'] ?? ''); ?></h4>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <!-- Compact Stepper -->
                        <div class="d-flex justify-content-center gap-4 mb-4 border-bottom pb-3">
                            <div class="step-link active" id="step-btn-1" style="cursor:pointer;">
                                <span class="badge rounded-circle bg-primary me-1">1</span> <span class="small fw-bold">Info</span>
                            </div>
                            <div class="step-link text-muted" id="step-btn-2" style="cursor:pointer;">
                                <span class="badge rounded-circle bg-light text-dark me-1">2</span> <span class="small fw-bold">Steps</span>
                            </div>
                            <div class="step-link text-muted" id="step-btn-3" style="cursor:pointer;">
                                <span class="badge rounded-circle bg-light text-dark me-1">3</span> <span class="small fw-bold">Items</span>
                            </div>
                            <div class="step-link text-muted" id="step-btn-4" style="cursor:pointer;">
                                <span class="badge rounded-circle bg-light text-dark me-1">4</span> <span class="small fw-bold">Stats</span>
                            </div>
                        </div>

                        <form id="add-recipe-form" action="" method="POST" enctype="multipart/form-data">
                            <!-- Step 1: Core Info -->
                            <div class="step-content active" id="step-1">
                                <div class="row g-4">
                                    <div class="col-md-8">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Recipe Name <span class="text-danger">*</span></label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-utensils"></i></span>
                                                    <input type="text" name="name" class="form-control border-0 py-2" placeholder="e.g. Grilled Salmon" value="<?php echo htmlspecialchars($currentRecipe['name'] ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Category</label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-list"></i></span>
                                                    <select name="category" class="form-select border-0 py-2">
                                                        <option value="Breakfast" <?php echo (isset($currentRecipe['category']) && $currentRecipe['category'] == 'Breakfast') ? 'selected' : ''; ?>>Breakfast</option>
                                                        <option value="Lunch" <?php echo (isset($currentRecipe['category']) && $currentRecipe['category'] == 'Lunch') ? 'selected' : ''; ?>>Lunch</option>
                                                        <option value="Dinner" <?php echo (isset($currentRecipe['category']) && $currentRecipe['category'] == 'Dinner') ? 'selected' : ''; ?>>Dinner</option>
                                                        <option value="Snacks" <?php echo (isset($currentRecipe['category']) && $currentRecipe['category'] == 'Snacks') ? 'selected' : ''; ?>>Snacks</option>
                                                        <option value="Dessert" <?php echo (isset($currentRecipe['category']) && $currentRecipe['category'] == 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Cuisine</label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-globe"></i></span>
                                                    <select name="cuisine" class="form-select border-0 py-2">
                                                        <option value="Italian" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'Italian') ? 'selected' : ''; ?>>Italian</option>
                                                        <option value="Mexican" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'Mexican') ? 'selected' : ''; ?>>Mexican</option>
                                                        <option value="American" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'American') ? 'selected' : ''; ?>>American</option>
                                                        <option value="Indian" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'Indian') ? 'selected' : ''; ?>>Indian</option>
                                                        <option value="Chinese" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'Chinese') ? 'selected' : ''; ?>>Chinese</option>
                                                        <option value="French" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'French') ? 'selected' : ''; ?>>French</option>
                                                        <option value="Other" <?php echo (isset($currentRecipe['cuisine']) && $currentRecipe['cuisine'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Difficulty</label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-gauge-high"></i></span>
                                                    <select name="difficulty" class="form-select border-0 py-2">
                                                        <option value="Easy" <?php echo (isset($currentRecipe['difficulty']) && $currentRecipe['difficulty'] == 'Easy') ? 'selected' : ''; ?>>Easy</option>
                                                        <option value="Medium" <?php echo (isset($currentRecipe['difficulty']) && $currentRecipe['difficulty'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                                        <option value="Hard" <?php echo (isset($currentRecipe['difficulty']) && $currentRecipe['difficulty'] == 'Hard') ? 'selected' : ''; ?>>Hard</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Servings</label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-users"></i></span>
                                                    <input type="number" name="servings" class="form-control border-0 py-2" placeholder="4" value="<?php echo htmlspecialchars($currentRecipe['servings'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Prep (Min)</label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-clock"></i></span>
                                                    <input type="number" name="prep_time_min" class="form-control border-0 py-2" placeholder="10" value="<?php echo htmlspecialchars($currentRecipe['prep_time_min'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold">Cook (Min)</label>
                                                <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                                    <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-fire"></i></span>
                                                    <input type="number" name="cook_time_min" class="form-control border-0 py-2" placeholder="20" value="<?php echo htmlspecialchars($currentRecipe['cook_time_min'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Visibility</label>
                                                <div class="btn-group w-100 shadow-sm rounded-3 overflow-hidden border" role="group">
                                                    <input type="radio" class="btn-check" name="visibility" id="vis-public" value="public" autocomplete="off" <?php echo (isset($currentRecipe['visibility']) && $currentRecipe['visibility'] == 'public') ? 'checked' : ''; ?>>
                                                    <label class="btn btn-outline-primary py-2 px-1 fw-semibold small d-flex align-items-center justify-content-center flex-fill" for="vis-public">
                                                        <i class="fa-solid fa-globe me-2"></i> Public
                                                    </label>
                                                    <input type="radio" class="btn-check" name="visibility" id="vis-private" value="private" autocomplete="off" <?php echo (!isset($currentRecipe['visibility']) || $currentRecipe['visibility'] == 'private') ? 'checked' : ''; ?>>
                                                    <label class="btn btn-outline-primary py-2 px-1 fw-semibold small d-flex align-items-center justify-content-center flex-fill" for="vis-private">
                                                        <i class="fa-solid fa-lock me-2"></i> Private
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-12 mt-2">
                                                <label class="form-label fw-semibold d-block">Additional Photos <small class="text-muted fw-normal">(Max 5, < 1MB each)</small></label>
                                                <div class="d-flex flex-wrap gap-2" id="additional-images-container">
                                                    <?php
                                                    $additionalImages = array_filter($currentRecipe['images'] ?? [], function($img) { return $img['is_main'] == 0; });
                                                    foreach ($additionalImages as $img):
                                                    ?>
                                                    <div class="position-relative" style="width: 80px; height: 80px;">
                                                        <img src="<?php echo $path_prefix . htmlspecialchars($img['image_path']); ?>" class="w-100 h-100 object-fit-cover rounded-3 border shadow-sm">
                                                    </div>
                                                    <?php endforeach; ?>
                                                    <div class="upload-box border rounded-3 d-flex align-items-center justify-content-center bg-light cursor-pointer shadow-sm <?php echo (count($additionalImages) >= 5) ? 'd-none' : ''; ?>" style="width: 80px; height: 80px; border: 2px dashed #dee2e6 !important;" onclick="document.getElementById('additional-images-input').click();">
                                                        <i class="fa-solid fa-plus text-muted"></i>
                                                    </div>
                                                    <input type="file" id="additional-images-input" name="additional_images[]" class="d-none" accept="image/*" multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Main Recipe Image</label>
                                        <div class="border rounded-4 d-flex flex-column align-items-center justify-content-center p-3 bg-light cursor-pointer text-center shadow-sm" style="border: 2px dashed #dee2e6 !important; height: 200px;" onclick="document.getElementById('imageInput').click();">
                                            <i class="fa-solid fa-camera text-secondary display-6 mb-2 <?php echo !empty($currentRecipe['image_url']) ? 'd-none' : ''; ?>"></i>
                                            <p class="mb-0 small fw-medium text-muted <?php echo !empty($currentRecipe['image_url']) ? 'd-none' : ''; ?>">Upload Cover Photo</p>
                                            <input type="file" id="imageInput" name="recipe_image" class="d-none" accept="image/*">
                                            <img id="recipe-preview" src="<?php echo !empty($currentRecipe['image_url']) ? $path_prefix . htmlspecialchars($currentRecipe['image_url']) : '#'; ?>" alt="Preview" class="<?php echo !empty($currentRecipe['image_url']) ? '' : 'd-none'; ?> w-100 h-100 object-fit-cover rounded-3" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Step 2: Instructions -->
                            <div class="step-content" id="step-2">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Preparation Steps</h5>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" id="add-step-btn">Add Step</button>
                                </div>
                                <div id="steps-container">
                                    <?php
                                    $stepsData = $currentRecipe['steps'] ?? [['instruction' => '', 'equipment' => '', 'temperature' => '']];
                                    if (empty($stepsData)) $stepsData = [['instruction' => '', 'equipment' => '', 'temperature' => '']];
                                    foreach ($stepsData as $index => $step): 
                                    ?>
                                    <div class="dynamic-row border rounded-4 p-3 mb-3 shadow-sm bg-light step-item-row">
                                        <div class="d-flex gap-3 align-items-start mb-3">
                                            <span class="badge rounded-circle bg-primary p-2 step-number" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><?php echo $index + 1; ?></span>
                                            <textarea name="instruction[]" class="form-control border-0 bg-transparent py-0" rows="2" placeholder="Describe this step in detail..." required><?php echo htmlspecialchars($step['instruction']); ?></textarea>
                                            <button type="button" class="btn btn-link text-danger p-0 remove-step-btn"><i class="fa-solid fa-trash-can"></i></button>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm border rounded-2 bg-white">
                                                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fa-solid fa-blender"></i></span>
                                                    <input type="text" name="equipment[]" class="form-control border-0" placeholder="Equipment (e.g. Pan)" value="<?php echo htmlspecialchars($step['equipment'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-sm border rounded-2 bg-white">
                                                    <span class="input-group-text bg-transparent border-0 text-muted"><i class="fa-solid fa-temperature-half"></i></span>
                                                    <input type="text" name="temperature[]" class="form-control border-0" placeholder="Temp (e.g. 350°F)" value="<?php echo htmlspecialchars($step['temperature'] ?? ''); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-white border border-dashed w-100 rounded-3 py-2 text-muted fw-medium mt-2" id="add-step-alt-btn">
                                    <i class="fa-solid fa-plus me-2"></i>Add Another Step
                                </button>
                            </div>
                            <!-- Step 3: Ingredients -->
                            <div class="step-content" id="step-3">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Ingredients</h5>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" id="add-ingredient-btn">Add Ingredient</button>
                                </div>
                                <div id="ingredients-container">
                                    <?php
                                    $ingredientsData = $currentRecipe['ingredients'] ?? [['quantity' => '', 'unit' => '', 'ingredient_name' => '', 'prep_notes' => '']];
                                    if (empty($ingredientsData)) $ingredientsData = [['quantity' => '', 'unit' => '', 'ingredient_name' => '', 'prep_notes' => '']];
                                    foreach ($ingredientsData as $ingredient):
                                    ?>
                                    <div class="row g-2 mb-3 align-items-end ingredient-item-row">
                                        <div class="col-md-2">
                                            <label class="form-label extra-small fw-semibold text-muted mb-1">Qty</label>
                                            <input type="text" name="ingredient_qty[]" class="form-control form-control-sm border rounded-2 shadow-sm" placeholder="2" value="<?php echo htmlspecialchars($ingredient['quantity'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label extra-small fw-semibold text-muted mb-1">Unit</label>
                                            <input type="text" name="ingredient_unit[]" class="form-control form-control-sm border rounded-2 shadow-sm" placeholder="pcs" value="<?php echo htmlspecialchars($ingredient['unit'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label extra-small fw-semibold text-muted mb-1">Ingredient Name</label>
                                            <input type="text" name="ingredient_name[]" class="form-control form-control-sm border rounded-2 shadow-sm" placeholder="Fresh Salmon" value="<?php echo htmlspecialchars($ingredient['ingredient_name'] ?? ''); ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label extra-small fw-semibold text-muted mb-1">Notes</label>
                                            <input type="text" name="ingredient_notes[]" class="form-control form-control-sm border rounded-2 shadow-sm" placeholder="Skin on" value="<?php echo htmlspecialchars($ingredient['prep_notes'] ?? ''); ?>">
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button type="button" class="btn btn-link text-danger p-0 mb-1 remove-ingredient-btn"><i class="fa-solid fa-trash-can"></i></button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-white border border-dashed w-100 rounded-3 py-2 text-muted fw-medium mt-2" id="add-ingredient-alt-btn">
                                    <i class="fa-solid fa-plus me-2"></i>Add Ingredient
                                </button>
                            </div>
                            <!-- Step 4: Nutrition -->
                            <div class="step-content" id="step-4">
                                <h5 class="fw-bold mb-4">Nutritional Info & Notes</h5>
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3">
                                        <div class="bg-light rounded-4 p-3 text-center border shadow-sm">
                                            <label class="extra-small fw-bold text-primary mb-1 d-block">CALORIES</label>
                                            <input type="number" step="0.1" name="calories" class="form-control border-0 bg-transparent text-center fw-bold fs-4 p-0" value="<?php echo htmlspecialchars($currentRecipe['nutrition']['calories'] ?? '0'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bg-light rounded-4 p-3 text-center border shadow-sm">
                                            <label class="extra-small fw-bold text-success mb-1 d-block">PROTEIN (g)</label>
                                            <input type="number" step="0.1" name="protein" class="form-control border-0 bg-transparent text-center fw-bold fs-4 p-0" value="<?php echo htmlspecialchars($currentRecipe['nutrition']['protein'] ?? '0'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bg-light rounded-4 p-3 text-center border shadow-sm">
                                            <label class="extra-small fw-bold text-warning mb-1 d-block">CARBS (g)</label>
                                            <input type="number" step="0.1" name="carbs" class="form-control border-0 bg-transparent text-center fw-bold fs-4 p-0" value="<?php echo htmlspecialchars($currentRecipe['nutrition']['carbs'] ?? '0'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="bg-light rounded-4 p-3 text-center border shadow-sm">
                                            <label class="extra-small fw-bold text-danger mb-1 d-block">FAT (g)</label>
                                            <input type="number" step="0.1" name="fat" class="form-control border-0 bg-transparent text-center fw-bold fs-4 p-0" value="<?php echo htmlspecialchars($currentRecipe['nutrition']['fat'] ?? '0'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <label class="form-label fw-semibold">Chef's Notes / Variations</label>
                                <div class="input-group border rounded-4 overflow-hidden shadow-sm mb-4">
                                    <textarea name="variations_notes" class="form-control border-0 py-3" rows="4" placeholder="Share tips, variations, or serving suggestions..."><?php echo htmlspecialchars($currentRecipe['variations_notes'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-3 mt-4 border-top pt-4">
                                <button type="button" id="prev-btn" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark" style="visibility: hidden;">Back</button>
                                <button type="button" id="next-btn" class="btn btn-primary px-5 py-2 fw-medium rounded-3 shadow-sm">Next Step</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Helper Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-circle-info me-2"></i> Creating Recipes</h5>
                        <p class="text-dark opacity-75 small">Adding recipes helps you plan your family meals more efficiently and build a personal cookbook.</p>
                        <ul class="text-dark opacity-75 small ps-3">
                            <li>Fill in the core info to make your recipe searchable.</li>
                            <li>Add clear, step-by-step instructions for others to follow.</li>
                            <li>Include accurate measurements for the best results.</li>
                            <li>Nutrition info is optional but helpful for meal planning.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($error_msg)): ?>
        showAlert(<?php echo json_encode($error_msg); ?>, "error");
    <?php endif; ?>

    <?php if (isset($success_msg)): ?>
        showAlert(<?php echo json_encode($success_msg); ?>, "success");
        setTimeout(() => window.location.href = 'recipes.php', 2000);
    <?php endif; ?>

    let currentStep = 1;
    const totalSteps = 4;
    const nextBtn = document.getElementById('next-btn');
    const prevBtn = document.getElementById('prev-btn');
    const recipeForm = document.getElementById('add-recipe-form');

    // Image Preview Logic
    const imageInput = document.getElementById('imageInput');
    const recipePreview = document.getElementById('recipe-preview');
    const uploadPlaceholder = imageInput.parentElement.querySelector('i');
    const uploadText = imageInput.parentElement.querySelector('p');

    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {

            const reader = new FileReader();
            reader.onload = function(e) {
                recipePreview.src = e.target.result;
                recipePreview.classList.remove('d-none');
                if (uploadPlaceholder) uploadPlaceholder.classList.add('d-none');
                if (uploadText) uploadText.classList.add('d-none');
            }
            reader.readAsDataURL(file);
        }
    });

    // Additional Images Logic
    const additionalImagesInput = document.getElementById('additional-images-input');
    const additionalImagesContainer = document.getElementById('additional-images-container');
    const existingImagesCount = <?php echo isset($additionalImages) ? count($additionalImages) : 0; ?>;
    const MAX_ADDITIONAL_IMAGES = 5 - existingImagesCount;
    let selectedFiles = [];

    window.removeAdditionalImage = function(btn, fileName) {
        const wrapper = btn.parentElement;
        wrapper.remove();
        selectedFiles = selectedFiles.filter(f => f.name !== fileName);
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        additionalImagesInput.files = dataTransfer.files;
        if (selectedFiles.length < MAX_ADDITIONAL_IMAGES) {
            additionalImagesContainer.querySelector('.upload-box').classList.remove('d-none');
        }
    };

    additionalImagesInput.addEventListener('change', function(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => {
            if (selectedFiles.length >= MAX_ADDITIONAL_IMAGES) return;

            selectedFiles.push(file);
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative';
                wrapper.style.width = '80px';
                wrapper.style.height = '80px';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" class="w-100 h-100 object-fit-cover rounded-3 border shadow-sm">
                    <button type="button" class="btn btn-danger rounded-circle position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; transform: translate(30%, -30%);" onclick="removeAdditionalImage(this, '${file.name}')">
                        <i class="fa-solid fa-xmark" style="font-size: 10px;"></i>
                    </button>`;
                additionalImagesContainer.insertBefore(wrapper, additionalImagesContainer.firstChild);
                if (selectedFiles.length >= MAX_ADDITIONAL_IMAGES) {
                    additionalImagesContainer.querySelector('.upload-box').classList.add('d-none');
                }
            }
            reader.readAsDataURL(file);
        });
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        this.files = dataTransfer.files;
    });

    function updateStepper() {
        document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
        document.getElementById(`step-${currentStep}`).classList.add('active');
        document.querySelectorAll('.step-link').forEach((el, index) => {
            const badge = el.querySelector('.badge');
            if (index + 1 === currentStep) {
                el.classList.add('active', 'text-primary');
                el.classList.remove('text-muted');
                badge.classList.replace('bg-light', 'bg-primary');
                badge.classList.replace('text-dark', 'text-white');
            } else {
                el.classList.remove('active', 'text-primary');
                el.classList.add('text-muted');
                badge.classList.replace('bg-primary', 'bg-light');
                badge.classList.replace('text-white', 'text-dark');
            }
        });
        prevBtn.style.visibility = (currentStep === 1) ? 'hidden' : 'visible';
        nextBtn.innerText = (currentStep === totalSteps) ? 'Save Recipe' : 'Next Step';
        if(currentStep === totalSteps) {
            nextBtn.classList.replace('btn-primary', 'btn-success');
        } else {
            nextBtn.classList.replace('btn-success', 'btn-primary');
        }
    }

    nextBtn.addEventListener('click', () => {
        if (currentStep < totalSteps) { 
            currentStep++; 
            updateStepper(); 
        } else { 
            recipeForm.submit();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) { 
            currentStep--; 
            updateStepper(); 
        }
    });

    document.querySelectorAll('.step-link').forEach((link, index) => {
        link.addEventListener('click', () => {
            currentStep = index + 1;
            updateStepper();
        });
    });

    // Dynamic Steps Management
    const stepsContainer = document.getElementById('steps-container');
    const addStepBtns = [document.getElementById('add-step-btn'), document.getElementById('add-step-alt-btn')];
    function updateStepNumbers() {
        stepsContainer.querySelectorAll('.step-item-row').forEach((row, index) => {
            row.querySelector('.step-number').innerText = index + 1;
        });
    }
    addStepBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const newRow = stepsContainer.querySelector('.step-item-row').cloneNode(true);
            newRow.querySelectorAll('input, textarea').forEach(input => input.value = '');
            stepsContainer.appendChild(newRow);
            updateStepNumbers();
            newRow.querySelector('.remove-step-btn').addEventListener('click', function() {
                if (stepsContainer.querySelectorAll('.step-item-row').length > 1) {
                    newRow.remove();
                    updateStepNumbers();
                }
            });
        });
    });
    stepsContainer.querySelector('.remove-step-btn').addEventListener('click', function() {
        if (stepsContainer.querySelectorAll('.step-item-row').length > 1) {
            this.closest('.step-item-row').remove();
            updateStepNumbers();
        }
    });

    // Dynamic Ingredients Management
    const ingredientsContainer = document.getElementById('ingredients-container');
    const addIngBtns = [document.getElementById('add-ingredient-btn'), document.getElementById('add-ingredient-alt-btn')];
    addIngBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const newRow = ingredientsContainer.querySelector('.ingredient-item-row').cloneNode(true);
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            ingredientsContainer.appendChild(newRow);
            newRow.querySelector('.remove-ingredient-btn').addEventListener('click', function() {
                if (ingredientsContainer.querySelectorAll('.ingredient-item-row').length > 1) {
                    newRow.remove();
                }
            });
        });
    });
    ingredientsContainer.querySelector('.remove-ingredient-btn').addEventListener('click', function() {
        if (ingredientsContainer.querySelectorAll('.ingredient-item-row').length > 1) {
            this.closest('.ingredient-item-row').remove();
        }
    });
});
</script>

<?php include $path_prefix . 'components/footer.php'; ?>
