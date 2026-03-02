<?php
session_start();
include 'includes/header.php'; // Header එක include කිරීම

// සාමාන්‍යයෙන් Checkout එකෙන් පස්සේ Order ID එක session එකක තබා ගනී
$order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 'N/A';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmed | Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-icon {
            font-size: 80px;
            color: #28a745;
        }
        .confirmation-card {
            max-width: 600px;
            margin: 50px auto;
            border-radius: 15px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container text-center">
    <div class="card shadow confirmation-card p-5">
        <div class="card-body">
            <div class="success-icon mb-4">
                <i class="bi bi-check-circle-fill"></i> ✔
            </div>
            
            <h1 class="text-success fw-bold">Success!</h1>
            <h3 class="mt-3">Thank you for your purchase.</h3>
            
            <p class="lead text-muted mt-3">
                Your order has been placed successfully. 
                <?php if($order_id !== 'N/A'): ?>
                    Your Order ID is: <strong>#<?php echo $order_id; ?></strong>
                <?php endif; ?>
            </p>

            <div class="alert alert-info mt-4">
                <p class="mb-0">You can track your order status and access digital downloads in your <strong>Customer Dashboard</strong>.</p>
            </div>

            <div class="d-grid gap-2 d-md-block mt-4">
                <a href="customer/my_account.php" class="btn btn-primary btn-lg px-4 me-md-2">Go to Dashboard</a>
                <a href="shop.php" class="btn btn-outline-dark btn-lg px-4">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<?php 
// ඇණවුම අවසන් නිසා Session එකේ තියෙන Order ID එක ඉවත් කරමු
unset($_SESSION['last_order_id']);

include 'includes/footer.php'; // Footer එක include කිරීම
?>