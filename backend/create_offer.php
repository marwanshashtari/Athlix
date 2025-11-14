<?php
session_start();
require_once __DIR__ . '/config.php';

// Ensure the user is logged in and is a university
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'University') {
    http_response_code(403);
    exit('Access denied');
}

$userId = (int)$_SESSION['user_id'];

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Sanitize and validate inputs
$title       = trim($_POST['title']       ?? '');
$discountStr = trim($_POST['discount']    ?? '');
$description = trim($_POST['description'] ?? '');
$expiry      = trim($_POST['expiry']      ?? '');

if ($title === '' || $discountStr === '' || $description === '' || $expiry === '') {
    http_response_code(400);
    exit('All fields are required');
}

// Parse discount as number 
if (!is_numeric($discountStr)) {
    http_response_code(400);
    exit('Discount must be a number (percentage)');
}
$discount = (float)$discountStr;
if ($discount <= 0 || $discount > 100) {
    http_response_code(400);
    exit('Discount must be between 0 and 100');
}

// validate date format "YYYY-MM-DD"
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expiry)) {
    http_response_code(400);
    exit('Invalid date format');
}

// Insert the new offer into the database
q(
    'INSERT INTO [dbo].[Scholarship]
        ([Uni_ID], [Percentage], [Active], [Deadline], [Eligibility_Criteria], [Description])
     VALUES (?, ?, 1, ?, ?, ?)',
    [$userId, $discount, $expiry, $description, $title]
);


header('Location: ../frontend/uni_dashboard.html');
exit;
