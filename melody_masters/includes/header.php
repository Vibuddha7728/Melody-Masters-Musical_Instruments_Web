<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (file_exists('includes/db_config.php')) {
    include 'includes/db_config.php'; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Outfit:wght@400;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* --- 1. Global Theme Variables --- */
        :root {
            --main-bg: #0b0b0b;
            --main-text: #ffffff;
            --nav-bg-glass: rgba(0, 0, 0, 0.3);
            --modal-glass: rgba(20, 20, 20, 0.85);
            --input-glass: rgba(255, 255, 255, 0.07);
            --border-line: rgba(255, 193, 7, 0.1);
            --accent-gold: #ffc107;
        }

        body.light-mode {
            --main-bg: #ffffff;
            --main-text: #121212;
            --nav-bg-glass: rgba(255, 255, 255, 0.85);
            --modal-glass: rgba(245, 245, 245, 0.98);
            --input-glass: rgba(0, 0, 0, 0.05);
            --border-line: rgba(0, 0, 0, 0.1);
        }

        /* --- 2. Base Styles --- */
        body {
            background-color: var(--main-bg) !important;
            color: var(--main-text) !important;
            font-family: 'Inter', sans-serif;
            margin: 0; padding: 0;
            transition: background 0.4s ease, color 0.4s ease;
            overflow-x: hidden; /* Horizontal scroll වැලැක්වීමට */
        }

        /* --- 3. Navbar Styles --- */
        .navbar { transition: all 0.4s ease; padding: 15px 0; width: 100%; z-index: 1000; }
        .navbar-transparent { background: var(--nav-bg-glass) !important; backdrop-filter: blur(10px); border-bottom: 1px solid var(--border-line); }
        .navbar-scrolled { background: var(--nav-bg-glass) !important; backdrop-filter: blur(15px); padding: 10px 0; border-bottom: 1px solid var(--border-line); }
        
        .nav-link { color: var(--main-text) !important; margin: 0 10px; transition: 0.3s; font-weight: 500; font-family: 'Outfit', sans-serif; }
        .nav-link:hover, .nav-link.active { color: var(--accent-gold) !important; }
        .navbar-brand { font-family: 'Outfit', sans-serif; letter-spacing: 1px; color: var(--accent-gold) !important; }

        /* --- 4. Theme Switcher Button --- */
        .theme-switch-wrapper { display: flex; align-items: center; margin-right: 20px; }
        .switch { position: relative; display: inline-block; width: 45px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 16px; width: 16px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: var(--accent-gold); }
        input:checked + .slider:before { transform: translateX(23px); }

        /* --- 5. Modals & Inputs --- */
        .modal-content.glass-modal {
            background: var(--modal-glass) !important;
            backdrop-filter: blur(25px) saturate(180%) !important;
            border: 1px solid var(--border-line) !important;
            border-radius: 25px;
            color: var(--main-text);
        }
        .glass-input {
            background: var(--input-glass) !important;
            border: 1px solid var(--border-line) !important;
            color: var(--main-text) !important;
            border-radius: 12px !important;
            padding: 12px 15px !important;
        }
        .btn-theme-gradient { 
            background: linear-gradient(45deg, #ffc107, #ff8f00); 
            border: none; color: #000; font-weight: 700; border-radius: 12px; padding: 12px;
            text-transform: uppercase; transition: 0.3s;
        }

        /* =============================================
        MOBILE & TABLET RESPONSIVENESS
        ============================================= */

        /* Mobile Menu (Hamburger menu) Styling */
        @media (max-width: 991px) {
            .navbar-collapse {
                background: var(--modal-glass);
                backdrop-filter: blur(20px);
                margin-top: 15px;
                padding: 20px;
                border-radius: 20px;
                border: 1px solid var(--border-line);
            }
            .nav-link { margin: 10px 0; text-align: center; }
            .theme-switch-wrapper { margin: 15px 0; justify-content: center; }
            .navbar-nav + .d-flex { flex-direction: column; width: 100%; gap: 10px; }
            .navbar-nav + .d-flex button, 
            .navbar-nav + .d-flex a { width: 100%; }
            .navbar-nav + .d-flex .text-warning { margin-bottom: 10px; text-align: center; display: block; }
        }

        /* Small Screen Modal adjustments */
        @media (max-width: 576px) {
            .navbar-brand { font-size: 1.4rem !important; }
            .modal-dialog { margin: 15px; }
            .glass-modal { border-radius: 20px; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-transparent" id="mainNav">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php">MELODY MASTERS</a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="customer/my_account.php">My Account</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Cart <i class="bi bi-cart3"></i></a></li>
                <li class="nav-item"><a class="nav-link" href="about_us.php">About Us</a></li>
            </ul>
            <div class="d-flex align-items-center">
                
                <div class="theme-switch-wrapper">
                    <i id="mode-icon" class="bi bi-moon-stars-fill text-warning me-2"></i>
                    <label class="switch">
                        <input type="checkbox" id="theme-toggle">
                        <span class="slider"></span>
                    </label>
                </div>

                <?php if(!isset($_SESSION['user_id'])): ?>
                    <button class="btn btn-outline-warning btn-sm me-lg-2 rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
                    <button class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
                <?php else: ?>
                    <span class="text-warning fw-medium me-lg-3">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h4 class="fw-bold text-warning m-0">Welcome Back</h4>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="auth.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-warning fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control glass-input" required placeholder="Enter your email">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-warning fw-bold">Password</label>
                        <input type="password" name="password" class="form-control glass-input" required placeholder="Enter password">
                    </div>
                    <button type="submit" name="login" class="btn btn-theme-gradient w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <div class="modal-header border-0">
                <h4 class="fw-bold text-warning m-0">Create Account</h4>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="auth.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label small text-warning fw-bold">Username</label>
                        <input type="text" name="username" class="form-control glass-input" required placeholder="Choose a username">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-warning fw-bold">Email Address</label>
                        <input type="email" name="email" class="form-control glass-input" required placeholder="Enter your email">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small text-warning fw-bold">Password</label>
                        <input type="password" name="password" class="form-control glass-input" required placeholder="Create a password">
                    </div>
                    <button type="submit" name="register" class="btn btn-theme-gradient w-100">Register Now</button>
                    <p class="text-center mt-3 small opacity-75">Already have an account? <a href="#" class="text-warning" data-bs-target="#loginModal" data-bs-toggle="modal">Login here</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Navbar Scroll Logic
    window.addEventListener('scroll', function () {
        const nav = document.getElementById('mainNav');
        if (window.scrollY > 50) {
            nav.classList.add('navbar-scrolled');
        } else {
            nav.classList.remove('navbar-scrolled');
        }
    });

    // 2. Global Theme Switch Logic
    const themeToggle = document.getElementById('theme-toggle');
    const modeIcon = document.getElementById('mode-icon');
    const body = document.body;

    if (localStorage.getItem('theme') === 'light') {
        body.classList.add('light-mode');
        themeToggle.checked = true;
        modeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
    }

    themeToggle.addEventListener('change', () => {
        if (themeToggle.checked) {
            body.classList.add('light-mode');
            localStorage.setItem('theme', 'light');
            modeIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        } else {
            body.classList.remove('light-mode');
            localStorage.setItem('theme', 'dark');
            modeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
        }
    });
</script>