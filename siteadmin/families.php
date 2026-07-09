<?php
$path_prefix = "../";
$page_title = "Manage Families";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Family.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Family Management</h4>
            <a href="add-family.php" class="btn btn-primary"><i class="ri-add-line me-1"></i> Add New Family</a>
        </div>

        <div class="card admin-card border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="ri-search-line"></i></span>
                            <input type="text" id="familySearch" class="form-control bg-light border-0" placeholder="Search families...">
                        </div>
                    </div>
                    <div class="col-md-8 text-md-end">
                        <button class="btn btn-light btn-sm"><i class="ri-filter-line me-1"></i> Filter</button>
                        <button class="btn btn-light btn-sm ms-2"><i class="ri-download-line me-1"></i> Export</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="overflow: visible;">
                    <table id="familiesTable" class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 border-0">Family Name</th>
                                <th class="border-0">Account Number</th>
                                <th class="border-0">Timezone</th>
                                <th class="border-0">Promo Code</th>
                                <th class="border-0">Monthly Amount</th>
                                <th class="border-0">Storage</th>
                                <th class="border-0">Members</th>
                                <th class="border-0">Status</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $families = Family::getAllFamilies();

                            foreach ($families as $family):
                                $members_count = $family['member_count'];
                            ?>
                            <tr id="family-row-<?php echo $family['id']; ?>">
                                <td class="ps-4">
                                    <div class="fw-bold"><?php echo htmlspecialchars(!empty($family['name']) ? $family['name'] : 'N/A'); ?></div>
                                    <div class="small text-muted" style="font-size: 0.8em;">
                                        <?php echo htmlspecialchars(!empty($family['email']) ? $family['email'] : ''); ?>
                                        <?php if (!empty($family['location'])): ?>
                                            &bull; <?php echo htmlspecialchars($family['location']); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="small">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($family['account_number'])): ?>
                                            <a href="bills.php?account=<?php echo htmlspecialchars($family['account_number']); ?>" class="text-primary text-decoration-none fw-medium">
                                                <?php echo htmlspecialchars($family['account_number']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                        
                                        <?php 
                                        $acStatus = $family['account_status'] ?? 'N/A';
                                        $badgeClass = 'secondary';
                                        if ($acStatus === 'active') $badgeClass = 'success';
                                        elseif ($acStatus === 'suspended') $badgeClass = 'danger';
                                        elseif ($acStatus === 'trial') $badgeClass = 'info';
                                        
                                        if ($acStatus !== 'N/A'):
                                        ?>
                                            <span class="badge bg-<?php echo $badgeClass; ?> text-capitalize px-2 py-1" style="font-size: 0.75em;"><?php echo htmlspecialchars($acStatus); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="small text-muted"><?php echo htmlspecialchars(!empty($family['timezone']) ? $family['timezone'] : 'N/A'); ?></td>
                                <td class="small"><?php echo htmlspecialchars(!empty($family['promo_code']) ? $family['promo_code'] : '-'); ?></td>
                                <td class="small">
                                    <div class="d-flex align-items-center">
                                        <span class="monthly-amount-text" id="amount-text-<?php echo $family['id']; ?>">
                                            $<?php echo number_format((float)($family['monthly_amount'] ?? 0), 2); ?>
                                        </span>
                                        <button class="btn btn-sm btn-link text-primary ms-2 p-0 edit-amount-btn" data-id="<?php echo $family['id']; ?>" data-amount="<?php echo htmlspecialchars($family['monthly_amount'] ?? '0.00'); ?>">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="small text-muted">
                                    <?php 
                                    $usedMb = round(($family['storage_used'] ?? 0) / (1024 * 1024), 2);
                                    $alloc = $family['storage_allocated'] ?? 500;
                                    $percent = $alloc > 0 ? min(100, round(($usedMb / $alloc) * 100)) : 0;
                                    $color = $percent > 90 ? 'danger' : ($percent > 75 ? 'warning' : 'primary');
                                    ?>
                                    <div class="d-flex flex-column" style="width: 80px;">
                                        <span class="mb-1" style="font-size: 0.8em;"><?php echo $usedMb; ?> / <?php echo $alloc; ?> MB</span>
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $percent; ?>%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="avatar-group d-flex">
                                        <?php for($i=0; $i<min($members_count, 3); $i++): ?>
                                            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; margin-left: -8px; font-size: 10px; z-index: <?php echo 10-$i; ?>;">
                                                <i class="ri-user-line"></i>
                                            </div>
                                        <?php endfor; ?>
                                        <?php if($members_count > 3): ?>
                                            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; margin-left: -8px; font-size: 10px; z-index: 1;">
                                                +<?php echo $members_count - 3; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($members_count == 0): ?>
                                            <small class="text-muted">No members</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $family['approved'] ? 'success' : 'danger'; ?>"><?php echo htmlspecialchars($family['approved'] ? 'Approved' : 'Pending'); ?></span>
                                    <?php if (!empty($family['is_locked'])): ?>
                                        <span class="badge bg-warning text-dark mt-1"><i class="ri-lock-line"></i> Locked</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-end pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon" data-bs-toggle="dropdown" data-bs-boundary="window"><i class="ri-more-2-fill"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                            <li><a class="dropdown-item" href="edit-family.php?id=<?php echo $family['id']; ?>"><i class="ri-edit-line me-2"></i>Edit Family</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="openUpdateAccountModal(<?php echo $family['id']; ?>); return false;"><i class="ri-bank-card-line me-2"></i>Update Account</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="ri-group-line me-2"></i>Manage Members</a></li>
                                            <li>
                                                <a class="dropdown-item <?php echo !empty($family['approved']) ? 'disabled' : ''; ?>" href="#" onclick="approveFamily(<?php echo $family['id']; ?>); return false;" <?php echo !empty($family['approved']) ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                                                    <i class="ri-check-line me-2"></i>
                                                    <?php echo !empty($family['approved']) ? 'Approved' : 'Approve'; ?>
                                                </a>
                                            </li>
                                            <?php if (empty($family['is_locked'])): ?>
                                            <li>
                                                <a class="dropdown-item text-warning" href="#" onclick="lockFamily(<?php echo $family['id']; ?>); return false;">
                                                    <i class="ri-lock-line me-2"></i> Lock Family
                                                </a>
                                            </li>
                                            <?php else: ?>
                                            <li>
                                                <a class="dropdown-item text-success" href="#" onclick="unlockFamily(<?php echo $family['id']; ?>); return false;">
                                                    <i class="ri-lock-unlock-line me-2"></i> Unlock Family
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="../helpers/admin/deleteFamily.php?id=<?php echo $family['id']; ?>" onclick="return confirm('Are you sure you want to delete the family \'<?php echo htmlspecialchars(!empty($family['name']) ? $family['name'] : 'N/A'); ?>\'? This will also remove all associated members.')"><i class="ri-delete-bin-line me-2"></i>Delete</a></li>
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

<!-- Monthly Amount Edit Modal -->
<div class="modal fade" id="editAmountModal" tabindex="-1" aria-labelledby="editAmountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-6" id="editAmountModalLabel">Edit Monthly Amount</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_family_id">
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" step="0.01" class="form-control" id="edit_monthly_amount">
        </div>
      </div>
      <div class="modal-footer p-2">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-sm btn-primary" id="saveAmountModalBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Update Account Modal -->
<div class="modal fade" id="updateAccountModal" tabindex="-1" aria-labelledby="updateAccountModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-6" id="updateAccountModalLabel">Update Account Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="update_account_id">
        <div class="mb-3">
            <label class="form-label fw-medium">Next Billing Date</label>
            <input type="date" class="form-control" id="update_next_billing_date">
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Account Status</label>
            <select class="form-select" id="update_account_status">
                <option value="trial">Trial</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
      </div>
      <div class="modal-footer p-2">
        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-sm btn-primary" id="saveAccountModalBtn">Save Changes</button>
      </div>
    </div>
  </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    const table = $('#familiesTable').DataTable({
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
            { "orderable": false, "targets": 8 } // Disable ordering on Actions column
        ]
    });

    // Custom search logic
    $('#familySearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Modal Edit Monthly Amount Logic
    $('.edit-amount-btn').on('click', function() {
        const id = $(this).data('id');
        const amount = $(this).data('amount');
        
        $('#edit_family_id').val(id);
        $('#edit_monthly_amount').val(parseFloat(amount).toFixed(2));
        
        const modal = new bootstrap.Modal(document.getElementById('editAmountModal'));
        modal.show();
    });

    $('#saveAmountModalBtn').on('click', function() {
        const id = $('#edit_family_id').val();
        const amount = $('#edit_monthly_amount').val();
        const btn = $(this);
        
        btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin"></i> Saving...');

        $.ajax({
            url: '../api/admin/family.php',
            type: 'POST',
            data: {
                action: 'update_monthly_amount',
                id: id,
                amount: amount
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (typeof showAlert === 'function') {
                        showAlert(response.message, 'success');
                    } else {
                        alert(response.message);
                    }
                    // Update text
                    $('#amount-text-' + id).text('$' + parseFloat(amount).toFixed(2));
                    // Update data-amount on edit button
                    $('#amount-text-' + id).siblings('.edit-amount-btn').data('amount', amount);
                    
                    const modalEl = document.getElementById('editAmountModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                } else {
                    if (typeof showAlert === 'function') {
                        showAlert(response.message, 'error');
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {
                alert('An error occurred while updating the monthly amount.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Save');
            }
        });
    });

});

function approveFamily(id) {
    if(confirm('Are you sure you want to approve this family?')) {
        fetch(`../api/admin/family.php?action=approve&id=${id}`)
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
            alert('An error occurred while approving the family.');
        });
    }
}

function lockFamily(id) {
    if(confirm('Are you sure you want to lock this family? They will not be able to log in.')) {
        fetch(`../api/admin/family.php?action=lock&id=${id}`)
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
            alert('An error occurred while locking the family.');
        });
    }
}

function unlockFamily(id) {
    if(confirm('Are you sure you want to unlock this family?')) {
        fetch(`../api/admin/family.php?action=unlock&id=${id}`)
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
            alert('An error occurred while unlocking the family.');
        });
    }
}
</script>

<script>
function openUpdateAccountModal(familyId) {
    // Show loading state or clear fields
    $('#update_account_id').val('');
    $('#update_next_billing_date').val('');
    $('#update_account_status').val('active');

    $.ajax({
        url: '../api/admin/family.php',
        type: 'GET',
        data: { action: 'get_account', id: familyId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                $('#update_account_id').val(response.data.id);
                $('#update_next_billing_date').val(response.data.next_billing_date);
                $('#update_account_status').val(response.data.account_status);
                
                const modal = new bootstrap.Modal(document.getElementById('updateAccountModal'));
                modal.show();
            } else {
                if (typeof showAlert === 'function') {
                    showAlert('Account not found for this family', 'error');
                } else {
                    alert('Account not found for this family');
                }
            }
        },
        error: function() {
            alert('Failed to fetch account details.');
        }
    });
}

$(document).ready(function() {
    $('#saveAccountModalBtn').on('click', function() {
        const accountId = $('#update_account_id').val();
        const nextBillingDate = $('#update_next_billing_date').val();
        const accountStatus = $('#update_account_status').val();
        const btn = $(this);
        
        btn.prop('disabled', true).html('<i class="ri-loader-4-line ri-spin"></i> Saving...');

        $.ajax({
            url: '../api/admin/family.php',
            type: 'POST',
            data: {
                action: 'update_account',
                account_id: accountId,
                next_billing_date: nextBillingDate,
                account_status: accountStatus
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (typeof showAlert === 'function') {
                        showAlert(response.message, 'success');
                    } else {
                        alert(response.message);
                    }
                    const modalEl = document.getElementById('updateAccountModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                } else {
                    if (typeof showAlert === 'function') {
                        showAlert(response.message, 'error');
                    } else {
                        alert(response.message);
                    }
                }
            },
            error: function() {
                alert('An error occurred while updating the account.');
            },
            complete: function() {
                btn.prop('disabled', false).text('Save Changes');
            }
        });
    });
});
</script>
