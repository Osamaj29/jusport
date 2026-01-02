<?php
session_start();

// Check if logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verify that this user is admin
$user_id = $_SESSION['user_id'];
$check = $conn->prepare("SELECT role FROM users WHERE id = ?");
$check->bind_param("i", $user_id);
$check->execute();
$result = $check->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    echo "Access denied. Admins only.";
    exit();
}

// Handle status update
$update_msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $booking_id = intval($_POST['booking_id']);
    $status = $_POST['status'];

    $update = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $update->bind_param("si", $status, $booking_id);

    if ($update->execute()) {
        $update_msg = "✅ Booking status updated successfully.";
    } else {
        $update_msg = "❌ Failed to update booking status.";
    }
    $update->close();
}

// Fetch all bookings with venue name
$sql = "SELECT b.id, u.name AS user_name, f.name AS venue_name, b.booking_date, b.booking_time, b.status
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN facilities f ON b.venue = f.id
        ORDER BY b.booking_date DESC, b.booking_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin - All Bookings</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      margin: 0;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px 20px;
      box-sizing: border-box;
    }
    .container {
      background: white;
      max-width: 1100px;
      width: 100%;
      border-radius: 12px;
      padding: 30px 35px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      animation: fadeIn 0.4s ease-in-out;
    }
    h1 {
      text-align: center;
      color: #002b5b;
      font-weight: 700;
      margin-bottom: 30px;
      font-size: 2rem;
    }
    .msg {
      text-align: center;
      font-weight: 600;
      margin-bottom: 20px;
      color: #007f5f;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
      border-radius: 8px;
      overflow: hidden;
    }
    thead {
      background-color: #002b5b;
      color: white;
    }
    th, td {
      padding: 14px 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tbody tr:hover {
      background-color: #ffefde;
    }
    .status-approved {
      color: green;
      font-weight: 700;
    }
    .status-rejected {
      color: red;
      font-weight: 700;
    }
    .status-pending {
      color: orange;
      font-weight: 700;
    }
    select {
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      background-color: #f8f8f8;
    }
    button {
      background-color: #002b5b;
      color: white;
      padding: 6px 14px;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background-color: #005792;
    }
    a.back-btn {
      display: inline-block;
      background-color: #002855;
      color: white;
      padding: 12px 28px;
      margin: 30px auto 0;
      border-radius: 8px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(24, 16, 137, 0.4);
      transition: background-color 0.3s ease;
      user-select: none;
    }
    a.back-btn:hover {
      background-color: #10519aff;
    }
    p.no-bookings {
      text-align: center;
      color: #002b5b;
      font-weight: 600;
      font-size: 18px;
      margin-top: 40px;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>All Bookings - Admin Panel</h1>

    <?php if ($update_msg): ?>
      <p class="msg"><?= htmlspecialchars($update_msg) ?></p>
    <?php endif; ?>

    <?php if ($result && $result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>User</th>
            <th>Facility</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php
              $statusClass = '';
              if (strtolower($row['status']) === 'approved') {
                  $statusClass = 'status-approved';
              } elseif (strtolower($row['status']) === 'rejected') {
                  $statusClass = 'status-rejected';
              } elseif (strtolower($row['status']) === 'pending') {
                  $statusClass = 'status-pending';
              }
            ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['user_name']) ?></td>
              <td><?= htmlspecialchars($row['venue_name']) ?></td>
              <td><?= htmlspecialchars($row['booking_date']) ?></td>
              <td><?= htmlspecialchars($row['booking_time']) ?></td>
              <td class="<?= $statusClass ?>"><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
              <td>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['id']) ?>">
                  <select name="status" required>
                    <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $row['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $row['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                  </select>
                  <button type="submit">Update</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <div style="text-align: center;">
        <a href="admin_dashboard.php" class="back-btn">🏠 Back to Home</a>
      </div>

    <?php else: ?>
      <p class="no-bookings">No bookings found.</p>
      <div style="text-align: center;">
        <a href="admin_dashboard.php" class="back-btn">🏠 Back to Home</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
