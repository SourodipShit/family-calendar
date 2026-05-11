<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$path_prefix = isset($path_prefix) ? $path_prefix : "";
$current_page = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
    if ($current_page !== 'login.php' && !isset($is_public_page)) {
        header("Location: {$path_prefix}login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Family Calendar"; ?></title>
    <link rel="shortcut icon" href="<?php echo $path_prefix; ?>public/favicon.ico" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <!-- Custom CSS -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $path_prefix; ?>public/css/style.css">
    <script>
        const API_PATH = "<?php echo $path_prefix; ?>api/";
    </script>
</head>

<body>

    <?php if (!isset($no_wrapper) || !$no_wrapper): ?>
        <div class="d-flex" id="wrapper">
        <?php endif; ?>