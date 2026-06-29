<?php
$path_prefix = "../";
$page_title = "Manage Promo Codes";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/PromoCode.php';
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Promo Code Management</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromoModal">
                <i class="ri-add-line me-1"></i> Add New Promo Code
            </button>
        </div>

        <div class="card admin-card border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="promocodesTable">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 border-0">Code</th>
                                <th class="border-0">Description</th>
                                <th class="border-0">Months Free</th>
                                <th class="border-0">Usage</th>
                                <th class="border-0">Status</th>
                                <th class="text-end pe-4 border-0">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = PromoCode::fetchAll();
                            if ($res['status'] === 'success' && !empty($res['data'])) {
                                foreach ($res['data'] as $promo):
                            ?>
                                    <tr id="promo-row-<?php echo $promo['id']; ?>">
                                        <td class="ps-4 fw-bold text-primary"><?php echo htmlspecialchars($promo['code']); ?></td>
                                        <td class="small"><?php echo htmlspecialchars($promo['description']); ?></td>
                                        <td><span class="badge bg-success-subtle text-success"><?php echo (int)$promo['months_free']; ?> Months</span></td>
                                        <td class="small">
                                            <?php echo (int)$promo['times_used']; ?> / <?php echo (int)$promo['max_uses']; ?>
                                            <div class="progress mt-1" style="height: 4px; width: 60px;">
                                                <?php
                                                $pct = ($promo['max_uses'] > 0) ? ($promo['times_used'] / $promo['max_uses']) * 100 : 0;
                                                $color = $pct >= 100 ? 'bg-danger' : 'bg-primary';
                                                ?>
                                                <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo $pct; ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($promo['is_active']): ?>
                                                <span class="badge bg-success rounded-pill px-3">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary rounded-pill px-3">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-light text-primary edit-btn"
                                                data-id="<?php echo $promo['id']; ?>"
                                                data-code="<?php echo htmlspecialchars($promo['code']); ?>"
                                                data-desc="<?php echo htmlspecialchars($promo['description']); ?>"
                                                data-months="<?php echo htmlspecialchars($promo['months_free']); ?>"
                                                data-max="<?php echo htmlspecialchars($promo['max_uses']); ?>"
                                                data-active="<?php echo htmlspecialchars($promo['is_active']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#editPromoModal">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light text-danger delete-btn ms-1"
                                                data-id="<?php echo $promo['id']; ?>">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                            <?php
                                endforeach;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addPromoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="addPromoForm">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add Promo Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="addAlert" class="alert d-none"></div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-muted small fw-bold mb-0">PROMO CODE</label>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" id="generateAddCodeBtn" style="font-size: 12px;">Generate Random</button>
                        </div>
                        <div class="d-flex gap-2" id="addCodeInputs">
                            <input type="text" class="form-control bg-light border-2 text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border-2 text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border-2 text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border-2 text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border-2 text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border-2 text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                        </div>
                        <input type="hidden" name="code" id="addCodeHidden">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">DESCRIPTION</label>
                        <input type="text" class="form-control bg-light border-0" name="description" placeholder="Internal description">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">MONTHS FREE</label>
                            <input type="number" class="form-control bg-light border-0" name="months_free" value="1" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">MAX USES</label>
                            <input type="number" class="form-control bg-light border-0" name="max_uses" value="10" min="1" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addActive" checked>
                        <label class="form-check-label" for="addActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Create Code</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editPromoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editPromoForm">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Promo Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editAlert" class="alert d-none"></div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label text-muted small fw-bold mb-0">PROMO CODE</label>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" id="generateEditCodeBtn" style="font-size: 12px;">Generate Random</button>
                        </div>
                        <div class="d-flex gap-2" id="editCodeInputs">
                            <input type="text" class="form-control bg-light border text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                            <input type="text" class="form-control bg-light border text-center text-uppercase fs-5 fw-bold otp-input" maxlength="1" required>
                        </div>
                        <input type="hidden" name="code" id="editCodeHidden">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">DESCRIPTION</label>
                        <input type="text" class="form-control bg-light border-0" id="editDesc" name="description">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">MONTHS FREE</label>
                            <input type="number" class="form-control bg-light border-0" id="editMonths" name="months_free" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold">MAX USES</label>
                            <input type="number" class="form-control bg-light border-0" id="editMax" name="max_uses" min="1" required>
                        </div>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive">
                        <label class="form-check-label" for="editActive">Active</label>
                    </div>
                    <!-- hidden input to handle unchecked checkbox -->
                    <input type="hidden" name="is_active_hidden" value="0">
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if ($.fn.DataTable) {
            $('#promocodesTable').DataTable({
                "pageLength": 10,
                "ordering": false
            });
        }

        // Setup OTP Inputs behavior
        function setupOtpInputs(containerId, hiddenInputId) {
            const container = document.getElementById(containerId);
            if (!container) return;
            const inputs = container.querySelectorAll('.otp-input');
            const hidden = document.getElementById(hiddenInputId);

            function updateHidden() {
                let val = '';
                inputs.forEach(inp => val += inp.value.toUpperCase());
                hidden.value = val;
            }

            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    if (this.value.length > 1) {
                        this.value = this.value.slice(0, 1);
                    }
                    if (this.value.length === 1) {
                        if (index < inputs.length - 1) inputs[index + 1].focus();
                    }
                    updateHidden();
                });
                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !this.value) {
                        if (index > 0) {
                            inputs[index - 1].focus();
                            inputs[index - 1].value = '';
                        }
                    }
                });

                // Prevent skipping boxes
                input.addEventListener('focus', function(e) {
                    let firstEmpty = -1;
                    for (let i = 0; i < inputs.length; i++) {
                        if (!inputs[i].value) {
                            firstEmpty = i;
                            break;
                        }
                    }
                    if (firstEmpty !== -1 && firstEmpty < index) {
                        inputs[firstEmpty].focus();
                    }
                });

                // Handle paste (e.g., pasting a 6 letter code)
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
                    for (let i = 0; i < pastedData.length; i++) {
                        if (inputs[i]) {
                            inputs[i].value = pastedData[i];
                        }
                    }
                    if (pastedData.length > 0) {
                        const nextIndex = Math.min(pastedData.length, inputs.length - 1);
                        inputs[nextIndex].focus();
                    }
                    updateHidden();
                });
            });
        }

        setupOtpInputs('addCodeInputs', 'addCodeHidden');
        setupOtpInputs('editCodeInputs', 'editCodeHidden');

        // Generator function
        function generateRandomCode(containerId, hiddenInputId) {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 6; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            const inputs = document.getElementById(containerId).querySelectorAll('.otp-input');
            for (let i = 0; i < 6; i++) {
                inputs[i].value = result[i];
            }
            document.getElementById(hiddenInputId).value = result;
        }

        document.getElementById('generateAddCodeBtn').addEventListener('click', () => {
            generateRandomCode('addCodeInputs', 'addCodeHidden');
        });

        document.getElementById('generateEditCodeBtn').addEventListener('click', () => {
            generateRandomCode('editCodeInputs', 'editCodeHidden');
        });

        // Populate Edit Modal
        const editBtns = document.querySelectorAll('.edit-btn');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('editId').value = this.dataset.id;

                // Populate OTP inputs
                const code = this.dataset.code;
                const inputs = document.getElementById('editCodeInputs').querySelectorAll('.otp-input');
                for (let i = 0; i < 6; i++) {
                    if (code[i]) inputs[i].value = code[i];
                    else inputs[i].value = '';
                }
                document.getElementById('editCodeHidden').value = code;

                document.getElementById('editDesc').value = this.dataset.desc;
                document.getElementById('editMonths').value = this.dataset.months;
                document.getElementById('editMax').value = this.dataset.max;
                document.getElementById('editActive').checked = this.dataset.active === "1";
            });
        });

        // Handle Add Submit
        document.getElementById('addPromoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let data = Object.fromEntries(formData.entries());

            // Handle checkbox 
            data.is_active = document.getElementById('addActive').checked ? 1 : 0;

            fetch('../api/admin/promocodes.php?action=create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        let alert = document.getElementById('addAlert');
                        alert.className = 'alert alert-danger';
                        alert.textContent = res.message;
                    }
                });
        });

        // Handle Delete
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this promo code?')) {
                    fetch('../api/admin/promocodes.php?action=delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: this.dataset.id })
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.status === 'success') {
                                location.reload();
                            } else {
                                alert(res.message);
                            }
                        });
                }
            });
        });

        // Handle Edit Submit
        document.getElementById('editPromoForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let data = Object.fromEntries(formData.entries());

            // Handle checkbox correctly since unchecked doesn't send
            data.is_active = document.getElementById('editActive').checked ? 1 : 0;

            fetch('../api/admin/promocodes.php?action=update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        location.reload();
                    } else {
                        let alert = document.getElementById('editAlert');
                        alert.className = 'alert alert-danger';
                        alert.textContent = res.message;
                    }
                });
        });
    });
</script>