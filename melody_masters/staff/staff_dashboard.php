<?php
session_start();
include '../includes/db_config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'admin')) {
    header("Location: ../index.php");
    exit();
}

// Assignment Metrics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Pending'")->fetch_assoc()['count'];
$low_stock_count = $conn->query("SELECT COUNT(*) as count FROM products WHERE stock < 5")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Console | Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #ffc107;
            --glass: rgba(10, 10, 10, 0.8);
            --border: rgba(255, 255, 255, 0.15);
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow: hidden; /* Scroll වීම වැළැක්වීමට */
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: #ffffff;
        }

        /* Full Screen Container */
        .viewport-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        /* Glass Cards */
        .glass-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            transition: 0.3s ease-in-out;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .glass-card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        /* Top Navbar Style */
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Stats Section */
        .stat-box {
            text-align: left;
            padding: 15px;
        }

        .stat-icon {
            font-size: 1.5rem;
            color: var(--primary);
            background: rgba(255, 193, 7, 0.1);
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin: 0;
            color: #fff;
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.6);
            font-weight: 700;
        }

        /* Buttons & Links */
        .action-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 25px;
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
            transition: 0.3s;
            height: 100%;
        }

        .action-btn:hover {
            background: rgba(255, 193, 7, 0.1);
            border-color: var(--primary);
            color: var(--primary);
        }

        .action-btn i {
            font-size: 2.5rem;
            margin-right: 20px;
        }

        /* Alerts List */
        .alert-scroll {
            max-height: calc(100vh - 450px);
            overflow-y: auto;
            padding-right: 10px;
        }

        .alert-item {
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-item.critical { border-left-color: #ff4757; background: rgba(255, 71, 87, 0.1); }

        .role-badge {
            background: var(--primary);
            color: #000;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
    </style>
</head>
<body>

<div class="viewport-wrapper container-fluid">
    <header class="header-bar glass-card">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-lock-fill text-warning fs-3 me-2"></i>
            <div>
                <h5 class="fw-bold mb-0">MELODY MASTERS <span class="text-warning">PANEL</span></h5>
                <small class="opacity-50">Operational Control Center</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <div class="small fw-bold"><?php echo $_SESSION['username']; ?></div>
                <span class="role-badge"><?php echo $_SESSION['role']; ?></span>
            </div>
            <a href="../logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-4">Logout</a>
        </div>
    </header>

    <div class="row g-4 flex-grow-1 overflow-hidden">
        <div class="col-lg-8 d-flex flex-column gap-4">
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="glass-card stat-box h-100">
                        <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                        <p class="stat-label">Total Products</p>
                        <h2 class="stat-value"><?php echo $total_products; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card stat-box h-100">
                        <div class="stat-icon" style="color:#00d2d3; background:rgba(0,210,211,0.1);"><i class="bi bi-cart-dash"></i></div>
                        <p class="stat-label">Pending Orders</p>
                        <h2 class="stat-value"><?php echo $pending_orders; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="glass-card stat-box h-100">
                        <div class="stat-icon" style="color:#ff4757; background:rgba(255,71,87,0.1);"><i class="bi bi-lightning-charge"></i></div>
                        <p class="stat-label">Stock Alerts</p>
                        <h2 class="stat-value text-danger"><?php echo $low_stock_count; ?></h2>
                    </div>
                </div>
            </div>

            <div class="glass-card flex-grow-1">
                <h5 class="fw-bold mb-4 text-uppercase small letter-spacing-1">Core Responsibilities</h5>
                <div class="row g-4 h-75">
                    <div class="col-md-6">
                        <a href="manage_inventory.php" class="action-btn">
                            <i class="bi bi-boxes"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Manage Products</h5>
                                <p class="small opacity-50 mb-0">Update stock levels, specifications & images.</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="process_orders.php" class="action-btn">
                            <i class="bi bi-truck"></i>
                            <div>
                                <h5 class="fw-bold mb-1">Process Orders</h5>
                                <p class="small opacity-50 mb-0">Verify shipments and assign tracking numbers.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="glass-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>CRITICAL STOCK</h5>
                    <span class="badge bg-warning text-dark rounded-pill"><?php echo $low_stock_count; ?> Items</span>
                </div>

                <div class="alert-scroll">
                    <?php if ($low_stock_count > 0): ?>
                        <?php
                        $low_stock_query = $conn->query("SELECT name, stock FROM products WHERE stock < 5 ORDER BY stock ASC");
                        while($item = $low_stock_query->fetch_assoc()):
                            $is_out = ($item['stock'] == 0);
                        ?>
                        <div class="alert-item <?php echo $is_out ? 'critical' : ''; ?>">
                            <div>
                                <div class="fw-bold small"><?php echo htmlspecialchars($item['name']); ?></div>
                                <span class="badge <?php echo $is_out ? 'bg-danger' : 'bg-dark'; ?> x-small" style="font-size:0.6rem;">
                                    <?php echo $is_out ? 'OUT OF STOCK' : 'LOW STOCK'; ?>
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="fw-800 <?php echo $is_out ? 'text-danger' : 'text-warning'; ?>"><?php echo $item['stock']; ?></div>
                                <a href="manage_inventory.php" class="text-info x-small text-decoration-none fw-bold" style="font-size:0.7rem;">RESTOCK</a>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5 opacity-25">
                            <i class="bi bi-check2-all display-1"></i>
                            <p>No issues detected</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center py-3 opacity-50 small">
        &copy; 2026 Melody Masters - Advanced Staff Portal [v3.0 - High Performance]
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>