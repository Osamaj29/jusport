<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Jeddah University Sport Facilities Booking</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
      overflow-x: hidden;
    }

    /* ================= HEADER ================= */
    .header-bar {
      background: #002b5b;
      color: white;
      padding: 10px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      animation: fadeInDown 1s ease forwards;
    }

    .header-bar h1 {
      margin: 0;
      font-size: 18px;
      font-weight: 600;
    }

    .navbar {
      background: rgba(228, 228, 228, 0.71);
      animation: fadeInDown 1.2s ease forwards;
    }

    .navbar .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      padding: 10px 30px;
    }

    nav {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 6px 10px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      background-color: rgba(79, 150, 216, 0.86);
      border-radius: 4px;
      transform: translateY(-2px);
    }

    nav a.btn {
      background: orange;
      color: white;
      border-radius: 4px;
      transition: transform 0.3s ease;
    }

    nav a.btn:hover {
      transform: scale(1.05);
    }

    nav a.btn-secondary {
      background: white;
      color: #004080;
      border: 1px solid #004080;
      border-radius: 4px;
      transition: transform 0.3s ease;
    }

    nav a.btn-secondary:hover {
      transform: scale(1.05);
    }

    /* ================= HERO ================= */
    .hero {
      background: linear-gradient(to right, rgb(189, 196, 204), #0059b3);
      color: white;
      padding: 60px 20px;
      text-align: center;
      animation: fadeInUp 1.5s ease forwards;
    }

    /* ================= VENUES ================= */
    .venues {
      padding: 40px 20px;
      animation: fadeIn 1.5s ease forwards;
    }

    .venue-grid {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .venue-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      padding: 16px;
      width: 300px;
      text-align: center;
      transform: translateY(40px);
      opacity: 0;
      animation: fadeUpCard 0.8s ease forwards;
    }

    .venue-card:nth-child(1) { animation-delay: 0.3s; }
    .venue-card:nth-child(2) { animation-delay: 0.5s; }
    .venue-card:nth-child(3) { animation-delay: 0.7s; }

    .venue-card:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .venue-card img {
      width: 100%;
      border-radius: 8px;
      height: 160px;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .venue-card:hover img {
      transform: scale(1.05);
    }

    .tags {
      margin: 10px 0;
      color: #777;
    }

    .card-actions a.btn-sm {
      margin: 4px;
      display: inline-block;
      padding: 6px 10px;
      font-size: 14px;
      text-decoration: none;
      color: white;
      background: #007bff;
      border-radius: 4px;
      transition: transform 0.3s ease, background 0.3s ease;
    }

    .card-actions a.btn-sm:hover {
      transform: scale(1.05);
      background: #0056b3;
    }

    .card-actions a.btn-primary {
      background: #28a745;
    }

    .card-actions a.btn-primary:hover {
      background: #1e7e34;
    }

    /* ================= FOOTER ================= */
    .footer {
      background: #002b5b;
      color: white;
      text-align: center;
      padding: 15px 0;
      margin-top: 40px;
      animation: fadeInUp 1s ease forwards;
    }

    /* ================= BACK BUTTON ================= */
    .top-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      background: orange;
      color: white;
      padding: 8px 14px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      transition: transform 0.3s ease, background 0.3s ease;
    }

    .top-btn:hover {
      transform: scale(1.1);
      background: darkorange;
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 768px) {
      .navbar .container {
        flex-direction: column;
        align-items: flex-start;
      }

      nav {
        justify-content: flex-start;
        gap: 10px;
        margin-top: 10px;
      }

      .header-bar {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
    }

    /* ================= ANIMATIONS ================= */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes fadeUpCard {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <!-- Back to Home if not index -->
  <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
    <a href="index.php" class="top-btn">Back to Home</a>
  <?php endif; ?>

  <!-- Top title bar -->
  <div class="header-bar">
    <h1>Jeddah University Sport Facilities Booking System</h1>
  </div>

  <!-- Navigation Bar -->
  <header class="navbar">
    <div class="container">
      <nav>
        <a href="index.php">Home</a>
        <a href="facilities.php">Facilities</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="dashboard.php">Dashboard</a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="logout.php" class="btn btn-secondary">Logout</a>
        <?php else: ?>
          <a href="login.php" class="btn">Login</a>
          <a href="register.php" class="btn btn-secondary">Register</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <h2>Explore & Book Sports Venues</h2>
      <p>Discover top-rated sports venues and book instantly.</p>
    </div>
  </section>

  <!-- Facilities Section -->
  <section id="facilities" class="venues">
    <div class="container">
      <h2 style="text-align: center; margin-bottom: 30px;">Venues</h2>
      <div class="venue-grid">

        <!-- Tennis -->
        <div class="venue-card">
        <img src="uploads/tennis.jpg" alt="Tennis Court" />
          <h3>Tennis</h3>
          <p>Jeddah University Campus</p>
          <p><strong>Hours:</strong> 08:00 - 10:00</p>
          <div class="tags">Tennis, Padel</div>
          <div class="card-actions">
            <a href="venue_details.php?sport=tennis" class="btn-sm">Details</a>
            <a href="booking.php?sport=tennis" class="btn-sm btn-primary">Book Now</a>
          </div>
        </div>

        <!-- Football -->
        <div class="venue-card">
          <img src="uploads/football.jpg" alt="Football Field" />
          <h3>Football</h3>
          <p>Jeddah University Stadium</p>
          <p><strong>Hours:</strong> 11:00 - 01:00</p>
          <div class="tags">5v5, Full Field</div>
          <div class="card-actions">
            <a href="venue_details.php?sport=football" class="btn-sm">Details</a>
            <a href="booking.php?sport=football" class="btn-sm btn-primary">Book Now</a>
          </div>
        </div>

        <!-- Swimming -->
        <div class="venue-card">
          <img src="uploads/swimming.jpg" alt="Swimming Pool" />
          <h3>Swimming</h3>
          <p>Jeddah University Pool Center</p>
          <p><strong>Hours:</strong> 03:00 - 06:00</p>
          <div class="tags">Indoor, Olympic Size</div>
          <div class="card-actions">
            <a href="venue_details.php?sport=swimming" class="btn-sm">Details</a>
            <a href="booking.php?sport=swimming" class="btn-sm btn-primary">Book Now</a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Footer -->
  <div class="footer">
  &copy; 2025 Jeddah Universiti Sport Facilities Booking System
</div>

</body>
</html>
