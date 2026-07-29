<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    if (!isset($_GET['add_account'])) {
        if ($_SESSION['user']['role'] == 'siteadmin') {
            echo '<script>window.location.href = "siteadmin/index.php";</script>';
        } else if ($_SESSION['user']['role'] == 'coach') {
            echo '<script>window.location.href = "coach/index.php";</script>';
        } else {
            echo '<script>window.location.href = "users/index.php";</script>';
        }
        exit;
    }
}
$path_prefix = "";
$page_title = "Login - Family Calendar";
$page_image = "";
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/Auth.php';
include_once __DIR__ . '/classes/LoginLogs.php';
include_once __DIR__ . '/classes/GlobalSettings.php';

$setting = GlobalSettings::getSetting("login_page_image");
$page_image = ($setting['status'] === 'success' && !empty($setting['data']['setting_value'])) ? $setting['data']['setting_value'] : "";

if (isset($_GET['success_msg'])) {
    $success = urldecode($_GET['success_msg']);
}
if (isset($_GET['msg'])) {
    $error = urldecode($_GET['msg']);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = Auth::login($email, $password);

    if ($result['status'] == 'success') {
        $user = $result['data'];
        $is_siteadmin = ($user['role'] === 'siteadmin');
        $is_coach = ($user['role'] === 'coach');

        if ($is_siteadmin) {
            $_SESSION['accounts'][$user['id']] = $user;
            $_SESSION['active_account_id'] = $user['id'];
            $_SESSION['user'] = $user;
            $success = "Logged in successfully! Redirecting...";
        } else if ($is_coach) {
            require_once __DIR__ . '/config/Database.php';
            $stmt = Database::runPrepared("SELECT approval_status FROM coach_profiles WHERE user_id = ?", [$user['id']]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($profile) {
                if ($profile['approval_status'] === 'approved') {
                    $_SESSION['accounts'][$user['id']] = $user;
                    $_SESSION['active_account_id'] = $user['id'];
                    $_SESSION['user'] = $user;
                    LoginLogs::track($user['id']);
                    $success = "Logged in successfully! Redirecting to Coach Dashboard...";
                } else if ($profile['approval_status'] === 'pending') {
                    $error = "Your coach application is currently pending approval.";
                } else {
                    $error = "Your coach application has been rejected.";
                }
            } else {
                $error = "Coach profile not found!";
            }
        } else {
            if (empty($user['families'])) {
                $error = "You do not belong to any family!";
            } else if (count($user['families']) == 1) {
                $selected_family = $user['families'][0];
                if (empty($selected_family['approved'])) {
                    $error = "Family not approved!";
                } else if (!empty($selected_family['is_locked'])) {
                    $error = "Family is locked!";
                } else {
                    $user['active_family_id'] = $selected_family['family_id'] ?? $selected_family['id'];
                    $user['active_family'] = $selected_family;
                    
                    $_SESSION['accounts'][$user['id']] = $user;
                    $_SESSION['active_account_id'] = $user['id'];
                    $_SESSION['user'] = $user;
                    LoginLogs::track($user['id']);
                    $success = "Logged in successfully! Redirecting...";
                }
            } else {
                $show_family_modal = true;
                $families_list = $user['families'];
                $_SESSION['temp_login_user'] = $user;
            }
        }
    } else {
        $error = $result['message'];
    }
} else if (isset($_POST['select_family'])) {
    $family_id = $_POST['family_id'];
    if (isset($_SESSION['temp_login_user'])) {
        $user = $_SESSION['temp_login_user'];
        $selected_family = null;
        foreach ($user['families'] as $f) {
            $f_id = $f['family_id'] ?? $f['id'];
            if ($f_id == $family_id) {
                $selected_family = $f;
                break;
            }
        }
        
        if ($selected_family) {
            if (empty($selected_family['approved'])) {
                $error = "Family not approved!";
            } else if (!empty($selected_family['is_locked'])) {
                $error = "Family is locked!";
            } else {
                $user['active_family_id'] = $selected_family['family_id'] ?? $selected_family['id'];
                $user['active_family'] = $selected_family;
                
                $_SESSION['accounts'][$user['id']] = $user;
                $_SESSION['active_account_id'] = $user['id'];
                $_SESSION['user'] = $user;
                LoginLogs::track($user['id']);
                unset($_SESSION['temp_login_user']);
                $success = "Logged in successfully! Redirecting...";
            }
        } else {
            $error = "Invalid family selected.";
        }
    } else {
        $error = "Session expired. Please login again.";
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
                        <p class="text-secondary mb-4">Want to set up a new family? <a href="signup.php" class="fw-semibold">Register here</a></p>

                        <div class="mt-4 pt-3 border-top text-secondary small">
                            <a href="agreement.php?type=terms_of_service" class="text-secondary text-decoration-none mx-1 hover-primary">Terms of Service</a> &bull;
                            <a href="agreement.php?type=privacy_policy" class="text-secondary text-decoration-none mx-1 hover-primary">Privacy Policy</a> &bull;
                            <a href="agreement.php?type=opt_in_agreement" class="text-secondary text-decoration-none mx-1 hover-primary">Opt-In/Out</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($show_family_modal)): ?>
<div class="modal fade show" id="familySelectModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 p-md-5">
                <form id="familySelectForm" method="post" action="">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 64px; height: 64px;">
                            <i class="fa-solid fa-users fs-3"></i>
                        </div>
                        <h4 class="fw-bold mb-2">Select Family</h4>
                        <p class="text-muted small mb-0">You belong to multiple families. Please choose one to proceed.</p>
                    </div>
                    
                    <div class="mb-4">
                        <div class="row g-3">
                            <?php foreach ($families_list as $index => $f): 
                                $f_id = $f['family_id'] ?? $f['id'];
                                $f_name = htmlspecialchars($f['name']);
                                $f_status = "";
                                $disabled = "";
                                $card_class = "border border-secondary-subtle hover-shadow transition-all";
                                if (empty($f['approved'])) { 
                                    $f_status = "<span class='badge bg-warning text-dark ms-auto'>Pending</span>"; 
                                    $disabled = "disabled"; 
                                    $card_class = "border-light bg-light opacity-75";
                                } else if (!empty($f['is_locked'])) { 
                                    $f_status = "<span class='badge bg-danger ms-auto'>Locked</span>"; 
                                    $disabled = "disabled"; 
                                    $card_class = "border-light bg-light opacity-75";
                                }
                            ?>
                            <div class="col-12">
                                <label class="card w-100 <?php echo $card_class; ?>" style="<?php echo $disabled ? 'cursor: not-allowed;' : 'cursor: pointer;'; ?>">
                                    <div class="card-body d-flex align-items-center p-3">
                                        <div class="form-check mb-0 fs-5">
                                            <input class="form-check-input border-secondary" type="radio" name="family_id" id="family_<?php echo $f_id; ?>" value="<?php echo $f_id; ?>" <?php echo $disabled; ?> required>
                                        </div>
                                        <div class="ms-3 d-flex flex-grow-1 align-items-center">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0"><?php echo $f_name; ?></h6>
                                                <small class="text-muted"><i class="fa-solid fa-house-user me-1"></i> Family Group</small>
                                            </div>
                                            <?php echo $f_status; ?>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" name="select_family" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill mb-2 shadow-sm">Continue to Dashboard</button>
                    <a href="login.php?logout=1" class="btn btn-light btn-lg w-100 fw-medium rounded-pill text-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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
            <?php } else if ($_SESSION['user']['role'] == 'coach') { ?>
                setTimeout(() => {
                    window.location.href = 'coach/index.php';
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