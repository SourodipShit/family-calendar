<?php
$path_prefix = "../";
$page_title = "Edit Family Member";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';

include_once __DIR__ . '/../classes/User.php';
include_once __DIR__ . '/../classes/File.php';

$user_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$user_id) {
    $error_msg = "Failed to get user ID";
}

$user = User::getUserById($user_id);

if (!$user) {
    $error_msg = "User not found";
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $image = $_FILES['member_image'];
    $password = $_POST['password'];

    $update_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone
    ];

    if (!empty($password)) {
        $update_data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        $upload_result = File::upload($image, 'users');
        if ($upload_result['status'] === 'success') {
            $update_data['image'] = $upload_result['filePath'];
            
            // Delete old file if it exists and is an uploaded file
            if (!empty($user['image']) && strpos($user['image'], 'uploads') !== false) {
                File::deleteFile($user['image']);
            }
        } else {
            $error_msg = $upload_result['message'];
        }
    }

    if (!isset($error_msg)) {
        $update_result = User::updateUser($user_id, $update_data);
        
        if ($update_result['status'] === 'success') {
            $_SESSION['success_msg'] = $update_result['message'];
            // Refresh user data for display
            $user = User::getUserById($user_id);
        } else {
            $error_msg = $update_result['message'];
        }
    }
}

// Check for session messages
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}
if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}
?>

<div id="page-content-wrapper" class="flex-grow-1 bg-white">
    <?php include $path_prefix . 'components/navbar.php'; ?>

    <div class="container-fluid all-width-sames p-4">
        <div class="d-flex align-items-center mb-4">
            <a href="index.php" class="btn btn-light rounded-circle me-3">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h4 class="fw-bold mb-0">Edit Family Member</h4>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card border rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <form action="#" method="POST" enctype="multipart/form-data">
                            <div class="row g-4">
                                <!-- Profile Image Upload -->
                                <div class="col-12 text-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        <div class="profile-image-preview rounded-circle border d-flex align-items-center justify-content-center bg-light overflow-hidden" style="width: 120px; height: 120px;">
                                            <?php if ($user['image']): ?>
                                                <img id="image-preview" src="<?php echo $path_prefix . str_replace('../', '', $user['image']); ?>" alt="Preview" class="w-100 h-100 object-fit-cover" />
                                            <?php else: ?>
                                                <i class="fa-solid fa-user text-secondary display-4" id="placeholder-icon"></i>
                                                <img id="image-preview" src="#" alt="Preview" class="d-none w-100 h-100 object-fit-cover" />
                                            <?php endif; ?>
                                        </div>
                                        <label for="member_image" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-2 shadow-sm" style="width: 35px; height: 35px;">
                                            <i class="fa-solid fa-camera"></i>
                                            <input type="file" id="member_image" name="member_image" class="d-none" accept="image/*">
                                        </label>
                                    </div>
                                    <p class="text-muted small mt-2">Update profile picture</p>
                                </div>

                                <!-- Full Name -->
                                <div class="col-md-12">
                                    <label for="full_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-user"></i></span>
                                        <input type="text" class="form-control border-0 py-2" id="full_name" name="name" placeholder="Enter full name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                </div>

                                <!-- Email Address -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                        <input type="email" class="form-control border-0 py-2" id="email" name="email" placeholder="email@example.com" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-phone-flip"></i></span>
                                        <input type="tel" class="form-control border-0 py-2" id="phone" name="phone" placeholder="+1 (555) 000-0000" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-12">
                                    <label for="password" class="form-label fw-semibold">Password <span class="text-muted small">(Leave blank to keep current)</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" class="form-control border-0 py-2" id="password" name="password" placeholder="Enter new password">
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="col-12 mt-4">
                                    <hr class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button type="button" class="btn btn-outline-danger px-4 py-2 fw-medium rounded-3" onclick="confirmDelete(<?php echo $user_id; ?>)">
                                            <i class="fa-solid fa-trash-can me-2"></i> Delete Member
                                        </button>
                                        <div class="d-flex gap-3">
                                            <a href="index.php" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark">Cancel</a>
                                            <button type="submit" name="submit" class="btn btn-primary px-5 py-2 fw-medium rounded-3 shadow-sm">
                                                Update Member
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<script>
    function confirmDelete(userId) {
        if (confirm("Are you sure you want to delete this family member? This action cannot be undone.")) {
            window.location.href = "../helpers/delete-user.php?id=" + userId;
        }
    }
</script>

            <!-- Helper Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-pen me-2"></i> Editing Member</h5>
                        <p class="text-dark opacity-75 small">You are currently editing <strong><?php echo htmlspecialchars($user['name']); ?>'s</strong> profile information.</p>
                        <ul class="text-dark opacity-75 small ps-3">
                            <li>Update contact details and profile picture.</li>
                            <li>Change password only if necessary.</li>
                            <li>These changes will reflect immediately on the dashboard.</li>
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
        <?php endif; ?>

        document.getElementById('member_image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    const placeholder = document.getElementById('placeholder-icon');

                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('d-none');
                    }
                    if (placeholder) placeholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>