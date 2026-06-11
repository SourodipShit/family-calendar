<?php
$path_prefix = "";
$is_public_page = true;
require_once __DIR__ . '/classes/GlobalSettings.php';

$type = $_GET['type'] ?? 'terms_of_service';

// Define valid types and their titles
$valid_types = [
    'terms_of_service' => 'Terms of Service',
    'privacy_policy' => 'Privacy Policy',
    'opt_in_agreement' => 'Opt-In/Opt-Out Agreement'
];

if (!array_key_exists($type, $valid_types)) {
    $type = 'terms_of_service';
}

$page_title = $valid_types[$type] . " - Family Calendar";
$page_image = "";
include_once __DIR__ . '/components/header.php';

$setting_img = GlobalSettings::getSetting("login_page_image");
$page_image = ($setting_img['status'] === 'success' && !empty($setting_img['data']['setting_value'])) ? $setting_img['data']['setting_value'] : "";

$setting = GlobalSettings::getSetting($type);
$content = ($setting['status'] === 'success' && !empty($setting['data']['setting_value']))
    ? $setting['data']['setting_value']
    : '<div class="text-center text-muted p-5"><i class="fa-solid fa-file-circle-xmark fs-1 mb-3"></i><p>Content not available yet.</p></div>';
?>

<link rel="stylesheet" href="public/css/login.css">

<div class="login-container">
    <div class="row g-0">
        <!-- Left Side: Illustration & Branding -->
        <div class="col-lg-6 d-none d-lg-flex flex-column justify-content-center align-items-center left-panel" style="position: sticky; top: 0; height: 100vh; overflow-y: hidden;">
            <div class="brand-content w-100" style="max-width: 480px; padding: 2rem;">
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
                <p class="lead mb-5 text-secondary">Organize meals, events, chores and important moments – all in one place for your family.</p>

                <div class="illustration-container mt-4 mb-5" style="height: 250px;">
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

        <!-- Right Side: Agreement Content -->
        <div class="col-lg-6 d-flex justify-content-center right-panel bg-white">
            <div class="login-form-wrapper" style="max-width: 800px; width: 100%; padding: 4rem 2rem;">
                <div class="mobile-logo d-lg-none mb-4">
                    <i class="fa-solid fa-calendar-check text-primary fs-1"></i>
                    <span class="h3 fw-bold ms-2">Family Calendar</span>
                </div>

                <!-- Back to Login / Home -->
                <div class="mb-4">
                    <a href="login.php" class="text-decoration-none text-secondary hover-primary transition-all fw-medium">
                        <i class="fa-solid fa-arrow-left me-2"></i> Back
                    </a>
                </div>

                <div class="form-header mb-4 d-flex align-items-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle me-3" style="width: 56px; height: 56px;">
                        <?php if ($type === 'terms_of_service'): ?>
                            <i class="fa-solid fa-file-contract fs-3"></i>
                        <?php elseif ($type === 'privacy_policy'): ?>
                            <i class="fa-solid fa-shield-halved fs-3"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-clipboard-check fs-3"></i>
                        <?php endif; ?>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($valid_types[$type]); ?></h2>
                </div>

                <hr class="mb-5 border-light border-2 opacity-50">

                <div class="agreement-content pb-5">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-primary:hover {
        color: #0d6efd !important;
    }

    .transition-all {
        transition: all 0.2s ease;
    }

    .agreement-content {
        line-height: 1.8;
        color: #4b5563;
        font-size: 1.05rem;
    }

    .agreement-content h1,
    .agreement-content h2,
    .agreement-content h3 {
        color: #1f2937;
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }

    .agreement-content h1 {
        font-size: 1.75rem;
    }

    .agreement-content h2 {
        font-size: 1.5rem;
    }

    .agreement-content h3 {
        font-size: 1.25rem;
    }

    .agreement-content p {
        margin-bottom: 1.25rem;
    }

    .agreement-content a {
        color: #2563eb;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .agreement-content a:hover {
        color: #1d4ed8;
    }

    .agreement-content ul,
    .agreement-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }

    .agreement-content li {
        margin-bottom: 0.5rem;
    }

    .agreement-content blockquote {
        border-left: 4px solid #e5e7eb;
        padding-left: 1rem;
        color: #6b7280;
        font-style: italic;
        margin-bottom: 1.25rem;
    }
</style>

<?php include 'components/footer.php'; ?>