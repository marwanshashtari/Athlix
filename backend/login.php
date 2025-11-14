<?php
session_start();
require_once __DIR__ . '/config.php';

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// get and sanitize inputs
$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email format');
}

if ($password === '') {
    http_response_code(400);
    exit('Password is required');
}

// find user
$user = q_row(
    'SELECT [User_ID], [Password], [Role]
     FROM [dbo].[User]
     WHERE [Email] = ?',
    [$email]
);

// verify credentials
if (!$user || !password_verify($password, $user['Password'])) {
    usleep(250000); // Delay to avoid brute-force attacks
    http_response_code(401);
    exit('Invalid email or password');
}

// map role (0/1) to string 
//  0 = Student, 1 = University
$roleBit   = (int)$user['Role'];
$roleLabel = ($roleBit === 1) ? 'University' : 'Student';

// Success: rotate session ID
session_regenerate_id(true);

// Set session variables
$_SESSION['user_id']   = (int)$user['User_ID'];
$_SESSION['user_role'] = $roleLabel; // 'Student' or 'University'

// Redirect to the appropriate dashboard
if ($roleLabel === 'Student') {
    header('Location: ../frontend/student_dashboard.php');
} 
else { // 'University'
    header('Location: ../frontend/uni_dashboard.php');
}
exit;
