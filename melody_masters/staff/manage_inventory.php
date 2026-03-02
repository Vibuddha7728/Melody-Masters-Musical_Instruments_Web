<?php
session_start();
include '../includes/db_config.php';

// Staff හෝ Admin පමණක් ඇතුළු වීමට ඉඩ දීම
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'staff' && $_SESSION['role'] != 'admin')) {
    header("Location: ../index.php");
    exit();
}

$message = "";

if (isset($_POST['update_stock'])) {
    $id = intval($_POST['p_id']);
    $new_stock = intval($_POST['qty']);

    $check = $conn->query("SELECT id FROM products WHERE id = $id");
    
    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_stock, $id);
        
        if ($stmt->execute()) {
            $message = "Swal.fire({
                icon: 'success',
                title: 'Stock Updated',
                text: 'Product #$id inventory successfully synchronized.',
                background: '#111',
                color: '#fff',
                confirmButtonColor: '#ffc107'
            });";
        } else {
            $message = "Swal.fire({ icon: 'error', title: 'Error', text: 'Database update failed.' });";
        }
    } else {
        $message = "Swal.fire({ icon: 'warning', title: 'Invalid ID', text: 'Product ID not found.' });";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Inventory | Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #ffc107;
            --glass: rgba(20, 20, 20, 0.9);
            --border: rgba(255, 255, 255, 0.15);
        }

        body {
            background: linear-gradient(rgba(0,0,0,0.85), rgba(0,0,0,0.85)), 
                        url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .navbar-custom {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--border);
            padding: 15px 0;
        }

        .glass-panel {
            background: var(--glass);
            backdrop-filter: blur(25px);
            border: 1px solid var(--border);
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.6);
        }

        .section-title {
            font-weight: 800;
            letter-spacing: -1px;
            font-size: 1.75rem;
            margin-bottom: 5px;
            color: #fff;
        }

        .form-label-elite {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--primary);
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: block;
        }

        /* Fixed Placeholder visibility */
        .input-elite {
            background: rgba(255,255,255,0.07);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: #fff !important;
            padding: 12px 15px;
            transition: 0.3s;
        }

        .input-elite::placeholder {
            color: rgba(255,255,255,0.4) !important;
        }

        .input-elite:focus {
            background: rgba(255,255,255,0.12);
            border-color: var(--primary);
            box-shadow: none;
            color: #fff;
        }

        .btn-update-elite {
            background: var(--primary);
            color: #000;
            font-weight: 800;
            border-radius: 12px;
            padding: 14px;
            border: none;
            width: 100%;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        .btn-update-elite:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(255, 193, 7, 0.4);
        }

        /* Table Design Fixes */
        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border-radius: 15px;
            margin-top: 10px;
        }

        .table { 
            color: #fff !important; 
            border-collapse: separate; 
            border-spacing: 0 10px; 
            background: transparent !important;
        }

        .table thead th {
            background: rgba(255,255,255,0.05) !important;
            border: none;
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 15px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        /* Fixed the white row issue */
        .table tbody tr {
            background: rgba(255,255,255,0.03) !important;
            transition: 0.3s;
        }

        .table tbody tr td {
            background: transparent !important;
            color: #fff !important;
            padding: 20px 15px;
            border: none;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(255,255,255,0.08) !important;
            transform: translateY(-2px);
        }

        .badge-stock {
            padding: 8px 14px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .search-bar {
            background: rgba(255,255,255,0.08);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px 20px;
            color: #fff;
            width: 100%;
            transition: 0.3s;
        }

        .search-bar::placeholder { color: rgba(255,255,255,0.3); }

        .search-bar:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255,255,255,0.12);
        }

        .unit-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
        }

        .tiny-text {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-custom mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="#">
            <i class="bi bi-box-seam text-warning me-2"></i>MELODY MASTERS <span class="text-warning">STOCK</span>
        </a>
        <a href="staff_dashboard.php" class="btn btn-sm btn-outline-warning rounded-pill px-4 fw-bold">
            <i class="bi bi-arrow-left"></i> EXIT TO DASHBOARD
        </a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-panel h-100">
                <h3 class="section-title">UPDATE<span class="text-warning">.</span></h3>
                <p class="tiny-text mb-4">Modify warehouse stock levels instantly.</p>
                
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label-elite">Product Identifier (ID)</label>
                        <input type="number" name="p_id" class="form-control input-elite" required placeholder="Ex: 001, 002">
                    </div>
                    <div class="mb-4">
                        <label class="form-label-elite">New Stock Quantity</label>
                        <input type="number" name="qty" class="form-control input-elite" required placeholder="Enter units count">
                    </div>
                    <button type="submit" name="update_stock" class="btn-update-elite shadow-sm">
                        <i class="bi bi-arrow-repeat me-2"></i> Sync Inventory
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-panel">
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <h3 class="section-title">INVENTORY<span class="text-warning">.</span></h3>
                        <p class="tiny-text mb-0">Live tracking of available products.</p>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <input type="text" id="tableSearch" class="search-bar" placeholder="Search by name or ID...">
                    </div>
                </div>

                <div class="table-container">
                    <table class="table" id="inventoryTable">
                        <thead>
                            <tr>
                                <th width="15%">REF ID</th>
                                <th width="45%">PRODUCT DESCRIPTION</th>
                                <th width="20%">STOCK LEVEL</th>
                                <th width="20%">UNIT PRICE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM products ORDER BY id ASC");
                            if ($result && $result->num_rows > 0):
                                while($row = $result->fetch_assoc()):
                                    $low_stock = $row['stock'] < 10;
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-warning">#<?php echo str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-white"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div class="tiny-text text-uppercase">Premium Collection</div>
                                </td>
                                <td>
                                    <span class="badge-stock <?php echo $low_stock ? 'bg-danger bg-opacity-75 text-white' : 'bg-success bg-opacity-75 text-white'; ?>">
                                        <i class="bi <?php echo $low_stock ? 'bi-exclamation-triangle' : 'bi-check-circle'; ?>"></i>
                                        <?php echo $row['stock']; ?> UNITS
                                    </span>
                                </td>
                                <td>
                                    <span class="unit-price">£<?php echo number_format($row['price'], 2); ?></span>
                                </td>
                            </tr>
                            <?php endwhile; 
                            else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No products found in database.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live Search Functionality
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll("#inventoryTable tbody tr");
        rows.forEach(row => {
            if(row.innerText.trim() !== "No products found in database.") {
                row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
            }
        });
    });

    // Handle SweetAlert Messages
    <?php if($message) echo $message; ?>
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>