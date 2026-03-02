<?php 
include 'includes/header.php'; 
include 'includes/db_config.php'; 
include 'ai_widget.php';
?>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    /* --- Base Styles --- */
    body { background-color: #000; overflow-x: hidden; width: 100%; }

    /* --- Chatbot Fixed & Floating Animation --- */
    #ai-widget-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        animation: float-bot 3s ease-in-out infinite;
    }

    @keyframes float-bot {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    #ai-widget-container:hover {
        animation-play-state: paused;
    }

    /* --- Hero Section --- */
    .hero-bg {
        background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.9)), 
                          url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=2070');
        height: 100vh; background-position: center; background-size: cover;
        display: flex; align-items: center; justify-content: center; text-align: center;
        position: relative;
    }

    /* --- Mouse Icon --- */
    .mouse-container { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); z-index: 10; }
    .mouse {
        width: 25px; height: 45px; border: 2px solid #555; border-radius: 20px;
        display: flex; justify-content: center; padding-top: 8px; position: relative;
        animation: border-glow 2s linear infinite;
    }
    @keyframes border-glow {
        0%, 100% { border-color: #555; box-shadow: 0 0 5px rgba(85, 85, 85, 0.2); }
        50% { border-color: #ffc107; box-shadow: 0 0 15px rgba(255, 193, 7, 0.6); }
    }
    .mouse-wheel {
        width: 4px; height: 8px; background: #ffc107; border-radius: 2px;
        animation: scroll-wheel 1.5s infinite;
    }
    @keyframes scroll-wheel { 0% { opacity: 1; transform: translateY(0); } 100% { opacity: 0; transform: translateY(15px); } }

    /* --- Features Bar --- */
    .features-bar { background-color: #080808; padding: 60px 0; border-bottom: 1px solid #111; }
    .feature-item i { font-size: 2.5rem; color: #ffc107; margin-bottom: 15px; display: block; transition: 0.3s; }
    .feature-item:hover i { transform: scale(1.2); text-shadow: 0 0 15px rgba(255,193,7,0.5); }

    /* --- Section Divider --- */
    .section-divider { display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    .divider-line { height: 1px; width: 100px; background: linear-gradient(90deg, transparent, #ffc107, transparent); opacity: 0.8; }
    .divider-text { color: #ffc107; font-weight: 700; font-size: 0.8rem; letter-spacing: 2px; margin: 0 15px; text-transform: uppercase; }

    /* --- Headings --- */
    .main-heading { color: #ffffff; font-weight: 800; font-size: 1.5rem; margin-bottom: 10px; letter-spacing: 1px; }
    .main-heading span { color: #ffc107; }

    /* --- Product Cards --- */
    .section-padding { padding: 100px 0 60px 0; } 
    .product-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px; overflow: hidden; transition: 0.5s cubic-bezier(0.4, 0, 0.2, 1); height: 100%;
        backdrop-filter: blur(5px);
    }
    .product-card:hover { transform: translateY(-15px); border-color: rgba(255, 193, 7, 0.5); }
    .product-img-container { height: 250px; background: #fff; display: flex; align-items: center; justify-content: center; padding: 30px; position: relative; }
    .product-img { max-width: 100%; max-height: 100%; object-fit: contain; transition: 0.5s; }

    /* --- Testimonials --- */
    .testimonials-section { padding: 100px 0; background: #000; }
    .review-card {
        background: rgba(15, 15, 15, 0.6); 
        backdrop-filter: blur(15px);
        border-radius: 24px; padding: 35px;
        border: 1px solid rgba(255, 193, 7, 0.15); 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        height: 100%; display: flex; flex-direction: column; position: relative;
    }
    
    .review-card:hover {
        transform: translateY(-10px) scale(1.02);
        border-color: #ffc107;
        box-shadow: 0 10px 30px rgba(255, 193, 7, 0.1);
        background: rgba(20, 20, 20, 0.8);
    }

    .quote-icon { position: absolute; top: 20px; right: 30px; font-size: 2.5rem; color: rgba(255, 193, 7, 0.1); transition: 0.3s; }
    .review-card:hover .quote-icon { color: rgba(255, 193, 7, 0.3); transform: rotate(-15deg); }

    .review-text { color: #d1d1d1; font-size: 1rem; line-height: 1.6; margin-bottom: 30px; font-style: italic; }
    .user-info { display: flex; align-items: center; gap: 15px; margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.05); }
    .user-avatar { width: 55px; height: 55px; border-radius: 16px; background: #222; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255, 193, 7, 0.3); transition: 0.3s; }
    .review-card:hover .user-avatar { border-color: #ffc107; background: #2a2a2a; }
    .user-avatar-initial { color: #ffc107; font-weight: 800; }
    
    .star-rating { color: #ffc107; font-size: 1.1rem; }

    /* --- Promo Section --- */
    .promo-section-padding { padding: 40px 0 100px 0; } 
    .promo-box { position: relative; height: 450px; overflow: hidden; border-radius: 20px; cursor: pointer; }
    .promo-box img { width: 100%; height: 100%; object-fit: cover; transition: 1.5s ease; }
    .promo-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px; }

    /* RESPONSIVENESS */
    @media (max-width: 991px) {
        .hero-bg h1 { font-size: 4rem !important; }
        .section-padding { padding: 80px 0 40px 0; }
        .promo-box { height: 350px; }
        .feature-item i { font-size: 2rem; }
    }

    @media (max-width: 767px) {
        .hero-bg h1 { font-size: 2.8rem !important; }
        .hero-bg p { font-size: 1.2rem !important; }
        .main-heading { font-size: 1.3rem; }
        .divider-line { width: 50px; }
        .features-bar { padding: 40px 0; }
        .feature-item h6 { font-size: 0.9rem; }
        .product-img-container { height: 200px; padding: 15px; }
        .testimonials-section { padding: 60px 0; }
        .review-card { padding: 25px; margin-bottom: 10px; }
        .review-text { font-size: 0.9rem; margin-bottom: 20px; }
        .promo-box { height: 300px; margin-bottom: 20px; }
        .promo-box h3 { font-size: 1.4rem; }
    }

    @media (max-width: 480px) {
        .hero-bg h1 { font-size: 2.2rem !important; }
        .user-info { flex-direction: column; align-items: flex-start; gap: 10px; }
    }
</style>

<div class="hero-bg">
    <div class="container" data-aos="zoom-out">
        <h1 class="display-1 fw-bold mb-3">Feel the <span class="text-warning">Vibe.</span></h1>
        <p class="lead fs-3 mb-5 opacity-75">Your journey to musical excellence starts here.</p>
        <a href="shop.php" class="btn btn-warning btn-lg px-5 py-3 rounded-pill fw-bold shadow text-dark">Shop Now</a>
    </div>
    <a href="#featured" class="mouse-container text-decoration-none">
        <div class="mouse"><div class="mouse-wheel"></div></div>
    </a>
</div>

<section class="features-bar">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="feature-item"><i class="bi bi-truck"></i><h6>Free Shipping</h6><p class="small text-white-50 d-none d-md-block">On order over $100</p></div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-item"><i class="bi bi-shield-check"></i><h6>Quick Payment</h6><p class="small text-white-50 d-none d-md-block">100% Secure</p></div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-item"><i class="bi bi-gift"></i><h6>Gift Certificate</h6><p class="small text-white-50 d-none d-md-block">Buy Now $500+</p></div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-item"><i class="bi bi-headset"></i><h6>24/7 Support</h6><p class="small text-white-50 d-none d-md-block">Customer Support</p></div>
            </div>
        </div>
    </div>
</section>

<div id="featured" class="container section-padding">
    <div class="text-center mb-5" data-aos="fade-up">
        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-text">MELODY MASTERS COLLECTION</div>
            <div class="divider-line"></div>
        </div>
        <h2 class="main-heading">FEATURED <span>INSTRUMENTS</span></h2>
    </div>

    <div class="row g-4">
        <?php
        $query = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $delay = 100;
            while($row = mysqli_fetch_assoc($result)) {
                $imagePath = "assets/uploads/products/" . $row['image'];
                $hasImage = (!empty($row['image']) && file_exists($imagePath));
                ?>
                <div class="col-lg-3 col-md-6 col-12" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                    <div class="product-card text-center">
                        <div class="product-img-container mb-3">
                            <?php if ($hasImage): ?>
                                <img src="<?php echo $imagePath; ?>" class="product-img" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <?php else: ?>
                                <div class="placeholder-icon-wrapper">
                                    <i class="bi bi-music-note-beamed"></i>
                                    <span class="placeholder-label">Instrument</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-3">
                            <h5 class="fw-bold fs-6 mb-2 text-white"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="text-warning fw-bold mb-3 fs-5">$<?php echo number_format($row['price'], 2); ?></p>
                            <a href="product_view.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning btn-sm w-100 rounded-pill py-2">View Details</a>
                        </div>
                    </div>
                </div>
                <?php $delay += 100;
            }
        }
        ?>
    </div>
</div>

<section class="testimonials-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-divider">
                <div class="divider-line"></div>
                <div class="divider-text">MELODY MASTERS FEEDBACK</div>
                <div class="divider-line"></div>
            </div>
            <h2 class="main-heading">CUSTOMER <span>FEEDBACKS</span></h2>
        </div>

        <div class="row g-4">
            <?php
            $rev_query = "SELECT r.*, u.username, p.name as prod_name 
                          FROM reviews r 
                          JOIN users u ON r.user_id = u.id 
                          JOIN products p ON r.product_id = p.id 
                          ORDER BY r.id DESC LIMIT 3";
            $rev_result = mysqli_query($conn, $rev_query);

            if ($rev_result && mysqli_num_rows($rev_result) > 0):
                $delay = 100;
                while($rev = mysqli_fetch_assoc($rev_result)):
            ?>
            <div class="col-lg-4 col-md-6 col-12" data-aos="flip-left" data-aos-delay="<?php echo $delay; ?>">
                <div class="review-card">
                    <span class="quote-icon"><i class="bi bi-quote"></i></span>
                    <p class="review-text">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                    <div class="user-info">
                        <div class="user-avatar">
                            <span class="user-avatar-initial"><?php echo strtoupper(substr($rev['username'], 0, 1)); ?></span>
                        </div>
                        <div class="user-details">
                            <h6 class="user-name mb-0 text-white"><?php echo htmlspecialchars($rev['username']); ?></h6>
                            <span class="star-rating">
                                <?php for($i=1; $i<=5; $i++) echo ($i <= $rev['rating']) ? '★ ' : '☆ '; ?>
                            </span>
                            <small class="prod-bought d-block text-white-50">Purchased: <?php echo htmlspecialchars($rev['prod_name']); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php $delay += 150; endwhile; endif; ?>
        </div>
    </div>
</section>

<div class="container promo-section-padding">
    <div class="row g-4 promo-container">
        <div class="col-md-6 col-12" data-aos="fade-right">
            <div class="promo-box">
                <img src="assets/uploads/products/gitar.png" alt="Guitars">
                <div class="promo-overlay">
                    <h3 class="text-white fw-bold">Acoustic & Electric Guitars</h3>
                    <a href="shop.php" class="btn btn-outline-light rounded-pill px-4 py-2 mt-3">Shop Now</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12" data-aos="fade-left">
            <div class="promo-box">
                <img src="assets/uploads/products/pionos.png" alt="Pianos">
                <div class="promo-overlay">
                    <h3 class="text-white fw-bold">Professional Keyboards</h3>
                    <a href="shop.php" class="btn btn-outline-light rounded-pill px-4 py-2 mt-3">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 1000, once: true });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>