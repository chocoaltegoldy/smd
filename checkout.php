<?php
session_start();
include("db.php");

$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum($cart);

// Helper to get product info
function getProduct($conn, $id) {
    $stmt = $conn->prepare("SELECT name, price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - SMD Medicare</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    .checkout-container { max-width: 1000px; margin: 40px auto; padding: 20px; background: #f9f9f9; border-radius: 10px; }
    .checkout-container h2 { text-align: center; margin-bottom: 30px; }
    .checkout-options { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px; }
    .checkout-box { flex: 1; min-width: 300px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .checkout-box h3 { margin-bottom: 15px; color: #0072bc; }
    .checkout-box form input, .checkout-box form textarea { width: 100%; margin-bottom: 10px; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
    .checkout-box form button { background: #0072bc; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; }
    .checkout-box form button:hover { background: #3aaa35; }
    .product-summary { font-size: 14px; background: #eef; padding: 10px; border-radius: 5px; margin-top: 10px; max-height: 150px; overflow-y: auto; }
  </style>
</head>
<body>

<!-- Header -->
<header class="navbar">
  <div class="logo"><a href="index.php"><img src="images/SMD MEDICARE.jpeg" alt="SMD Logo"/></a></div>
  <nav>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="products.php">Products</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="cart.php" class="active">Cart (<?= $cartCount ?>)</a></li>
    </ul>
  </nav>
</header>

<section class="hero">
  <h1>Checkout</h1>
  <p>Choose how you want to proceed</p>
</section>

<div class="checkout-container">

  <div class="checkout-options">

    <!-- BUY NOW -->
    <div class="checkout-box">
      <h3>Buy Now</h3>
      <form action="process_payment.php" method="post">
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="phone" placeholder="Phone Number" required />
        <textarea name="address" placeholder="Shipping Address" rows="3" required></textarea>
        <input type="hidden" name="total" value="<?= $cartCount ?>" />
        <button type="submit">Proceed to Pay</button>
      </form>
    </div>

    <!-- REQUEST QUOTATION -->
    <div class="checkout-box">
      <h3>Request Personalized Quotation</h3>
      <form action="request_quote.php" method="post">
        <input type="text" name="name" placeholder="Full Name" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="text" name="phone" placeholder="Phone Number" required />
        <textarea name="message" placeholder="Any specific request or message..." rows="3"></textarea>
        <div class="product-summary">
          <strong>Items in Cart:</strong><br>
          <?php
            if (empty($cart)) {
              echo "Cart is empty.";
            } else {
              foreach ($cart as $id => $qty) {
                $product = getProduct($conn, $id);
                echo htmlspecialchars($product['name']) . " (Qty: $qty)<br>";
              }
            }
          ?>
        </div>
        <button type="submit">Send Quotation Request</button>
      </form>
    </div>

  </div>
</div>

<?php include("footer.php"); ?>

</body>
</html>
