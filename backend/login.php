<?php
session_start();
require_once __DIR__ . '/config.php';

if(isset($_POST['login_type'], $_POST['email'], $_POST['password'])) {
    $loginType = $_POST['login_type'];
    $email = strtolower(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Store login type in session to use later
    $_SESSION['login_type'] = $loginType;
    $role = ($loginType === 'student') ? 0 : 1;
    //authenticate user
    $sql = "SELECT [User_ID], [Email], [Password], [Role]
            FROM [dbo].[User]
            WHERE [Email] = ? AND [Role] = ?";
    $params = [$email, $role]; 
    $user = q_row($sql, $params);
    if($user) {
        //valid login info
        if(password_verify($password, $user['Password'])) {
            //successful login
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['user_role'] = $role === 0 ? 'student' : 'university';
            // Redirect based on login type
            if($role === 0) {
                header('Location: ../dashboard/student_dashboard.php');
                exit();
            } else {
                header('Location: ../dashboard/uni_dashboard.php');
                exit();
            }
        } else {
            //invalid password
            $_SESSION['login_error'] = 'Invalid password. Please try again.';
            header('Location: ../landing_page/landing_page.php');
            exit();
        }
    } else {
        //invalid login info
        $_SESSION['login_error'] = 'User not found.';
        header('Location: ../landing_page/landing_page.php');
        exit();
    }
} else {
    header('Location: ../landing_page/landing_page.php');
    exit();
}

/* tala's code
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
*/

?>
