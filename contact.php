<?php
session_start();
include("db.php");

// Calculate cart count
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartCount += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - SMD MEDICARE</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: Arial, sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
    }

    .contact-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      padding: 40px 20px;
      background-color: #f9f9f9;
    }

    .contact-card, .contact-form {
      background: white;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 30px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .contact-card h2,
    .contact-form h2 {
      color: #004d40;
      margin-bottom: 20px;
      text-align: center;
    }

    .contact-card p {
      margin: 10px 0;
      font-size: 16px;
      line-height: 1.6;
    }

    .contact-card a {
      color: #00695c;
      text-decoration: none;
    }

    .contact-form form {
      display: flex;
      flex-direction: column;
    }

    .contact-form input,
    .contact-form textarea {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    .contact-form textarea {
      resize: vertical;
    }

    .contact-form button {
      padding: 12px;
      background-color: #00bfa5;
      color: black;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    .contact-form button:hover {
      background-color: #0072bc;
    }

    .map-container {
      margin: 40px auto;
      max-width: 90%;
    }

    iframe {
      width: 100%;
      height: 400px;
      border: none;
      border-radius: 8px;
    }

    footer {
      background-color: #00bcd4;
      color: black;
      font-family: Arial, sans-serif;
    }

    footer a {
      color: black;
      text-decoration: none;
    }

    footer h4 {
      margin-top: 0;
    }

    .footer-container {
      max-width: 1200px;
      margin: auto;
      padding: 30px 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 30px;
    }

    .footer-bottom {
      text-align: center;
      border-top: 1px solid rgba(255,255,255,0.3);
      padding: 15px;
      font-size: 14px;
    }
  </style>
</head>

<body>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/919555422455?text=Hello%20SMD%20Medicare,%20I%20need%20assistance%20with%20your%20products" 
   class="whatsapp-float" target="_blank" aria-label="WhatsApp Chat">
  <img src="https://img.icons8.com/ios-filled/50/ffffff/whatsapp--v1.png" alt="WhatsApp Icon" />
</a>

<!-- Navbar -->
<header class="navbar">
  <div class="logo">
    <a href="index.php">
      <img src="images/SMD MEDICARE.jpeg" alt="SMD MEDICARE Logo" />
    </a>
  </div>
  <nav>
    <ul>
      <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a></li>
      <li><a href="products.php" class="<?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>">Products</a></li>
      <li><a href="contact.php" class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">Contact</a></li>
      <li><a href="cart.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Cart (<?= $cartCount ?>)</a></li>
    </ul>
  </nav>
</header>

<!-- Hero Section -->
<section class="hero" style="background-color: #004d40; color: white; padding: 20px; text-align: center;">
  <h1>Contact Us</h1>
  <p>We're here to support you with your diagnostic needs</p>
</section>

<!-- Main Contact Info & Enquiry Form -->
<main>
  <div class="contact-container">
    <!-- Contact Info -->
    <div class="contact-card">
      <h2>Get in Touch</h2>
      <p>📍 <strong>Address:</strong> Shakumbari Vihar, Phase 2, Ganeshpur, Roorkee, Haridwar – 247667, Uttarakhand, India</p>
      <p>📞 <strong>Phone/WhatsApp:</strong> <a href="tel:+919555422455">+91-95554 22455</a></p>
      <p>📧 <strong>Email:</strong> <a href="mailto:rahul@smdmedicare.com">rahul@smdmedicare.com</a></p>
      <p>🌐 <strong>Website:</strong> <a href="https://www.smdmedicare.com" target="_blank">www.smdmedicare.com</a></p>
      <p>🕒 <strong>Hours:</strong> Monday – Saturday, 9:00 AM – 6:00 PM</p>
    </div>

    <!-- Enquiry Form -->
    <div class="contact-form">
      <h2>Send an Enquiry</h2>
      <form action="send_enquiry.php" method="POST">
        <input type="text" name="name" placeholder="Your Name" required />
        <input type="email" name="email" placeholder="Your Email" required />
        <input type="tel" name="phone" placeholder="Phone Number" required />
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit">Submit</button>
      </form>
    </div>
  </div>

  <!-- Map Section -->
  <div class="map-container">
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3443.023437540569!2d77.85217931495768!3d29.854350081944254!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3909459df2e64897%3A0x23ec5e34369d7dc0!2sRoorkee%2C%20Uttarakhand!5e0!3m2!1sen!2sin!4v1687000000000!5m2!1sen!2sin"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
</main>

<?php include("./footer.php"); ?>

</body>
</html>
