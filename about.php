<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About | Jeddah University Sport Facilities</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #f9fafb;
      color: #333;
      line-height: 1.6;
    }

    /* Header Bar */
    .header-bar {
      background-color: #002b5b;
      padding: 14px 40px;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .header-bar h1 {
      margin: 0;
      font-size: 20px;
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    /* Navbar */
    .navbar {
      background-color: #ffffff;
      border-bottom: 1px solid #e5e7eb;
    }

    .navbar .container {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      padding: 14px 30px;
    }

    nav {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
    }

    nav a {
      color: #002b5b;
      text-decoration: none;
      font-weight: 600;
      padding: 6px 10px;
      transition: all 0.25s ease;
    }

    nav a:hover {
      color: #004080;
    }

    /* Logout style - gray box like screenshot */
    nav a.btn-secondary {
      background: #808080;   /* gray */
      color: white;
      border: 1px solid #555;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
    }
    nav a.btn-secondary:hover {
      opacity: 0.9;
    }

    /* Hero Section */
    .hero {
      background: linear-gradient(120deg, #004080, #0058a3);
      color: white;
      padding: 70px 20px;
      text-align: center;
    }

    .hero h1 { font-size: 38px; font-weight: 700; margin-bottom: 12px; }
    .hero p  { font-size: 18px; opacity: 0.95; max-width: 700px; margin: 0 auto; }

    /* Content */
    .content { max-width: 1000px; margin: 0 auto; padding: 50px 20px; }
    .content h2 { font-size: 28px; color: #002855; margin-bottom: 20px; font-weight: 700; text-align: center; }
    .content p  { font-size: 1.1rem; line-height: 1.8; margin-bottom: 20px; text-align: justify; }

    /* Back Button */
    .back-btn {
      display: inline-block;
      background-color: #002855;
      color: white;
      padding: 12px 26px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      box-shadow: 0 3px 6px rgba(0,0,0,0.1);
      transition: background 0.3s ease;
    }
    .back-btn:hover { background-color: #001b40; }

    /* Footer */
    .footer {
      background: #002b5b;
      color: white;
      text-align: center;
      padding: 15px 0;
      font-size: 14px;
      margin-top: 60px;
    }

    @media (max-width: 768px) {
      .navbar .container { flex-direction: column; align-items: flex-start; }
      nav { justify-content: flex-start; gap: 15px; margin-top: 12px; }
      .hero h1 { font-size: 30px; }
      .hero p  { font-size: 16px; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header-bar">
    <h1>Jeddah University Sport Facilities Booking System</h1>
  </div>

  <!-- Navbar -->
  <header class="navbar">
    <div class="container">
      <nav>
        <a href="index.php">Home</a>
        <a href="facilities.php">Facilities</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="dashboard.php">Dashboard</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="logout.php" class="btn-secondary">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <h1>Empowering Sports Access for All</h1>
    <p>Discover how our system transforms sports facility booking into a seamless, smart, and accessible experience.</p>
  </section>

  <!-- Content Section -->
  <section class="content">
    <h2>About the Booking Platform</h2>
    <p>The <strong>Jeddah University Sport Facilities Booking System</strong> is a digital solution designed to make booking sports venues simple and efficient.</p>
    <p>With a wide variety of facilities available—including football, basketball, swimming, tennis, and more—users can easily plan games and practices.</p>
    <p>Built with modern web technologies, the system ensures compatibility across devices and prioritizes security and usability.</p>
    <p>We continuously improve the platform and welcome feedback to serve the university community better.</p>

    <div style="text-align: center; margin-top: 30px;">
      <a href="index.php" class="back-btn">🏠 Back to Home</a>
    </div>
  </section>

  <!-- Footer -->
  <div class="footer">
    &copy; 2025 Jeddah University Sport Facilities Booking System. 
  </div>

</body>
</html>
