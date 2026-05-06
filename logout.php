<?php
session_start();
require_once __DIR__ . '/classes/Auth.php';
Auth::logout();
echo "<script>window.location.href='login.php?logout=success';</script>";
