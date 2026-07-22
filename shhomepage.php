<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>StayHub</title>

  <link rel="stylesheet" href="shhomepagestylesheet.css">
  </head>

  <body>
  <header class="navbar">
    <div class="navbar-left">
      <h1>StayHub</h1>
    </div>

    <button class="mobile-toggle" id="mobileToggle">☰</button>
    <nav class="navbar-center" id="navbarCenter">
      <a href="#home">Home</a>
      <a href="#browse">Browse</a>
      <a href="#about">About Us</a>
      <a href="#contact">Contact Us</a>
    </nav>

    <div class="navbar-right">
      <button class="user-btn" id="userBtn">👤</button>
      <div class="user-menu" id="userMenu">
        <a href="register.php">Register</a>
        <a href="login.php">Login</a>
        <a href="adminlogin.php">Admin</a>
      </div>
    </div>
  </header>

  <main>
    <!-- 1) Hero: picture on right, text on left -->
    <section class="hero-banner">

      <div class="hero-left">
        <h1>For Comfortable &<br>Convenient Stays</h1>
      </div>

      <div class="hero-right">
        <img src="images/homepage.jpg" alt="homepage">
      </div>

    </section>

    <!-- 2) Browse your stay -->
    <section id="browse" class="section">
      <h2 class="cards-heading">Browse Your Next Stay</h2>
      <div class="cards-row">
        <div class="card">
          <a href="blocka.php"><h2>Block A<br>(Female's block)</h2></a>
        </div>
        <div class="card">
          <a href="blockb.php"><h2>Block B<br>(Male's block)</h2></a>
        </div>
      </div>
    </section>

    <!-- 3) About -->
    <section id="about" class="section">
      <h2 class="cards-heading">About StayHub</h2>
      <div class="about-card">
        <h3>We are set on ensuring a comfortable and convenient
            stay for you throughout your campus experience. StayHub
            is your number one choice for an easy and trustworthy room
            booking experience.</h3>
      </div>
    </section>

  </main>

  <script src="shscript.js"></script>

  <footer class="footer">
    <h2 class="footer-heading">For more information:</h2>
    <div class="footer-columns">
      <div class="footer-left" id="contact">
        <h3>Contact Us</h3>
        <p class="hours-text">📞 +255 123 456 789</p>
        <p class="hours-text">📞 +255 987 654 321</p>
      </div>

      <div class="footer-center" id="social-icons">
        <h3>Our socials</h3>
        <div class="social-icons">
          <a href="#" aria-label="Facebook">
            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 12a10 10 0 10-11.5 9.9v-7h-2.2v-3h2.2V9.2c0-2.2 1.3-3.4 3.3-3.4.96 0 1.97.17 1.97.17v2.2h-1.12c-1.1 0-1.44.68-1.44 1.38V12h2.45l-.39 3h-2.06v7A10 10 0 0022 12z"/></svg>
          </a>
          <a href="#" aria-label="Twitter">
            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22 5.92c-.63.28-1.3.48-2 .57a3.48 3.48 0 001.53-1.92 6.96 6.96 0 01-2.2.84 3.48 3.48 0 00-5.93 3.17A9.88 9.88 0 013 4.89a3.48 3.48 0 001.08 4.65 3.4 3.4 0 01-1.58-.44v.04a3.48 3.48 0 002.79 3.41 3.5 3.5 0 01-1.57.06 3.48 3.48 0 003.25 2.41A6.99 6.99 0 012 19.54 9.86 9.86 0 008.29 21c6.02 0 9.32-5 9.32-9.33v-.42A6.67 6.67 0 0022 5.92z"/></svg>
          </a>
          <a href="#" aria-label="Instagram">
            <svg class="social-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 6.2a4.8 4.8 0 100 9.6 4.8 4.8 0 000-9.6zm6.4-3.1a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z"/></svg>
          </a>
        </div>
      </div>

      <div class="footer-right" id="consultation">
        <h3>Consultation</h3>
        <p class="hours-text">Open from: 8:00am-17:00pm</p>
      </div>
    </div>

    <div class="footer-bottom">
      <p class="hours-text">www.StayHub.com</p>
    </div>
  </footer>

</body>
</html>