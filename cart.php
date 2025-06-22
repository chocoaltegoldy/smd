<?php
session_start();
include("db.php");

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$total = 0;

// Helper: Find product by ID (from DB)
function findProduct($conn, $id) {
    $stmt = $conn->prepare("SELECT id, name, description, category, image, price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Calculate cart count
$cartCount = 0;
foreach ($cart as $qty) {
    $cartCount += $qty;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Your Cart - SMD Medicare</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    .cart-table { width: 100%; border-collapse: collapse; margin: 30px auto; max-width: 1000px; }
    .cart-table th, .cart-table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
    .cart-table th { background: #0072bc; color: #fff; }
    .cart-table img { width: 50px; height: 50px; object-fit: contain; }
    .cart-actions { margin: 20px auto; max-width: 1000px; text-align: right; }
    .empty-cart { text-align: center; margin: 40px 0; color: #888; }
    .hero-button { background: #0072bc; color: #fff; border: none; padding: 8px 18px; border-radius: 20px; cursor: pointer; font-weight: bold; }
    .hero-button:hover { background: #3aaa35; }
  </style>
</head>

<body>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/919555422455?text=Hello%20SMD%20Medicare,%20I%20need%20assistance%20with%20your%20products" 
   class="whatsapp-float" target="_blank" aria-label="WhatsApp Chat">
  <img src="https://img.icons8.com/ios-filled/50/ffffff/whatsapp--v1.png" alt="WhatsApp" />
</a>

<!-- Header / Navbar -->
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

<!-- Hero -->
<section class="hero">
  <h1>🛒 Your Cart</h1>
  <p>Review your selected products below</p>
</section>

<main>
  <?php if (empty($cart)): ?>
    <div class="empty-cart">
      Your cart is empty.<br>
      <a href="products.php" class="hero-button" style="margin-top:15px;display:inline-block;">Browse Products</a>
    </div>
  <?php else: ?>
    <form method="post" action="update_cart.php">
      <table class="cart-table">
        <tr>
          <th>Product</th>
          <th>Name</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Remove</th>
        </tr>
        <?php foreach ($cart as $id => $qty): 
          $product = findProduct($conn, $id);
          if (!$product) continue;
          $subtotal = $product['price'] * $qty;
          $total += $subtotal;
        ?>
        <tr>
          <td>
            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          </td>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td>₹<?= number_format($product['price'], 2) ?></td>
          <td>
            <input type="number" name="qty[<?= $id ?>]" value="<?= $qty ?>" min="1" style="width:50px;">
          </td>
          <td>₹<?= number_format($subtotal, 2) ?></td>
          <td>
            <input type="checkbox" name="remove[]" value="<?= $id ?>">
          </td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
          <td colspan="2"><strong>₹<?= number_format($total, 2) ?></strong></td>
        </tr>
      </table>

      <div class="cart-actions">
        <button type="submit" class="hero-button">Update Cart</button>
        <a href="checkout.php" class="hero-button">Checkout</a>
      </div>
    </form>
  <?php endif; ?>
</main>

<?php
// ⭐️ Product Suggestion Bar - 20 products ⭐️
$suggestionQuery = "SELECT id, name, image FROM products ORDER BY RAND() LIMIT 20";
$suggestionResult = $conn->query($suggestionQuery);
$suggestions = [];
if ($suggestionResult) {
  while ($row = $suggestionResult->fetch_assoc()) {
    $suggestions[] = $row;
  }
}
?>

<!-- ✅ Continuous Slider Suggestion Bar -->
<style>
  .suggestion-bar {
    width: 100%;
    background: #f9f9f9;
    border-top: 2px solid #0072bc;
    border-bottom: 2px solid #0072bc;
    overflow: hidden;
    padding: 10px 0;
    margin-top: 40px;
  }

  .suggestion-bar h3 {
    font-size: 16px;
    margin: 0 20px 10px;
    color: #333;
    text-align: center;
  }

  .suggestion-wrapper {
    overflow: hidden;
    position: relative;
    width: 100%;
  }

  .suggestion-track {
    display: flex;
    width: max-content;
    animation: scroll-loop 40s linear infinite;
  }

  .suggestion-track a {
    display: inline-block;
    margin: 0 10px;
    text-align: center;
    color: #0072bc;
    text-decoration: none;
  }

  .suggestion-track img {
    width: 70px;
    height: 70px;
    object-fit: contain;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fff;
    padding: 5px;
    transition: transform 0.3s ease;
  }

  .suggestion-track img:hover {
    transform: scale(1.1);
  }

  @keyframes scroll-loop {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
  }
</style>

<div class="suggestion-bar">
  <h3>💡 You may also like:</h3>
  <div class="suggestion-wrapper">
    <div class="suggestion-track">
      <?php foreach (array_merge($suggestions, $suggestions) as $p): ?>
        <a href="product_detail.php?id=<?= $p['id'] ?>">
          <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" title="<?= htmlspecialchars($p['name']) ?>">
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include("footer.php"); ?>

<?php
// Recalculate cart count
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cartCount += $qty;
    }
}
?>

<!-- ✅ Floating Sidebar -->
<style>
  .floating-sidebar {
    position: fixed;
    top: 50%;
    left: 0;
    transform: translateY(-50%);
    background: linear-gradient(135deg, #0072bc, #3aaa35);
    padding: 10px 0;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    z-index: 9999;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
  }

  .floating-sidebar a {
    display: block;
    color: #fff;
    padding: 10px 15px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
  }

  .floating-sidebar a:hover {
    background-color: rgba(255, 255, 255, 0.2);
  }

  .floating-sidebar a + a {
    border-top: 1px solid rgba(255, 255, 255, 0.3);
  }
</style>

<div class="floating-sidebar">
  <a href="index.php">🏠 Home</a>
  <a href="products.php">📦 Products</a>
  <a href="contact.php">📞 Contact</a>
  <a href="cart.php">🛒 Cart (<?= $cartCount ?>)</a>
</div>

</body>
</html>
