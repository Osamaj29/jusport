<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

require_once "db.php";

$user_id = $_SESSION['user_id'];
$sql = "SELECT username, email, nickname, age, address, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $user = $result->fetch_assoc();
} else {
  die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard | Jeddah University Sport Facilities</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #00509e);
      color: #333;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* HEADER */
    .header-bar {
      background: white;
      color: #002b5b;
      padding: 15px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      border-bottom: 5px solid #004080;
    }

    .header-bar h1 {
      font-size: 20px;
      font-weight: 700;
    }

    /* NAVIGATION */
    .navbar {
      background: #abb3c5ff;
      border-bottom: 1px solid #ccc;
    }

    .navbar .container {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      padding: 12px 30px;
    }

    nav {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    nav a {
      color: #002b5b;
      text-decoration: none;
      font-weight: 600;
      padding: 8px 14px;
      border-radius: 6px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      background-color: #004080;
      color: white;
    }

    nav a.btn-secondary {
      background: white;
      color: #004080;
      border: 1px solid #004080;
    }

    nav a.btn-secondary:hover {
      background: #004080;
      color: white;
    }

    /* DASHBOARD SECTION */
    .dashboard {
      flex: 1;
      padding: 60px 20px;
      text-align: center;
    }

    .dashboard h1 {
      font-size: 2.5rem;
      color: white;
      margin-bottom: 10px;
    }

    .dashboard p {
      color: #dfe9f3;
      font-size: 1.1rem;
      margin-bottom: 40px;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
      gap: 40px;
      max-width: 1100px;
      margin: 0 auto;
    }

    /* CARD STYLING */
    .card {
      border-radius: 18px;
      padding: 25px;
      background: linear-gradient(135deg, #ffffff, #e9eef6);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .card h2 {
      color: #002b5b;
      font-size: 1.4rem;
      margin-bottom: 20px;
      border-left: 4px solid #004080;
      padding-left: 10px;
      text-align: left;
    }

    .profile-pic {
      text-align: center;
      margin-bottom: 20px;
    }

    .profile-pic img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #004080;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .card p {
      text-align: left;
      font-size: 15px;
      margin: 8px 0;
      color: #333;
    }

    /* ACTION BUTTONS */
    .action-btn {
      display: block;
      background: linear-gradient(135deg, #004080, #007bff);
      color: white;
      padding: 14px 20px;
      text-decoration: none;
      border-radius: 10px;
      font-weight: 600;
      text-align: center;
      transition: all 0.3s ease;
      width: 85%;
      max-width: 320px;
      margin: 0 auto;
      box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    }

    .action-btn:hover {
      background: linear-gradient(135deg, #007bff, #0099ff);
      transform: scale(1.04);
    }

    .footer {
      background: #001f3f;
      color: white;
      text-align: center;
      padding: 18px 0;
      font-size: 0.95rem;
      margin-top: 50px;
    }

    @media(max-width: 768px) {
      .header-bar { flex-direction: column; align-items: flex-start; }
      nav { justify-content: center; gap: 10px; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="header-bar">
    <h1>🏛️ Jeddah University Sport Facilities Booking System</h1>
  </div>

  <!-- Navigation -->
  <header class="navbar">
    <div class="container">
      <nav>
        <a href="index.php">Home</a>
        <a href="facilities.php">Facilities</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="my_orders.php" class="btn">My Bookings</a>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Dashboard -->
  <section class="dashboard">
    <h1>Welcome to Your Dashboard</h1>
    <p>Manage your bookings and account below.</p>

    <div class="dashboard-grid">
      <!-- Profile Card -->
      <div class="card">
        <h2>Profile Info</h2>
        <?php if (!empty($user['profile_pic']) && file_exists("uploads/" . $user['profile_pic'])): ?>
          <div class="profile-pic">
            <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture">
          </div>
        <?php else: ?>
          <p style="text-align:center;"><em>No profile picture uploaded.</em></p>
        <?php endif; ?>
        <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Nickname:</strong> <?= htmlspecialchars($user['nickname']) ?></p>
        <p><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
      </div>

      <!-- Quick Actions -->
      <div class="card">
        <h2>Quick Actions</h2>
        <div style="display: flex; flex-direction: column; align-items: center; gap: 14px; margin-top: 20px;">
          <a href="edit_profile.php" class="action-btn">✏️ Edit Profile</a>
          <a href="facilities.php" class="action-btn">📅 Book New Facility</a>
          <a href="my_orders.php" class="action-btn">📋 View My Bookings</a>
          <a href="browse_facilities.php" class="action-btn">🏟️ Browse Facilities</a>
          <a href="booking_management.php" class="action-btn">⚙️ Booking Management</a>
          <a href="booking_history.php" class="action-btn">🕓 Booking History</a>
          <a href="reset_password111.php" class="action-btn">🔒 Reset Password</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <div class="footer">
    &copy; 2025 Jeddah University Sport Facilities Booking System.

</body>
</html>
