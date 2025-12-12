<?php
  session_start();
  $login_error = $_SESSION['login_error'] ?? '';
  $show_login_modal = !empty($login_error);
  unset($_SESSION['login_error']); // remove error after displaying it
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Athlix</title>
  <link rel="stylesheet" href="landing_page.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>
  <!--add logo-->
  <div class="navbar">
    <div class="logo">
      <img src="landing_images/logo_athlix.png" alt="logo" height="40">
      <div class="logo-text">Athlix</div>
    </div>
    <div class="login-buttons">
        <button type="button" class="btn btn-dark learn-more">More about us</button>
      <!--action must change to request_login_type.php: a page that determines the user type from the form and saves it then redirects back to login page-->
      <form action="" method="post">
        <input type="hidden" name="type" value="student">
        <button class="btn btn-dark student-login">Student Login</button>
      </form>

      <!--action must change to request_login_type.php: a page that determines the user type from the form and saves it then redirects back to login page-->
      <form action="" method="post">
        <input type="hidden" name="type" value="university">
        <button class="btn btn-dark university-login">University Login</button>
      </form>

    </div>
  </div>

  <div id="loginModal" class="modal">
  <div class="modal-content">
    <i class="fa-solid fa-person-running"></i>
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

  <div class="intro-section">
    <div class="slideshow-intro">
          <img src="landing_images/intro_slideshow1.jpg" alt="psut">
          <img src="landing_images/intro_slideshow2.jpg" alt="psut">
          <img src="landing_images/intro_slideshow3.jpg" alt="psut">
          <img src="landing_images/intro_slideshow4.jpg" alt="psut">
          <img src="landing_images/intro_slideshow5.jpg" alt="psut">
          <img src="landing_images/intro_slideshow6.jpg" alt="psut">
          <img src="landing_images/intro_slideshow7.jpg" alt="psut">
          <img src="landing_images/intro_slideshow8.jpg" alt="psut">
          <img src="landing_images/intro_slideshow9.jpg" alt="psut">
          <img src="landing_images/intro_slideshow10.jpg" alt="psut">
          <img src="landing_images/intro_slideshow11.jpg" alt="psut">
        </div>
      <div class="intro-content">
      <h1>Match Your Sports Achievement with University Discounts</h1>
      <p>Connect student athletes with universities offering exclusive discounts. Verify your sports participation and unlock benefits.</p>
          
        <div class="buttons-container">
          <form action="../signup_page/signup.php" method="post">
              <input type="hidden" name="type" value="student">
              <button class="btn btn-primary get-started-student">I'm a Student</button>
          </form>
          
          <form action="../signup_page/signup.php" method="post">
              <input type="hidden" name="type" value="university">
              <button class="btn btn-outline get-started-uni">I'm a University</button>
          </form>
      </div>

    </div>
  </div>


  <div class="university-grid-text">
    <h1 >Enroll to your dream university</h1>
  </div>
  <div class="university-grid-section">
    <div class="university-grid-content">
      <div class="university-logo psut">
        <div class="slideshow">
          <img src="landing_images/PSUT_Logo.png" alt="psut" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo asu">
        <div class="slideshow">
          <img src="landing_images/asu_logo.jpg" alt="asu" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo hashmite">
        <div class="slideshow">
          <img src="landing_images/Hashmite_logo.jpg" alt="hashmite" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo yarmouk">
        <div class="slideshow">
          <img src="landing_images/yarmouk_logo.png" alt="yarmouk" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo just">
        <div class="slideshow">
          <img src="landing_images/JUST_logo.png" alt="just" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo gju">
        <div class="slideshow">
          <img src="landing_images/GJU_Logo.png" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo ju">
        <div class="slideshow">
          <img src="landing_images/JU_Logo.png" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div>
      </div>
      <div class="university-logo htu">
        <div class="slideshow">
          <img src="landing_images/htu_logo.ico" alt="htu" class="active">
          <img src="landing_images/sport2.jpg">
          <img src="landing_images/sport1.jpg">
        </div> 
      </div>
    </div>
  </div>

  <div class="our-offers">
<div class="student-section">
    <div class="student-image">
      <img src="landing_images/student_sport_landing.bmp" alt="student sport image">
    </div>
    <div class="student-content">
      <h2>For Students</h2>

      <div class="student-text">
        <ul>
          <li><i class="fa-solid fa-check"></i> Access exclusive university discounts</li>
          <li><i class="fa-solid fa-check"></i> Get rewarded for your athlete achievements</li>
          <li><i class="fa-solid fa-check"></i> Simple verification process</li>
          <li><i class="fa-solid fa-check"></i> Upload proof once, use everywhere</li>
          <li><i class="fa-solid fa-check"></i> Track all your offers in one place</li>
          <li><i class="fa-solid fa-check"></i> Easy dashboard to manage benefits</li>
        </ul>
      </div>
    </div>
  </div>
    <div class="university-section">
    <div class="university-image">
      <img src="landing_images/university_image_landing.jpg" alt="student sport image">
    </div>
    <div class="university-content">
      <h2>For Universities</h2>
      <div class="university-text">
        <ul>
          <li><i class="fa-solid fa-check"></i> Attract top athlete talent</li>
          <li><i class="fa-solid fa-check"></i> Connect with student athletes who meet your criteria</li>
          <li><i class="fa-solid fa-check"></i> Streamlined verification system</li>
          <li><i class="fa-solid fa-check"></i> Easily review and approve student credentials</li>
          <li><i class="fa-solid fa-check"></i> Manage discount programs</li>
          <li><i class="fa-solid fa-check"></i> Create and track your offers from one dashboard</li>
        </ul>
      </div>
    </div>
  </div>  
</div>
  
<div class="reviews-section">
  <div class="review">
    <div class="stars">
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-regular fa-star"></i>
      <i class="fa-regular fa-star"></i>
      <i class="fa-regular fa-star"></i>
    </div>
    <p>"Amazing website! Very easy to use and intuitive."</p>
    <p>- Alice</p>
  </div>

  <div class="review">
    <div class="stars">
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
    </div>
    <p>"Exceptional experience, would recommend to everyone."</p>
    <p>- Bob</p>
  </div>

  <div class="review">
    <div class="stars">
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-regular fa-star"></i>
      <i class="fa-regular fa-star"></i>
    </div>
    <p>"Good, but could use some improvements in UI."</p>
    <p>- Clara</p>
  </div>

  <div class="review">
    <div class="stars">
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-solid fa-star"></i>
      <i class="fa-regular fa-star"></i>
    </div>
    <p>"Very helpful content, learned a lot from this site."</p>
    <p>- Daniel</p>
  </div>
</div>

  <div class="how-it-works-section">
    <h2 style="color: rgb(197, 51, 7);">Kickstart Your Athlete Journey!</h2>
    <p>Just three simple steps to connect our student athletes with their best opportunities</p>
    <div class="steps-container">
      <div class="step">
        <i class="fa-solid fa-file-upload" style="color: rgb(238, 238, 39);"></i>
        <h3>Students Submit Proof</h3>
        <p>Upload evidence of your sports participation, achievements, or team membership</p>
      </div>
      <div class="step">
        <i class="fa-solid fa-magnifying-glass" style="color: rgb(182, 20, 20);"></i>
        <h3>Universities Verify</h3>
        <p>Partner universities review and verify your sports credentials securely</p>
      </div>
      <div class="step">
        <i class="fa-solid fa-graduation-cap" style="color: rgb(14, 165, 11);"></i>
        <h3>Unlock Discounts</h3>
        <p>Get matched with exclusive university discounts and special offers</p>
      </div>
    </div>
  </div>

  <div class="motivation-section">
    <h2>Ready to Get Started?</h2>
    <p>Join hundreds of students and universities already using SportMatch.</p>
    
      <div class="motivation-buttons-container">
        <form action="../signup_page/signup.php" method="post">
            <input type="hidden" name="type" value="student">
            <button class="btn btn-outline get-started-student">Sign Up as Student</button>
        </form>
        
        <form action="../signup_page/signup.php" method="post">
            <input type="hidden" name="type" value="university">
            <button class="btn btn-primary get-started-uni">Sign Up as University</button>
        </form>
    </div>
    
  </div>

  <div class="footer-section">
      <h3><i></i> Athlix</h3>
    <div id="footer-container" class="footer-container">
        <!--add icon-->
        <div class="about-us">
          <img src="landing_images/logo_athlix.png" alt="logo" height="100" class="logo-footer">
          <p>the printing and typesetting industry.</p>
          <p>the printing and typesetting industry.</p>
          <p>the printing and typesetting industry.</p>
        </div>
        <div class="contact-us">
          <p>instagram</p>
          <p>facebook</p>
          <p>email</p>
        </div>
      </div>
       <p>© 2025 Athlix. Connecting athletes with opportunities.</p>
  </div>

  <script>

document.addEventListener('DOMContentLoaded', () => {
  const scrollBtn = document.querySelector('.learn-more');
  scrollBtn.addEventListener('click', () => {
    document.querySelector('.footer-container').scrollIntoView({ behavior: 'smooth'});
  });

   const contents = document.querySelectorAll('.student-content, .university-content, .motivation-section');
  const images = document.querySelectorAll('.student-image, .university-image');

  function checkSlide() {
    const trigger = window.innerHeight * 0.85;

    
    contents.forEach(content => {
      if(content.getBoundingClientRect().top < trigger){
        content.classList.add('active');
      }
    });

    images.forEach(image => {
      if(image.getBoundingClientRect().top < trigger){
        image.classList.add('active');
      }
    });
  }

  window.addEventListener('scroll', checkSlide);
  checkSlide();

  const logos = document.querySelectorAll('.university-logo');

  logos.forEach(unilogo => {
    const slideshow = unilogo.querySelector('.slideshow');
    const slideImages = slideshow.querySelectorAll('img');
    let current = 0;
    let interval = null;

    function startSlideshow() {
     let firstCycle = true;

    interval = setTimeout(() => {
    slideImages[current].classList.remove('active');
    current = 1;
    slideImages[current].classList.add('active');

    interval = setInterval(() => {
      slideImages[current].classList.remove('active');
      current++;
      if (current >= slideImages.length) current = 1; 
      slideImages[current].classList.add('active');
    }, 2000); 
    }, 1000); 
    }

    function stopSlideshow() {
    clearInterval(interval);
    slideImages[current].classList.remove('active');
    current = 0;
    slideImages[current].classList.add('active');    }

    unilogo.addEventListener('mouseenter', startSlideshow);
    unilogo.addEventListener('mouseleave', stopSlideshow);
  });
 });

 document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('loginModal');
  const closeBtn = modal.querySelector('.close');
  const userTypeInput = document.getElementById('userType');


  const studentLoginBtn = document.querySelector('.student-login');
  const universityLoginBtn = document.querySelector('.university-login');

  function openModal(type) {
    userTypeInput.value = type; 
    modal.style.display = 'block';
  }

  studentLoginBtn.addEventListener('click', (e) => {
    e.preventDefault();
    openModal('student');
  });

  universityLoginBtn.addEventListener('click', (e) => {
    e.preventDefault();
    openModal('university');
  });

  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
  });

  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
});
const universitySection = document.querySelector('.university-section');
const studentSection = document.querySelector('.student-section');
const unigridsec = document.querySelector('.university-grid-section');

window.addEventListener('scroll', () => {
  const triggerPoint = unigridsec.getBoundingClientRect().bottom  +100 ;

  if (triggerPoint <= 0) {
    universitySection.style.transform = 'translate(-50%, 0)';
    universitySection.style.opacity = '1';
    studentSection.classList.add('fade-out');
  } else {
    universitySection.style.transform = 'translate(-50%, 100px)';
    universitySection.style.opacity = '0';
    studentSection.classList.remove('fade-out');
  }
});

const reviews = document.querySelectorAll('.review');
let currentIndex = 0;

reviews[currentIndex].style.opacity = '1';

setInterval(() => {
  reviews[currentIndex].style.opacity = '0';
  currentIndex = (currentIndex + 1) % reviews.length;
  reviews[currentIndex].style.opacity = '1';
}, 1500);

//to keep the login modal open if there is an error
document.addEventListener('DOMContentLoaded', () => {
    <?php if($show_login_modal): ?>
        const modal = document.getElementById('loginModal');
        modal.style.display = 'block';
    <?php endif; ?>
});

</script>
</body>
</html>
