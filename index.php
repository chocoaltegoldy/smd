<?php
session_start();
include("db.php");

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['product_id'])) {
    $productId = (int) $_GET['product_id'];
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
    header("Location: index.php?added=1");
    exit();
}

// Load products
$sql = "SELECT id, name, description, category, image, price FROM products ORDER BY id DESC LIMIT 5";
$result = $conn->query($sql);
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Cart count
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) $cartCount += $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="description" content="SMD Medicare - Trusted supplier of diagnostic kits and equipment.">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  <title>SMD MEDICARE</title>
  <style>
    .product-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      padding: 30px;
      justify-content: center;
    }
    .product-card {
      background: white;
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 0;
      width: 280px;
      box-sizing: border-box;
      transition: box-shadow 0.2s;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }
    .product-card:hover {
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .product-link {
      display: block;
      padding: 15px;
      color: inherit;
      text-decoration: none;
    }
    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: contain;
    }
    .product-link h3, .product-link p {
      margin: 10px 0;
    }
    .enquiry-button {
      display: block;
      background-color: #0072bc;
      color: white;
      padding: 10px 15px;
      text-align: center;
      font-weight: bold;
      text-decoration: none;
      border-top: 1px solid #ccc;
    }
    .enquiry-button:hover {
      background-color: #005fa3;
    }
    .hero {
      text-align: center;
      padding: 60px 20px;
      background: linear-gradient(135deg, #0072bc, #3aaa35);
      color: white;
    }
    .hero h1 {
      font-size: 2.8em;
      margin-bottom: 10px;
    }
    .hero p {
      font-size: 1.2em;
    }
    .hero-button {
      background: #0072bc;
      color: white;
      padding: 10px 16px;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
    }
    .hero-button:hover {
      background: #005fa3;
    }
    .section {
      padding: 40px 20px;
      text-align: center;
    }
    .section h2 {
      margin-bottom: 20px;
      font-size: 2em;
      color: #0072bc;
    }
    .section p {
      max-width: 800px;
      margin: auto;
      line-height: 1.6;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<header class="navbar">
  <div class="logo">
    <a href="index.php"><img src="images/SMD MEDICARE.jpeg" alt="SMD MEDICARE Logo" /></a>
  </div>
  <nav>
    <ul>
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="cart.php">Cart (<?= $cartCount ?>)</a></li>
    </ul>
  </nav>
</header>

<!-- Hero -->
<section class="hero">
  <h1>SMD MEDICARE</h1>
  <p>Your Trusted Partner in Diagnostics & Healthcare Solutions</p>
  <a href="products.php" class="hero-button">Explore Our Products</a>
</section>

<!-- About Us -->
<section class="section">
  <h2>About Us</h2>
  <p><strong>SMD MEDICARE – A Decade of Trust, Innovation & Diagnostic Supply Excellence</strong></p>
  <p>For over 10 years, <strong>SMD MEDICARE</strong> has been a leading force in India’s diagnostic healthcare sector — specializing in the supply of monoclonal antibodies and recombinant antigens, the critical raw materials behind today’s cutting-edge diagnostic kits.</p>
  <p>We proudly support diagnostic kit manufacturers, hospitals, laboratories, and research centers with a comprehensive portfolio that includes rapid test kits, ELISA kits, biochemistry products, validated positive human serum samples, and medical equipment. Our reputation is built on consistent quality, regulatory compliance, and cost-effective solutions, backed by strong technical support.</p>
  <p>At SMD MEDICARE, we don’t just supply products — we deliver reliability, scientific insight, and long-term partnerships that help our clients stay ahead in the fast-evolving world of diagnostics.</p>
  <p><em>Let’s build a healthier future — together.</em></p>
  <p><strong>📞</strong> <a href="tel:+919555422455">+91-9555422455</a><br>
     <strong>📧</strong> <a href="mailto:rahul@smdmedicare.com">rahul@smdmedicare.com</a></p>
</section>

<!-- Featured Products -->
<section class="section">
  <h2>Featured Products</h2>
  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <div class="product-card">
        <a href="product_detail.php?id=<?= $product['id'] ?>" class="product-link">
          <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
          <h3><?= htmlspecialchars($product['name']) ?></h3>
          <p><?= htmlspecialchars($product['description']) ?></p>
          <p><strong>₹<?= number_format($product['price'], 2) ?></strong></p>
        </a>
        <a href="index.php?action=add&product_id=<?= $product['id'] ?>" class="enquiry-button">Add To Cart</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Browse by Category -->
<section class="section">
  <h2>Browse by Category</h2>
  <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-top: 20px;">
    <?php
      $categories = [
        "Raw Materials", "Uncut Sheet", "Rapid Test Kits", "ELISA Kits",
        "Reagents", "Medical Equipment", "Positive Human Serum Samples"
      ];
      foreach ($categories as $cat) {
        echo '<a href="category.php?cat=' . urlencode($cat) . '" class="hero-button">' . htmlspecialchars($cat) . '</a>';
      }
    ?>
  </div>
</section>

<!-- Footer -->
<?php include("footer.php"); ?>
</body>
</html>
