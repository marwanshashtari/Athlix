<?php
session_start();
require_once __DIR__ . '/config.php';

// Ensure the user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role'])) {
    http_response_code(403);
    exit('Access denied');
}

$userId   = (int)$_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Common fields
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email format');
}

if ($phone !== '' && !preg_match('/^\+962\d{8}$/', $phone)) {
    http_response_code(400);
    exit('Invalid phone number format. Must start with +962 and contain 8 digits.');
}

//  Student profile update 
if ($userRole === 'Student') {

    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName']  ?? '');
    $bio       = trim($_POST['bio']       ?? '');
    $gpa       = isset($_POST['gpa']) ? floatval($_POST['gpa']) : 0;

    if ($firstName === '' || $lastName === '') {
        http_response_code(400);
        exit('First name and last name are required');
    }

    $fullName = $firstName . ' ' . $lastName;

    if ($gpa < 0 || $gpa > 100) {
        http_response_code(400);
        exit('GPA must be between 0 and 100');
    }

    q(
        'UPDATE [dbo].[Student]
         SET [Name] = ?, [GPA] = ?, [Phone_Number] = ?, [Bio] = ?
         WHERE [User_ID] = ?',
        [$fullName, $gpa, $phone, $bio, $userId]
    );

}
//  University profile update //edit when uni update form is changed
elseif ($userRole === 'University') {

    $name     = trim($_POST['name']     ?? '');
    $location = trim($_POST['location'] ?? '');
    $website  = trim($_POST['website']  ?? '');

    if ($name === '') {
        http_response_code(400);
        exit('Name is required');
    }

    q(
        'UPDATE [dbo].[University_]
         SET [Name] = ?, [Location] = ?, [Contact_Number] = ?, [Contact_Email] = ?, [Website_URL] = ?
         WHERE [User_ID] = ?',
        [$name, $location, $phone, $email, $website, $userId]
    );

} 
else {
    http_response_code(400);
    exit('Invalid user role');
}

//  Update the email in the User table 
q(
    'UPDATE [dbo].[User]
     SET [Email] = ?
     WHERE [User_ID] = ?',
    [$email, $userId]
);

// Redirect back to appropriate dashboard
if ($userRole === 'Student') {
    header('Location: ../frontend/student_dashboard.php');
} 
else {
    header('Location: ../frontend/uni_dashboard.php');
}
exit;

