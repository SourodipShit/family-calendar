<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    if (!isset($_GET['add_account'])) {
        if($_SESSION['user']['role'] == 'siteadmin'){
            header("Location: siteadmin/index.php");
        }else{
            header("Location: users/index.php");
        }
        exit;
    }
}
$path_prefix = "";
$page_title = "Forgot Password - Family Calendar";
$page_image = "";
$is_public_page = true;
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/GlobalSettings.php';

$setting = GlobalSettings::getSetting("login_page_image");
$page_image = ($setting['status'] === 'success' && !empty($setting['data']['setting_value'])) ? $setting['data']['setting_value'] : "";

$type = isset($_GET['type']) ? $_GET['type'] : 'find-account';

$user_display_html = '
<div class="user-profile-widget text-center mb-4 p-3 rounded" style="background-color: rgba(0,0,0,0.02); border: 1px solid #eaeaea;">
    ';
if (isset($_SESSION['reset_user']) && !empty($_SESSION['reset_user'])) {
    $img = !empty($_SESSION['reset_user']['profile_image']) ? $_SESSION['reset_user']['profile_image'] : 'public/images/default_user.png';
    $name = htmlspecialchars($_SESSION['reset_user']['name']);
    $email = htmlspecialchars($_SESSION['reset_user']['email']);
    $user_display_html .= '
    <img src="' . $img . '" class="rounded-circle mb-2" width="64" height="64" alt="User" style="object-fit: cover; border: 2px solid var(--bs-primary);">
    <h5 class="fw-bold mb-0">' . $name . '</h5>
    <p class="text-muted small mb-0">' . $email . '</p>
    ';
} else {
    $user_display_html .= '
    <div class="rounded-circle bg-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 64px; height: 64px; border: 2px dashed #ccc;">
        <i class="fa-solid fa-user text-secondary fs-3"></i>
    </div>
    <h5 class="fw-bold mb-0 text-muted">No user selected</h5>
    <p class="text-muted small mb-0">Please find your account first</p>
    ';
}
$user_display_html .= '</div>';
?>

<link rel="stylesheet" href="public/css/login.css">

<div class="login-container">
    <div class="row g-0 h-100">
        <!-- Left Side: Illustration & Branding -->
        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center left-panel">
            <div class="brand-content">
                <div class="logo-wrapper mb-4">
                    <div class="logo-icon">
                        <i class="fa-solid fa-calendar-check"></i>
                        <div class="family-avatars">
                            <i class="fa-solid fa-user-group"></i>
                        </div>
                    </div>
                    <div class="logo-text">
                        <span class="logo-title">Family</span>
                        <span class="logo-subtitle">Calendar</span>
                    </div>
                </div>

                <h1 class="display-5 fw-bold mb-3">Plan together.<br>Stay connected.</h1>
                <p class="lead mb-5 text-secondary">Organize meals, events, chores and important moments – all in
                    one place for your family.</p>

                <div class="illustration-container mt-4 mb-5">
                    <?php $img_src = !empty($page_image) ? str_replace('../', $path_prefix, $page_image) : 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&q=80&w=800'; ?>
                    <img src="<?php echo htmlspecialchars($img_src); ?>"
                        alt="Family Calendar Illustration" class="img-fluid illustration-img" style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); width: 100%; height: 100%; object-fit: cover;">
                </div>

                <div class="privacy-badge mt-auto">
                    <div class="badge-icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="badge-text">
                        <strong>Your family. Your privacy.</strong>
                        <p class="mb-0">We keep your data safe and secure.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Forms -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center right-panel">
            <div class="login-form-wrapper">
                <div class="mobile-logo d-lg-none mb-4">
                    <i class="fa-solid fa-calendar-check text-primary fs-1"></i>
                    <span class="h3 fw-bold ms-2">Family Calendar</span>
                </div>

                <?php if ($type === 'enter-otp'): ?>
                    <?php echo $user_display_html; ?>
                    <div class="form-header mb-4">
                        <h2 class="fw-bold">Enter OTP</h2>
                        <p class="text-muted">Please enter the 6-digit OTP sent to your email</p>
                    </div>

                    <form id="otpForm" method="post" action="?type=set-new-password">
                        <div class="mb-4">
                            <label class="form-label fw-medium w-100 mb-2">Enter 6-digit OTP</label>
                            <div class="d-flex justify-content-between gap-2 mb-2">
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-input" style="width: 50px; height: 50px; padding: 0; border: 2px solid #ced4da; border-radius: 8px;" maxlength="1" required>
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-input" style="width: 50px; height: 50px; padding: 0; border: 2px solid #ced4da; border-radius: 8px;" maxlength="1" required>
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-input" style="width: 50px; height: 50px; padding: 0; border: 2px solid #ced4da; border-radius: 8px;" maxlength="1" required>
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-input" style="width: 50px; height: 50px; padding: 0; border: 2px solid #ced4da; border-radius: 8px;" maxlength="1" required>
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-input" style="width: 50px; height: 50px; padding: 0; border: 2px solid #ced4da; border-radius: 8px;" maxlength="1" required>
                                <input type="text" class="form-control text-center fs-4 fw-bold otp-input" style="width: 50px; height: 50px; padding: 0; border: 2px solid #ced4da; border-radius: 8px;" maxlength="1" required>
                            </div>
                            <input type="hidden" name="otp" id="otp-hidden" required>
                        </div>

                        <button type="submit" name="verify_otp" class="btn btn-primary w-100 btn-lg btn-signin mb-4">
                            Verify OTP
                        </button>
                    </form>

                <?php elseif ($type === 'set-new-password'): ?>
                    <?php echo $user_display_html; ?>
                    <div class="form-header mb-4">
                        <h2 class="fw-bold">Set New Password</h2>
                        <p class="text-muted">Enter your new password below</p>
                    </div>

                    <form id="newPasswordForm" method="post" action="login.php">
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">New Password</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your new password" required>
                                <button type="button" class="btn-toggle-password" id="togglePassword">
                                    <i class="fa-regular fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label fw-medium">Confirm New Password</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your new password" required>
                                <button type="button" class="btn-toggle-password" id="toggleConfirmPassword">
                                    <i class="fa-regular fa-eye" id="eyeConfirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" name="set_password" class="btn btn-primary w-100 btn-lg btn-signin mb-4 mt-3">
                            Set New Password
                        </button>
                    </form>

                <?php else: ?>
                    <div class="form-header mb-4">
                        <h2 class="fw-bold">Forgot Password</h2>
                        <p class="text-muted">Enter your email to find your account</p>
                    </div>

                    <form id="findAccountForm" method="post" action="?type=enter-otp">
                        <div class="mb-4">
                            <label for="email" class="form-label fw-medium">Email address</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="youremail@email.com" required>
                            </div>
                        </div>

                        <button type="submit" name="find_account" class="btn btn-primary w-100 btn-lg btn-signin mb-4">
                            Find Account
                        </button>
                    </form>
                <?php endif; ?>

                <div class="text-center">
                    <p class="text-secondary mb-1">Remember your password? <a href="login.php" class="fw-semibold">Sign in</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const eyeIcon = document.getElementById('eyeIcon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }
        
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        if (toggleConfirmPassword) {
            toggleConfirmPassword.addEventListener('click', function() {
                const passwordInput = document.getElementById('confirm_password');
                const eyeIcon = document.getElementById('eyeConfirmIcon');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }

        const otpInputs = document.querySelectorAll('.otp-input');
        const otpHidden = document.getElementById('otp-hidden');
        
        if (otpInputs.length > 0) {
            otpInputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    if (e.target.value.length > 1) {
                        e.target.value = e.target.value.slice(0, 1);
                    }
                    if (e.target.value !== '' && index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                    updateHiddenOtp();
                });
                
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                });

                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, otpInputs.length);
                    for (let i = 0; i < pastedData.length; i++) {
                        otpInputs[i].value = pastedData[i];
                        if (i < otpInputs.length - 1) {
                            otpInputs[i + 1].focus();
                        }
                    }
                    updateHiddenOtp();
                });
            });

            function updateHiddenOtp() {
                let otpValue = '';
                otpInputs.forEach(input => {
                    otpValue += input.value;
                });
                otpHidden.value = otpValue;
            }
        }
    });
</script>

<?php include 'components/footer.php'; ?>
