<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    if (!isset($_GET['add_account'])) {
        if ($_SESSION['user']['role'] == 'siteadmin') {
            header("Location: siteadmin/index.php");
        } else {
            header("Location: users/index.php");
        }
        exit;
    }
}
$path_prefix = "";
$page_title = "Login - Family Calendar";
$page_image = "";
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/Auth.php';

include_once __DIR__ . '/classes/GlobalSettings.php';

$setting = GlobalSettings::getSetting("login_page_image");
$page_image = ($setting['status'] === 'success' && !empty($setting['data']['setting_value'])) ? $setting['data']['setting_value'] : "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = Auth::login($email, $password);

    if ($result['status'] == 'success') {
        $user = $result['data'];
        $is_siteadmin = ($user['role'] === 'siteadmin');

        $family_approved = true;
        if (!$is_siteadmin && !empty($user['families'])) {
            // Assuming we check the primary family (first one)
            $family_approved = !empty($user['families'][0]['approved']);
        }

        if (!$is_siteadmin && !$family_approved) {
            $error = "Family not approved!";
        } else {
            $_SESSION['accounts'][$user['id']] = $user;
            $_SESSION['active_account_id'] = $user['id'];
            $_SESSION['user'] = $user;
            $success = "Logged in successfully! Redirecting...";
        }
    } else {
        $error = $result['message'];
    }
}

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

        <!-- Right Side: Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center right-panel">
            <div class="login-form-wrapper">
                <div class="mobile-logo d-lg-none mb-4">
                    <i class="fa-solid fa-calendar-check text-primary fs-1"></i>
                    <span class="h3 fw-bold ms-2">Family Calendar</span>
                </div>

                <div class="form-header mb-4">
                    <h2 class="fw-bold">Welcome Back!</h2>
                    <p class="text-muted">Sign in to continue to Family Calendar</p>
                </div>

                <form id="loginForm" method="post" action="">
                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium">Email address</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="youremail@email.com" required>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Password</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="btn-toggle-password" id="togglePassword">
                                <i class="fa-regular fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label text-secondary" for="rememberMe">
                                Remember me
                            </label>
                        </div>
                        <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                    </div>

                    <!-- Sign In Button -->
                    <button type="submit" name="login" class="btn btn-primary w-100 btn-lg btn-signin mb-4">
                        Sign In
                    </button>

                    <!-- Divider -->
                    <div class="divider mb-4">
                        <span>or continue with</span>
                    </div>

                    <!-- Social Login -->
                    <div class="row g-3 mb-5">
                        <div class="col-6">
                            <button type="button" class="btn btn-social">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/60px-Google_%22G%22_logo.svg.png"
                                    alt="Google">
                                <span>Google</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-social">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg"
                                    alt="Microsoft">
                                <span>Microsoft</span>
                            </button>
                        </div>
                    </div>

                    <!-- Footer Link -->
                    <div class="text-center">
                        <p class="text-secondary mb-1">Don't have an account? <a href="#" class="fw-semibold">Contact
                                your admin</a></p>
                        <p class="text-secondary">Want to set up a new family? <a href="index.php" class="fw-semibold">Register here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success'): ?>
            showAlert("Logged out successfully!", "success");
            // Remove the parameter from the URL without refreshing
            if (window.history.replaceState) {
                const url = new URL(window.location.href);
                url.searchParams.delete('logout');
                window.history.replaceState({}, document.title, url.pathname);
            }
        <?php endif; ?>

        <?php if (isset($error)): ?>
            showAlert("<?php echo $error; ?>", "error");
        <?php endif; ?>

        <?php if (isset($success)): ?>
            showAlert("<?php echo $success; ?>", "success");
            <?php if ($_SESSION['user']['role'] == 'siteadmin') { ?>
                setTimeout(() => {
                    window.location.href = 'siteadmin/index.php';
                }, 2000);
            <?php } else { ?>
                setTimeout(() => {
                    window.location.href = 'users/index.php';
                }, 2000);
            <?php } ?>
        <?php endif; ?>

        document.getElementById('togglePassword').addEventListener('click', function() {
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

        /* document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Add a nice animation or redirection here
            this.querySelector('button[type="submit"]').innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Signing In...';
            setTimeout(() => {
                window.location.href = 'users/index.php';
            }, 1500);
        }); */
    });
</script>

<?php include 'components/footer.php'; ?>