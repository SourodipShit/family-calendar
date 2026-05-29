<?php
$path_prefix = "../";
$page_title = "Manage Recipes";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Recipe.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Recipe Management</h4>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="recipeSearch" class="form-control bg-light border-0" placeholder="Search recipes...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="recipesTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">Recipe Name</th>
                                <th class="border-0">Author</th>
                                <th class="border-0">Category</th>
                                <th class="border-0">Difficulty</th>
                                <th class="border-0">Visibility</th>
                                <th class="border-0">Status</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recipesResponse = Recipe::getAllRecipesAdmin();
                            $recipes = [];
                            if ($recipesResponse['status'] === 'success' && isset($recipesResponse['data'])) {
                                $recipes = $recipesResponse['data'];
                            }

                            foreach ($recipes as $recipe):
                                $statusBadgeClass = 'secondary';
                                if ($recipe['status'] === 'approved') {
                                    $statusBadgeClass = 'success';
                                } elseif ($recipe['status'] === 'rejected') {
                                    $statusBadgeClass = 'danger';
                                } elseif ($recipe['status'] === 'pending') {
                                    $statusBadgeClass = 'warning';
                                }
                            ?>
                            <tr id="recipe-row-<?php echo $recipe['id']; ?>">
                                <td class="ps-4 fw-bold"><?php echo htmlspecialchars($recipe['name']); ?></td>
                                <td class="small">
                                    <?php echo htmlspecialchars($recipe['user_name'] ?? 'Unknown'); ?><br>
                                    <span class="text-muted" style="font-size: 0.8em;"><?php echo htmlspecialchars($recipe['family_name'] ?? ''); ?></span>
                                </td>
                                <td class="small"><?php echo htmlspecialchars($recipe['category']); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars(ucfirst($recipe['difficulty'])); ?></td>
                                <td class="small text-muted"><?php echo htmlspecialchars(ucfirst($recipe['visibility'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $statusBadgeClass; ?>"><?php echo htmlspecialchars(ucfirst($recipe['status'])); ?></span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown"><i class="ri-more-2-fill"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="#" onclick="viewRecipeModal(<?php echo $recipe['id']; ?>); return false;"><i class="ri-eye-line me-2"></i>View Recipe</a></li>
                                            <?php if ($recipe['status'] !== 'approved'): ?>
                                            <li>
                                                <a class="dropdown-item text-success" href="#" onclick="updateRecipeStatus(<?php echo $recipe['id']; ?>, 'approve'); return false;">
                                                    <i class="ri-check-line me-2"></i>Approve
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php if ($recipe['status'] !== 'rejected'): ?>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="updateRecipeStatus(<?php echo $recipe['id']; ?>, 'reject'); return false;">
                                                    <i class="ri-close-line me-2"></i>Reject
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recipe Modal -->
<div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="recipeModalLabel">Recipe Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="recipeModalBody">
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    const table = $('#recipesTable').DataTable({
        "dom": '<"d-none"f>rt<"d-flex justify-content-between align-items-center p-3"ip>',
        "pageLength": 10,
        "language": {
            "search": "",
            "paginate": {
                "next": '<i class="ri-arrow-right-s-line"></i>',
                "previous": '<i class="ri-arrow-left-s-line"></i>'
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": 6 } // Disable ordering on Actions column
        ],
        "order": [[ 5, "desc" ]] // Optional: adjust default ordering
    });

    // Custom search logic
    $('#recipeSearch').on('keyup', function() {
        table.search(this.value).draw();
    });
});

function updateRecipeStatus(id, action) {
    let actionText = action === 'approve' ? 'approve' : 'reject';
    if(confirm(`Are you sure you want to ${actionText} this recipe?`)) {
        fetch(`../api/admin/recipe.php?action=${action}&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                if(typeof showAlert === 'function') {
                    showAlert(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                if(typeof showAlert === 'function') {
                    showAlert(data.message, 'error');
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert(`An error occurred while trying to ${actionText} the recipe.`);
        });
    }
}

function viewRecipeModal(id) {
    const modalBody = document.getElementById('recipeModalBody');
    modalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    const myModal = new bootstrap.Modal(document.getElementById('recipeModal'));
    myModal.show();

    fetch(`../api/admin/recipe.php?action=get&id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data) {
                const recipe = data.data;
                
                // Construct HTML based on retrieved data
                let html = `
                    <div class="row mb-3">
                        ${recipe.image_url ? `<div class="col-md-5 mb-3 mb-md-0"><img src="${'../' + recipe.image_url.replace('../', '')}" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; max-height: 250px;" alt="Recipe Image"></div>` : ''}
                        <div class="col-md-${recipe.image_url ? '7' : '12'}">
                            <h3 class="fw-bold text-primary mb-2">${recipe.name}</h3>
                            <p class="mb-2"><i class="ri-user-line me-1 text-muted"></i> <strong>Author:</strong> ${recipe.user_name || 'Unknown'}</p>
                            <p class="mb-2"><i class="ri-folder-2-line me-1 text-muted"></i> <strong>Category:</strong> ${recipe.category} | <strong>Cuisine:</strong> ${recipe.cuisine || 'N/A'}</p>
                            
                            <div class="d-flex gap-3 mt-3 flex-wrap">
                                <span class="badge bg-light text-dark border p-2"><i class="ri-time-line me-1"></i> Prep: ${recipe.prep_time_min}m</span>
                                <span class="badge bg-light text-dark border p-2"><i class="ri-fire-line me-1"></i> Cook: ${recipe.cook_time_min}m</span>
                                <span class="badge bg-light text-dark border p-2"><i class="ri-group-line me-1"></i> Serves: ${recipe.servings}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-5 mb-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Ingredients</h5>
                            <ul class="list-group list-group-flush bg-transparent">
                                ${recipe.ingredients && recipe.ingredients.length > 0 ? 
                                    recipe.ingredients.map(ing => `
                                        <li class="list-group-item bg-transparent px-0 py-2 border-bottom border-light">
                                            <strong>${ing.quantity || ''} ${ing.unit || ''} ${ing.ingredient_name} </strong>
                                            ${ing.prep_notes ? `<br><small class="text-muted fst-italic">(${ing.prep_notes})</small>` : ''}
                                        </li>`).join('') 
                                    : '<li class="list-group-item bg-transparent px-0 text-muted">No ingredients specified.</li>'
                                }
                            </ul>
                        </div>
                        <div class="col-md-7 mb-4">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Instructions</h5>
                            <ol class="list-group list-group-numbered list-group-flush bg-transparent">
                                ${recipe.steps && recipe.steps.length > 0 ? 
                                    recipe.steps.map(step => `
                                        <li class="list-group-item bg-transparent px-0 py-2 border-0 d-flex align-items-start">
                                            <div class="ms-2 ms-md-3 flex-grow-1">${step.instruction}</div>
                                        </li>`).join('')
                                    : '<li class="list-group-item bg-transparent px-0 border-0 text-muted">No instructions specified.</li>'
                                }
                            </ol>
                        </div>
                    </div>
                `;
                
                if (recipe.variations_notes) {
                    html += `
                        <div class="mt-2 bg-light p-3 rounded border">
                            <h6 class="fw-bold text-secondary mb-2"><i class="ri-sticky-note-line me-1"></i> Notes & Variations</h6>
                            <p class="mb-0 small">${recipe.variations_notes.replace(/\n/g, '<br>')}</p>
                        </div>
                    `;
                }
                
                modalBody.innerHTML = html;
            } else {
                modalBody.innerHTML = `<div class="alert alert-danger">${data.message || 'Failed to load recipe details.'}</div>`;
            }
        })
        .catch(err => {
            console.error(err);
            modalBody.innerHTML = `<div class="alert alert-danger">An error occurred while loading the recipe.</div>`;
        });
}
</script>
