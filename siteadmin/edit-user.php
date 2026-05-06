<?php
$path_prefix = "../";
$page_title = "Edit User";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/User.php';
require_once $path_prefix . 'classes/Family.php';
require_once $path_prefix . 'classes/File.php';

$message = "";
$status = "";

$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    echo "<script>window.location.href='users.php';</script>";
    exit;
}

$user = User::getUserById($user_id);
if (!$user) {
    echo "<script>window.location.href='users.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'role' => $_POST['role']
    ];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = File::upload($_FILES['image'], 'users');
        if ($upload['status'] === 'success') {
            $data['image'] = $upload['filePath'];
            
            // Delete old image if it exists
            if (!empty($user['image']) && file_exists($user['image'])) {
                unlink($user['image']);
            }
        }
    }

    // If password is provided, update it
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $result = User::updateUser($user_id, $data);
    $status = $result['status'];
    $message = $result['message'];

    if ($status == 'success') {
        echo "<script>window.location.href='users.php?status=success&msg=" . urlencode($message) . "';</script>";
        exit;
    }
}

$families = Family::getAllFamilies();
// Get current family
$current_family_sql = "SELECT family_id FROM user_family WHERE user_id = ?";
$stmt = Database::runPrepared($current_family_sql, [$user_id]);
$current_family_id = $stmt->fetchColumn();
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="users.php" class="btn btn-icon btn-light rounded-circle me-3"><i class="ri-arrow-left-line"></i></a>
                    <h4 class="fw-bold mb-0">Edit User: <?php echo htmlspecialchars($user['name']); ?></h4>
                </div>

                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $status == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row g-4">
                                <!-- Image Upload Section -->
                                <div class="col-12 mb-2">
                                    <label class="form-label fw-medium d-block">Profile Picture</label>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                        $current_image = !empty($user['image']) ? $user['image'] : '';
                                        $clean_image_path = preg_replace('/^(\.\.\/)+/', '', $current_image);
                                        $full_image_path = !empty($clean_image_path) ? $path_prefix . $clean_image_path : '';
                                        ?>
                                        <div id="imagePreview" class="bg-light rounded-circle d-flex align-items-center justify-content-center border-2 me-4" style="width: 100px; height: 100px; cursor: pointer; overflow: hidden; border: 2px dashed #ccc;" onclick="document.getElementById('imageInput').click();">
                                            <?php if (!empty($full_image_path) && file_exists($full_image_path)): ?>
                                                <img src="<?php echo $full_image_path; ?>" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                                            <?php else: ?>
                                                <i class="ri-image-add-line fs-2 text-muted"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                            <button type="button" class="btn btn-light btn-sm mb-2" onclick="document.getElementById('imageInput').click();">Change Image</button>
                                            <p class="small text-muted mb-0">Recommended: Square image, max 2MB (JPG, PNG)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-user-line"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-mail-line"></i></span>
                                        <input type="email" name="email" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-phone-line"></i></span>
                                        <input type="text" name="phone" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-shield-line"></i></span>
                                        <select name="role" class="form-select bg-light border-0" required>
                                            <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="family-head" <?php echo $user['role'] == 'family-head' ? 'selected' : ''; ?>>Family Head</option>
                                            <option value="member" <?php echo $user['role'] == 'member' ? 'selected' : ''; ?>>Member</option>
                                            <option value="siteadmin" <?php echo $user['role'] == 'siteadmin' ? 'selected' : ''; ?>>Site Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Assign to Family</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-group-line"></i></span>
                                        <select name="family_id" class="form-select bg-light border-0" required>
                                            <option value="">Select a Family</option>
                                            <?php foreach ($families as $family): ?>
                                                <option value="<?php echo $family['id']; ?>" <?php echo $current_family_id == $family['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($family['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Reset Password (Leave blank to keep current)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-lock-line"></i></span>
                                        <input type="password" name="password" class="form-control bg-light border-0" placeholder="New password" minlength="6">
                                    </div>
                                </div>
                                <div class="col-12 mt-5">
                                    <hr class="opacity-50 mb-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="users.php" class="btn btn-light px-4">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4">Update User</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once $path_prefix . 'components/admin-footer.php'; ?>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '" class="rounded-circle w-100 h-100" style="object-fit: cover;">';
            document.getElementById('imagePreview').style.border = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
