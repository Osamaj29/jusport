<?php
session_start();
$_SESSION = [];
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Logged Out</title>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      text-align: center;
      padding: 50px;
      background-color: #f4f4f4;
      color: #333;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #f60;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }
    a:hover {
      background-color: #e05500;
    }
  </style>
</head>
<body>
  <h1>You have been logged out.</h1>
  <a href="login.php">Return to Login</a>
</body>
</html>
