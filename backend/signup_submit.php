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


if ($roleLabel === 'Student') {
    // Get extra fields from form
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $fullName = trim($fname . ' ' . $lname);

    $birthYear  = (int)($_POST['birth_year'] ?? 0);
    $birthMonth = (int)($_POST['birth_month'] ?? 0);
    $birthDay   = (int)($_POST['birth_day'] ?? 0);

    if ($fname === '' || $lname === '') {
        bad('First and last name are required');
    }
    if ($birthYear < 1900 || $birthMonth < 1 || $birthMonth > 12 || $birthDay < 1 || $birthDay > 31) {
        bad('Invalid birth date');
    }

    $dob = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

    $genderInput = $_POST['gender'] ?? 'male';
    $genderBit = ($genderInput === 'female') ? 1 : 0;

    // Insert student with safe defaults to satisfy NOT NULL columns
    q(
        "INSERT INTO Student 
         (User_ID, Name, Date_of_Birth, Gender, Height, Weight, Primary_Sport_ID, 
          GPA, School, City, Phone_Number, Expected_Graduation_Year, Student_Type, 
          Major_1, Major_2, Major_3)
         VALUES (?, ?, ?, ?, 0, 0, 1,
                 0.0, 'N/A', 'Amman', '+962000000000', 2025, 0, 'Undeclared', 'N/A', 'N/A')",
        [$userId, $fullName, $dob, $genderBit]
    );

    // Link selected sports (if any) from checkboxes
    if (!empty($_POST['sports']) && is_array($_POST['sports'])) {
        foreach ($_POST['sports'] as $sportName) {
            if ($sportName === 'others') {
                continue;
            }

            // Ensure the sport exists in Sports table and make sure lower letter to match db
            $sportNameDb = ucfirst(strtolower($sportName));
            $sRow = q_row("SELECT Sport_ID FROM Sports WHERE Name = ?", [$sportNameDb]);

            if ($sRow) {
                q(
                    "INSERT INTO Sports_Student
                     (Std_ID, Sport_ID, Number_of_Tournaments_Won, Tournaments_Description, Achievements, Years_of_Experience)
                     VALUES (?, ?, 0, '', '', 0)",
                    [$userId, $sRow['Sport_ID']]
                );
            }
        }
    }

} else { // University signup
    $uniName = $_POST['universities_menu'] ?? 'New University';

    // Insert university profile with defaults for NOT NULL fields
    q(
        "INSERT INTO University_
         (User_ID, Name, Location, Website_URL, Contact_Number, Contact_Email)
         VALUES (?, ?, 'Unknown', 'https://unknown.com', '+962000000000', ?)",
        [$userId, $uniName, $email]
    );
}

// start session as this user (login)
$_SESSION['user_id']   = $userId;
$_SESSION['user_role'] = $roleLabel;   // 'Student' or 'University'



if ($roleLabel === 'Student') {
    header('Location: ../frontend/dashboards/student_dashboard/student_dashboard.php');
} 
else {
    header('Location: ../frontend/dashboards/university_dashboard/uni_dashboard.php');
}

exit;
