<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to place an order!'); window.location='index.php';</script>";
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$subtotal = 0;
$has_physical = false; 

$cart_items = [];
foreach ($_SESSION['cart'] as $id => $qty) {
    $id = (int)$id;
    $res = $conn->query("SELECT * FROM products WHERE id = $id");
    if($res && $res->num_rows > 0){
        $product = $res->fetch_assoc();
        $product['qty'] = $qty;
        $cart_items[] = $product;
        $subtotal += ($product['price'] * $qty);
        
        // Product එක Physical ද යන්න පරීක්ෂා කිරීම (Database එකේ product_type column එක අනුව)
        if (isset($product['product_type']) && strtolower($product['product_type']) == 'physical') { 
            $has_physical = true; 
        }
    }
}

// Physical අයිතමයක් නැත්නම් shipping 0 වේ
$shipping = ($has_physical && $subtotal < 100) ? 50.00 : 0.00;
$grand_total = $subtotal + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Checkout | Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ffc107;
            --bg: #050505;
            --card-bg: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.1);
            --input-bg: #111111;
            --text-main: #ffffff;
            --text-dim: #b0b0b0;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            margin: 0;
            padding-bottom: 60px;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px var(--input-bg) inset !important;
            -webkit-text-fill-color: #ffffff !important;
        }

        .checkout-nav {
            text-align: center;
            padding: 40px 0 30px 0;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .checkout-nav a { text-decoration: none; color: var(--text-dim); transition: 0.3s; }
        .checkout-nav a:hover { color: #fff; }
        .checkout-nav .active { color: var(--primary); pointer-events: none; }
        .checkout-nav span { color: #333; margin: 0 15px; }

        .main-wrapper {
            width: 100%;
            max-width: 1150px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
            align-items: start;
            padding: 0 20px;
        }

        .checkout-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 30px;
            padding: 45px;
        }

        h1 { font-size: 2.8rem; font-weight: 800; margin-bottom: 35px; letter-spacing: -1.5px; }
        .step-label { color: var(--primary); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; display: block; margin-bottom: 15px; }

        .form-control {
            background-color: var(--input-bg) !important;
            border: 1.5px solid var(--border) !important;
            border-radius: 16px !important;
            color: #fff !important;
            padding: 18px 22px !important;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .form-control:focus { border-color: var(--primary) !important; box-shadow: none !important; }
        .form-control::placeholder { color: #555 !important; font-weight: 500; }

        .summary-panel {
            background: #0d0d0d;
            border: 1px solid var(--primary);
            border-radius: 30px;
            padding: 40px;
            position: sticky;
            top: 20px;
        }

        .summary-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 25px; color: var(--text-main); border-bottom: 1px solid rgba(255,193,7,0.2); padding-bottom: 15px; }

        .prod-item { 
            display: flex; align-items: center; gap: 15px; margin-bottom: 18px; 
            padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); 
        }
        .prod-thumb { width: 55px; height: 55px; border-radius: 12px; object-fit: cover; border: 1px solid var(--border); }
        .prod-info .name { font-weight: 700; color: #fff; font-size: 0.95rem; display: block; }
        .prod-info .qty { color: var(--text-dim); font-size: 0.8rem; }
        .prod-price { font-weight: 700; color: #fff; margin-left: auto; }

        .price-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 0.95rem; }
        .price-row .label { color: var(--text-dim); }
        .price-row .val { color: #fff; font-weight: 600; }

        .total-row { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-top: 20px; padding-top: 20px; border-top: 1px dashed rgba(255,193,7,0.4); 
        }
        .total-row .label { font-weight: 800; font-size: 1.1rem; color: #fff; }
        .total-row .val { font-weight: 800; font-size: 1.8rem; color: var(--primary); }

        .btn-finish {
            background: var(--primary); color: #000; width: 100%; padding: 20px;
            border-radius: 18px; font-weight: 800; border: none; transition: 0.4s; margin-top: 25px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .btn-finish:hover { background: #fff; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.4); }

        .payment-grid { display: grid; gap: 15px; margin-top: 15px; }
        .single-column { grid-template-columns: 1fr; }
        .double-column { grid-template-columns: 1fr 1fr; }

        .pay-box { 
            cursor: pointer; padding: 20px; border: 1.5px solid var(--border); border-radius: 20px; 
            text-align: center; transition: 0.3s; background: rgba(255,255,255,0.02);
        }
        input[type="radio"] { display: none; }
        input[type="radio"]:checked + .pay-box { border-color: var(--primary); background: rgba(255,193,7,0.1); }
        .pay-box i { font-size: 1.5rem; display: block; margin-bottom: 5px; color: var(--text-dim); }
        input[type="radio"]:checked + .pay-box i { color: var(--primary); }
        .pay-box span { font-weight: 700; font-size: 0.85rem; }

        @media (max-width: 992px) { .main-wrapper { grid-template-columns: 1fr; } .summary-panel { position: static; } }
    </style>
</head>
<body>

<div class="checkout-nav">
    <a href="cart.php">Cart</a>
    <span>/</span>
    <a href="#" class="active">Checkout</a>
</div>

<form action="process_checkout.php" method="POST" autocomplete="off" class="w-100">
    <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
    <input type="hidden" name="has_physical" value="<?php echo $has_physical ? '1' : '0'; ?>">

    <div class="main-wrapper">
        
        <div class="checkout-card">
            <h1>Check out.</h1>

            <span class="step-label">Step 01: Contact Information</span>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>

            <?php if ($has_physical): ?>
            <span class="step-label mt-4">Step 02: Shipping Details</span>
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="fname" class="form-control" placeholder="First Name" required>
                </div>
                <div class="col-md-6">
                    <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                </div>
                <div class="col-12">
                    <input type="text" name="address" class="form-control" placeholder="Street Address / House Number" required>
                </div>
            </div>
            <?php endif; ?>

            <span class="step-label mt-4">Step 03: Payment Choice</span>
            <div class="payment-grid <?php echo $has_physical ? 'double-column' : 'single-column'; ?>">
                <label class="m-0 w-100">
                    <input type="radio" name="payment_method" value="card" checked>
                    <div class="pay-box">
                        <i class="bi bi-credit-card-2-front"></i>
                        <span>Debit/Credit Card</span>
                    </div>
                </label>

                <?php if ($has_physical): ?>
                <label class="m-0 w-100">
                    <input type="radio" name="payment_method" value="cod">
                    <div class="pay-box">
                        <i class="bi bi-truck"></i>
                        <span>Cash On Delivery</span>
                    </div>
                </label>
                <?php endif; ?>
            </div>
        </div>

        <div class="summary-panel">
            <div class="summary-title">ORDER SUMMARY</div>
            
            <div class="items-list mb-4">
                <?php foreach ($cart_items as $item): ?>
                <div class="prod-item">
                    <img src="assets/uploads/products/<?php echo $item['image']; ?>" class="prod-thumb">
                    <div class="prod-info">
                        <span class="name"><?php echo $item['name']; ?></span>
                        <span class="qty">Qty: <?php echo $item['qty']; ?></span>
                    </div>
                    <div class="prod-price">£<?php echo number_format($item['price'] * $item['qty'], 2); ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="pricing-details">
                <div class="price-row">
                    <span class="label">Subtotal</span>
                    <span class="val">£<?php echo number_format($subtotal, 2); ?></span>
                </div>
                
                <?php if ($has_physical): ?>
                <div class="price-row">
                    <span class="label">Shipping</span>
                    <span class="val <?php echo ($shipping == 0) ? 'text-success' : ''; ?>">
                        <?php echo ($shipping == 0) ? 'FREE' : '£'.number_format($shipping, 2); ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <div class="total-row">
                    <span class="label">GRAND TOTAL</span>
                    <span class="val">£<?php echo number_format($grand_total, 2); ?></span>
                </div>

                <button type="submit" name="place_order" class="btn-finish">
                    COMPLETE ORDER <i class="bi bi-shield-check ms-2"></i>
                </button>
                
                <p class="text-center mt-3 mb-0" style="font-size: 0.7rem; color: #444; letter-spacing: 0.5px;">
                    <i class="bi bi-lock-fill me-1"></i> SECURED BY SSL ENCRYPTION
                </p>
            </div>
        </div>

    </div>
</form>

</body>
</html>