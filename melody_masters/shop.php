<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_config.php';
include 'includes/functions.php';
include 'includes/header.php';

$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 1000000; 
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT p.*, dp.product_id AS is_digital 
        FROM products p 
        LEFT JOIN digital_products dp ON p.id = dp.product_id 
        WHERE p.price BETWEEN $min_price AND $max_price";

if ($category_filter !== '') {
    $sql .= " AND p.category_id = '$category_filter'";
}
if ($search_query !== '') {
    $sql .= " AND p.name LIKE '%$search_query%'";
}

$result = $conn->query($sql);

$current_cat_name = "All Shop";
if($category_filter !== '') {
    $cat_query = $conn->query("SELECT name FROM categories WHERE id = '$category_filter'");
    if($cat_query && $cat_query->num_rows > 0) {
        $current_cat_name = $cat_query->fetch_assoc()['name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters | Premium Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --accent: #ffc107;
            --accent-grad: linear-gradient(135deg, #ff9800 0%, #ffc107 100%);
            --bg-deep: #050505;
            --card-bg: #0f0f0f;
            --glass-bg: rgba(255, 255, 255, 0.03);
            --text-main: 'Plus Jakarta Sans', sans-serif;
            --text-body: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-deep);
            color: #ffffff;
            font-family: var(--text-body);
            overflow-x: hidden;
        }

        .shop-container { padding-top: 120px; padding-bottom: 80px; }

        .breadcrumb-side {
            margin-bottom: 20px;
            padding-left: 5px;
            font-size: 0.85rem;
        }
        .breadcrumb-side a { 
            text-decoration: none; 
            color: rgba(255,255,255,0.6); 
            transition: 0.3s;
        }
        .breadcrumb-side a.active-link { 
            color: var(--accent); 
            font-weight: 600;
        }
        .breadcrumb-side .sep { color: rgba(255,255,255,0.3); margin: 0 8px; }

        .sidebar-widget {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.03);
        }

        .widget-title {
            font-family: var(--text-main);
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .btn-filter-grad {
            background: var(--accent-grad);
            border: none;
            color: #000;
            font-weight: 700;
            padding: 10px 20px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .cat-item {
            color: #fff !important;
            opacity: 0.7;
            padding: 10px 0;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid rgba(255,255,255,0.03);
            transition: 0.3s;
        }
        .cat-item:hover, .cat-item.active {
            opacity: 1;
            color: var(--accent) !important;
            padding-left: 5px;
        }

        .product-card {
            background: var(--card-bg);
            border-radius: 25px;
            border: 1px solid rgba(255,255,255,0.04);
            padding: 20px;
            transition: 0.4s;
            height: 100%;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .product-badge {
            position: absolute;
            top: -10px; 
            right: 15px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.8px;
            z-index: 15;
            text-transform: uppercase;
            box-shadow: 0 8px 20px rgba(0,0,0,0.6);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .badge-digital { background: var(--accent-grad); color: #000; }
        .badge-physical { background: #ffffff; color: #000; }

        .img-box {
            background: #fff;
            height: 200px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin-bottom: 15px;
            overflow: hidden;
            position: relative;
        }
        
        .product-img { 
            max-width: 100%; 
            max-height: 100%; 
            object-fit: contain; 
            transition: transform 0.6s cubic-bezier(0.2, 1, 0.3, 1);
        }

        .product-card:hover {
            border-color: rgba(255, 193, 7, 0.5);
            transform: translateY(-8px);
        }

        .product-card:hover .product-img { transform: scale(1.1); }

        .top-status-bar {
            background: var(--glass-bg);
            border-radius: 15px;
            padding: 12px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* --- Mobile & Tablet Responsiveness --- */
        @media (max-width: 991px) {
            .shop-container { padding-top: 100px; }
            .sidebar-widget { margin-bottom: 15px; padding: 20px; }
            .top-status-bar { flex-direction: column; gap: 10px; text-align: center; }
            .top-status-bar h5 { font-size: 0.9rem; }
        }

        @media (max-width: 767px) {
            .shop-container { padding-top: 90px; }
            .img-box { height: 180px; }
            .product-card { padding: 15px; }
            /* Make sidebar stack nicely */
            .sidebar-widget { border-radius: 15px; }
            .widget-title { margin-bottom: 15px; }
            
            /* Center the reset button on empty results */
            .text-center py-5 { padding: 30px 0 !important; }
        }

        @media (max-width: 480px) {
            .product-card h4 { font-size: 0.85rem; }
            .img-box { height: 150px; }
            .top-status-bar { padding: 10px 15px; }
            .product-badge { padding: 4px 10px; font-size: 0.6rem; right: 10px; }
        }
    </style>
</head>
<body>

<div class="container shop-container">
    <div class="row">
        <div class="col-lg-3 order-2 order-lg-1">
            <div class="breadcrumb-side d-none d-lg-block">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <a href="shop.php" class="<?php echo ($category_filter == '') ? 'active-link' : ''; ?>">Shop</a>
                <?php if($category_filter): ?>
                    <span class="sep">/</span>
                    <a href="#" class="active-link text-capitalize"><?php echo htmlspecialchars($current_cat_name); ?></a>
                <?php endif; ?>
            </div>

            <div class="sidebar-widget">
                <h6 class="widget-title">Search Gear</h6>
                <form action="shop.php" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" class="form-control bg-dark border-0 text-white shadow-none" placeholder="Find gear..." style="border-radius: 10px 0 0 10px;">
                        <button class="btn btn-warning" type="submit" style="border-radius: 0 10px 10px 0;"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="sidebar-widget">
                <h6 class="widget-title">Price Range</h6>
                <form action="shop.php" method="GET">
                    <?php if($category_filter): ?>
                        <input type="hidden" name="category" value="<?php echo $category_filter; ?>">
                    <?php endif; ?>
                    <input type="range" class="form-range" min="0" max="1000000" step="1000" value="<?php echo $max_price; ?>" id="priceRange" name="max_price">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="small opacity-75">Up to: <b>£<span id="priceVal"><?php echo number_format($max_price); ?></span></b></span>
                        <button type="submit" class="btn-filter-grad">FILTER</button>
                    </div>
                </form>
            </div>

            <div class="sidebar-widget">
                <h6 class="widget-title">Categories</h6>
                <div class="nav flex-column">
                    <a href="shop.php" class="cat-item <?php echo ($category_filter == '') ? 'active' : ''; ?>">All Instruments</a>
                    <a href="shop.php?category=1" class="cat-item <?php echo ($category_filter == '1') ? 'active' : ''; ?>">Guitars</a>
                    <a href="shop.php?category=2" class="cat-item <?php echo ($category_filter == '2') ? 'active' : ''; ?>">Keyboards</a>
                    <a href="shop.php?category=3" class="cat-item <?php echo ($category_filter == '3') ? 'active' : ''; ?>">Drums</a>
                    <a href="shop.php?category=7" class="cat-item <?php echo ($category_filter == '7') ? 'active' : ''; ?>">Digital Sheets</a>
                </div>
            </div>
        </div>

        <div class="col-lg-9 order-1 order-lg-2 mb-4 mb-lg-0">
            <div class="breadcrumb-side d-lg-none">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <a href="shop.php" class="active-link">Shop</a>
            </div>

            <div class="top-status-bar">
                <h5 class="mb-0 fw-bold" style="font-family: var(--text-main); font-size: 1rem; letter-spacing: 1px; text-transform: uppercase;"><?php echo htmlspecialchars($current_cat_name); ?></h5>
                <span class="badge bg-dark border border-secondary px-3 py-2 text-warning">
                    <i class="bi bi-music-note-beamed me-2"></i><?php echo $result->num_rows; ?> Items Available
                </span>
            </div>

            <div class="row g-3 g-md-4">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="col-6 col-md-6 col-xl-4">
                            <div class="product-card">
                                <?php if($row['is_digital'] || $row['category_id'] == 7): ?>
                                    <div class="product-badge badge-digital">
                                        <i class="bi bi-cloud-download-fill d-none d-sm-inline"></i> DIGITAL 
                                    </div>
                                <?php else: ?>
                                    <div class="product-badge badge-physical">
                                        <i class="bi bi-box-seam-fill d-none d-sm-inline"></i> PHYSICAL
                                    </div>
                                <?php endif; ?>

                                <div class="img-box">
                                    <?php 
                                        if ($row['name'] == 'Electric Guitar Solo Pack') {
                                            $img_name = 'digital_default.png';
                                        } else {
                                            $img_name = !empty($row['image']) ? $row['image'] : 'default.png';
                                        }
                                        $file_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
                                        
                                        if(($file_ext === 'pdf' || $row['category_id'] == 7) && $row['name'] !== 'Electric Guitar Solo Pack'): 
                                    ?>
                                        <i class="bi bi-file-earmark-music-fill text-dark" style="font-size: 3rem;"></i>
                                    <?php else: ?>
                                        <img src="assets/uploads/products/<?php echo $img_name; ?>" 
                                             class="product-img" 
                                             alt="Product"
                                             onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'bi bi-file-earmark-music-fill text-dark\' style=\'font-size: 3rem;\'></i>';">
                                    <?php endif; ?>
                                </div>
                                <h4 class="h6 fw-bold mb-2 text-truncate" title="<?php echo htmlspecialchars($row['name']); ?>"><?php echo htmlspecialchars($row['name']); ?></h4>
                                <div class="text-warning fw-bold h5 mb-3">£<?php echo number_format($row['price'], 2); ?></div>
                                <div class="mt-auto">
                                    <a href="product_view.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-light w-100 border-secondary" style="border-radius: 10px; font-size: 0.75rem; font-weight: 600;">VIEW DETAILS</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search-heart opacity-25 mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No products found in this selection.</p>
                        <a href="shop.php" class="btn btn-warning rounded-pill px-4">Reset Shop</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    const priceRange = document.getElementById('priceRange');
    const priceVal = document.getElementById('priceVal');
    if(priceRange) {
        priceRange.addEventListener('input', function() {
            priceVal.textContent = Number(this.value).toLocaleString();
        });
    }
</script>

</body>
</html>