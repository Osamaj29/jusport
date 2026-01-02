<?php
session_start();

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "sport_booking");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle delete
$delete_message = "";
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    if ($conn->query("DELETE FROM users WHERE id = $delete_id AND role != 'admin'")) {
        $delete_message = "User deleted successfully.";
    } else {
        $delete_message = "Error deleting user: " . $conn->error;
    }
}

// Handle add user
$add_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // ✅ Password validation (server-side)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $add_message = "Password must include uppercase, lowercase, number, special character, and be at least 8 characters long.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed, $role);
        if ($stmt->execute()) {
            $add_message = "User added successfully.";
        } else {
            $add_message = "Error: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manage Users</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      margin: 0;
      min-height: 100vh;
      padding: 40px 20px;
      box-sizing: border-box;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }
    .container {
      background: white;
      max-width: 1000px;
      width: 100%;
      padding: 30px 35px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      animation: fadeIn 0.4s ease-in-out;
    }
    h1, h2 {
      color: #002b5b;
      font-weight: 700;
      margin-bottom: 20px;
    }
    h2 {
      margin-top: 40px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
      margin-top: 20px;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
      border-radius: 8px;
      overflow: hidden;
      background: #fafafa;
    }
    th, td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    thead th {
      background-color: #002b5b;
      color: white;
      font-weight: 600;
    }
    tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    tbody tr:hover {
      background-color: #ffefde;
    }
    label {
      font-weight: 600;
      display: block;
      margin-top: 12px;
      margin-bottom: 6px;
      color: #002b5b;
    }
    input, select {
      width: 100%;
      padding: 12px;
      font-size: 14px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }
    .submit-btn {
      margin-top: 20px;
      background-color: #ff6a00;
      color: white;
      border: none;
      padding: 12px;
      font-weight: 700;
      font-size: 15px;
      border-radius: 8px;
      cursor: pointer;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    .submit-btn:hover {
      background-color: #e65c00;
    }
    .message {
      color: green;
      font-weight: 700;
      margin-bottom: 15px;
      text-align: center;
    }
    .error {
      color: red;
      font-weight: 700;
      margin-bottom: 15px;
      text-align: center;
    }
    .btn {
      padding: 8px 16px;
      background-color: #ff6a00;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 700;
      font-size: 14px;
      display: inline-block;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    .btn-danger {
      background-color: #d90000;
    }
    .btn:hover {
      opacity: 0.9;
    }
    .actions-cell {
      min-width: 110px;
    }
    .back-btn-wrapper {
      text-align: center;
      margin-top: 30px;
    }
    a.back-btn {
      display: inline-block;
      background-color: #002b5b;
      color: white;
      padding: 12px 28px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(0,43,91,0.3);
      transition: background-color 0.3s ease;
      user-select: none;
    }
    a.back-btn:hover {
      background-color: #004080;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
  <script>
    // ✅ Client-side password validation
    function validatePassword() {
      const pass = document.getElementById("password").value;
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      if (!regex.test(pass)) {
        alert("Password must contain uppercase, lowercase, number, special character, and be at least 8 characters long.");
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
  <div class="container">
    <h1>Manage Users</h1>

    <?php if ($add_message): ?>
      <div class="<?= strpos($add_message, 'Error') !== false ? 'error' : 'message' ?>">
        <?= $add_message ?>
      </div>
    <?php endif; ?>

    <?php if ($delete_message): ?>
      <div class="message"><?= $delete_message ?></div>
    <?php endif; ?>

    <div class="form-section">
      <h2>Add New User</h2>
      <form method="POST" onsubmit="return validatePassword();" novalidate>
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required />

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required />

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required />

        <label for="role">Role:</label>
        <select id="role" name="role">
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>

        <button type="submit" class="submit-btn">Add User</button>
      </form>
    </div>

    <h2>All Registered Users</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th class="actions-cell">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($user['id']) ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['role']) ?></td>
            <td class="actions-cell">
              <?php if ($user['role'] !== 'admin'): ?>
                <a class="btn btn-danger" href="?delete_id=<?= $user['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
              <?php else: ?>
                <em>Protected</em>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <div class="back-btn-wrapper">
      <a href="admin_dashboard.php" class="back-btn">🏠 Back to Home</a>
    </div>
  </div>
</body>
</html>
