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
    $roleLabel = 'student';   
} 
elseif ($type === 'university') {
    $roleBit   = 1;
    $roleLabel = 'university';
} 
else {
    bad('Invalid signup type');
}

// inputs
$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password']  ?? '';
//$pass2 = $_POST['password2'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    bad('Invalid email');
}
if (strlen($pass) < 8) {
    bad('Password must be at least 8 characters');
}
// if ($pass !== $pass2) {
//     bad('Passwords do not match');
// }

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


if ($roleLabel === 'student') {
    // Get extra fields from form
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $fullName = trim($fname . ' ' . $lname);

    $birthYear  = (int)($_POST['birth_year'] ?? 0);
    $birthMonth = (int)($_POST['birth_month'] ?? 0);
    $birthDay   = (int)($_POST['birth_day'] ?? 0);
    $dob = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

    $genderInput = $_POST['gender'] ?? 'male';
    $genderBit = ($genderInput === 'female') ? 1 : 0;
    
    $gpa = floatval($_POST['GPA'] ?? 0);
    if ($gpa < 0 || $gpa > 100) $gpa = 0;

    $school = trim($_POST['school'] ?? 'N/A');
    $city   = trim($_POST['city'] ?? 'Amman');
    $height = floatval($_POST['height'] ?? 0);
    $weight = floatval($_POST['weight'] ?? 0);
    $status = (int)($_POST['status'] ?? 0);
    $phone  = trim($_POST['pn'] ?? '+962000000000');
    $bio    = trim($_POST['bio'] ?? '');
    $expectedGradYear = (int)($_POST['Expected_Graduation_Year'] ?? (date('Y') + 4));
    $studentType = (int)($_POST['std_type'] ?? 0);
    $health = trim($_POST['Health_Issues'] ?? 'None');
    $yearsOfExperience = (int)($_POST['exp_years'] ?? 0);
    //
    // Primary sport default
    $primarySportId = 1;
    if (!empty($_POST['sports']) && is_array($_POST['sports'])) {
        $firstSport = $_POST['sports'][0];
        $sRow = q_row("SELECT Sport_ID FROM Sports WHERE Name = ?", [$firstSport]);
        if ($sRow) $primarySportId = (int)$sRow['Sport_ID'];
    }

    // Insert student with safe defaults to satisfy NOT NULL columns
     q(
        "INSERT INTO Student
        (User_ID, Name, Date_of_Birth, Gender, GPA, School, City, Weight, Height, Status, Phone_Number, Bio, Expected_Graduation_Year, Student_Type, Primary_Sport_ID, Health_Issues, Major_1, Major_2, Major_3)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Undeclared', 'N/A', 'N/A')",
        [$userId, $fullName, $dob, $genderBit, $gpa, $school, $city, $weight, $height, $status, $phone, $bio, $expectedGradYear, $studentType, $primarySportId, $health]
    );

    if (!empty($_POST['sports']) && is_array($_POST['sports'])) {
        // Delete old sports first
        q("DELETE FROM Sports_Student WHERE Std_ID = ?", [$userId]);

        foreach ($_POST['sports'] as $sportName) {
            $sportNameDb = ucfirst(strtolower($sportName)); // normalize name
            $sport = q_row("SELECT Sport_ID FROM Sports WHERE Name = ?", [$sportNameDb]);
            if ($sport) {
                q("INSERT INTO Sports_Student (Std_ID, Sport_ID, Number_of_Tournaments_Won, Tournaments_Description, Achievements, Years_of_Experience)
                   VALUES (?, ?, 0, '', '', ?)", [$userId, $sport['Sport_ID'], $yearsOfExperience]);
            }
        }
    }

} else { // University signup
    $uniName = trim($_POST['universities_menu'] ?? 'New University');
    $location = trim($_POST['Location'] ?? 'Unknown');
    $website = trim($_POST['web_url'] ?? 'https://unknown.com');
    $phone = trim($_POST['pn'] ?? '+962000000000');

    // Insert university profile with defaults for NOT NULL fields
    q(
        "INSERT INTO University_
        (User_ID, Name, Location, Website_URL, Contact_Number, Contact_Email, Scholarship_Available)
        VALUES (?, ?, ?, ?, ?, ?, 0)",
        [$userId, $uniName, $location, $website, $phone, $email]
    );
}

// start session as this user (login)
$_SESSION['user_id']   = $userId;
$_SESSION['user_role'] = $roleLabel;   // 'student' or 'university'



if ($roleLabel === 'student') {
    header('Location: ../frontend/dashboards/student_dashboard/student_dashboard.php');
} 
else {
    header('Location: ../frontend/dashboards/university_dashboard/uni_dashboard.php');
}

exit;
