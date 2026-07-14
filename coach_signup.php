<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    header("Location: users/index.php");
    exit;
}
$path_prefix = "";
$page_title = "Coach Sign Up - Family Calendar";
$page_image = "";
$is_public_page = true;
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/GlobalSettings.php';
include_once __DIR__ . '/classes/CoachCategory.php';

$setting = GlobalSettings::getSetting("sign_up_page_image");
$page_image = ($setting['status'] === 'success' && !empty($setting['data']['setting_value'])) ? $setting['data']['setting_value'] : "";

// Fetch Categories for the dropdown
$categoriesResult = CoachCategory::getAll();
$categories = $categoriesResult['status'] === true ? $categoriesResult['data'] : [];

if (isset($_POST['signup_coach'])) {
    require_once __DIR__ . '/classes/Auth.php';
    require_once __DIR__ . '/classes/File.php';
    
    $imagePath = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $originalDir = getcwd();
        if (!file_exists('users')) mkdir('users');
        chdir('users');
        $upload = File::upload($_FILES['profile_image'], 'profiles');
        chdir($originalDir);
        
        if ($upload['status'] === 'success') {
            $imagePath = $upload['filePath'];
        }
    }

    $certifications = [];
    if (isset($_POST['cert_name']) && is_array($_POST['cert_name'])) {
        foreach ($_POST['cert_name'] as $index => $name) {
            if (!empty($name)) {
                $certFile = null;
                if (isset($_FILES['cert_image']['name'][$index]) && $_FILES['cert_image']['error'][$index] === UPLOAD_ERR_OK) {
                    $certFile = [
                        'name' => $_FILES['cert_image']['name'][$index],
                        'type' => $_FILES['cert_image']['type'][$index],
                        'tmp_name' => $_FILES['cert_image']['tmp_name'][$index],
                        'error' => $_FILES['cert_image']['error'][$index],
                        'size' => $_FILES['cert_image']['size'][$index]
                    ];
                }
                
                $certifications[] = [
                    'name' => $name,
                    'description' => $_POST['cert_description'][$index] ?? '',
                    'file' => $certFile
                ];
            }
        }
    }

    $data = [
        'user' => [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ?? '',
            'image' => $imagePath
        ],
        'coach' => [
            'description' => $_POST['description'] ?? '',
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'certifications' => $certifications
        ]
    ];
    
    $originalDir = getcwd();
    if (!file_exists('users')) mkdir('users');
    chdir('users');
    $result = Auth::registerAsCoach($data);
    chdir($originalDir);
    
    if ($result['status'] == 'success') {
        $success = "Coach registration successful! Redirecting to login...";
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
                        <i class="fa-solid fa-dumbbell"></i>
                        <div class="family-avatars">
                            <i class="fa-solid fa-person-chalkboard"></i>
                        </div>
                    </div>
                    <div class="logo-text">
                        <span class="logo-title">Family</span>
                        <span class="logo-subtitle">Calendar</span>
                    </div>
                </div>

                <h1 class="display-5 fw-bold mb-3">Become a Coach.<br>Guide families to success.</h1>
                <p class="lead mb-5 text-secondary">Join our platform as a certified coach and offer your expertise to families.</p>

                <div class="illustration-container mt-4 mb-5">
                    <?php $img_src = !empty($page_image) ? str_replace('../', $path_prefix, $page_image) : 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?auto=format&fit=crop&q=80&w=800'; ?>
                    <img src="<?php echo htmlspecialchars($img_src); ?>"
                        alt="Coach Sign Up Illustration" class="img-fluid illustration-img" style="border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); width: 100%; height: 100%; object-fit: cover;">
                </div>

                <div class="privacy-badge mt-auto">
                    <div class="badge-icon">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <div class="badge-text">
                        <strong>Private & Secure.</strong>
                        <p class="mb-0">Your professional details are safely stored.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Sign Up Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center right-panel">
            <div class="login-form-wrapper" style="max-width: 450px;">
                <div class="mobile-logo d-lg-none mb-4 text-center">
                    <i class="fa-solid fa-dumbbell text-primary fs-1"></i>
                    <span class="h3 fw-bold ms-2">Family Calendar</span>
                </div>

                <div class="form-header mb-2 text-center">
                    <h2 class="fw-bold">Coach Registration</h2>
                    <p class="text-muted">Create your professional profile</p>
                </div>
                
                <div class="d-flex justify-content-center mb-4">
                    <div class="bg-light rounded-pill p-1 d-inline-flex" style="border: 1px solid var(--border-color);">
                        <a href="index.php" class="btn btn-sm rounded-pill fw-medium px-4 text-muted text-decoration-none" style="transition: all 0.2s;">Family Head</a>
                        <a href="coach_signup.php" class="btn btn-sm rounded-pill fw-medium px-4 btn-primary text-white text-decoration-none" style="transition: all 0.2s;">Coach</a>
                    </div>
                </div>

                <div class="step-indicator-dots">
                    <div class="dot active" id="dot-1"></div>
                    <div class="dot" id="dot-2"></div>
                </div>

                <form id="signupForm" method="post" action="" enctype="multipart/form-data">
                    
                    <!-- Part 1: Personal Info -->
                    <div class="step-content active" id="step-1">
                        <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-user me-2"></i>Personal Information</h5>
                        
                        <!-- Name Input -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Your Name</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-regular fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" required>
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

                        <!-- Phone Input -->
                        <div class="mb-3">
                            <label for="phone" class="form-label fw-medium">Mobile Number</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your mobile number" required>
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
                            <label class="form-label fw-medium">Profile Photo <span class="text-muted small">(Optional)</span></label>
                            <div class="d-flex align-items-center gap-3">
                                <div id="imagePreview" class="rounded-circle border d-flex align-items-center justify-content-center bg-light shadow-sm" style="width: 50px; height: 50px; overflow: hidden; flex-shrink: 0;">
                                    <i class="fa-solid fa-user text-muted fs-5"></i>
                                </div>
                                <input type="file" class="form-control form-control-sm" id="profile_image" name="profile_image" accept="image/*" onchange="previewProfileImage(this)">
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100 btn-lg btn-signin mb-4" id="btnNext">
                            Continue to Coach Details <i class="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <!-- Part 2: Coach Details -->
                    <div class="step-content" id="step-2">
                        <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-id-card-clip me-2"></i>Professional Details</h5>
                        
                        <!-- Category Input -->
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-medium">Specialty/Category</label>
                            <div class="input-group-custom">
                                <span class="input-icon"><i class="fa-solid fa-list"></i></span>
                                <select class="form-select border-0 bg-transparent ps-5 py-2 w-100" id="category_id" name="category_id" style="outline:none; box-shadow:none; height:45px;" required>
                                    <option value="" selected disabled>Select your category...</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Description Input -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-medium">Professional Description</label>
                            <textarea class="form-control rounded-3 border bg-light" id="description" name="description" rows="3" placeholder="Tell us about your experience and how you can help families..." required></textarea>
                        </div>

                        <!-- Certifications Area -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-medium mb-0">Certifications</label>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3" id="addCertificationBtn">
                                    <i class="fa-solid fa-plus me-1"></i> Add
                                </button>
                            </div>
                            <small class="text-muted d-block mb-3">Add your certifications, degrees, or licenses.</small>
                            
                            <div id="certificationsContainer" class="d-flex flex-column gap-3">
                                <!-- First Certification (Required) -->
                                <div class="certification-item border rounded-3 p-3 bg-white shadow-sm position-relative">
                                    <div class="mb-2">
                                        <input type="text" class="form-control form-control-sm" name="cert_name[]" placeholder="Certification Name" required>
                                    </div>
                                    <div class="mb-2">
                                        <textarea class="form-control form-control-sm" name="cert_description[]" rows="2" placeholder="Brief description (optional)"></textarea>
                                    </div>
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <input type="file" class="form-control form-control-sm cert-file-input" name="cert_image[]" accept="image/*" onchange="previewCertImage(this, 0)" required>
                                        </div>
                                        <div id="cert-preview-0" class="cert-image-preview rounded border bg-light d-flex align-items-center justify-content-center d-none" style="width: 50px; height: 50px; overflow: hidden; flex-shrink: 0;">
                                            <i class="fa-solid fa-image text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Timezone Input -->
                        <input type="hidden" id="timezone" name="timezone" value="">

                        <div class="d-flex gap-2 mb-4">
                            <button type="button" class="btn btn-light btn-lg flex-grow-1 border fw-semibold text-secondary" id="btnPrev">
                                <i class="fa-solid fa-arrow-left me-2"></i> Back
                            </button>
                            <button type="submit" name="signup_coach" class="btn btn-primary btn-lg flex-grow-1 btn-signin">
                                Submit Registration <i class="fa-solid fa-check ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="divider mb-4">
                        <span>or</span>
                    </div>

                    <!-- Footer Link -->
                    <div class="text-center">
                        <p class="text-secondary mb-4">Already have an account? <a href="login.php" class="fw-semibold">Sign in here</a></p>
                        
                        <div class="mt-4 pt-3 border-top text-secondary small">
                            <span class="d-block mb-2">By registering as a coach, you agree to our</span>
                            <a href="agreement.php?type=terms_of_service" class="text-secondary text-decoration-none mx-1 hover-primary">Terms of Service</a> &bull; 
                            <a href="agreement.php?type=privacy_policy" class="text-secondary text-decoration-none mx-1 hover-primary">Privacy Policy</a>
                        </div>
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
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if(!name || !email || !password) {
                if(typeof showAlert === 'function') {
                    showAlert('Please fill in all required fields before continuing.', 'error');
                } else {
                    alert('Please fill in all required fields before continuing.');
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
            document.getElementById('timezone').value = tz;
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

        let certCount = 1;
        document.getElementById('addCertificationBtn').addEventListener('click', function() {
            const container = document.getElementById('certificationsContainer');
            const newIndex = certCount++;
            
            const div = document.createElement('div');
            div.className = 'certification-item border rounded-3 p-3 bg-white shadow-sm position-relative';
            div.innerHTML = `
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 shadow-none" aria-label="Remove" onclick="this.closest('.certification-item').remove()"></button>
                <div class="mb-2 pe-4">
                    <input type="text" class="form-control form-control-sm" name="cert_name[]" placeholder="Certification Name" required>
                </div>
                <div class="mb-2">
                    <textarea class="form-control form-control-sm" name="cert_description[]" rows="2" placeholder="Brief description (optional)"></textarea>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <div class="flex-grow-1">
                        <input type="file" class="form-control form-control-sm cert-file-input" name="cert_image[]" accept="image/*" onchange="previewCertImage(this, ${newIndex})" required>
                    </div>
                    <div id="cert-preview-${newIndex}" class="cert-image-preview rounded border bg-light d-flex align-items-center justify-content-center d-none" style="width: 50px; height: 50px; overflow: hidden; flex-shrink: 0;">
                        <i class="fa-solid fa-image text-muted"></i>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });

        window.previewCertImage = function(input, index) {
            const preview = document.getElementById('cert-preview-' + index);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '<i class="fa-solid fa-image text-muted"></i>';
                preview.classList.add('d-none');
            }
        };
    });
</script>

<?php include 'components/footer.php'; ?>
