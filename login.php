<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin - SMD MEDICARE</title>
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f4f8fb;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .navbar {
      background-color: #fff;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .navbar img {
      height: 50px;
    }

    .navbar ul {
      list-style: none;
      display: flex;
      gap: 25px;
      margin: 0;
      padding: 0;
    }

    .navbar ul li a {
      color: #00bcd4;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .navbar ul li a:hover,
    .navbar ul li a.active {
      color: #0097a7;
      border-bottom: 2px solid #0097a7;
    }

    .hero-section {
      background: linear-gradient(to right, #00c9a7, #00b5ef);
      color: white;
      padding: 60px 20px;
      text-align: center;
    }

    .login-form {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      max-width: 400px;
      margin: 40px auto;
      border: 1px solid #ddd;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      text-align: center;
    }

    .login-form img {
      width: 60px;
      margin-bottom: 10px;
    }

    .login-form h2 {
      margin-bottom: 20px;
      color: #333;
    }

    .input-group {
      position: relative;
      margin-bottom: 15px;
    }

    .input-group .icon {
      position: absolute;
      top: 12px;
      left: 12px;
      font-size: 16px;
      color: #555;
    }

    .login-form input {
      width: 100%;
      padding: 12px 12px 12px 40px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1em;
    }

    .login-form button {
      width: 100%;
      background-color: #00bcd4;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1em;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .login-form button:hover {
      background-color: #0097a7;
      transform: scale(1.03);
    }

    .show-password {
      text-align: left;
      margin: 5px 0 15px;
      font-size: 0.9em;
    }

    .error-message {
      background: black;
      color: #d32f2f;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
    }

    footer {
      text-align: center;
      padding: 20px;
      background-color: #00bcd4;
      color: white;
      margin-top: auto;
      font-size: 1em;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<header class="navbar">
  <div class="logo">
    <a href="index.php">
      <img src="images/SMD MEDICARE.jpeg" alt="SMD MEDICARE Logo" />
    </a>
  </div>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="login.php" class="active">Admin</a></li>
      <li><a href="contact.php">Contact</a></li>
    </ul>
  </nav>
</header>

<!-- Hero Section -->
<section class="hero-section">
  <h1>Admin Panel Access</h1>
  <p>Please log in to manage your product listings.</p>
</section>

<!-- Login Form -->
<section class="login-form">
  <img src="images/SMD MEDICARE.jpeg" alt="Logo" />
  <h2>Welcome Admin</h2>
  <form method="post" action="">
    <div class="input-group">
      <span class="icon">👤</span>
      <input type="text" name="username" placeholder="Username" required />
    </div>
    <div class="input-group">
      <span class="icon">🔒</span>
      <input type="password" name="password" id="password" placeholder="Password" required />
    </div>
    <div class="show-password">
      <input type="checkbox" onclick="togglePassword()" /> Show Password
    </div>
    <button type="submit">Login</button>
  </form>

  <?php if ($error): ?>
    <p class="error-message"><?php echo $error; ?></p>
  <?php endif; ?>
</section>

<!-- Footer -->
<footer>
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 1200px; margin: auto;">
    <div>
      <h4>About Us</h4>
      <p>SMD MEDICARE – Specializing in raw materials (monoclonal antibodies & antigens), rapid test kits, positive human serum, ELISA kits, biochemistry reagents & medical equipment — trusted partners in diagnostics for 10+ years.</p>
    </div>
    <div>
      <h4>Quick Links</h4>
      <p><a href="index.php">Home</a></p>
      <p><a href="products.php">Products</a></p>
      <p><a href="contact.php">Contact</a></p>
    </div>
    <div>
      
    <h4>Contact Info</h4>
      <p><strong>Hours:</strong> Monday–Saturday, 9:00 AM – 6:00 PM</p>
      <p><strong>Address:</strong> Shakumbari Vihar, Phase 2, Ganeshpur, Roorkee, Haridwar – 247667</p>
      <p><strong>Phone:</strong> <a href="tel:+919555422455">+91 95554 22455</a></p>
      <p><strong>Email:</strong> <a href="mailto:rahul@smdmedicare.com">rahul@smdmedicare.com</a></p>
    </div>
  </div>
  <div style="text-align: center; padding-top: 20px; font-weight: bold;">
    &copy; <?= date("Y") ?> SMD MEDICARE | All Rights Reserved
  </div>
</footer>


<!-- JS: Toggle Password -->
<script>
  function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
  }
</script>

</body>
</html>
