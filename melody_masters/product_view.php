<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_config.php';
include 'includes/header.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // Digital product ද යන්න පරීක්ෂා කිරීමට left join එකක් එක් කරන ලදී
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name, dp.product_id AS is_digital FROM categories c 
                            JOIN products p ON p.category_id = c.id 
                            LEFT JOIN digital_products dp ON p.id = dp.product_id
                            WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $p = $result->fetch_assoc();

    if (!$p) {
        echo "<div class='container mt-5 pt-5 mb-5'><div class='alert alert-warning text-center fw-bold'>Product not found!</div></div>";
        include 'includes/footer.php';
        exit();
    }
} else {
    header("Location: shop.php");
    exit();
}

$is_digital_item = ($p['is_digital'] || $p['category_id'] == 7);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($p['name']); ?> | Premium Master</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --accent: #ffc107;
            --accent-glow: rgba(255, 193, 7, 0.4);
            --dark-bg: #050505;
            --surface: #0f0f0f;
            --text-main: 'Plus Jakarta Sans', sans-serif;
        }

        body, html {
            min-height: 100vh;
            margin: 0;
            background-color: var(--dark-bg);
            color: #ffffff;
            font-family: var(--text-main);
            overflow-x: hidden;
        }

        .details-wrapper {
            padding: 120px 5% 50px 5%;
        }

        /* --- Image Gallery --- */
        .image-display-area {
            position: relative;
            width: 100%;
            max-width: 550px;
            height: 450px;
            margin: 0 auto;
            overflow: hidden; 
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #mainProductImg {
            width: 100%;
            height: 100%;
            display: block;
            transition: transform 0.4s ease-out, opacity 0.3s ease;
            cursor: zoom-in;
            object-fit: cover;
        }

        .digital-icon-placeholder {
            font-size: 120px;
            color: #222;
        }

        .thumb-nav {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            justify-content: center;
        }

        .thumb-card {
            width: 65px; height: 65px;
            border-radius: 12px;
            cursor: pointer;
            overflow: hidden;
            border: 2px solid transparent;
            transition: 0.3s;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thumb-card img { width: 100%; height: 100%; object-fit: cover; opacity: 0.5; }
        .thumb-card.active { border-color: var(--accent); box-shadow: 0 0 10px var(--accent-glow); }
        .thumb-card.active img { opacity: 1; }
        .thumb-card i { font-size: 30px; color: #222; opacity: 0.5; }
        .thumb-card.active i { opacity: 1; }

        /* --- Content Panel --- */
        .product-info-panel { padding-left: 2.5rem; }
        
        .category-tag {
            color: var(--accent);
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 8px;
            display: block;
        }

        .product-title {
            font-size: 3.2rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 15px;
            letter-spacing: -1.5px;
        }

        .price-tag {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 20px;
        }

        .product-description {
            font-size: 1rem;
            line-height: 1.7;
            color: #bbb;
            margin-bottom: 30px;
            border-left: 3px solid var(--accent);
            padding-left: 20px;
        }

        .option-group { margin-bottom: 30px; }
        .option-title { font-size: 0.75rem; font-weight: 800; color: #666; text-transform: uppercase; margin-bottom: 12px; display: block; }
        
        .color-dots { display: flex; gap: 15px; }
        .color-circle {
            width: 34px; height: 34px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid #1a1a1a;
            transition: 0.3s;
        }
        .color-circle.active { border-color: #fff; transform: scale(1.15); box-shadow: 0 0 15px var(--accent-glow); }

        .action-row { display: flex; gap: 15px; margin-bottom: 35px; }
        .qty-input {
            width: 75px; height: 55px;
            background: #111; border: 1px solid #333;
            border-radius: 14px; color: white; text-align: center;
        }

        .btn-add-cart {
            flex-grow: 1;
            background: #fff; color: #000;
            border: none; border-radius: 14px;
            font-weight: 800; text-transform: uppercase;
            transition: 0.3s;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-add-cart:hover { background: var(--accent); transform: translateY(-2px); box-shadow: 0 10px 20px var(--accent-glow); }

        .trust-box {
            display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
        }
        .trust-tag {
            background: #0a0a0a; padding: 12px; border-radius: 12px;
            display: flex; align-items: center; gap: 10px; font-size: 0.8rem;
            border: 1px solid #151515; color: #888;
        }
        .trust-tag i { color: var(--accent); }

        @media (max-width: 991px) {
            .product-info-panel { padding-left: 0; margin-top: 40px; }
            .product-title { font-size: 2.5rem; }
        }
    </style>
</head>
<body>

<div class="details-wrapper">
    <div class="container">
        <div class="row align-items-start">
            
            <div class="col-lg-6 sticky-lg-top" style="top: 100px;">
                <div class="image-display-area" id="zoomContainer">
                    <?php 
                        if ($p['name'] == 'Electric Guitar Solo Pack') {
                            $img_path = "assets/uploads/products/digital_default.png";
                            $file_ext = "png";
                        } else {
                            $img_path = "assets/uploads/products/" . $p['image'];
                            $file_ext = strtolower(pathinfo($p['image'], PATHINFO_EXTENSION));
                        }
                        
                        if(($file_ext === 'pdf' || $is_digital_item) && $p['name'] !== 'Electric Guitar Solo Pack'): 
                    ?>
                        <i class="bi bi-file-earmark-music-fill digital-icon-placeholder" id="mainProductImg"></i>
                    <?php else: ?>
                        <img src="<?php echo $img_path; ?>" id="mainProductImg" onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'bi bi-file-earmark-music-fill digital-icon-placeholder\' id=\'mainProductImg\'></i>';">
                    <?php endif; ?>
                </div>
                
                <div class="thumb-nav">
                    <?php 
                        $dir = "assets/uploads/products/";
                        $main_img_name = $p['image'];
                        $info = pathinfo($main_img_name);
                        $filename = $info['filename'];
                        $ext = $info['extension'];

                        // 1. Main Image Thumbnail
                    ?>
                    <div class="thumb-card active" onclick="changeImage('<?php echo $dir . $main_img_name; ?>', this, '<?php echo $ext; ?>')">
                        <?php if(($ext === 'pdf' || $is_digital_item) && $p['name'] !== 'Electric Guitar Solo Pack'): ?>
                            <i class="bi bi-file-earmark-music-fill"></i>
                        <?php else: ?>
                            <img src="<?php echo $dir . $main_img_name; ?>">
                        <?php endif; ?>
                    </div>

                    <?php
                        // 2. Extra Images Check (_2, _3, _4)
                        for ($i = 2; $i <= 4; $i++) {
                            $extra_img_name = $filename . "_" . $i . "." . $ext;
                            $extra_img_path = $dir . $extra_img_name;
                            if (file_exists($extra_img_path)) {
                                ?>
                                <div class="thumb-card" onclick="changeImage('<?php echo $extra_img_path; ?>', this, '<?php echo $ext; ?>')">
                                    <img src="<?php echo $extra_img_path; ?>">
                                </div>
                                <?php
                            }
                        }
                    ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="product-info-panel">
                    <span class="category-tag"><?php echo htmlspecialchars($p['category_name']); ?></span>
                    <h1 class="product-title"><?php echo htmlspecialchars($p['name']); ?></h1>
                    <div class="price-tag">£<?php echo number_format($p['price'], 2); ?></div>

                    <div class="option-title">Description</div>
                    <div class="product-description">
                        <?php echo !empty($p['description']) ? nl2br(htmlspecialchars($p['description'])) : "Experience premium quality and unparalleled performance with the " . htmlspecialchars($p['name']) . "."; ?>
                    </div>

                    <?php if(!$is_digital_item): ?>
                    <div class="option-group">
                        <span class="option-title">Select Finish</span>
                        <div class="color-dots">
                            <div class="color-circle active" style="background: #000;" onclick="selectColor(this, '<?php echo $img_path; ?>')"></div>
                            <div class="color-circle" style="background: #b22222;" onclick="selectColor(this, 'assets/uploads/products/red.jpg')"></div>
                            <div class="color-circle" style="background: #8B4513;" onclick="selectColor(this, 'assets/uploads/products/brown.jpg')"></div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form action="cart_logic.php" method="POST">
                        <input type="hidden" name="p_id" value="<?php echo $p['id']; ?>">
                        <div class="action-row">
                            <input type="number" name="qty" value="1" min="1" class="qty-input shadow-none">
                            <button type="submit" name="add_to_cart" class="btn-add-cart">
                                <i class="bi bi-bag-plus-fill me-2"></i> Add to Basket
                            </button>
                        </div>
                    </form>

                    <div class="trust-box">
                        <div class="trust-tag"><i class="bi bi-check2-circle"></i> Genuine Certified</div>
                        <div class="trust-tag"><i class="bi bi-shield-lock"></i> 2 Year Warranty</div>
                        <div class="trust-tag"><i class="bi bi-truck"></i> Free Global Shipping</div>
                        <div class="trust-tag"><i class="bi bi-arrow-repeat"></i> 30-Day Returns</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const mainImg = document.getElementById('mainProductImg');
    const zoomContainer = document.getElementById('zoomContainer');

    function changeImage(src, el, ext) {
        const isSoloPack = "<?php echo $p['name']; ?>" === "Electric Guitar Solo Pack";
        const isDigital = (ext === 'pdf' || <?php echo $is_digital_item ? 'true' : 'false'; ?>) && !isSoloPack;
        
        mainImg.style.opacity = '0';
        setTimeout(() => {
            if(isDigital) {
                zoomContainer.innerHTML = `<i class="bi bi-file-earmark-music-fill digital-icon-placeholder" id="mainProductImg"></i>`;
            } else {
                zoomContainer.innerHTML = `<img src="${src}" id="mainProductImg" style="width:100%; height:100%; object-fit:cover;">`;
            }
            const newMainImg = document.getElementById('mainProductImg');
            newMainImg.style.opacity = '1';
        }, 200);

        if(el) {
            document.querySelectorAll('.thumb-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
        }
    }

    function selectColor(btn, src) {
        document.querySelectorAll('.color-circle').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        changeImage(src, null, 'jpg');
    }

    zoomContainer.addEventListener('mousemove', (e) => {
        const target = document.getElementById('mainProductImg');
        if(target && target.tagName === 'IMG') {
            const { left, top, width, height } = zoomContainer.getBoundingClientRect();
            const x = ((e.pageX - left) / width) * 100;
            const y = ((e.pageY - (top + window.scrollY)) / height) * 100;
            
            target.style.transformOrigin = `${x}% ${y}%`;
            target.style.transform = "scale(1.5)"; 
        }
    });

    zoomContainer.addEventListener('mouseleave', () => {
        const target = document.getElementById('mainProductImg');
        if(target && target.tagName === 'IMG') {
            target.style.transform = "scale(1)";
        }
    });
</script>

</body>
</html>