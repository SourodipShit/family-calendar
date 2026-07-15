<?php
$path_prefix = "../";
$page_title = "Coach Profile";
require_once $path_prefix . 'components/coach-header.php';
require_once $path_prefix . 'components/coach-sidebar.php';
require_once $path_prefix . 'classes/Coach.php';
require_once $path_prefix . 'classes/CoachCategory.php';

$userId = $_SESSION['user']['id'];
$coachData = Coach::getByUserId($userId);
$profile = [];
$certifications = [];
if ($coachData['status'] === 'success') {
    $profile = $coachData['data']['profile'];
    $certifications = $coachData['data']['certifications'] ?? [];
}

$categoriesResult = CoachCategory::getAll();
$categories = $categoriesResult['status'] === true ? $categoriesResult['data'] : [];
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php require_once $path_prefix . 'components/coach-navbar.php'; ?>

    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="fw-bold mb-1">My Profile</h3>
                <p class="text-secondary mb-0">Update your public profile, description, and contact info.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card admin-card border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <form id="coachProfileForm" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profile">

                            <div class="d-flex align-items-center mb-4">
                                <div class="position-relative me-4">
                                    <?php
                                    $img_src = !empty($profile['profile_image']) ? $profile['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($profile['user_name'] ?? 'C') . '&background=random';
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="Profile" class="rounded-circle object-fit-cover shadow-sm" style="width: 100px; height: 100px;" id="profilePreview">
                                </div>
                                <div>
                                    <label class="form-label fw-medium">Profile Image</label>
                                    <input class="form-control" type="file" name="profile_image" id="profileImageInput" accept="image/*">
                                    <small class="text-muted">Upload a new image to update your avatar.</small>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($profile['user_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-medium">Category / Specialty</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo (isset($profile['category_id']) && $profile['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Bio / Description</label>
                                <textarea class="form-control" name="description" rows="5" placeholder="Tell families about your coaching style and experience..."><?php echo htmlspecialchars($profile['description'] ?? ''); ?></textarea>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4 py-2" id="saveProfileBtn">
                                    <i class="ri-save-line me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Certifications Section -->
                <div class="card admin-card border-0 shadow-sm mt-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Certifications</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCertModal">
                            <i class="ri-add-line"></i> Add New
                        </button>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <?php if (empty($certifications)): ?>
                            <p class="text-secondary mb-0">No certifications added yet.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($certifications as $cert): ?>
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 position-relative bg-light">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($cert['name']); ?></h6>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-secondary p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editCertModal"
                                                                data-id="<?php echo $cert['id']; ?>"
                                                                data-name="<?php echo htmlspecialchars($cert['name']); ?>"
                                                                data-desc="<?php echo htmlspecialchars($cert['description']); ?>"
                                                                data-image="<?php echo !empty($cert['image']) ?  htmlspecialchars($cert['image']) : ''; ?>">
                                                                <i class="ri-pencil-line me-2 text-primary"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#" onclick="deleteCertification(<?php echo $cert['id']; ?>)">
                                                                <i class="ri-delete-bin-line me-2"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <p class="text-secondary small mb-2"><?php echo htmlspecialchars($cert['description']); ?></p>
                                            <?php if (!empty($cert['image'])): ?>
                                                <div class="mt-2 text-center">
                                                    <img src="<?php echo  htmlspecialchars($cert['image']); ?>" alt="Certificate" class="img-fluid rounded" style="max-height: 150px; object-fit: cover;">
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-primary-subtle border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="ri-information-line fs-2 text-primary me-2"></i>
                            <h5 class="fw-bold mb-0 text-primary">Profile Tips</h5>
                        </div>
                        <p class="text-secondary mb-2">Families see your profile when looking for a coach. A well-written profile increases your chances of getting hired.</p>
                        <ul class="text-secondary ps-3 mb-0">
                            <li class="mb-1">Use a professional and friendly photo.</li>
                            <li class="mb-1">Keep your bio clear and concise.</li>
                            <li>Mention your key specialties and achievements.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertModal" tabindex="-1" aria-labelledby="addCertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addCertModalLabel">Add Certification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCertForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_certification">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Certification Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description (Optional)</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Document / Image (Optional)</label>
                        <input type="file" class="form-control" name="cert_image" id="addCertImage" accept="image/*,application/pdf">
                        <div class="mt-3 text-center d-none" id="addCertPreviewContainer">
                            <img src="" alt="Preview" id="addCertPreview" class="img-fluid rounded border shadow-sm" style="max-height: 200px; object-fit: cover;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveAddCertBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Certification Modal -->
<div class="modal fade" id="editCertModal" tabindex="-1" aria-labelledby="editCertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editCertModalLabel">Edit Certification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCertForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit_certification">
                    <input type="hidden" name="cert_id" id="editCertId">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Certification Name</label>
                        <input type="text" class="form-control" name="name" id="editCertName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Description (Optional)</label>
                        <textarea class="form-control" name="description" id="editCertDesc" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Document / Image (Optional)</label>
                        <input type="file" class="form-control" name="cert_image" id="editCertImage" accept="image/*,application/pdf">
                        <small class="text-muted">Leave empty to keep existing file.</small>
                        <div class="mt-3 text-center d-none" id="editCertPreviewContainer">
                            <img src="" alt="Preview" id="editCertPreview" class="img-fluid rounded border shadow-sm" style="max-height: 200px; object-fit: cover;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" id="saveEditCertBtn">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo $path_prefix; ?>public/js/alert.js"></script>
<script>
    document.getElementById('profileImageInput').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    document.getElementById('coachProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('saveProfileBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        btn.disabled = true;

        const formData = new FormData(this);

        fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;

                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showAlert('An unexpected error occurred.', 'error');
            });
    });

    // Handle Add Certification
    document.getElementById('addCertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveAddCertBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        btn.disabled = true;

        fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showAlert('An unexpected error occurred.', 'error');
            });
    });

    // Handle Add Certification Image Preview
    document.getElementById('addCertImage').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('addCertPreviewContainer');
        const previewImg = document.getElementById('addCertPreview');
        if (this.files && this.files[0] && this.files[0].type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            previewImg.src = '';
            previewContainer.classList.add('d-none');
        }
    });

    // Populate Edit Certification Modal
    const editCertModal = document.getElementById('editCertModal');
    editCertModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        document.getElementById('editCertId').value = button.getAttribute('data-id');
        document.getElementById('editCertName').value = button.getAttribute('data-name');
        document.getElementById('editCertDesc').value = button.getAttribute('data-desc');

        const imageUrl = button.getAttribute('data-image');
        const previewContainer = document.getElementById('editCertPreviewContainer');
        const previewImg = document.getElementById('editCertPreview');

        if (imageUrl) {
            previewImg.src = imageUrl;
            previewContainer.classList.remove('d-none');
        } else {
            previewImg.src = '';
            previewContainer.classList.add('d-none');
        }
    });

    // Handle Edit Certification Image Preview
    document.getElementById('editCertImage').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('editCertPreviewContainer');
        const previewImg = document.getElementById('editCertPreview');
        if (this.files && this.files[0] && this.files[0].type.startsWith('image/')) {
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('d-none');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Handle Edit Certification
    document.getElementById('editCertForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('saveEditCertBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        btn.disabled = true;

        fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showAlert('An unexpected error occurred.', 'error');
            });
    });

    // Handle Delete Certification
    function deleteCertification(certId) {
        if (confirm("Are you sure you want to delete this certification?")) {
            const formData = new FormData();
            formData.append('action', 'delete_certification');
            formData.append('cert_id', certId);

            fetch('<?php echo $path_prefix; ?>api/coach_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showAlert(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An unexpected error occurred.', 'error');
                });
        }
    }
</script>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>