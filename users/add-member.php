<?php
$path_prefix = "../";
$page_title = "Add Family Member";
include $path_prefix . 'components/header.php';
include $path_prefix . 'components/sidebar.php';

include_once __DIR__ . '/../classes/User.php';
include_once __DIR__ . '/../classes/File.php';


if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $nickname = $_POST['nickname'] ?? null;
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $image = $_FILES['member_image'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $image_path = null; // Default image avatar

    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        $upload_result = File::upload($image, 'users');
        if ($upload_result['status'] === 'success') {
            $image_path = $upload_result['filePath'];
        } else {
            $error_msg = $upload_result['message'];
        }
    }

    if (!isset($error_msg)) {
        $user_data = [
            'name' => $name,
            'nickname' => $nickname,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'image' => $image_path,
            'password' => $hashed_password,
            'family_id' => $_SESSION['user']['families'][0]['family_id']
        ];

        $add_result = User::addUser($user_data);

        if ($add_result['status'] === 'success') {
            $_SESSION['success_msg'] = $add_result['message'];

            // Send Emails
            require_once __DIR__ . '/../services/mail/Mail.php';
            require_once __DIR__ . '/../classes/GlobalSettings.php';
            
            // 1. Notify Family Heads
            $family_id = $_SESSION['user']['families'][0]['family_id'];
            $stmt = Database::runPrepared("SELECT u.name, u.email FROM users u JOIN user_family uf ON u.id = uf.user_id WHERE uf.family_id = ? AND u.role = 'family-head'", [$family_id]);
            $heads = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($heads) {
                foreach ($heads as $head) {
                    if (!empty($head['email']) && $head['email'] !== $email) {
                        Mail::sendMemberAddedNotification($head['email'], $head['name'], $name);
                    }
                }
            }
            
            // 2. Send Invitation to New Member
            if (!empty($email)) {
                $baseUrlData = GlobalSettings::getSetting('base_url');
                $baseUrl = (!empty($baseUrlData['data']) && !empty($baseUrlData['data']['setting_value'])) ? rtrim($baseUrlData['data']['setting_value'], '/') : 'http://' . $_SERVER['HTTP_HOST'];
                $invitationLink = $baseUrl . '/login.php';
                Mail::sendMemberInvitation($email, $name, $invitationLink);
            }
        } else {
            $error_msg = $add_result['message'];
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
            <h4 class="fw-bold mb-0">Add New Family Member</h4>
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
                                            <i class="fa-solid fa-user text-secondary display-4" id="placeholder-icon"></i>
                                            <img id="image-preview" src="#" alt="Preview" class="d-none w-100 h-100 object-fit-cover" />
                                        </div>
                                        <label for="member_image" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 p-2 shadow-sm" style="width: 35px; height: 35px;">
                                            <i class="fa-solid fa-camera"></i>
                                            <input type="file" id="member_image" name="member_image" class="d-none" accept="image/*">
                                        </label>
                                    </div>
                                    <p class="text-muted small mt-2">Upload profile picture</p>
                                </div>

                                <!-- Full Name -->
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-user"></i></span>
                                        <input type="text" class="form-control border-0 py-2" id="full_name" name="name" placeholder="Enter full name" required>
                                    </div>
                                </div>

                                <!-- Nickname -->
                                <div class="col-md-6">
                                    <label for="nickname" class="form-label fw-semibold">Nickname (optional)</label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-tag"></i></span>
                                        <input type="text" class="form-control border-0 py-2" id="nickname" name="nickname" placeholder="Enter nickname">
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="col-md-12">
                                    <label for="role" class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-users"></i></span>
                                        <select class="form-select border-0 py-2" id="role" name="role" required>
                                            <option value="" disabled>Select a role</option>
                                            <option value="family-head">Family Head</option>
                                            <option value="member" selected>Member</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Email Address -->
                                <div class="col-md-6 email-wrapper">
                                    <label for="email" class="form-label fw-semibold">Email Address <span class="req-star text-danger d-none">*</span> <span class="opt-text">(optional)</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                        <input type="email" class="form-control border-0 py-2" id="email" name="email" placeholder="email@example.com">
                                    </div>
                                </div>

                                <!-- Phone Number -->
                                <div class="col-md-6 phone-wrapper">
                                    <label for="phone" class="form-label fw-semibold">Phone Number <span class="req-star text-danger d-none">*</span> <span class="opt-text">(optional)</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-phone-flip"></i></span>
                                        <input type="tel" class="form-control border-0 py-2" id="phone" name="phone" placeholder="+1 (555) 000-0000">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-12">
                                    <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                    <div class="input-group border rounded-3 overflow-hidden shadow-sm">
                                        <span class="input-group-text bg-white border-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                        <input type="password" class="form-control border-0 py-2" id="password" name="password" placeholder="Enter password" required>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="col-12 mt-4">
                                    <hr class="mb-4">
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="index.php" class="btn btn-white border px-4 py-2 fw-medium rounded-3 text-dark">Cancel</a>
                                        <button type="submit" name="submit" class="btn btn-primary px-5 py-2 fw-medium rounded-3 shadow-sm">
                                            Add Member
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Helper Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 bg-primary bg-opacity-10 rounded-4 p-3">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-circle-info me-2"></i> Adding Members</h5>
                        <p class="text-dark opacity-75 small">Adding family members allows you to assign chores, meals, and specific events to them.</p>
                        <ul class="text-dark opacity-75 small ps-3">
                            <li>Members will appear on the calendar dashboard.</li>
                            <li>You can choose distinct colors for each member later.</li>
                            <li>Image uploads are recommended for better visual identification.</li>
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

        const roleSelect = document.getElementById('role');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const emailOptText = document.querySelector('.email-wrapper .opt-text');
        const emailReqStar = document.querySelector('.email-wrapper .req-star');
        const phoneOptText = document.querySelector('.phone-wrapper .opt-text');
        const phoneReqStar = document.querySelector('.phone-wrapper .req-star');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'family-head') {
                emailInput.required = true;
                phoneInput.required = true;
                if (emailOptText) emailOptText.classList.add('d-none');
                if (emailReqStar) emailReqStar.classList.remove('d-none');
                if (phoneOptText) phoneOptText.classList.add('d-none');
                if (phoneReqStar) phoneReqStar.classList.remove('d-none');
            } else {
                emailInput.required = false;
                phoneInput.required = false;
                if (emailOptText) emailOptText.classList.remove('d-none');
                if (emailReqStar) emailReqStar.classList.add('d-none');
                if (phoneOptText) phoneOptText.classList.remove('d-none');
                if (phoneReqStar) phoneReqStar.classList.add('d-none');
            }
        });

        // Trigger change to set initial state if needed
        if (roleSelect.value) {
            roleSelect.dispatchEvent(new Event('change'));
        }

        document.getElementById('member_image').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').src = e.target.result;
                    document.getElementById('image-preview').classList.remove('d-none');
                    document.getElementById('placeholder-icon').classList.add('d-none');
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<?php include $path_prefix . 'components/footer.php'; ?>