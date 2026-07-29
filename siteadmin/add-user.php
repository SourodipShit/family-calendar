<?php
$path_prefix = "../";
$page_title = "Add New User";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/User.php';
require_once $path_prefix . 'classes/Family.php';
require_once $path_prefix . 'classes/File.php';

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $imagePath = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = File::upload($_FILES['image'], 'users');
        if ($upload['status'] === 'success') {
            $imagePath = $upload['filePath'];
        }
    }

    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'role' => $_POST['role'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'family_id' => $_POST['family_id'],
        'image' => $imagePath
    ];

    $result = User::addUser($data);
    $status = $result['status'];
    $message = $result['message'];

    if ($status == 'success') {
        require_once $path_prefix . 'services/mail/Mail.php';
        require_once $path_prefix . 'classes/GlobalSettings.php';
        
        $role = $_POST['role'];
        $family_id = $_POST['family_id'];
        $email = $_POST['email'];
        $name = $_POST['name'];
        
        // Notify Family Heads if the new user is a member
        if (($role === 'member' || $role === 'user') && !empty($family_id)) {
            $stmt = Database::runPrepared("SELECT u.name, u.email FROM users u JOIN user_family uf ON u.id = uf.user_id WHERE uf.family_id = ? AND u.role = 'family-head'", [$family_id]);
            $heads = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($heads) {
                foreach ($heads as $head) {
                    if (!empty($head['email']) && $head['email'] !== $email) {
                        Mail::sendMemberAddedNotification($head['email'], $head['name'], $name);
                    }
                }
            }
        }
        
        // Send Invitation to the newly added user (if email is provided)
        if (!empty($email)) {
            $baseUrlData = GlobalSettings::getSetting('base_url');
            $baseUrl = (!empty($baseUrlData['data']) && !empty($baseUrlData['data']['setting_value'])) ? rtrim($baseUrlData['data']['setting_value'], '/') : 'http://' . $_SERVER['HTTP_HOST'];
            $invitationLink = $baseUrl . '/login.php';
            Mail::sendMemberInvitation($email, $name, $invitationLink);
        }

        echo "<script>window.location.href='users.php?status=success&msg=" . urlencode($message) . "';</script>";
        exit;
    }
}

$families = Family::getAllFamilies();
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="users.php" class="btn btn-icon btn-light rounded-circle me-3"><i class="ri-arrow-left-line"></i></a>
                    <h4 class="fw-bold mb-0">Add New User</h4>
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
                                        <div id="imagePreview" class="bg-light rounded-circle d-flex align-items-center justify-content-center border-dashed border-2 me-4" style="width: 100px; height: 100px; border: 2px dashed #ccc; cursor: pointer;" onclick="document.getElementById('imageInput').click();">
                                            <i class="ri-image-add-line fs-2 text-muted"></i>
                                        </div>
                                        <div>
                                            <input type="file" name="image" id="imageInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                                            <button type="button" class="btn btn-light btn-sm mb-2" onclick="document.getElementById('imageInput').click();">Choose Image</button>
                                            <p class="small text-muted mb-0">Recommended: Square image, max 2MB (JPG, PNG)</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-user-line"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="e.g. John Doe" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-mail-line"></i></span>
                                        <input type="email" name="email" class="form-control bg-light border-0" placeholder="e.g. john@example.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-phone-line"></i></span>
                                        <input type="text" name="phone" class="form-control bg-light border-0" placeholder="e.g. +1 234 567 890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Role</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-shield-line"></i></span>
                                        <select name="role" class="form-select bg-light border-0" required>
                                            <option value="user">User</option>
                                            <option value="family-head">Family Head</option>
                                            <option value="member">Member</option>
                                            <option value="siteadmin">Site Admin</option>
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
                                                <option value="<?php echo $family['id']; ?>"><?php echo htmlspecialchars($family['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-lock-line"></i></span>
                                        <input type="password" name="password" class="form-control bg-light border-0" placeholder="Minimum 6 characters" required minlength="6">
                                    </div>
                                </div>
                                <div class="col-12 mt-5">
                                    <hr class="opacity-50 mb-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="users.php" class="btn btn-light px-4">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4">Create User</button>
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

document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    const emailInput = document.querySelector('input[name="email"]');
    const phoneInput = document.querySelector('input[name="phone"]');
    
    function updateRequirements() {
        if (!roleSelect || !emailInput || !phoneInput) return;
        
        const role = roleSelect.value;
        if (role === 'member') {
            emailInput.required = false;
            phoneInput.required = false;
        } else if (role === 'family-head') {
            emailInput.required = true;
            phoneInput.required = true;
        } else {
            emailInput.required = true;
            phoneInput.required = false;
        }
    }

    if (roleSelect) {
        roleSelect.addEventListener('change', updateRequirements);
        updateRequirements();
    }
});
</script>
