<?php
// Redirect to your payment gateway
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// In production, log this info and redirect to payment
header("Location: https://your-payment-link.com?name=" . urlencode($name));
exit();