<?php
session_start();
include("db.php");

$category = $_GET['cat'] ?? '';

// Fetch products
$stmt = $conn->prepare("SELECT id, name, description, image, price FROM products WHERE category = ?");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
$filteredProducts = $result->fetch_all(MYSQLI_ASSOC);

// Add to cart logic
if (isset($_GET['action'], $_GET['product_id']) && $_GET['action'] == "add") {
    $product_id = (int)$_GET['product_id'];
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
    header("Location: category.php?cat=" . urlencode($category));
    exit();
}

// Count cart items
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) $cartCount += $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($category) ?> - SMD MEDICARE</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
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
    .hero h1 { font-size: 2.5em; margin-bottom: 5px; }
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
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="cart.php">Cart (<?= $cartCount ?>)</a></li>
    </ul>
  </nav>
</header>

<!-- Hero -->
<section class="hero">
  <h1><?= htmlspecialchars($category) ?></h1>
  <p>Explore our selection of <?= htmlspecialchars($category) ?> products</p>
</section>

<!-- Category Navigation -->
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

<!-- Product Cards -->
<section>
  <div class="product-grid">
    <?php if ($filteredProducts): ?>
      <?php foreach ($filteredProducts as $product): ?>
        <div class="product-card">
          <a href="product_detail.php?id=<?= $product['id'] ?>" class="product-link">
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
            <h3><?= htmlspecialchars($product['name']) ?></h3>
            <p><?= htmlspecialchars($product['description']) ?></p>
            <p><strong>₹<?= number_format($product['price'], 2) ?></strong></p>
          </a>
          <a href="category.php?action=add&product_id=<?= $product['id'] ?>&cat=<?= urlencode($category) ?>" class="enquiry-button">Add To Cart</a>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align:center; font-weight:bold;">No products found in this category.</p>
    <?php endif; ?>
  </div>
</section>

<?php include("./footer.php"); ?>
</body>
</html>
