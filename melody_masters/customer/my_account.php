<?php
session_start();
include '../includes/db_config.php';

// පරිශීලකයා login වී ඇත්දැයි බැලීම
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../index.php"); 
    exit(); 
}

$user_id = (int)$_SESSION['user_id']; 
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | Melody Masters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --accent: #ffc107;
            --bg: #050505;
            --surface: rgba(255, 255, 255, 0.03);
            --border: rgba(255, 255, 255, 0.15);
            --card-glow: rgba(255, 193, 7, 0.05);
        }

        body {
            background-color: var(--bg);
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 193, 7, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 193, 7, 0.03) 0%, transparent 40%);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .nav-glass {
            background: rgba(5, 5, 5, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 18px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -1px;
            text-decoration: none;
            color: #fff;
        }

        .welcome-section { padding: 50px 0 30px; }

        .user-badge {
            background: var(--card-glow);
            border: 1px solid rgba(255, 193, 7, 0.2);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--accent);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .main-card {
            background: var(--surface);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.8);
        }

        .card-header-custom {
            padding: 25px 35px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.01);
        }

        .table { color: #fff; margin-bottom: 0; }
        .table thead th {
            background: rgba(255, 255, 255, 0.05);
            color: #fff !important;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 2px;
            font-weight: 700;
            padding: 20px 35px;
            border-bottom: 1px solid var(--border);
        }

        .table tbody td {
            padding: 25px 35px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            background: transparent;
        }

        .order-id-text { 
            color: var(--accent); 
            font-weight: 700; 
            font-size: 1.1rem; 
            cursor: pointer; 
            display: inline-block;
            border-bottom: 1px dashed var(--accent);
            transition: 0.2s;
        }
        .order-id-text:hover { opacity: 0.8; }
        
        .product-names { font-size: 0.85rem; color: #bbb; margin-top: 5px; }

        .status-pill {
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .status-completed { background: rgba(34, 197, 94, 0.2); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.3); }
        .status-pending { background: rgba(255, 193, 7, 0.2); color: #ffc107; border: 1px solid rgba(255, 193, 7, 0.3); }

        .btn-download {
            background: #fff;
            color: #000;
            border-radius: 14px;
            padding: 10px 18px;
            font-weight: 700;
            font-size: 0.85rem;
            transition: 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-download:hover { background: var(--accent); transform: translateY(-2px); color: #000; }

        .btn-logout {
            color: #ff5f5f;
            background: rgba(255, 95, 95, 0.05);
            border: 1px solid rgba(255, 95, 95, 0.1);
            padding: 8px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-logout:hover { background: #ff5f5f; color: #fff; }

        .btn-home {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
        }
        .btn-home:hover { background: rgba(255, 255, 255, 0.15); color: #fff; }

        .modal-content {
            background: #0f0f0f;
            border: 1px solid var(--border);
            color: white;
            border-radius: 24px;
        }
        
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 8px;
        }
        .star-rating input { display: none; }
        .star-rating label {
            font-size: 2rem;
            color: rgba(255,255,255,0.1);
            cursor: pointer;
        }
        .star-rating input:checked ~ label, .star-rating label:hover, .star-rating label:hover ~ label {
            color: var(--accent);
        }

        tr { transition: 0.3s; }
        tr:hover { background: rgba(255, 255, 255, 0.02); }

        /* Modernized Tooltip Styling */
        .tooltip-inner {
            background: #121212 !important;
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #fff !important;
            padding: 15px;
            border-radius: 18px;
            text-align: left;
            box-shadow: 0 15px 30px rgba(0,0,0,0.6);
            min-width: 260px;
            backdrop-filter: blur(10px);
        }
        .tooltip-item-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .tooltip-item-row:last-child { border-bottom: none; }
        .tooltip-prod-name { font-weight: 600; font-size: 13px; color: #efefef; }
        .tooltip-prod-meta { font-size: 11px; color: #888; }
        .tooltip-header { font-size: 10px; font-weight: 800; color: var(--accent); letter-spacing: 1px; margin-bottom: 10px; text-transform: uppercase; }
    </style>
</head>
<body>

<nav class="nav-glass">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../index.php" class="logo-text">MELODY<span class="text-warning">MASTERS</span></a>
        <div class="d-flex gap-2">
            <a href="../index.php" class="btn-home"><i class="bi bi-house-door me-2"></i>Home</a>
            <a href="../logout.php" class="btn-logout"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="welcome-section">
        <div class="user-badge"><i class="bi bi-shield-check me-1"></i> Verified Account</div>
        <h2 class="fw-bold text-white mb-1" style="font-size: 2.8rem; letter-spacing: -1px;">Welcome back, <?php echo $username; ?>!</h2>
        <p class="text-secondary fs-5">Track your orders and access your digital library.</p>
    </div>

    <div class="main-card mb-5">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-stack me-2 text-warning"></i> Order History</h5>
            <span class="badge bg-dark border border-secondary rounded-pill px-3 py-2" style="font-size: 0.7rem;">RECENT ACTIVITY</span>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions & Downloads</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT id, total_amount, status FROM orders WHERE user_id = ? ORDER BY id DESC");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $orders = $stmt->get_result();
                    
                    $modal_html = ""; 

                    if($orders && $orders->num_rows > 0):
                        while($row = $orders->fetch_assoc()):
                            $o_id = (int)$row['id'];
                            $order_status = strtolower(trim($row['status']));
                            $is_completed = ($order_status == 'completed');
                            $order_total = $row['total_amount'];

                            $p_stmt = $conn->prepare("SELECT p.id as prod_id, p.name, oi.quantity, oi.price, dp.file_path FROM order_items oi JOIN products p ON oi.product_id = p.id LEFT JOIN digital_products dp ON p.id = dp.product_id WHERE oi.order_id = ?");
                            $p_stmt->bind_param("i", $o_id);
                            $p_stmt->execute();
                            $p_query = $p_stmt->get_result();
                            
                            $product_names_arr = [];
                            $tooltip_list = "<div class='tooltip-header'>Order Content</div>";
                            $digital_assets = [];
                            $has_digital = false;
                            $first_prod_name = "";
                            $first_prod_id = 0;

                            while($p = $p_query->fetch_assoc()){
                                if(empty($first_prod_name)) { 
                                    $first_prod_name = $p['name']; 
                                    $first_prod_id = $p['prod_id'];
                                }
                                $product_names_arr[] = htmlspecialchars($p['name']);
                                
                                // Updated Tooltip HTML for better clarity
                                $tooltip_list .= "<div class='tooltip-item-row'>";
                                $tooltip_list .= "<div><div class='tooltip-prod-name'>" . htmlspecialchars($p['name']) . "</div>";
                                $tooltip_list .= "<div class='tooltip-prod-meta'>Quantity: " . $p['quantity'] . "</div></div>";
                                $tooltip_list .= "<div class='fw-bold text-warning small'>£" . number_format($p['price'], 2) . "</div>";
                                $tooltip_list .= "</div>";
                                
                                if(!empty($p['file_path'])) {
                                    $has_digital = true;
                                    $digital_assets[] = ['name' => $p['name'], 'file' => $p['file_path']];
                                }
                            }
                            $display_names = implode(", ", $product_names_arr);

                            if($is_completed) {
                                $modal_html .= '
                                <div class="modal fade" id="revModal'.$o_id.'" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-800">Write a Review</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="submit_review.php" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="order_id" value="'.$o_id.'">
                                                    <input type="hidden" name="product_id" value="'.$first_prod_id.'">
                                                    <div class="mb-4 text-center">
                                                        <p class="mb-2 text-secondary">How was your experience with</p>
                                                        <h6 class="fw-bold text-warning">'.$first_prod_name.'</h6>
                                                    </div>
                                                    <div class="mb-4">
                                                        <div class="star-rating justify-content-center">
                                                            <input type="radio" name="rating" id="star5-'.$o_id.'" value="5" required/><label for="star5-'.$o_id.'" class="bi bi-star-fill"></label>
                                                            <input type="radio" name="rating" id="star4-'.$o_id.'" value="4"/><label for="star4-'.$o_id.'" class="bi bi-star-fill"></label>
                                                            <input type="radio" name="rating" id="star3-'.$o_id.'" value="3"/><label for="star3-'.$o_id.'" class="bi bi-star-fill"></label>
                                                            <input type="radio" name="rating" id="star2-'.$o_id.'" value="2"/><label for="star2-'.$o_id.'" class="bi bi-star-fill"></label>
                                                            <input type="radio" name="rating" id="star1-'.$o_id.'" value="1"/><label for="star1-'.$o_id.'" class="bi bi-star-fill"></label>
                                                        </div>
                                                    </div>
                                                    <div class="mb-0">
                                                        <textarea name="comment" class="form-control" rows="4" placeholder="Share your thoughts..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="submit_review" class="btn btn-warning px-4 py-2 rounded-3 fw-800">SUBMIT REVIEW</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>';
                            }
                    ?>
                    <tr>
                        <td>
                            <div class="order-id-text" 
                                 data-bs-toggle="tooltip" 
                                 data-bs-html="true" 
                                 data-bs-placement="right"
                                 title="<?php echo htmlspecialchars($tooltip_list); ?>">
                                #ORD-<?php echo $o_id; ?>
                            </div>
                            <div class="product-names text-truncate" style="max-width: 250px;">
                                <i class="bi bi-music-note-beamed me-1"></i> <?php echo $display_names; ?>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold fs-5 text-white">
                                £<?php echo number_format($order_total, 2); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-pill <?php echo $is_completed ? 'status-completed' : 'status-pending'; ?>">
                                <i class="bi <?php echo $is_completed ? 'bi-patch-check-fill' : 'bi-hourglass-split'; ?> me-1"></i>
                                <?php echo strtoupper(htmlspecialchars($row['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if($has_digital && $is_completed): 
                                    foreach($digital_assets as $asset): 
                                        $file_url = "../assets/uploads/products/" . htmlspecialchars($asset['file']);
                                ?>
                                    <a href="<?php echo $file_url; ?>" download class="btn-download">
                                        <i class="bi bi-download"></i> Get File
                                    </a>
                                <?php endforeach; 
                                elseif($has_digital && !$is_completed): ?>
                                    <span class="text-secondary small d-flex align-items-center"><i class="bi bi-lock-fill me-1 text-warning"></i> Unlocks after payment</span>
                                <?php endif; ?>

                                <?php if($is_completed): ?>
                                    <button type="button" class="btn btn-outline-warning rounded-3 fw-bold px-3" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#revModal<?php echo $o_id; ?>">
                                        Review
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="4" class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-secondary mb-3 d-block"></i>
                        <p class="text-secondary">No orders found.</p>
                        <a href="../shop.php" class="btn btn-warning fw-bold px-4">Browse Collection</a>
                    </td></tr>
                    <?php endif; $stmt->close(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $modal_html; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Tooltips Initialize කිරීම
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>
</body>
</html>