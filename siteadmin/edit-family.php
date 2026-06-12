<?php
$path_prefix = "../";
$page_title = "Edit Family";
require_once $path_prefix . 'components/admin-header.php';
require_once $path_prefix . 'components/admin-sidebar.php';
require_once $path_prefix . 'classes/Family.php';

$message = "";
$status = "";

if (!isset($_GET['id'])) {
    header("Location: families.php");
    exit;
}

$family_id = $_GET['id'];
$family = Family::getFamily($family_id);

if (!$family) {
    header("Location: families.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'id' => $family_id,
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'location' => $_POST['location'],
        'timezone' => $_POST['timezone'],
        'storage_allocated' => $_POST['storage_allocated']
    ];

    $result = Family::update($data);
    $status = $result['status'];
    $message = $result['message'];

    if ($status == 'success') {
        echo "<script>window.location.href='families.php?status=success&msg=" . urlencode($message) . "';</script>";
        exit;
    }
}
?>

<div id="page-content-wrapper" class="flex-grow-1">
    <?php require_once $path_prefix . 'components/admin-navbar.php'; ?>

    <div class="container-fluid p-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center mb-4">
                    <a href="families.php" class="btn btn-icon btn-light rounded-circle me-3"><i class="ri-arrow-left-line"></i></a>
                    <h4 class="fw-bold mb-0">Edit Family</h4>
                </div>

                <div class="card admin-card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                            <div class="alert alert-<?php echo $status == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                                <?php echo $message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Family Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-group-line"></i></span>
                                        <input type="text" name="name" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($family['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Family Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-mail-line"></i></span>
                                        <input type="email" name="email" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($family['email']); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-map-pin-line"></i></span>
                                        <input type="text" name="location" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($family['location'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Timezone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-time-line"></i></span>
                                        <select name="timezone" class="form-select bg-light border-0">
                                            <?php
                                            require_once $path_prefix . 'classes/GlobalSettings.php';
                                            $timezone_setting = GlobalSettings::getSetting('timezone');
                                            $timezones = [];
                                            if ($timezone_setting['status'] === 'success' && !empty($timezone_setting['data'])) {
                                                $timezones = json_decode($timezone_setting['data']['setting_value'], true);
                                            }
                                            if (is_array($timezones)) {
                                                foreach ($timezones as $tz) {
                                                    $val = htmlspecialchars($tz['timezone']);
                                                    $lbl = htmlspecialchars($tz['lable']);
                                                    $selected = ($family['timezone'] == $tz['timezone']) ? 'selected' : '';
                                                    echo "<option value=\"$val\" $selected>$lbl</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Storage Allocated (MB)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="ri-hard-drive-2-line"></i></span>
                                        <input type="number" name="storage_allocated" class="form-control bg-light border-0" value="<?php echo htmlspecialchars($family['storage_allocated'] ?? 500); ?>" min="1" required>
                                    </div>
                                </div>

                                <div class="col-12 mt-5">
                                    <hr class="opacity-50 mb-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="families.php" class="btn btn-light px-4">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-4">Update Family</button>
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