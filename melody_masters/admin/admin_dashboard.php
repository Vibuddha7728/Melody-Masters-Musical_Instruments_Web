<?php
session_start();
include '../includes/db_config.php';
include '../includes/auth_check.php';
checkAccess('admin'); 

$success_msg = "";
$error_msg = "";

// --- 1. PRODUCT ADD LOGIC (FIXED) ---
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $price = mysqli_real_escape_string($conn, $_POST['p_price']);
    $category_name = mysqli_real_escape_string($conn, $_POST['p_category']);
    $stock = mysqli_real_escape_string($conn, $_POST['p_stock']);
    $p_type = mysqli_real_escape_string($conn, $_POST['p_type']); 
    
    // Automation: Category නම අනුව ID එක ලබා ගැනීම
    $category_id = 1; // Default
    if ($category_name == "Keyboards") $category_id = 2;
    if ($category_name == "Drums") $category_id = 3;
    if ($category_name == "Wind Instruments") $category_id = 1; 
    if ($category_name == "Digital Sheet Music") $category_id = 7;

    $target_dir = "../assets/uploads/products/"; 
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }

    $image_name = time() . "_" . basename($_FILES["p_image"]["name"]);
    $target_file = $target_dir . $image_name;

    // පින්තූර හෝ PDF වැනි ඕනෑම ගොනුවක් ඇතුළත් කිරීමට හැකි වන පරිදි මෙහි පරීක්ෂාව ලිහිල් කර ඇත
    if(isset($_FILES["p_image"]) && $_FILES["p_image"]["error"] == 0) {
        if (move_uploaded_file($_FILES["p_image"]["tmp_name"], $target_file)) {
            
            // Database Query
            $sql = "INSERT INTO products (name, price, category_id, stock, image) 
                    VALUES ('$name', '$price', '$category_id', '$stock', '$image_name')";
            
            if (mysqli_query($conn, $sql)) {
                $last_id = mysqli_insert_id($conn);

                // Digital Product check
                if ($p_type == "digital" || $category_id == 7) {
                    $file_path = $image_name; 
                    $sql_digital = "INSERT INTO digital_products (product_id, file_path, max_downloads) 
                                    VALUES ('$last_id', '$file_path', 10)";
                    mysqli_query($conn, $sql_digital);
                }

                $success_msg = "Product added successfully and linked!";
            } else { 
                $error_msg = "Database error: " . mysqli_error($conn); 
            }
        } else { $error_msg = "File upload failed."; }
    } else { $error_msg = "Invalid file or no file selected."; }
}

// --- 2. STAFF ADD LOGIC ---
if (isset($_POST['add_staff'])) {
    $s_name = mysqli_real_escape_string($conn, $_POST['s_name']);
    $s_email = mysqli_real_escape_string($conn, $_POST['s_email']);
    $s_pass = password_hash($_POST['s_pass'], PASSWORD_DEFAULT);
    
    $checkEmail = mysqli_query($conn, "SELECT id FROM users WHERE email='$s_email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        $error_msg = "Error: Staff email already exists!";
    } else {
        $sql = "INSERT INTO users (username, email, password, role) VALUES ('$s_name', '$s_email', '$s_pass', 'staff')";
        if (mysqli_query($conn, $sql)) { $success_msg = "New Staff Member registered successfully!"; }
    }
}

// --- 3. DASHBOARD STATS ---
$revenue_query = mysqli_query($conn, "SELECT SUM(total_amount) as total_revenue FROM orders");
$row_rev = mysqli_fetch_assoc($revenue_query);
$total_revenue = $row_rev['total_revenue'] ?? 0;

$user_query = mysqli_query($conn, "SELECT COUNT(id) as total_users FROM users");
$active_users = mysqli_fetch_assoc($user_query)['total_users'] ?? 0;

$order_query = mysqli_query($conn, "SELECT COUNT(id) as total_orders FROM orders");
$total_orders = mysqli_fetch_assoc($order_query)['total_orders'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(rgba(0,0,0,0.9), rgba(0,0,0,0.9)), 
                        url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=2070');
            background-size: cover; background-attachment: fixed; color: white; min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; padding: 25px;
        }
        .stat-card {
            padding: 20px; border-radius: 15px; border-left: 5px solid #ffc107;
            background: rgba(255, 255, 255, 0.03); margin-bottom: 20px;
        }
        .nav-link { color: rgba(255,255,255,0.5); font-weight: bold; border: none !important; }
        .nav-link.active { background: transparent !important; color: #ffc107 !important; border-bottom: 2px solid #ffc107 !important; }
        
        .form-control, .form-select { 
            background: rgba(255, 255, 255, 0.08) !important; 
            color: white !important; 
            border: 1px solid rgba(255,255,255,0.1) !important; 
        }
        .form-select option { 
            background-color: #1a1a1a !important; 
            color: white !important; 
        }
        
        .btn-warning { background: #ffc107; font-weight: bold; color: black; }
        .table { font-size: 0.85rem; color: white; border-color: rgba(255,255,255,0.1); }
        .product-img { width: 40px; height: 40px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0 text-warning text-uppercase">Admin Panel</h3>
            <p class="text-white-50 small">Melody Masters Management</p>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <span class="small bg-dark px-3 py-1 rounded-pill border border-secondary text-uppercase">
                <i class="bi bi-person-fill text-warning"></i> <?php echo $_SESSION['username']; ?>
            </span>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm rounded-pill">Logout</a>
        </div>
    </div>

    <?php if($success_msg): ?> <div class="alert alert-success alert-dismissible fade show"><?php echo $success_msg; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> <?php endif; ?>
    <?php if($error_msg): ?> <div class="alert alert-danger alert-dismissible fade show"><?php echo $error_msg; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card">
                <p class="small text-white-50 mb-1 text-uppercase">Total Revenue</p>
                <h3 class="fw-bold text-warning mb-0">$<?php echo number_format($total_revenue, 2); ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left-color: #0dcaf0;">
                <p class="small text-white-50 mb-1 text-uppercase">Active Users</p>
                <h3 class="fw-bold text-info mb-0"><?php echo $active_users; ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left-color: #f75d5d;">
                <p class="small text-white-50 mb-1 text-uppercase">Total Orders</p>
                <h3 class="fw-bold text-danger mb-0"><?php echo $total_orders; ?></h3>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-4">
            <div class="glass-card">
                <ul class="nav nav-tabs nav-fill mb-4" id="adminTab">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#product-pane">PRODUCT</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#staff-pane">ADD STAFF</button></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="product-pane">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="small text-warning fw-bold">PRODUCT NAME</label>
                                <input type="text" name="p_name" class="form-control" placeholder="Enter name" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="small text-warning fw-bold">PRICE ($)</label>
                                    <input type="number" step="0.01" name="p_price" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="small text-warning fw-bold">STOCK</label>
                                    <input type="number" name="p_stock" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-warning fw-bold">PRODUCT TYPE</label>
                                <select name="p_type" class="form-select" required>
                                    <option value="" disabled selected>Select Type</option>
                                    <option value="physical">Physical Product</option>
                                    <option value="digital">Digital Product</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small text-warning fw-bold">CATEGORY</label>
                                <select name="p_category" class="form-select">
                                    <option>Guitars</option>
                                    <option>Keyboards</option>
                                    <option>Drums</option>
                                    <option>Wind Instruments</option>
                                    <option>Digital Sheet Music</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="small text-warning fw-bold">UPLOAD IMAGE/FILE</label>
                                <input type="file" name="p_image" class="form-control" required>
                            </div>
                            <button type="submit" name="add_product" class="btn btn-warning w-100">ADD PRODUCT</button>
                        </form>
                    </div>
                    
                    <div class="tab-pane fade" id="staff-pane">
                        <form action="" method="POST">
                            <div class="mb-3"><label class="small text-warning fw-bold">STAFF USERNAME</label><input type="text" name="s_name" class="form-control" required></div>
                            <div class="mb-3"><label class="small text-warning fw-bold">EMAIL ADDRESS</label><input type="email" name="s_email" class="form-control" required></div>
                            <div class="mb-4"><label class="small text-warning fw-bold">PASSWORD</label><input type="password" name="s_pass" class="form-control" required></div>
                            <button type="submit" name="add_staff" class="btn btn-warning w-100">REGISTER STAFF</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-card mb-4">
                <h5 class="fw-bold text-warning mb-3"><i class="bi bi-cart-check"></i> LIVE ORDER MONITORING</h5>
                <div class="table-responsive" style="max-height: 250px;">
                    <table class="table table-dark table-hover">
                        <thead><tr><th>ORDER ID</th><th>CUSTOMER</th><th>TOTAL</th><th>STATUS</th></tr></thead>
                        <tbody>
                            <?php
                            $orders = mysqli_query($conn, "SELECT o.id, u.username, o.total_amount, o.status FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC LIMIT 10");
                            if(mysqli_num_rows($orders) > 0):
                                while($o = mysqli_fetch_assoc($orders)):
                            ?>
                            <tr>
                                <td>#ORD-<?php echo $o['id']; ?></td>
                                <td><?php echo $o['username']; ?></td>
                                <td class="text-warning fw-bold">$<?php echo number_format($o['total_amount'], 2); ?></td>
                                <td><span class="badge bg-primary text-capitalize"><?php echo $o['status']; ?></span></td>
                            </tr>
                            <?php endwhile; else: ?>
                            <tr><td colspan="4" class="text-center text-white-50">No orders found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass-card">
                <h5 class="fw-bold text-warning mb-3"><i class="bi bi-database"></i> SYSTEM RECORDS</h5>
                <p class="text-warning small fw-bold mb-2">PRODUCT INVENTORY</p>
                <div class="table-responsive" style="max-height: 280px;">
                    <table class="table table-dark table-sm">
                        <thead><tr><th>FILE</th><th>NAME</th><th>TYPE</th><th>STOCK</th><th>PRICE</th></tr></thead>
                        <tbody>
                            <?php
                            $prods = mysqli_query($conn, "SELECT p.*, d.id as is_digital FROM products p LEFT JOIN digital_products d ON p.id = d.product_id ORDER BY p.id DESC");
                            while($p = mysqli_fetch_assoc($prods)):
                            ?>
                            <tr>
                                <td>
                                    <?php 
                                    $ext = pathinfo($p['image'], PATHINFO_EXTENSION);
                                    if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                        <img src="../assets/uploads/products/<?php echo $p['image']; ?>" class="product-img" onerror="this.src='https://via.placeholder.com/50'">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded text-center small py-2" style="width:40px; height:40px;"><i class="bi bi-file-earmark-text"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $p['name']; ?></td>
                                <td>
                                    <span class="badge <?php echo $p['is_digital'] ? 'bg-info' : 'bg-secondary'; ?> py-1">
                                        <?php echo $p['is_digital'] ? 'DIGITAL' : 'PHYSICAL'; ?>
                                    </span>
                                </td>
                                <td><?php echo $p['stock']; ?></td>
                                <td class="text-warning">$<?php echo number_format($p['price'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>