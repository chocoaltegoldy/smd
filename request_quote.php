<?php
session_start();
include("db.php");

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

$cart = $_SESSION['cart'] ?? [];

$body = "Quotation Request from $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message\n\nCart Items:\n";

foreach ($cart as $id => $qty) {
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($product = $res->fetch_assoc()) {
        $body .= "- " . $product['name'] . " (Qty: $qty)\n";
    }
}

mail("rahul@smdmedicare.com", "SMD Medicare - Quotation Request", $body, "From: $email");

echo "<script>alert('Quotation request sent successfully!'); window.location.href='index.php';</script>";
