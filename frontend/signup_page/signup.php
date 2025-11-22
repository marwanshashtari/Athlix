<?php
// Path: frontend/signup_page/signup.php

// ==========================================
// 1. CONFIG & SESSION
// ==========================================
// Path Correction: Up 2 levels to backend
require_once '../../backend/config.php';

// Default type for the form view
$type = isset($_POST['type']) ? $_POST['type'] : 'student';

// ==========================================
// 2. HANDLE SIGNUP SUBMISSION
// ==========================================
// We check for 'email' AND 'password' to ensure it's a real submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    
    $email = $_POST['email'];
    $password = $_POST['password']; // In production, hash this!
    $postedType = $_POST['type']; // student or university
    
    // Role Bit: 0 = Student, 1 = University
    $roleBit = ($postedType === 'student') ? 0 : 1;
    
    try {
        // A. Create User Login
        q("INSERT INTO [User] (Email, Password, Role) VALUES (?, ?, ?)", [$email, $password, $roleBit]);
        
        // B. Get the new User ID
        $res = q_row("SELECT SCOPE_IDENTITY() as id");
        $user_id = $res['id'];
        
        // Set Session immediately
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_role'] = ($postedType === 'student') ? 'Student' : 'University';

        // C. Create Specific Profile
        if ($postedType === 'student') {
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $fullName = $fname . ' ' . $lname;
            
            // Format Date: YYYY-MM-DD
            $dob = $_POST['birth_year'] . '-' . 
                   str_pad($_POST['birth_month'], 2, '0', STR_PAD_LEFT) . '-' . 
                   str_pad($_POST['birth_day'], 2, '0', STR_PAD_LEFT);
                   
            $gender = ($_POST['gender'] == 'female') ? 1 : 0;
            
            // Insert Student Profile
            // Defaulting required NOT NULL fields like City, Phone, Major to generic values
            $sql = "INSERT INTO Student 
                    (User_ID, Name, Date_of_Birth, Gender, Height, Weight, Primary_Sport_ID, 
                     GPA, School, City, Phone_Number, Expected_Graduation_Year, Student_Type, Major_1, Major_2, Major_3) 
                    VALUES (?, ?, ?, ?, 0, 0, 1, 
                            0.0, 'N/A', 'Amman', '+96200000000', 2025, 0, 'Undeclared', 'N/A', 'N/A')";
            
            q($sql, [$user_id, $fullName, $dob, $gender]);
              
            // Handle Sports Checkboxes
            if(isset($_POST['sports']) && is_array($_POST['sports'])) {
                foreach($_POST['sports'] as $sportName) {
                    if($sportName == 'others') continue; 
                    
                    // Check if sport exists
                    $sRow = q_row("SELECT Sport_ID FROM Sports WHERE Name = ?", [$sportName]);
                    
                    if($sRow) {
                        // Link sport to student
                        q("INSERT INTO Sports_Student (Std_ID, Sport_ID, Number_of_Tournaments_Won, Tournaments_Description, Achievements, Years_of_Experience) 
                           VALUES (?, ?, 0, '', '', 0)", [$user_id, $sRow['Sport_ID']]);
                    }
                }
            }

            // Redirect to Edit Profile to finish details
            header("Location: ../edit_profile/edit_profile.php");
            exit();
            
        } else {
            // University Signup
            $uniName = $_POST['universities_menu'] ?? 'New University';
            
            // Create University Profile
            q("INSERT INTO University_ (User_ID, Name) VALUES (?, ?)", [$user_id, $uniName]);
            
            header("Location: ../dashboards/university_dashboard/uni_dashboard.php");
            exit();
        }

    } catch (Exception $e) {
        die("Signup Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up with Athlix</title>
  <link rel="stylesheet" href="signup.css">
</head>

<body>

  <div class="navbar">
    <div class="logo">
      <img src="../../landing_page/landing_images/logo_athlix.jpg" alt="logo" height="40">
      <div class="logo-text">Athlix</div>
    </div>
    <div class="login-buttons">
      <form action="login.php" method="post">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
        <button type="submit" class="btn btn-dark loginButton">I already have an account</button>
      </form>
    </div>
  </div>

  <div id="loginModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Login</h2>
      <form id="loginForm" action="../../backend/request_login_type.php" method="post">
        <input type="hidden" name="type" id="userType" value="">
        <label for="login_email">Email</label>
        <input type="email" name="email" id="login_email" placeholder="Enter your email" required>
        <label for="login_password">Password</label>
        <input type="password" name="password" id="login_password" placeholder="Enter your password" required>
        <button type="submit" class="btn btn-dark">Login</button>
      </form>
    </div>
  </div>

  <div class="signup-window">
    <div class="signup-form">
      <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
        
        <div class="input-div">
            <label for="email">Email</label>
            <input class="text" type="email" id="email" name="email" placeholder="myemail@...mail.com" required>
        </div>
        
        <br>
        <div class="input-div">
            <label for="signup_password">Password</label>
            <input class="text" type="password" id="signup_password" name="password" required>
        </div>
        <br>

        <?php if ($type == 'student') { ?>

            <div class="input-div">
                <label for="fname">First Name</label>
                <input class="text" type="text" id="fname" name="fname" placeholder="jane doe" required>
            </div>
            <br>
            <div class="input-div">
                <label for="lname">Last Name</label>
                <input class="text" type="text" id="lname" name="lname" required>
            </div>
            <br>       
            <div class="input-div">
                <label for="birthdate">Date of birth</label>
                <div class="birthdate-selects">
                    <select id="birth_month" name="birth_month" required>
                        <option value="0" selected>Month</option>
                        <option value="1">January</option> 
                        <option value="2">February</option> 
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option> 
                        <option value="8">August</option>
                        <option value="9">September</option>
                        <option value="10">October</option> 
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>

                    <select id="birth_day" name="birth_day" required>
                        <option value="0">Day</option>
                        <?php for($i=1; $i<=31; $i++): ?>
                            <option value="<?php echo $i; ?>" id="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>

                    <select name="birth_year" id="birth_year" required>
                        <option value="">Year</option>
                    </select>
                </div>
            </div>
            
            <br>
            <div class="input-div" style="display:none;"> 
                <label for="file">Upload your CV</label>
                <input class="file" type="file" id="file" name="file">
            </div>
            
            <div class="input-div">
                <label class="gender">Gender</label>
                <div class="gender-options">
                    <div class="gender-option">
                        <input class="check" type="radio" id="female" name="gender" value="female">
                        <label for="female">Female</label>
                    </div>
                    <div class="gender-option">
                        <input class="check" type="radio" id="male" name="gender" value="male">
                        <label for="male">Male</label>
                    </div>
                </div>
            </div>
            <br>
            <div class="input-div">
                <label>Select your sports</label>
                <div class="sport-options">
                    <div><input type="checkbox" id="football" name="sports[]" value="football"><label for="football">Football</label></div>
                    <div><input type="checkbox" id="volleyball" name="sports[]" value="volleyball"><label for="volleyball">Volleyball</label></div>
                    <div><input type="checkbox" id="tennis" name="sports[]" value="tennis"><label for="tennis">Tennis</label></div>
                    <div><input type="checkbox" id="basketball" name="sports[]" value="basketball"><label for="basketball">Basketball</label></div>
                    <div><input type="checkbox" id="handball" name="sports[]" value="handball"><label for="handball">Handball</label></div>
                    <div><input type="checkbox" id="table-tennis" name="sports[]" value="table-tennis"><label for="table-tennis">Table Tennis</label></div>
                    <div><input type="checkbox" id="others" value="others"><label for="others">Others</label></div>

                    <div id="other-sports" style="display:none;">
                        <label for="other-sport">What's your sport? </label>
                        <input type="text" name="other_sport_text" id="other-sport">
                    </div>
                </div>
            </div>
            <br>

        <?php } elseif ($type == 'university') { ?>

            <div class="input-div">
                <label for="universities_menu">Choose the university</label>
                <select id="universities_menu" name="universities_menu">
                    <option value="">Choose...</option>
                    <option value="PSUT">Princess Sumaya University for Technology</option>
                    <option value="GJU">German Jordanian University</option>
                    <option value="JU">Jordanian University</option>
                    <option value="JUST">Jordan University of Science and Technology</option>
                </select>
            </div>
            <br>

        <?php } ?>

        <input type="submit" value="Sign up">
      </form>
    </div>
  </div>

<script>
    // --- DATE LOGIC ---
    const yearSelect = document.getElementById("birth_year");
    
    if (yearSelect) {
        const currentYear = new Date().getFullYear();
        const startYear = 1980;

        for (let year = currentYear; year >= startYear; year--) {
            const option = document.createElement("option");
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }

        const monthselected = document.getElementById("birth_month");
        const daytoselect =  document.getElementById("birth_day");
        
        daytoselect.disabled = true;

        monthselected.addEventListener("change", function() {
            if(monthselected.value !== "0") {
                daytoselect.disabled = false;
            }
        });
    }

    // --- OTHER SPORT LOGIC ---
    const textother = document.getElementById("others");
    if (textother) {
        const othersport = document.getElementById("other-sports");
        
        textother.addEventListener("change", function() {
            othersport.style.display = textother.checked ? 'block' : 'none';
        });
    }

    // --- MODAL LOGIC ---
    const loginBtn = document.querySelector('.loginButton');
    const modal = document.getElementById('loginModal');
    const closeBtn = document.querySelector('.close');
    const userTypeInput = document.getElementById('userType');

    if (loginBtn) {
        loginBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if(userTypeInput) userTypeInput.value = "<?php echo $type; ?>"; 
            modal.style.display = 'block';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) { modal.style.display = 'none'; }
    });
</script>
</body>
</html>
