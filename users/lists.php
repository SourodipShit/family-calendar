<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../classes/UserList.php';

$path_prefix = "../";
$page_title = "My Lists";

include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';
?>

<!-- Page Content -->
<div id="page-content-wrapper" class="flex-grow-1 bg-light">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4" style="max-width: 1400px; margin: 0 auto;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">My Lists</h3>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addListModal">
                <i class="ri-add-line me-1"></i> Add List
            </button>
        </div>

        <div class="row g-4" id="lists-container">
            <!-- Lists will be populated here via JS -->
            <div class="col-12 text-center text-muted" id="loading-lists">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add List Modal -->
<div class="modal fade" id="addListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Add New List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addListForm">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold">List Name</label>
                        <input type="text" class="form-control" name="name" required placeholder="E.g., Groceries, Todo...">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addList()">Create List</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit List Modal -->
<div class="modal fade" id="editListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Edit List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editListForm">
                    <input type="hidden" name="id" id="edit_list_id">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold">List Name</label>
                        <input type="text" class="form-control" name="name" id="edit_list_name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateList()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'components/footer.php'; ?>

<script>
    $(document).ready(function() {
        loadLists();
    });

    function loadLists() {
        $.ajax({
            url: '../api/lists.php',
            type: 'GET',
            data: { action: 'GetByUserId' },
            dataType: 'json',
            success: function(response) {
                $('#loading-lists').hide();
                const container = $('#lists-container');
                container.find('.list-card-wrapper').remove();

                if (response.length === 0) {
                    container.append(`
                        <div class="col-12 text-center text-muted mt-5 list-card-wrapper">
                            <i class="ri-list-check-2 fs-1"></i>
                            <p class="mt-2">You don't have any lists yet. Create one!</p>
                        </div>
                    `);
                    return;
                }

                response.forEach(list => {
                    let itemsHtml = '';
                    if (list.items && list.items.length > 0) {
                        list.items.forEach(item => {
                            let isChecked = item.is_checked == 1 ? 'checked' : '';
                            let textClass = item.is_checked == 1 ? 'text-decoration-line-through text-muted' : 'text-dark';
                            
                            itemsHtml += `
                                <div class="d-flex align-items-center mb-2 item-row" data-id="${item.id}">
                                    <input class="form-check-input me-2 mt-0" type="checkbox" ${isChecked} onchange="toggleItemCheck(${item.id}, this.checked)">
                                    <div class="flex-grow-1 edit-item-container">
                                        <span class="item-text ${textClass}" style="cursor:pointer;" onclick="editItemMode(this, ${item.id}, '${item.content.replace(/'/g, "\\'")}')">${item.content}</span>
                                    </div>
                                    <button class="btn btn-sm text-danger border-0 px-2" onclick="deleteItem(${item.id})"><i class="ri-close-line"></i></button>
                                </div>
                            `;
                        });
                    } else {
                        itemsHtml = `<p class="text-muted small text-center my-3">No items in this list.</p>`;
                    }

                    let cardHtml = `
                        <div class="col-md-6 col-lg-4 list-card-wrapper">
                            <div class="card h-100 border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0 text-dark">${list.name}</h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li><a class="dropdown-item" href="#" onclick="openEditListModal(${list.id}, '${list.name.replace(/'/g, "\\'")}')"><i class="ri-pencil-line me-2"></i> Edit List</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteList(${list.id})"><i class="ri-delete-bin-line me-2"></i> Delete List</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="list-items-container mb-3" style="max-height: 250px; overflow-y: auto;">
                                        ${itemsHtml}
                                    </div>
                                    <div class="mt-auto">
                                        <form class="d-flex" onsubmit="addItem(event, ${list.id}, this)">
                                            <input type="text" class="form-control form-control-sm me-2 rounded-pill bg-light border-0 px-3" name="content" placeholder="Add an item..." required>
                                            <button type="submit" class="btn btn-sm btn-primary rounded-circle"><i class="ri-add-line"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(cardHtml);
                });
            },
            error: function() {
                toastr.error('Failed to load lists');
            }
        });
    }

    function addList() {
        let form = $('#addListForm');
        let name = form.find('input[name="name"]').val();
        
        if (!name.trim()) {
            toastr.error('List name is required');
            return;
        }

        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: 'addList', name: name },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    $('#addListModal').modal('hide');
                    form[0].reset();
                    loadLists();
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }

    function openEditListModal(id, name) {
        $('#edit_list_id').val(id);
        $('#edit_list_name').val(name);
        $('#editListModal').modal('show');
    }

    function updateList() {
        let id = $('#edit_list_id').val();
        let name = $('#edit_list_name').val();

        if (!name.trim()) {
            toastr.error('List name is required');
            return;
        }

        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: 'editList', id: id, name: name },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    $('#editListModal').modal('hide');
                    loadLists();
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }

    function deleteList(id) {
        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: 'deleteList', id: id },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    loadLists();
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }

    function addItem(e, listId, formElement) {
        e.preventDefault();
        let content = $(formElement).find('input[name="content"]').val();
        
        if (!content.trim()) return;

        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: 'addItem', list_id: listId, content: content },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    $(formElement)[0].reset();
                    loadLists(); // Can be optimized to append only
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }

    function toggleItemCheck(id, isChecked) {
        let action = isChecked ? 'CheckItem' : 'uncheckItem';
        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: action, id: id },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    loadLists(); // Refresh to update styles
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }

    function deleteItem(id) {
        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: 'deleteItem', id: id },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    loadLists();
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }

    function editItemMode(element, id, content) {
        let container = $(element).parent();
        container.html(`
            <form onsubmit="saveItemEdit(event, ${id}, this)" class="d-flex w-100">
                <input type="text" class="form-control form-control-sm me-1" name="content" value="${content}">
                <button type="submit" class="btn btn-sm btn-success px-2"><i class="ri-check-line"></i></button>
                <button type="button" class="btn btn-sm btn-light px-2 ms-1" onclick="loadLists()"><i class="ri-close-line"></i></button>
            </form>
        `);
        container.find('input').focus();
    }

    function saveItemEdit(e, id, formElement) {
        e.preventDefault();
        let content = $(formElement).find('input[name="content"]').val();
        
        if (!content.trim()) return;

        $.ajax({
            url: '../api/lists.php',
            type: 'POST',
            data: { action: 'editItem', id: id, content: content },
            dataType: 'json',
            success: function(res) {
                if(res.status === 'success') {
                    loadLists();
                } else {
                    toastr.error(res.message);
                }
            }
        });
    }
</script>
</body>
</html>
