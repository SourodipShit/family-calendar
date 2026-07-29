<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard based on role
if (isset($_SESSION['user']) && !empty($_SESSION['user'])) {
    if ($_SESSION['user']['role'] == 'siteadmin') {
        header("Location: siteadmin/index.php");
    } else if ($_SESSION['user']['role'] == 'coach') {
        header("Location: coach/index.php");
    } else {
        header("Location: users/index.php");
    }
    exit;
}

$page_title = "iS4B Family Calendar - Organize Your Family Life";
$is_public_page = true;
$no_wrapper = true; // To avoid the dashboard wrapper from header.php
include_once __DIR__ . '/components/header.php';
include_once __DIR__ . '/classes/GlobalSettings.php';

$setting = GlobalSettings::getSetting("sign_up_page_image");
$hero_image = ($setting['status'] === 'success' && !empty($setting['data']['setting_value'])) ? $setting['data']['setting_value'] : 'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&q=80&w=1200';
if (strpos($hero_image, 'http') === false) {
    $hero_image = str_replace('../', '', $hero_image);
}
?>

<style>
/* Custom Landing Page Styles */
:root {
    --primary-blue: #0d6efd;
    --primary-hover: #0b5ed7;
    --text-main: #2b3440;
    --text-muted: #6c757d;
    --bg-light: #f8faff;
}

body {
    background-color: #ffffff;
    font-family: 'Google Sans', 'Inter', sans-serif;
    color: var(--text-main);
}

/* Navbar */
.landing-navbar {
    padding: 1.5rem 0;
    background: transparent;
    transition: all 0.3s ease;
}
.landing-navbar.scrolled {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    padding: 1rem 0;
}
.navbar-brand .logo-icon {
    width: 40px; 
    height: 40px;
    background: var(--primary-blue);
    color: white;
    border-radius: 10px;
    display: inline-flex;
    align-items: center; 
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 10px;
    box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
}
.navbar-brand span { 
    font-weight: 700; 
    font-size: 1.25rem; 
    color: #333; 
}
.btn-login { 
    color: #333; 
    font-weight: 600; 
    padding: 0.5rem 1.5rem; 
    border-radius: 50rem; 
    transition: all 0.2s; 
}
.btn-login:hover { 
    background: #f0f4f8; 
    color: var(--primary-blue); 
}
.btn-signup { 
    font-weight: 600; 
    padding: 0.5rem 1.5rem; 
    border-radius: 50rem; 
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2); 
    transition: all 0.2s; 
}
.btn-signup:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 6px 15px rgba(13, 110, 253, 0.3); 
}

/* Hero Section */
.hero-section {
    padding: 9rem 0 5rem;
    background: linear-gradient(135deg, #f0f7ff 0%, #fff5f8 100%);
    position: relative;
    overflow: hidden;
}
.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.15;
    margin-bottom: 1.5rem;
    color: #1a1f36;
    letter-spacing: -1px;
}
.hero-subtitle {
    font-size: 1.25rem;
    color: #4f566b;
    margin-bottom: 2.5rem;
    line-height: 1.6;
}
.hero-img-wrapper {
    position: relative;
    perspective: 1000px;
}
.hero-img {
    border-radius: 24px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.1);
    transform: rotateY(-5deg) rotateX(5deg);
    animation: float 6s infinite ease-in-out;
}
@keyframes float {
    0%, 100% { transform: rotateY(-5deg) rotateX(5deg) translateY(0); }
    50% { transform: rotateY(-2deg) rotateX(2deg) translateY(-15px); }
}

/* Features */
.features-section { 
    padding: 7rem 0; 
    background-color: #fff; 
}
.section-title { 
    font-size: 2.5rem; 
    font-weight: 700; 
    margin-bottom: 1rem; 
    text-align: center; 
    color: #1a1f36; 
}
.section-subtitle { 
    font-size: 1.15rem; 
    color: var(--text-muted); 
    text-align: center; 
    margin-bottom: 4rem; 
}
.feature-card {
    padding: 2.5rem;
    border-radius: 20px;
    background: #fff;
    border: 1px solid #f0f0f0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    transition: all 0.3s ease;
    height: 100%;
}
.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    border-color: rgba(13, 110, 253, 0.1);
}
.feature-icon {
    width: 60px; 
    height: 60px;
    border-radius: 16px;
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
}
.icon-blue { background: #eff6ff; color: #0d6efd; }
.icon-pink { background: #fff0f5; color: #d81b60; }
.icon-green { background: #f0fdf4; color: #10b981; }
.icon-yellow { background: #fffbeb; color: #f59e0b; }

/* CTA Section */
.cta-section {
    padding: 6rem 0;
    background: var(--primary-blue);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cta-section::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2v-4h4v-2h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2v-4h4v-2H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.3;
}
.cta-title { 
    font-size: 2.75rem; 
    font-weight: 700; 
    margin-bottom: 1.5rem; 
    position: relative; 
    z-index: 1; 
}
.btn-cta {
    background: white; 
    color: var(--primary-blue);
    font-size: 1.1rem; 
    font-weight: 600; 
    padding: 1rem 2.5rem;
    border-radius: 50rem;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative; 
    z-index: 1;
}
.btn-cta:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 15px 30px rgba(0,0,0,0.15); 
    color: var(--primary-blue); 
    background: #f8f9fa; 
}

/* Footer */
.landing-footer {
    padding: 5rem 0 2rem;
    background: #f8f9fa;
    border-top: 1px solid #eaeaea;
}
.footer-logo .logo-icon { 
    width: 32px; 
    height: 32px; 
    font-size: 1rem; 
    border-radius: 8px; 
}
.footer-links h6 { 
    font-weight: 700; 
    margin-bottom: 1.25rem; 
    color: #1a1f36; 
}
.footer-links ul { 
    list-style: none; 
    padding: 0; 
    margin: 0; 
}
.footer-links li { 
    margin-bottom: 0.75rem; 
}
.footer-links a { 
    color: var(--text-muted); 
    text-decoration: none; 
    transition: color 0.2s; 
}
.footer-links a:hover { 
    color: var(--primary-blue); 
}

@media (max-width: 991.98px) {
    .hero-title {
        font-size: 2.5rem;
    }
    .hero-section {
        padding: 7rem 0 4rem;
    }
    .cta-title {
        font-size: 2.25rem;
    }
    .landing-navbar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        padding: 1rem 0;
    }
}
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top landing-navbar" id="navbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <div class="logo-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <span>iS4B Family Calendar</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link fw-medium text-dark" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark" href="coach_signup.php">For Coaches</a></li>
                <li class="nav-item"><a class="nav-link fw-medium text-dark" href="login.php">Dashboard</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                <a href="login.php" class="btn btn-login text-decoration-none">Log In</a>
                <a href="signup.php" class="btn btn-primary btn-signup text-decoration-none">Get Started</a>
            </div>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="pe-lg-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold mb-4 border border-primary border-opacity-25">
                        <i class="fa-solid fa-star me-1 text-warning"></i> The #1 app for busy families
                    </span>
                    <h1 class="hero-title">Organize your family life, <span class="text-primary">effortlessly.</span></h1>
                    <p class="hero-subtitle">Shared calendars, chores, meal planning, and rewards all in one place. Bring your family closer by managing the chaos together.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="signup.php" class="btn btn-primary btn-lg btn-signup px-5 py-3">Start for Free</a>
                        <a href="#features" class="btn btn-outline-secondary btn-lg px-4 py-3 rounded-pill fw-semibold bg-white text-dark">Explore Features</a>
                    </div>
                    
                    <div class="mt-5 d-flex align-items-center gap-3">
                        <div class="d-flex">
                            <img src="https://ui-avatars.com/api/?name=Sarah+M&background=random" class="rounded-circle border border-2 border-white" width="40" style="margin-left: 0; z-index: 4;">
                            <img src="https://ui-avatars.com/api/?name=John+D&background=random" class="rounded-circle border border-2 border-white" width="40" style="margin-left: -15px; z-index: 3;">
                            <img src="https://ui-avatars.com/api/?name=Emily+R&background=random" class="rounded-circle border border-2 border-white" width="40" style="margin-left: -15px; z-index: 2;">
                            <div class="rounded-circle border border-2 border-white bg-light d-flex align-items-center justify-content-center text-muted" style="width: 40px; height: 40px; margin-left: -15px; z-index: 1; font-size: 0.8rem; font-weight: bold;">5k+</div>
                        </div>
                        <div class="text-muted small fw-medium">Loved by 5,000+ families</div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="hero-img-wrapper">
                    <img src="<?php echo htmlspecialchars($hero_image); ?>" alt="Family organizing schedule" class="img-fluid hero-img" style="width: 100%; object-fit: cover;">
                    
                    <!-- Floating UI elements for aesthetic appeal -->
                    <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center gap-3 d-none d-sm-flex" style="bottom: 10%; left: -5%; animation: float 5s infinite ease-in-out reverse; z-index: 10;">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-solid fa-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-1">Chore Completed!</div>
                            <div class="text-muted small">Liam earned 50 points</div>
                        </div>
                    </div>
                    
                    <div class="position-absolute bg-white rounded-4 shadow-lg p-3 d-flex align-items-center gap-3 d-none d-sm-flex" style="top: 15%; right: -5%; animation: float 7s infinite ease-in-out; z-index: 10;">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="fa-regular fa-calendar-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark mb-1">Soccer Practice</div>
                            <div class="text-muted small">Today at 4:00 PM</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-5" style="background-color: #f8faff;">
    <div class="container text-center py-5">
        <h2 class="section-title mb-4">About iS4B Family Calendar</h2>
        <p class="lead text-muted" style="max-width: 800px; margin: 0 auto; line-height: 1.8;">
            iS4B Family Calendar helps families manage shared calendars, events, meals, coaching sessions, appointments, and daily activities in one place. By centralizing your family's schedule and responsibilities, we make it easier to stay connected and organized.
        </p>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <h2 class="section-title">Everything your family needs</h2>
        <p class="section-subtitle">Replace 5 different apps with one beautifully integrated platform.</p>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon icon-blue">
                        <i class="fa-regular fa-calendar-days"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Shared Calendar</h4>
                    <p class="text-muted mb-0">Keep everyone on the same page. Sync with Google Calendar, set reminders, and color-code events for each family member.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon icon-green">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Chore Tracking</h4>
                    <p class="text-muted mb-0">Make chores fun again. Assign tasks, set recurrences, and track progress with our interactive point-based reward system.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon icon-pink">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Meal Planning</h4>
                    <p class="text-muted mb-0">Plan weekly meals, save your favorite recipes, and automatically generate categorized grocery lists.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon icon-yellow">
                        <i class="fa-solid fa-gift"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Rewards & Points</h4>
                    <p class="text-muted mb-0">Motivate kids by awarding points for completed chores. Let them redeem points for custom rewards like "Extra Screen Time".</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon icon-blue" style="background: #f3e8ff; color: #9333ea;">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Coach Marketplace</h4>
                    <p class="text-muted mb-0">Hire professional coaches to guide your family. Receive actionable plans, track habits, and get expert feedback.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="feature-card">
                    <div class="feature-icon icon-green" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa-solid fa-mobile-screen"></i>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Multi-Device Sync</h4>
                    <p class="text-muted mb-0">Access from anywhere. Seamlessly switch between parent and child profiles on a shared kitchen tablet or personal phones.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SMS Notifications Section -->
<section class="py-5" style="background-color: #f8f9fa; border-top: 1px solid #eaeaea;">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h3 class="fw-bold mb-3 text-dark">Stay Updated with SMS</h3>
                <p class="text-muted mb-4 fs-5">Users who opt in may receive SMS notifications to help them stay on top of their family's schedule. You can receive updates for:</p>
                <ul class="list-unstyled text-muted fs-6">
                    <li class="mb-3"><i class="fa-solid fa-check text-primary me-2"></i>Event reminders</li>
                    <li class="mb-3"><i class="fa-solid fa-check text-primary me-2"></i>Meal reminders</li>
                    <li class="mb-3"><i class="fa-solid fa-check text-primary me-2"></i>Coaching reminders</li>
                    <li class="mb-3"><i class="fa-solid fa-check text-primary me-2"></i>Calendar updates</li>
                    <li class="mb-3"><i class="fa-solid fa-check text-primary me-2"></i>Account notifications</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="p-4 p-md-5 bg-white rounded-4 shadow-sm border border-light">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                            <i class="fa-solid fa-mobile-screen fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">SMS Compliance</h4>
                    </div>
                    <p class="text-muted mb-0 lh-lg" style="font-size: 0.95rem;">
                        Message frequency varies. Message and data rates may apply. Reply <strong>STOP</strong> to opt out or <strong>HELP</strong> for assistance. See our <a href="agreement.php?type=opt_in_agreement" class="text-primary text-decoration-none fw-medium">Opt-In Policy</a> for more information.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="cta-title">Ready to organize the chaos?</h2>
        <p class="lead mb-5 text-white text-opacity-75" style="max-width: 600px; margin: 0 auto;">Join thousands of families who have streamlined their lives and found more time for what matters most.</p>
        <a href="signup.php" class="btn btn-cta text-decoration-none">Start Your Free Setup</a>
    </div>
</section>

<!-- Footer -->
<footer class="landing-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="navbar-brand d-flex align-items-center mb-3 footer-logo">
                    <div class="logo-icon bg-primary text-white d-flex align-items-center justify-content-center me-2">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <span class="fw-bold fs-5 text-dark">iS4B Family Calendar</span>
                </div>
                <p class="text-muted pe-lg-5 mb-4">Bringing families closer through better organization, shared responsibilities, and smarter planning.</p>
                <div class="mb-4">
                    <h6 class="fw-bold mb-2 text-dark">Contact Us</h6>
                    <div class="d-flex align-items-center mb-2 text-muted small">
                        <i class="fa-solid fa-envelope me-2 text-primary"></i> support@is4sb.com
                    </div>
                    <div class="d-flex align-items-center text-muted small">
                        <i class="fa-solid fa-phone me-2 text-primary"></i> 817-637-4909
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-twitter fs-5"></i></a>
                    <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-facebook fs-5"></i></a>
                    <a href="#" class="text-muted hover-primary"><i class="fa-brands fa-instagram fs-5"></i></a>
                </div>
            </div>
            
            <div class="col-6 col-lg-2 mb-4 mb-lg-0 footer-links">
                <h6>Product</h6>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="coach_signup.php">For Coaches</a></li>
                    <li><a href="#">Download App</a></li>
                </ul>
            </div>
            
            <div class="col-6 col-lg-2 mb-4 mb-lg-0 footer-links">
                <h6>Resources</h6>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Family Guides</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Community</a></li>
                </ul>
            </div>
            
            <div class="col-6 col-lg-2 footer-links">
                <h6>Legal</h6>
                <ul>
                    <li><a href="agreement.php?type=terms_of_service">Terms of Service</a></li>
                    <li><a href="agreement.php?type=privacy_policy">Privacy Policy</a></li>
                    <li><a href="agreement.php?type=opt_in_agreement">Opt-In Policy</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-top mt-5 pt-4 text-center text-muted small">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> iS4B Family Calendar. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>

<?php include_once __DIR__ . '/components/footer.php'; ?>
