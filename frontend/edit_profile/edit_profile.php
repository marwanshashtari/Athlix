<?php
// Path: frontend/edit_profile/edit_profile.php

// ==========================================
// 1. SETUP & DATABASE CONNECTION
// ==========================================
session_start();
require_once '../../backend/config.php';

// Security: Check if logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../landing_page/landing_page.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// ==========================================
// 2. HANDLE FORM SUBMISSION (UPDATE)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Collect Data from Form
    $fName = $_POST['firstName'] ?? '';
    $lName = $_POST['lastName'] ?? '';
    $fullName = trim($fName . ' ' . $lName);
    
    $phone = $_POST['phone'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    // Academic Info
    $university = $_POST['university'] ?? '';
    $major = $_POST['major'] ?? '';
    $gpa = $_POST['gpa'] ?? 0.0;
    
    // 2. Update Database
    try {
        // Using q() helper from config.php
        $sql = "UPDATE Student SET 
                Name = ?, 
                Phone_Number = ?, 
                Bio = ?, 
                University = ?, 
                Major_1 = ?, 
                GPA = ?
                WHERE User_ID = ?";
        
        $params = [
            $fullName, 
            $phone, 
            $bio, 
            $university, 
            $major, 
            $gpa, 
            $user_id
        ];

        q($sql, $params);

        // 3. Redirect to Dashboard on Success
        header("Location: ../dashboards/student_dashboard/student_dashboard.php");
        exit();

    } catch (Exception $e) {
        $message = "Error updating profile: " . $e->getMessage();
    }
}

// ==========================================
// 3. FETCH EXISTING DATA (PRE-FILL)
// ==========================================
try {
    // Fetch Student Details using q_row helper
    $student = q_row("SELECT * FROM Student WHERE User_ID = ?", [$user_id]);

    // Fetch Email from User table using q_row helper
    $user = q_row("SELECT Email FROM [User] WHERE User_ID = ?", [$user_id]);

    // Split Name into First and Last
    $nameParts = explode(' ', $student['Name'] ?? '');
    $currentFName = $nameParts[0] ?? '';
    $currentLName = isset($nameParts[1]) ? implode(' ', array_slice($nameParts, 1)) : '';

    // Calculate Age from DOB
    $age = '';
    if (!empty($student['Date_of_Birth'])) {
        $dobRaw = $student['Date_of_Birth'];

        if ($dobRaw instanceof DateTimeInterface) {
            $dob = $dobRaw;
        } else {
            $dob = new DateTime($dobRaw);
        }

        $now = new DateTime();
        $age = $now->diff($dob)->y;
    }


} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit_profile.css">
    <title>Edit Profile</title>
</head>
<body>

  <div class="navbar">
    <div class="logo">
      <img src="../../landing_page/landing_images/logo_athlix.jpg" alt="logo" height="40">
      <div class="logo-text">Athlix</div>
    </div>
    <div class="login-buttons">
        <a href="../dashboards/student_dashboard/student_dashboard.php" class="btn btn-dark loginButton" style="text-decoration:none; color:white;">Back to Dashboard</a>
    </div>
  </div>

    <div class="profile_container">
        <div class="profile_picture">
            <img src="https://via.placeholder.com/150" alt="Profile">
            <button class="change-picture-btn">Change Picture</button>
        </div>
        
        <div class="tabs">
            <div class="tab active" data-index="0">Basic Info</div>
            <div class="tab" data-index="1">Sport Info</div>
            <div class="tab" data-index="2">Academic Info</div>
        </div>
        
        <form class="profile-form" id="profileForm" action="" method="POST">
            
            <?php if($message): ?>
                <div style="background-color: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="section active" id="section-0">
                <h2>Basic Information</h2>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($currentFName); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($currentLName); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address (Read Only)</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['Email']); ?>" readonly style="background-color: #f0f0f0; cursor: not-allowed;">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($student['Phone_Number'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="age">Age (Calculated from DOB)</label>
                        <input type="number" id="age" value="<?php echo $age; ?>" readonly style="background-color: #f0f0f0;">
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender" disabled style="background-color: #f0f0f0;">
                            <option value="male" <?php echo ($student['Gender'] == 0) ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo ($student['Gender'] == 1) ? 'selected' : ''; ?>>Female</option>
                        </select>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="bio">Bio</label>
                        <textarea id="bio" name="bio" placeholder="Tell us about yourself"><?php echo htmlspecialchars($student['Bio'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="section" id="section-1">
                <h2>Sport Information</h2>
                <div style="padding: 20px; background: #f9f9f9; border-radius: 8px;">
                    <p><strong>Note:</strong> Sports are managed via your Dashboard. Please save any changes here and return to the dashboard to add new sports.</p>
                </div>
            </div>
            
            <div class="section" id="section-2">
                <h2>Academic Information</h2>
                <div class="content-grid">
                    <div class="form-group">
                        <label for="university">University</label>
                        <input type="text" id="university" name="university" value="<?php echo htmlspecialchars($student['University'] ?? ''); ?>" placeholder="Enter your university">
                    </div>
                    
                    <div class="form-group">
                        <label for="major">Major/Field of Study</label>
                        <input type="text" id="major" name="major" value="<?php echo htmlspecialchars($student['Major_1'] ?? ''); ?>" placeholder="Enter your major">
                    </div>
                    
                    <div class="form-group">
                        <label for="gpa">GPA</label>
                        <input type="number" id="gpa" name="gpa" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($student['GPA'] ?? ''); ?>" placeholder="Enter your GPA">
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="academicAchievements">Academic Achievements</label>
                        <textarea id="academicAchievements" name="academicAchievements" placeholder="List your academic achievements"></textarea>
                    </div>
                </div>
            </div>
            
            <div class="buttons">
                <a href="../dashboards/student_dashboard/student_dashboard.php" class="btn btn-cancel" style="text-decoration:none; text-align:center;">Cancel</a>
                <button type="submit" class="btn btn-save">Save Changes</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const sections = document.querySelectorAll('.section');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    tabs.forEach(t => t.classList.remove('active'));
                    sections.forEach(s => s.classList.remove('active'));
                    
                    tab.classList.add('active');
                    
                    const index = tab.getAttribute('data-index');
                    document.getElementById(`section-${index}`).classList.add('active');
                });
            });
            
            document.querySelector('.change-picture-btn').addEventListener('click', function(e) {
                e.preventDefault();
                alert('Picture upload functionality coming soon!');
            });
        });
    </script>
</body>
</html>
