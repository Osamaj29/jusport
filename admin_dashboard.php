<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Hardcoded admin info (for demo/testing)
$admin = [
    'name' => 'Osama',
    'username' => 'osama_j29',
    'email' => 'osama0557562398@gmail..com',
    'address' => 'Jeddah, Saudi Arabia',
    'age' => 24
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      margin: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      max-width: 900px;
      width: 100%;
      padding: 40px;
      animation: fadeIn 0.4s ease-in-out;
    }
    h1 {
      color: #002b5b;
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      font-size: 2.2rem;
    }
    .card {
      background: #e1f0a6ff;
      border-radius: 12px;
      padding: 25px 30px;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    .card h2 {
      color: #002b5b;
      margin-bottom: 20px;
      font-weight: 600;
      text-align: center;
      font-size: 1.6rem;
    }
    .card p {
      font-size: 15px;
      color: #333;
      margin: 8px 0;
    }
    .profile-img {
      display: block;
      margin: 0 auto 20px auto;
      width: 130px;
      height: 130px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #002b5b;
    }
    .btn-group {
      text-align: center;
    }
    a.btn {
      display: inline-block;
      background-color: #ff6a00;
      color: white;
      padding: 12px 22px;
      margin: 10px 8px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      cursor: pointer;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    a.btn:hover {
      background-color: #e65c00;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Admin Dashboard</h1>

    <div class="card">
      <h2>Profile Info</h2>
      <img src="admin logo.jpg" alt="Admin Logo" class="profile-img" />
      <p><strong>Name:</strong> <?= htmlspecialchars($admin['name']) ?></p>
      <p><strong>Username:</strong> <?= htmlspecialchars($admin['username']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($admin['address']) ?></p>
      <p><strong>Age:</strong> <?= htmlspecialchars($admin['age']) ?></p>
    </div>

    <div class="btn-group">
      <a href="admin_bookings.php" class="btn">📋 View All Bookings</a>
      <a href="manage_users.php" class="btn">👥 Manage Users</a>
      <a href="manage_facilities.php" class="btn">➕ Add Facility</a>
      <a href="edit_facilities.php" class="btn">✏️ Edit Facility</a>
      <a href="delete_facility.php?id=1" class="btn" onclick="return confirm('Are you sure you want to delete this facility?');">🗑️ Delete Facility</a>
      <a href="logout.php" class="btn">🚪 Logout</a>
    </div>
  </div>
</body>
</html>
