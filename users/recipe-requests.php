<?php
$path_prefix = "../";
$page_title = "Recipe Access Requests";
include $path_prefix . 'components/header.php';
?>
<link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/recipes.css">

<?php include $path_prefix . 'components/sidebar.php'; ?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid p-4">
        
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Access Requests</h2>
                <p class="text-muted small mb-0">Manage requests for your private recipes</p>
            </div>
            <a href="recipes.php" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to Recipes
            </a>
        </div>

        <!-- Requests List -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0">Recipe</th>
                                <th class="py-3 border-0">Requester Family</th>
                                <th class="py-3 border-0">Date</th>
                                <th class="py-3 border-0 text-center">Status</th>
                                <th class="pe-4 py-3 border-0 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requests-list">
                            <!-- Requests will be loaded here via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="no-requests" class="text-center py-5 d-none">
            <div class="mb-3">
                <i class="fa-solid fa-envelope-open text-light display-1"></i>
            </div>
            <h4 class="text-muted">No pending requests</h4>
            <p class="text-muted small">When other families request access to your private recipes, they will appear here.</p>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestsList = document.getElementById('requests-list');
    const noRequests = document.getElementById('no-requests');

    function fetchRequests() {
        fetch('../api/recipe.php?action=getRequestsForFamily&userId=<?= $_SESSION['user']['id'] ?>')
            .then(response => response.json())
            .then(response => {
                if (response.status === 'success' && response.data.length > 0) {
                    requestsList.innerHTML = '';
                    noRequests.classList.add('d-none');
                    response.data.forEach(request => {
                        requestsList.innerHTML += createRequestRow(request);
                    });
                } else {
                    requestsList.innerHTML = '';
                    noRequests.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error fetching requests:', error);
                showAlert('Failed to load requests', 'error');
            });
    }

    function createRequestRow(request) {
        let statusBadge = '';
        let actionButtons = '';

        switch(request.status) {
            case 'pending':
                statusBadge = '<span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill fw-bold">Pending</span>';
                actionButtons = `
                    <button onclick="updateRequest(${request.id}, 'approve')" class="btn btn-sm btn-success rounded-pill px-3 me-2">Approve</button>
                    <button onclick="updateRequest(${request.id}, 'reject')" class="btn btn-sm btn-danger rounded-pill px-3">Reject</button>
                `;
                break;
            case 'approved':
                statusBadge = '<span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">Approved</span>';
                actionButtons = '<span class="text-muted small">No actions available</span>';
                break;
            case 'denied':
                statusBadge = '<span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-bold">Denied</span>';
                actionButtons = '<button onclick="updateRequest(${request.id}, \'approve\')" class="btn btn-sm btn-outline-success rounded-pill px-3">Approve Anyway</button>';
                break;
        }

        const date = new Date(request.requested_at).toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric'
        });

        return `
            <tr>
                <td class="ps-4 py-3 fw-bold text-dark">${request.recipe_name}</td>
                <td class="py-3">${request.requester_family_name}</td>
                <td class="py-3 text-muted small">${date}</td>
                <td class="py-3 text-center">${statusBadge}</td>
                <td class="pe-4 py-3 text-end">${actionButtons}</td>
            </tr>
        `;
    }

    window.updateRequest = function(requestId, action) {
        const formData = new FormData();
        formData.append('requestId', requestId);
        
        const apiAction = action === 'approve' ? 'approveAccess' : 'rejectAccess';

        fetch(`../api/recipe.php?action=${apiAction}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(response => {
            if (response.status === 'success') {
                showAlert(response.message, 'success');
                fetchRequests();
            } else {
                showAlert(response.message || 'Failed to update request', 'error');
            }
        })
        .catch(error => {
            console.error('Error updating request:', error);
            showAlert('Failed to update request', 'error');
        });
    };

    fetchRequests();
});
</script>

<?php include $path_prefix . 'components/footer.php'; ?>
