<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign up with Athlix</title>
  <link rel="stylesheet" href="signup.css">
</head>


<body>
<!--must do require('request_signup_type.php'); to know usertype -->
<?php
require_once '../../../backend/config.php';
$type = $_POST['type'] ; 
?>

    <!--add logo-->
  <div class="navbar">
    <div class="logo">
      <img src="" alt="logo" height="40">
      <div class="logo-text">Athlix</div>
    </div>
    <div class="login-buttons">
    <form action="login.php" method="post">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
        <button type="submit" class="btn btn-dark">I already have an account</button>
    </form>
    </div>
  </div>

  <!--I'll make two form right now but with the backend code, one will show and the other wont-->
  <div class="signup-window">
    <div class="signup-form">
        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
        <div class="input-div">
            <label for="email">Email</label>
            <input  class="text" type="email" id="email" name="email" placeholder="myemail@...mail.com" required>
        </div>
        <br>
        <!--php code: check if type is student and open braces-->
        <?php if ($type === 'student'): ?>
        
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
            <label for="birthdate">date of birth</label>
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
                    <option value="0" id="">Day</option>
                    <option value="1" id="1">1</option>
                    <option value="2" id="2">2</option>
                    <option value="3" id="3">3</option>
                    <option value="4" id="4">4</option>
                    <option value="5" id="5">5</option>
                    <option value="6" id="6">6</option>
                    <option value="7" id="7">7</option>
                    <option value="8" id="8">8</option>
                    <option value="9" id="9">9</option>
                    <option value="10" id="10">10</option>
                    <option value="11" id="11">11</option>
                    <option value="12" id="12">12</option>
                    <option value="13" id="13">13</option>
                    <option value="14" id="14">14</option>
                    <option value="15" id="15">15</option>
                    <option value="16" id="16">16</option>
                    <option value="17" id="17">17</option>
                    <option value="18" id="18">18</option>
                    <option value="19" id="19">19</option>
                    <option value="20" id="20">20</option>
                    <option value="21" id="21">21</option>
                    <option value="22" id="22">22</option>
                    <option value="23" id="23">23</option>
                    <option value="24" id="24">24</option>
                    <option value="25" id="25">25</option>
                    <option value="26" id="26">26</option>
                    <option value="27" id="27">27</option>
                    <option value="28" id="28">28</option>
                    <option value="29" id="29">29</option>
                    <option value="30" id="30">30</option>
                    <option value="31" id="31">31</option>
                </select>

                <select name="birth_year" id="birth_year" required>
                    <option name="" value="">Year</option>
                </select>
            </div>
        </div>
       
        <br>
        <div class="input-div">
            <label for="file">Upload your CV</label>
            <input class="file" type="file" id="file" name="file" required hidden>
        </div>
        <br>
        <div class="input-div">
            <label class="gender" for="gender">Gender</label>
            <div class="gender-options">
                <div class="gender-option"><input class="check" type="radio" id="gender" name="gender" value="female">
                <label for="female">female</label></div>
                <div class="gender-option"><input class="check" type="radio" id="gender" name="gender" value="male">
                <label for="male">male</label></div>
            </div>
        </div>
        <br>
        <div class="input-div">
        <label>select your sports</label>
            <div class="sport-options">
                <div class="sport-option"><label class="radio" for="football">football</label><input type="checkbox" id="football" value="football" name="sports[]"></div>
                <div class="sport-option"><label class="radio" for="volleyball">volleyball</label><input type="checkbox" id="volleyball" value="volleyball" name="sports[]"></div>
                <div class="sport-option"><label class="radio" for="tennis">tennis</label><input type="checkbox" id="tennis" value="tennis" name="sports[]"></div>
                <div class="sport-option"><label class="radio" for="others">others</label><input type="checkbox" id="others" value="others" name="sports[]"></div>
            </div>
        </div>
        <br>
        <!--close braces-->
    <?php endif; ?>


        <!--else if type equals to university: open braces-->
    <?php if ($type === 'university'): ?>

        <label for="universities_menu">Choose the university
        <select id="universities_menu" name="universities_menu">
          <option value="">Choose...</option>
          <option value="PSUT">princess sumaya university for technology</option>
          <option value="GJU">german jordanian university</option>
          <option value="JU">jordanian university</option>
          <option value="JUST">jordan university of science and technology</option>
        </select>
        </label>
    
    <?php endif; ?>
    <!--close braces-->
        

    <input type="button" value="Sign up">
    </form>
    </div>
  </div>
<script>
    const yearSelect = document.getElementById("birth_year");
    const currentYear = new Date().getFullYear();
    const startYear = 2000;

    for (let year = currentYear; year >= startYear; year--) {
      const option = document.createElement("option");
      option.value = year;
      option.textContent = year;
      yearSelect.appendChild(option);
    }
    
    
    const monthselected = document.getElementById("birth_month");
    const daytoselect =  document.getElementById("birth_day");
    const day31 = document.getElementById("31");
    const day30 = document.getElementById("30");
    const day29 = document.getElementById("29");
    const day28 = document.getElementById("28");
    
    daytoselect.disabled = true;
    monthselected.addEventListener("change", function() {
    const month = monthselected.value;

    if(month === "0") {
        daytoselect.value = "0";
        daytoselect.disabled = true;
        return;
    }

    daytoselect.disabled = false;

    for(let i=28; i<=31; i++) {
        const day = document.getElementById(i.toString());
        if(day) day.hidden = false;
    }


    switch(monthselected.value){
        case "2":
            if(currentYear%4===0){
            day31.hidden = true;
            day30.hidden = true;
            }
            else {
            day29.hidden = true;
            day31.hidden = true;
            day30.hidden = true;
            }
            break;
        case "4":
        case "6":
        case "9":
        case "11":
            day31.hidden = true;
            break;
    }
});
</script>
</body>
</html>
