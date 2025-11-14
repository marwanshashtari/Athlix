<?php
session_start();
require_once __DIR__ . '/config.php';

// guard HTTP method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// error helper function
function bad($msg, $code = 400) {
    http_response_code($code);
    echo htmlspecialchars($msg);
    exit;
}

// role from form: 'student' or 'university'
$type = $_POST['type'] ?? '';

// 
if ($type === 'student') {
    $roleBit   = 0;          
    $roleLabel = 'Student';   
} 
elseif ($type === 'university') {
    $roleBit   = 1;
    $roleLabel = 'University';
} 
else {
    bad('Invalid signup type');
}

// inputs
$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password']  ?? '';
$pass2 = $_POST['password2'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    bad('Invalid email');
}
if (strlen($pass) < 8) {
    bad('Password must be at least 8 characters');
}
if ($pass !== $pass2) {
    bad('Passwords do not match');
}

// check unique email
if (q_row('SELECT 1 FROM [dbo].[User] WHERE [Email] = ?', [$email])) {
    bad('Email already exists', 409);
}

// hash password
$hash = password_hash($pass, PASSWORD_DEFAULT);

// insert user (Role is BIT 0/1)
$stmt = q(
    'INSERT INTO [dbo].[User] ([Email],[Password],[Role])
     OUTPUT INSERTED.[User_ID]
     VALUES (?,?,?)',
    [$email, $hash, $roleBit]
);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if (!$row || empty($row['User_ID'])) {
    bad('Failed to create user', 500);
}

$userId = (int)$row['User_ID'];

// start session as this user (login)
$_SESSION['user_id']   = $userId;
$_SESSION['user_role'] = $roleLabel;   // 'Student' or 'University'


if ($roleLabel === 'Student') {
    header('Location: /frontend/student_dashboard.php');
} 
else {
    header('Location: /frontend/uni_dashboard.php');
}
exit;
