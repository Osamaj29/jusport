<?php
session_start();

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = htmlspecialchars(trim($_POST['name']));
  $email = htmlspecialchars(trim($_POST['email']));
  $message = htmlspecialchars(trim($_POST['message']));

  if (!empty($name) && !empty($email) && !empty($message)) {
    $to = 'osama0557562398@gmail.com';
    $subject = "New Message from Jeddah University Sport Facilities Contact Form";
    $body = "You have received a new message:\n\n" .
            "Name: $name\n" .
            "Email: $email\n\n" .
            "Message:\n$message";

    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n" .
               "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
      $success = "Your message has been sent successfully. We'll contact you soon!";
    } else {
      $error = "Something went wrong. Please try again later.";
    }
  } else {
    $error = "Please fill in all the fields.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact | Jeddah University Sport Facilities</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #002b5b, #005792);
      margin: 0;
      padding: 30px;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }

    .card {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
      width: 700px;
      max-width: 95%;
    }

    h2 {
      text-align: center;
      color: #002855;
      margin-bottom: 15px;
      font-size: 26px;
    }

    p.desc {
      text-align: center;
      color: #555;
      margin-bottom: 25px;
      font-size: 15px;
    }

    .contact-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    label {
      font-weight: 600;
      margin-bottom: 4px;
    }

    input, textarea {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
      width: 100%;
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    /* 🔸 Orange send button */
    .btn-submit {
      background: #f60;
      color: white;
      padding: 12px;
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
      transition: background 0.3s ease;
      border: none;
      cursor: pointer;
    }
    .btn-submit:hover {
      background: #e05500;
    }

    /* 🔹 Blue back button */
    .btn {
      background: #002855;
      color: white;
      padding: 12px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      text-align: center;
      transition: background 0.3s ease;
      display: block;
      margin-top: 20px;
    }
    .btn:hover {
      background: #001b40;
    }

    .message {
      margin-bottom: 15px;
      padding: 12px;
      border-radius: 6px;
      font-size: 14px;
    }

    .success { background-color: #d4edda; color: #155724; }
    .error { background-color: #f8d7da; color: #721c24; }

    @media(max-width:600px){
      .card { padding: 20px; }
    }
  </style>
</head>
<body>

<div class="card">
  <h2>Contact Us</h2>
  <p class="desc">We're here to help. Reach out using the form below.</p>

  <?php if ($success): ?>
    <div class="message success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= $error ?></div>
  <?php endif; ?>

  <form class="contact-form" method="POST" action="contact.php">
    <label for="name">Full Name</label>
    <input type="text" id="name" name="name" required />

    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" required />

    <label for="message">Your Message</label>
    <textarea id="message" name="message" required></textarea>

    <button type="submit" class="btn-submit">Send Message</button>
  </form>

  <a href="index.php" class="btn">← Back to Home</a>
</div>

</body>
</html>
