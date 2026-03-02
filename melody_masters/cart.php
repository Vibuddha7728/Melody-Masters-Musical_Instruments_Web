<?php
session_start();
include 'includes/db_config.php';
include 'includes/functions.php';

$subtotal = 0;
$has_physical = false;

// Logic to handle removal directly on the page for better reliability
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
    }
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | Melody Masters</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-accent: #ffc107;
            --bg-dark: #080808;
            --card-bg: rgba(255, 255, 255, 0.04);
            --border: rgba(255, 255, 255, 0.08);
            --font-main: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: #f8f9fa;
            font-family: var(--font-main);
            -webkit-font-smoothing: antialiased;
        }

        .breadcrumb-nav {
            padding: 50px 0 20px;
            text-align: center;
        }
        .breadcrumb-nav a {
            text-decoration: none;
            color: #666;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            transition: 0.3s;
        }
        .breadcrumb-nav a:hover { color: var(--primary-accent); }
        .breadcrumb-nav span { color: #333; margin: 0 15px; }
        .breadcrumb-nav .active { color: #fff; border-bottom: 2px solid var(--primary-accent); padding-bottom: 4px; }

        .bg-glow {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 0%, rgba(255, 193, 7, 0.05), transparent 50%);
            z-index: -1;
        }

        .cart-container { padding: 40px 0 100px; }

        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 30px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 24px 0;
            border-bottom: 1px solid var(--border);
            transition: 0.3s ease;
        }
        .cart-item:last-child { border-bottom: none; }
        
        .product-img-box {
            width: 110px; height: 110px;
            background: #111;
            border-radius: 18px;
            padding: 12px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .product-img-box img { 
            max-width: 100%; 
            max-height: 100%; 
            object-fit: contain; 
            border-radius: 8px; 
            z-index: 2;
        }

        /* Image එක නැති විට පෙන්වන modern style icon එක */
        .img-placeholder {
            font-size: 2.2rem;
            color: var(--primary-accent);
            opacity: 0.6;
            filter: drop-shadow(0 0 10px rgba(255, 193, 7, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-info { flex: 1; padding: 0 25px; }
        .product-name { font-size: 1.25rem; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.3px; }
        .product-sku { font-size: 0.75rem; color: #555; text-transform: uppercase; letter-spacing: 1px; }

        .qty-wrapper {
            display: flex;
            align-items: center;
            background: #000;
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 4px;
        }
        .btn-qty {
            width: 32px; height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .btn-qty:hover { background: var(--primary-accent); color: #000; }
        .qty-input { width: 40px; text-align: center; font-weight: 700; font-size: 0.95rem; }

        .summary-box {
            background: #000;
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 35px;
            position: sticky; top: 50px;
        }
        .summary-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 25px; }

        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; color: #888; font-weight: 500; }
        .summary-total { 
            display: flex; justify-content: space-between; 
            margin-top: 25px; padding-top: 25px; 
            border-top: 1px solid var(--border);
            font-size: 1.5rem; font-weight: 800; color: #fff;
        }

        .btn-checkout {
            background: var(--primary-accent);
            color: #000;
            width: 100%;
            padding: 20px;
            border-radius: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 30px;
            border: 1px solid var(--primary-accent);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-checkout:hover {
            background: #ffffff;
            color: #000000;
            border-color: #ffffff;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(255, 255, 255, 0.2);
        }

        .remove-icon { color: #888; transition: 0.3s; font-size: 1.3rem; cursor: pointer; text-decoration: none; }
        .remove-icon:hover { color: #ff4d4d; transform: scale(1.1); }
    </style>
</head>
<body>

<div class="bg-glow"></div>

<nav class="breadcrumb-nav">
    <a href="shop.php">Shop</a>
    <span>/</span>
    <span class="active">Cart</span>
</nav>

<div class="container cart-container">
    <div class="row g-5">
        <div class="col-lg-8">
            <h2 class="fw-800 mb-4" style="letter-spacing: -1.5px;">Shopping Cart<span class="text-warning">.</span></h2>
            
            <div class="glass-card">
                <?php
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $id => $qty) {
                        $id = (int)$id;
                        $res = $conn->query("SELECT id, name, price, image, product_type FROM products WHERE id = $id");
                        
                        if ($res && $res->num_rows > 0) {
                            $product = $res->fetch_assoc();
                            
                            if ($product['product_type'] === 'physical') {
                                $has_physical = true;
                            }

                            $imageFileName = !empty($product['image']) ? $product['image'] : 'default.png';
                            $imagePath = "assets/uploads/products/" . $imageFileName;
                            
                            $line_total = $product['price'] * $qty;
                            $subtotal += $line_total;
                            ?>
                            <div class="cart-item" id="row-<?php echo $id; ?>">
                                <div class="product-img-box">
                                    <img src="<?php echo $imagePath; ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="img-placeholder" style="display: none;">
                                        <i class="bi bi-music-note-beamed"></i>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <span class="product-sku">Item #<?php echo $id; ?></span>
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <p class="text-warning fw-700 m-0">£<?php echo number_format($product['price'], 2); ?></p>
                                </div>

                                <div class="d-flex align-items-center gap-4">
                                    <div class="qty-wrapper">
                                        <button class="btn-qty" onclick="updateQty(<?php echo $id; ?>, -1)"><i class="bi bi-dash"></i></button>
                                        <div class="qty-input" id="qty-<?php echo $id; ?>"><?php echo $qty; ?></div>
                                        <button class="btn-qty" onclick="updateQty(<?php echo $id; ?>, 1)"><i class="bi bi-plus"></i></button>
                                    </div>
                                    
                                    <div class="text-end" style="min-width: 100px;">
                                        <p class="fw-800 m-0">£<span id="line-total-<?php echo $id; ?>"><?php echo number_format($line_total, 2); ?></span></p>
                                    </div>

                                    <a href="cart.php?remove=<?php echo $id; ?>" class="remove-icon ms-2" title="Remove Item">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                } else {
                    echo '<div class="text-center py-5">
                            <i class="bi bi-bag-x mb-3 d-block" style="font-size: 3rem; color: #222;"></i>
                            <h4 class="fw-bold">Your cart is currently empty</h4>
                            <a href="shop.php" class="btn btn-warning rounded-pill px-5 mt-3 fw-bold">Return to Shop</a>
                          </div>';
                }
                ?>
            </div>
        </div>

        <?php 
        if ($subtotal > 0): 
            $shipping_cost = 0;
            if ($has_physical) {
                $shipping_cost = ($subtotal > 100) ? 0 : 100;
            }
            $total = $subtotal + $shipping_cost;
        ?>
        <div class="col-lg-4">
            <div class="summary-box">
                <h3 class="summary-title">Order Summary</h3>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="text-white">£<span id="summary-subtotal"><?php echo number_format($subtotal, 2); ?></span></span>
                </div>
                
                <div class="summary-row">
                    <span>Estimated Shipping</span>
                    <span id="summary-shipping" class="<?php echo ($shipping_cost == 0) ? 'text-success' : 'text-white'; ?>">
                        <?php echo ($shipping_cost == 0) ? 'Free' : '£' . number_format($shipping_cost, 2); ?>
                    </span>
                </div>

                <div class="summary-row">
                    <span>Tax</span>
                    <span class="text-white">£0.00</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span class="text-warning">£<span id="summary-total"><?php echo number_format($total, 2); ?></span></span>
                </div>

                <a href="checkout.php" class="btn-checkout">Checkout Now</a>
                
                <div class="text-center mt-4 opacity-50 small">
                    <i class="bi bi-shield-lock me-1"></i> Secure SSL encrypted payment.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQty(productId, change) {
    const qtyElement = document.getElementById('qty-' + productId);
    let currentQty = parseInt(qtyElement.innerText);
    let newQty = currentQty + change;
    
    if (newQty < 1) return;

    qtyElement.style.opacity = '0.5';

    const params = new URLSearchParams();
    params.append('id', productId);
    params.append('qty', newQty);

    fetch('update_cart_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            qtyElement.innerText = newQty;
            qtyElement.style.opacity = '1';
            document.getElementById('line-total-' + productId).innerText = data.line_total;
            document.getElementById('summary-subtotal').innerText = data.subtotal;
            document.getElementById('summary-total').innerText = data.total;
            
            const shippingSpan = document.getElementById('summary-shipping');
            shippingSpan.innerText = data.shipping;
            
            if(data.shipping === 'Free') {
                shippingSpan.classList.remove('text-white');
                shippingSpan.classList.add('text-success');
            } else {
                shippingSpan.classList.remove('text-success');
                shippingSpan.classList.add('text-white');
            }
        }
    })
    .catch(err => {
        console.error('Error:', err);
        qtyElement.style.opacity = '1';
    });
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>