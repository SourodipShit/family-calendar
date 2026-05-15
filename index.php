<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header("Location: users/index.php");
    exit;
}
$path_prefix = "";
$page_title = "Sign Up - Family Calendar";
$is_public_page = true;
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/Auth.php';

if (isset($_POST['signup'])) {
    require_once __DIR__ . '/classes/File.php';
    $imagePath = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Temporarily change directory to 'users' so File::upload points to the right place
        $originalDir = getcwd();
        if (!file_exists('users')) mkdir('users'); // Safety check
        chdir('users');
        $upload = File::upload($_FILES['profile_image'], 'profiles');
        chdir($originalDir);
        
        if ($upload['status'] === 'success') {
            $imagePath = $upload['filePath'];
        }
    }

    $data = [
        'user' => [
            'name' => $_POST['head_name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'image' => $imagePath
        ],
        'family' => [
            'name' => $_POST['family_name'],
            'email' => $_POST['family_email'],
            'location' => $_POST['location'],
            'timezone' => $_POST['timezone']
        ]
    ];
    $result = Auth::register($data);
    if ($result['status'] == 'success') {
        $success = "Registration successful! Redirecting to login...";
    } else {
        $error = $result['message'];
    }
}
?>

<link rel="stylesheet" href="public/css/login.css">
<style>
    .step-content {
        display: none;
    }
    .step-content.active {
        display: block;
        animation: fadeIn 0.4s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .step-indicator-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 2rem;
    }
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: var(--border-color);
        transition: all 0.3s ease;
    }
    .dot.active {
        background-color: var(--primary-blue);
        transform: scale(1.2);
    }
</style>

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

                <h1 class="display-5 fw-bold mb-3">Start your journey.<br>Bring family closer.</h1>
                <p class="lead mb-5 text-secondary">Set up your family calendar in minutes and start organizing your life together.</p>

                <div class="illustration-container mt-4 mb-5">
                    <img src="https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&q=80&w=800"
                        alt="Family Calendar Illustration" class="img-fluid illustration-img" style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                </div>

                <div class="privacy-badge mt-auto">
                    <div class="badge-icon">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <div class="badge-text">
                        <strong>Private & Secure.</strong>
                        <p class="mb-0">Your family's schedule is completely private.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Sign Up Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center right-panel">
            <div class="login-form-wrapper" style="max-width: 450px;">
                <div class="mobile-logo d-lg-none mb-4 text-center">
                    <i class="fa-solid fa-calendar-check text-primary fs-1"></i>
                    <span class="h3 fw-bold ms-2">Family Calendar</span>
                </div>

                <div class="form-header mb-2 text-center">
                    <h2 class="fw-bold">Create Account</h2>
                    <p class="text-muted">Set up your new family calendar</p>
                </div>
                
                <div class="step-indicator-dots">
                    <div class="dot active" id="dot-1"></div>
                    <div class="dot" id="dot-2"></div>
                </div>

                <form id="signupForm" method="post" action="" enctype="multipart/form-data">
                    
                    <!-- Part 1: Family Head Setup -->
                    <div class="step-content active" id="step-1">
                        <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-user me-2"></i>Family Head Setup</h5>
                        
                        <!-- Name Input -->
                        <div class="mb-3">
                            <label for="head_name" class="form-label fw-medium">Your Name</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-regular fa-user"></i></span>
                                <input type="text" class="form-control" id="head_name" name="head_name" placeholder="John Doe" required>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">Email address</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-regular fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" placeholder="youremail@email.com" required>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                                <button type="button" class="btn-toggle-password" id="togglePassword">
                                    <i class="fa-regular fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Profile Image Input -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Profile Image <span class="text-muted small">(Optional)</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="imagePreview" class="rounded-circle border d-flex align-items-center justify-content-center bg-light shadow-sm" style="width: 50px; height: 50px; overflow: hidden; flex-shrink: 0;">
                                    <i class="fa-solid fa-user text-muted fs-5"></i>
                                </div>
                                <input type="file" class="form-control form-control-sm" id="profile_image" name="profile_image" accept="image/*" onchange="previewProfileImage(this)">
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100 btn-lg btn-signin mb-4" id="btnNext">
                            Continue to Family Setup <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <!-- Part 2: Family Setup -->
                    <div class="step-content" id="step-2">
                        <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-users me-2"></i>Family Details</h5>
                        
                        <!-- Family Name Input -->
                        <div class="mb-3">
                            <label for="family_name" class="form-label fw-medium">Family Name</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-people-roof"></i></span>
                                <input type="text" class="form-control" id="family_name" name="family_name" placeholder="e.g. The Johnsons">
                            </div>
                        </div>

                        <!-- Family Email Input -->
                        <div class="mb-3">
                            <label for="family_email" class="form-label fw-medium">Family Email</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-envelope-open-text"></i></span>
                                <input type="email" class="form-control" id="family_email" name="family_email" placeholder="family@example.com">
                            </div>
                        </div>

                        <!-- Location Input -->
                        <div class="mb-3">
                            <label for="location" class="form-label fw-medium">Location</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-location-dot"></i></span>
                                <input type="text" class="form-control" id="location" name="location" placeholder="City, State/Country">
                            </div>
                        </div>

                        <!-- Timezone Input -->
                        <div class="mb-4">
                            <label for="timezone" class="form-label fw-medium">Timezone</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-globe"></i></span>
                                <select class="form-control text-muted" id="timezone" name="timezone" style="appearance: auto;">
                                    <option value="" disabled selected>Select your timezone</option>
                                    <?php
                                    require_once __DIR__ . '/classes/GlobalSettings.php';
                                    $timezone_setting = GlobalSettings::getSetting('timezone');
                                    $timezones = [];
                                    if ($timezone_setting['status'] === 'success' && !empty($timezone_setting['data'])) {
                                        $timezones = json_decode($timezone_setting['data']['setting_value'], true);
                                    }
                                    if (is_array($timezones)) {
                                        foreach ($timezones as $tz) {
                                            $val = htmlspecialchars($tz['timezone']);
                                            $lbl = htmlspecialchars($tz['lable']);
                                            echo "<option value=\"$val\">$lbl</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <button type="button" class="btn btn-light btn-lg flex-grow-1 border fw-semibold text-secondary" id="btnPrev">
                                <i class="fa-solid fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="submit" name="signup" class="btn btn-primary btn-lg flex-grow-1 btn-signin">
                                Complete Sign Up <i class="fa-solid fa-check ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="divider mb-4">
                        <span>or</span>
                    </div>

                    <!-- Footer Link -->
                    <div class="text-center">
                        <p class="text-secondary">Already have an account? <a href="login.php" class="fw-semibold">Sign in here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (isset($error)): ?>
            showAlert("<?php echo $error; ?>", "error");
        <?php endif; ?>

        <?php if (isset($success)): ?>
            showAlert("<?php echo $success; ?>", "success");
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        <?php endif; ?>

        // Password toggle
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

        // Step navigation
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const dot1 = document.getElementById('dot-1');
        const dot2 = document.getElementById('dot-2');
        const btnNext = document.getElementById('btnNext');
        const btnPrev = document.getElementById('btnPrev');

        // Prevent enter key from submitting the form early
        document.getElementById('signupForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (step1.classList.contains('active')) {
                    btnNext.click();
                } else if (step2.classList.contains('active')) {
                    this.submit();
                }
            }
        });

        btnNext.addEventListener('click', function() {
            // Basic validation for step 1
            const name = document.getElementById('head_name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if(!name || !email || !password) {
                if(typeof showAlert === 'function') {
                    showAlert('Please fill in all fields before continuing.', 'error');
                } else {
                    alert('Please fill in all fields before continuing.');
                }
                return;
            }

            step1.classList.remove('active');
            step2.classList.add('active');
            dot1.classList.remove('active');
            dot2.classList.add('active');
        });

        btnPrev.addEventListener('click', function() {
            step2.classList.remove('active');
            step1.classList.add('active');
            dot2.classList.remove('active');
            dot1.classList.add('active');
        });
        
        try {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            const tzSelect = document.getElementById('timezone');
            for(let i=0; i<tzSelect.options.length; i++) {
                if(tzSelect.options[i].value === tz) {
                    tzSelect.selectedIndex = i;
                    tzSelect.classList.remove('text-muted');
                    break;
                }
            }
        } catch(e) {}
        
        window.previewProfileImage = function(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<i class="fa-solid fa-user text-muted fs-5"></i>';
            }
        };
        
        document.getElementById('timezone').addEventListener('change', function() {
            this.classList.remove('text-muted');
        });
    });
</script>

<?php include 'components/footer.php'; ?>
