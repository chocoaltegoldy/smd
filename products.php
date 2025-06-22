<?php
session_start();
include("db.php");

// Handle Add to Cart
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['product_id'])) {
    $productId = (int) $_GET['product_id'];
    $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + 1;
    header("Location: products.php?added=1");
    exit();
}

// Load products
$sql = "SELECT id, name, description, category, image, price FROM products ORDER BY id DESC";
$result = $conn->query($sql);
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Load categories
$catResult = $conn->query("SELECT DISTINCT category FROM products ORDER BY category ASC");
$categories = ['All'];
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        if (!empty($row['category'])) {
            $categories[] = $row['category'];
        }
    }
}

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
  <meta name="description" content="Browse SMD Medicare Products">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Products - SMD MEDICARE</title>

  <style>
    .whatsapp-float {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 9999;
      background-color: #25d366;
      padding: 10px;
      border-radius: 50%;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .whatsapp-float img {
      width: 30px;
      height: 30px;
    }

    .product-section {
      display: flex;
      padding: 30px;
    }

    .sidebar-categories {
      flex: 0 0 220px;
      background: linear-gradient(135deg, #0072bc, #3aaa35);
      padding: 15px;
      border-radius: 10px;
      height: fit-content;
      position: sticky;
      top: 100px;
      align-self: flex-start;
    }
    .sidebar-categories h3 {
      color: white;
      margin-bottom: 12px;
    }
    .category-button {
      display: block;
      width: 100%;
      margin-bottom: 10px;
      padding: 10px;
      background: #ffffff;
      color: #0072bc;
      border: 2px solid #0072bc;
      border-radius: 4px;
      font-weight: bold;
      text-align: left;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .category-button:hover,
    .category-button.active {
      background: #3aaa35;
      color: #fff;
      border-color: #3aaa35;
    }

    .product-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-left: 30px;
      justify-content: flex-start;
    }

    .product-card {
      background: white;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 280px;
      box-sizing: border-box;
      transition: box-shadow 0.2s;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    .product-card:hover {
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .product-card a.card-link {
      text-decoration: none;
      color: inherit;
      display: block;
      padding: 15px;
    }

    .product-card img {
      width: 100%;
      height: 180px;
      object-fit: contain;
      margin-bottom: 10px;
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
      margin-top: 5px;
    }
  </style>
</head>
<body>

<!-- WhatsApp Button -->
<a href="https://wa.me/919555422455?text=Hello%20SMD%20Medicare,%20I%20need%20assistance%20with%20your%20products" class="whatsapp-float" target="_blank">
  <img src="https://img.icons8.com/ios-filled/50/ffffff/whatsapp--v1.png" alt="WhatsApp" />
</a>

<!-- Navbar -->
<header class="navbar">
  <div class="logo">
    <a href="index.php"><img src="images/SMD MEDICARE.jpeg" alt="SMD MEDICARE Logo" /></a>
  </div>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php" class="active">Products</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="cart.php">Cart (<?php echo $cartCount; ?>)</a></li>
    </ul>
  </nav>
</header>

<!-- Hero -->
<section class="hero">
  <h1>Our Product Range</h1>
  <p>Your Trusted Partner in Diagnostics & Healthcare Solutions</p>
</section>

<!-- Product Section -->
<section class="featured-products">
  <div class="product-section">

    <!-- Sidebar Filters -->
    <div class="sidebar-categories">
      <h3>Filter by Category</h3>
      <?php foreach ($categories as $cat): ?>
        <button class="category-button" data-category="<?php echo htmlspecialchars($cat); ?>">
          <?php echo htmlspecialchars($cat); ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Product Grid -->
    <div class="product-grid" id="productGrid">
      <?php foreach ($products as $product): ?>
        <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>">

          <!-- Card Content Clickable -->
          <a href="product_detail.php?id=<?= $product['id'] ?>" class="card-link">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <p><strong>₹<?php echo number_format($product['price'], 2); ?></strong></p>
          </a>

          <!-- Add to Cart Button -->
          <a href="products.php?action=add&product_id=<?php echo $product['id']; ?>" class="enquiry-button">Add To Cart</a>

        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<?php include("footer.php"); ?>

<!-- JS Filter Logic -->
<script>
  const buttons = document.querySelectorAll('.category-button');
  const cards = document.querySelectorAll('.product-card');

  buttons.forEach(button => {
    button.addEventListener('click', () => {
      buttons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      const selected = button.getAttribute('data-category');

      cards.forEach(card => {
        const cardCat = card.getAttribute('data-category');
        card.style.display = (selected === 'All' || cardCat === selected) ? 'block' : 'none';
      });
    });
  });
</script>

</body>
</html>
