<?php
$path_prefix = "../";
$page_title = "Add New Family";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Family.php';
require_once $path_prefix . 'classes/User.php';

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $familyData = [
        'name' => $_POST['family_name'],
        'email' => $_POST['family_email'],
        'location' => $_POST['location'],
        'timezone' => $_POST['timezone']
    ];

    // 1. Add Family
    $result = Family::add($familyData);
    
    if ($result['status'] == 'success') {
        $family_id = $result['id'];
        
        // 2. Add Family Head User
        $userData = [
            'name' => $_POST['head_name'],
            'email' => $_POST['head_email'],
            'phone' => $_POST['head_phone'],
            'role' => 'family-head',
            'password' => password_hash($_POST['head_password'], PASSWORD_DEFAULT),
            'family_id' => $family_id,
            'image' => '' // Default empty image
        ];

        $userResult = User::addUser($userData);
        
        if ($userResult['status'] == 'success') {
            $status = 'success';
            $message = "Family and Head User added successfully";
            echo "<script>window.location.href='families.php?status=success&msg=" . urlencode($message) . "';</script>";
            exit;
        } else {
            $status = 'error';
            $message = "Family added, but failed to add head user: " . $userResult['message'];
        }
    } else {
        $status = $result['status'];
        $message = $result['message'];
    }
}
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex align-items-center mb-4">
                    <a href="families.php" class="btn btn-icon btn-light rounded-circle me-3"><i class="ri-arrow-left-line"></i></a>
                    <h4 class="fw-bold mb-0">Add New Family</h4>
                </div>

                <div class="card admin-card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $status == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="row g-4">
                                <!-- Family Details -->
                                <div class="col-12">
                                    <h5 class="fw-bold mb-3 text-primary">Family Information</h5>
                                    <hr class="mt-0 mb-4 opacity-10">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Family Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-group-line"></i></span>
                                        <input type="text" name="family_name" class="form-control bg-light border-0" placeholder="e.g. The Smiths" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Family Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-mail-line"></i></span>
                                        <input type="email" name="family_email" class="form-control bg-light border-0" placeholder="e.g. family@smiths.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-map-pin-line"></i></span>
                                        <input type="text" name="location" class="form-control bg-light border-0" placeholder="e.g. New York, USA">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Timezone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-time-line"></i></span>
                                        <select name="timezone" class="form-select bg-light border-0">
                                            <option value="UTC">UTC</option>
                                            <option value="Eastern Time">Eastern Time</option>
                                            <option value="Central Time">Central Time</option>
                                            <option value="Mountain Time">Mountain Time</option>
                                            <option value="Pacific Time">Pacific Time</option>
                                            <option value="Kolkata, India">Kolkata, India</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Family Head Details -->
                                <div class="col-12 mt-5">
                                    <h5 class="fw-bold mb-3 text-primary">Family Head Information</h5>
                                    <hr class="mt-0 mb-4 opacity-10">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Head Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-user-star-line"></i></span>
                                        <input type="text" name="head_name" class="form-control bg-light border-0" placeholder="e.g. John Smith" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Head Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-mail-star-line"></i></span>
                                        <input type="email" name="head_email" class="form-control bg-light border-0" placeholder="e.g. john@smiths.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Head Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-phone-line"></i></span>
                                        <input type="text" name="head_phone" class="form-control bg-light border-0" placeholder="e.g. +1 234 567 890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Head Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-lock-line"></i></span>
                                        <input type="password" name="head_password" class="form-control bg-light border-0" placeholder="Minimum 6 characters" required minlength="6">
                                    </div>
                                </div>

                                <div class="col-12 mt-5">
                                    <hr class="opacity-50 mb-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="families.php" class="btn btn-light px-4">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4">Create Family & Head</button>
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
