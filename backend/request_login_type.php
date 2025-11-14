<?php
session_start();
require_once __DIR__ . '/config.php';

$type = $_POST['type'] ?? null;

if ($type === 'student') {
    $_SESSION['user_type'] = 'student';
    header('Location: ../frontend/login_page/login.php');
    exit();
} 

elseif ($type === 'university') {
    $_SESSION['user_type'] = 'university';
    header('Location: ../frontend/login_page/login.php');
    exit();
} 

else {
    die('Invalid user type.');
}

?>
