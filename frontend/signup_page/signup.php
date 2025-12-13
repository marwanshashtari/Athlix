
<?php
session_start();
require_once '../../backend/config.php';
$type = $_POST['type'] ?? $_POST['signup_type'] ?? ($_SESSION['user_type'] ?? 'student');
$_SESSION['user_type'] = $type;
$login_error = $_SESSION['login_error'] ?? '';
$show_login_modal = !empty($login_error);
unset($_SESSION['login_error']); // remove error after displaying it
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up with Athlix</title>
  <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<!--must do require('request_signup_type.php'); to know usertype -->
<body>
    <!--add logo-->
  <div class="navbar">
    <div class="logo">
      <img src="/xampp/htdocs/Athlix-main/Athlix-main/frontend/landing_page/landing_images/logo_athlix.jpg" alt="logo" height="40">
      <div class="logo-text">Athlix</div>
    </div>
    <div class="login-buttons">
    <form action="../../backend/login.php" method="post">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
        <button type="submit" class="btn btn-dark loginButton">I already have an account</button>
    </form>
    </div>
  </div>
    <div class="head-title"><h1>Sign up with Athlix!</h1></div>

  <div id="loginModal" class="modal">
  <div class="modal-content">
    <i class="fa-solid fa-person-running" style="color: var(--primary)"></i>
    <hr>
    <span class="close">&times;</span>
    <h2>Login</h2>
    <div class="login-grid">
      <form id="loginForm" action="../../backend/login.php" method="post">
        <input type="hidden" name="type" id="userType" value="">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>
              <!--to show login error-->
        <?php if(!empty($login_error)): ?>
          <div class="login-error" style="color:red; margin-bottom:10px;">
            <?= htmlspecialchars($login_error) ?>
          </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-dark">Login</button>
      </form>
      <div class="picture-modal" style="width: inherit; height: inherit;">
        <img src="../landing_page/landing_images/person_lefting_login.jpg">
      </div>      
    </div>
  </div>
</div>
  <!--I'll make two form right now but with the backend code, one will show and the other wont-->
  <div class="signup-window">
    <div class="signup-form">
      <form action="../../backend/signup_submit.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
         <?php if ($type == 'student') { ?>      
        <fieldset style="border: none ">
        <legend>Personal Information</legend>
        <!--php code: check if type is student and open braces-->
        <!-- email -->
        <div class="input-div" style="--icon-color:rgb(109, 109, 9);">
            <i class="fa-solid fa-football"></i>
            <label for="email">Email
            <input  class="text" type="email" id="email" name="email" placeholder="myemail@...mail.com" required>
            </label>
        </div>
        <br>
        <!-- password -->
        <div class="input-div" style="--icon-color: rgb(68, 12, 68);">
           <i class="fa-solid fa-baseball"></i>
            <label for="signup_password">Password</label>
            <input class="text" type="password" id="signup_password" name="password" required>
        </div>
        <br>
          <!-- fn -->
        <div class="input-div" style="--icon-color: teal;">
            <i class="fa-solid fa-basketball"></i>
            <label for="fname">First Name
            <input class="text" type="text" id="fname" name="fname" placeholder="jane doe" required></label>
        </div>
        <br>
        <!-- ln -->
        <div class="input-div" style="--icon-color: rgb(71, 1, 1);">
            <i class="fa-solid fa-baseball"></i>
            <label for="lname">Last Name
            <input class="text" type="text" id="lname" name="lname" required></label>
        </div>
        <br>
        <!-- gpa -->
        <div class="input-div" style="--icon-color: slateblue;" >
            <i class="fa-solid fa-person-biking"></i>
            <div class="GPA">
              <label for="GPA">GPA<input type="number" name="GPA" min=0 max=100 id="GPA">
            </div>
            </label>
        </div>
        <br>  
        <!-- dob -->
        <div class="input-div" style="--icon-color: rgb(63, 85, 20);">
            <i class="fa-solid fa-volleyball"></i>
            <label for="birthdate">date of birth
            <div class="birthdate-selects">
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
              </label>
        </div>
        <br>
        <!-- exppected grad year -->
        <div class="input-div" style="--icon-color:  rgb(71, 1, 1);">
            <i class="fa-solid fa-baseball"></i>
            <label for="grad_year">Expected Graduation Year
            <input class="text" type="text" id="Expected_Graduation_Year" name="Expected_Graduation_Year" placeholder="2030" required></label>
            </label>
        </div>
        <br>
        <!-- pn -->
        <div class="input-div" style="--icon-color: teal;">
            <i class="fa-solid fa-basketball"  ></i>
            <label for="pn">Phone Number 
            <input class="text" type="text" id="pn" name="pn" placeholder="+962..." required></label>
        </div>
        <br>
        <!-- health -->
        <div class="input-div" style="--icon-color: rgb(71, 1, 1);">
            <i class="fa-solid fa-football"  ></i>
            <label for="Health_Issues">Health Issues 
            <input class="text" type="text" id="Health_Issues" name="Health_Issues" placeholder="asthma" required></label>
        </div>
        <br>
        <!-- bio -->
        <div class="input-div" style="--icon-color: slateblue;">
            <i class="fa-solid fa-person-biking"></i>
            <label for="bio">Bio
            <textarea id="bio" name="bio" rows="5" cols="40" placeholder="Type your bio here..."></textarea>
        </div>
        <br>
        <!-- gender -->
        <div class="input-div" style="--icon-color: rgb(26, 48, 66);">
            <i class="fa-solid fa-bowling-ball" ></i>
            <label class="gender" for="gender">Gender
            <div class="gender-options">
                <div class="gender-option"><input class="check" type="radio" id="gender1" name="gender" value="1">
                  <label for="gender1">female</label>
                </div>
                <div class="gender-option"><input class="check" type="radio" id="gender0" name="gender" value="0">
                  <label for="gender0">male</label>
                </div>
              </div>
              </label>
          </div>
        <br>
        <!-- Status -->
        <div class="input-div" style="--icon-color: rgb(26, 48, 66);">
            <i class="fa-solid fa-bowling-ball"></i>
            <label class="status" for="status">are you available for a scholarship?
            <div class="status-options">
                <div class="status-option"><input class="check" type="radio" id="status0" name="status" value="0">
                  <label for="status0">yes</label>
                </div>
                <div class="status-option"><input class="check" type="radio" id="status1" name="status" value="1">
                  <label for="status1">no</label>
                </div>
              </div>
              </label>
          </div>
        <br>        
        <!-- student type -->
        <div class="input-div" style="--icon-color: rgb(68, 12, 68);">
            <i class="fa-solid fa-table-tennis-paddle-ball" ></i>
            <label class="std_type" for="std_type">student type
            <div class="std_type_options">
                <div class="std-option"><input class="check" type="radio" id="std0" name="std_type" value="0">
                  <label for="std0">school student</label>
                </div>
                <div class="std-option"><input class="check" type="radio" id="std1" name="std_type" value="1">
                  <label for="std1">university student</label>
                </div>
              </div>
              </label>
          </div>
        <br>
      </fieldset>
       <fieldset style="border: none ">
        <legend>Sports Information</legend>
            <!-- sports -->

        <div class="input-div" style="--icon-color: rgb(57, 22, 22);">
            <i class="fa-solid fa-golf-ball-tee"></i>
            <label for="sports-select">Select your sports
            <div class="sport-options">
            <div><input type="checkbox" id="football" name="sports[]" value="Football"><label for="football">Football</label></div>
            <div><input type="checkbox" id="volleyball" name="sports[]" value="Volleyball"><label for="volleyball">Volleyball</label></div>
            <div><input type="checkbox" id="tennis" name="sports[]" value="Tennis"><label for="tennis">Tennis</label></div>
            <div><input type="checkbox" id="basketball" name="sports[]" value="Basketball"><label for="basketball">Basketball</label></div>
            <div><input type="checkbox" id="handball" name="sports[]" value="Handball"><label for="handball">Handball</label></div>
            <div><input type="checkbox" id="table-tennis" name="sports[]" value="Table Tennis"><label for="table-tennis">Table Tennis</label></div>
            <div><input type="checkbox" id="karate" name="sports[]" value="Karate"><label for="karate">karate</label></div>
            <div><input type="checkbox" id="jiu-jitsu" name="sports[]" value="Jiu-Jitsu"><label for="jiu-jitsu">jiu jitsu</label></div>
            <div><input type="checkbox" id="taekewondo" name="sports[]" value="Taekewondo"><label for="taekewondo">taekewondo</label></div>
            <div><input type="checkbox" id="badminton" name="sports[]" value="Badminton"><label for="badminton">badminton</label></div>
            </div></label>
        </div>
        <br>
        <!-- height -->
        <div class="input-div" style="--icon-color: slateblue;">
            <i class="fa-solid fa-person-biking"></i>
            <div class="height"><label for="height">Height<input type="number" name="height" id="height"></div></label>
        </div>
        <br>
        <!-- weight -->
        <div class="input-div" style="--icon-color: turquoise;">
            <i class="fa-solid fa-person-running"></i>
            <div class="weight"><label for="weight">Weight<input type="number" name="weight" id="weight"></div></label>
        </div>
        <br>
        <!-- exp-years -->
       <div class="input-div" style="--icon-color: teal;">
        <i class="fa-solid fa-basketball"></i>
            <div class="exp-years"><label for="exp-years">How many years have you played sports<input type="number" name="exp_years" id="exp-years"></div></label>
        </div>
        <br>
        <!-- competition-years -->
        <div class="input-div" style="--icon-color: rgb(68, 12, 68);">
            <i class="fa-solid fa-table-tennis-paddle-ball"></i>
            <label class="competition">Have you participated in competitions before
            <div class="comp-options">
                <div class="comp-option">
                    <input class="check" type="radio" id="competition1" name="competition" value="yes">
                    <label for="competition1">Yes</label>
                </div>
                <div class="comp-option">
                    <input class="check" type="radio" id="competition0" name="competition" value="no">
                    <label for="competition0">No</label>
                </div>
                </div>
            </label>
            </div>
        <br>
        <div class="submit">
            <input type="submit" value="Sign up">
            <input type="reset" value="Cancel">
        </div>
        <br>
       </fieldset>
    </form>
  </div>
  </div>

 <?php } elseif ($type == 'university') { ?>

 <div class="signup-window">
    <div class="signup-form">
    <form id="loginForm" action="../../backend/signup_submit.php" method="post">
        <!-- uni name -->
        <div class="input-div" style="--icon-color: teal;">
            <i class="fa-solid fa-basketball"></i>
            <label for="universities_menu">Choose the university
                <select id="universities_menu" name="universities_menu">
                    <option value="">Choose...</option>
                    <option value="PSUT">princess sumaya university for technology</option>
                    <option value="GJU">german jordanian university</option>
                    <option value="JU">jordanian university</option>
                    <option value="ASU">Applied Science Private University</option>
                    <option value="hashmite">The Hashemite University</option>
                    <option value="JUST">jordan university of science and technology</option>
                    <option value="yarmouk">Yarmouk University</option>
                    <option value="htu">AlHussein Technical University</option>
                  </select>
          </label>
        </div>
        <br>
        <!-- uni email -->
        <div class="input-div" style="--icon-color: rgb(109, 109, 9);">
            <i class="fa-solid fa-football"></i>
            <label for="email">Email
            <input  class="text" type="email" id="email" name="email" placeholder="myemail@...mail.com" required>
            </label>
        </div>
        <br>
         <!-- uni location -->
        <div class="input-div" style="--icon-color: slateblue;">
            <i class="fa-solid fa-person-biking"></i>
            <label for="Location">Location
            <textarea id="Location" name="Location" rows="5" cols="40" placeholder="Type your location here..."></textarea>
        </div>
        <br>
        <!-- web_url -->
        <div class="input-div" style="--icon-color: rgb(63, 85, 20);">
            <i class="fa-solid fa-volleyball"></i>
            <label for="web_url">Website URL
            <input class="text" type="url" id="web_url" name="web_url" placeholder="www.example.com" required></label>
        </div>
        <br>
        <!--pn -->
        <div class="input-div" style="--icon-color: rgb(68, 12, 68);">
            <i class="fa-solid fa-table-tennis-paddle-ball"></i>
            <label for="pn">Phone Number
            <input class="number" type="number" id="pn" name="pn" placeholder="+962..." required></label>
        </div>
        <br>
        <div class="input-div" style="--icon-color: rgb(68, 12, 68);">
           <i class="fa-solid fa-baseball"></i>
            <label for="signup_password">Password</label>
            <input class="text" type="password" id="signup_password" name="password" required>
        </div>
        <br>
        <div class="submit">
            <input type="submit" value="Sign up">
            <input type="reset" value="Cancel">
        </div>
    </form>
    </div>
 </div>
  <?php } ?>
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





