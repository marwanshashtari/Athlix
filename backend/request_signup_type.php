<?php

require_once __DIR__ . '/config.php';

session_start();

$type = $_POST['type'] ?? null;

if ($type === 'student') {
    $_SESSION['user_type'] = 'student';
    header('Location: ../frontend/signup_page/signup.php');
    exit(); 
}

elseif ($type === 'university') {
    $_SESSION['user_type'] = 'university';
    header('Location: ../frontend/signup_page/signup.php');
    exit();
} 

else {
    die('Invalid user type.');
}
?>
