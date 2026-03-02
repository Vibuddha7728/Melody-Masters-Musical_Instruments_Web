<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary: #ffc107;
            --bg: #050505;
            --card-bg: rgba(255, 255, 255, 0.03);
            --text-dim: #b0b0b0;
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--bg);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            line-height: 1.6;
            overflow-x: hidden; /* Horizontal scroll වැලැක්වීමට */
        }

        .about-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 100px 20px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        /* Image Styling */
        .image-container {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .image-container img {
            width: 100%;
            display: block;
            transition: transform 0.5s ease;
        }

        .image-container:hover img {
            transform: scale(1.05);
        }

        /* Text Content Styling */
        .content-header {
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 20px;
            display: block;
        }

        h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 30px;
            letter-spacing: -2px;
        }

        h1 span {
            color: var(--primary);
        }

        .description {
            color: var(--text-dim);
            font-size: 1.05rem;
            margin-bottom: 25px;
        }

        /* Stats Bar */
        .stats-row {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 40px;
            border-top: 1px solid var(--border);
        }

        .stat-item {
            text-align: left;
        }

        .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--primary);
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #666;
            letter-spacing: 1.5px;
        }

        /* =============================================
           RESPONSIVENESS (MOBILE & TABLET)
           ============================================= */

        /* Tablet View (max-width: 992px) */
        @media (max-width: 992px) {
            .about-wrapper {
                padding: 80px 30px;
            }
            .main-grid {
                grid-template-columns: 1fr; /* එක් තීරුවකට හැරවීම */
                gap: 50px;
                text-align: center;
            }
            .image-container {
                max-width: 600px;
                margin: 0 auto; /* පින්තූරය මැදට ගැනීම */
            }
            h1 {
                font-size: 2.8rem;
            }
            .stats-row {
                justify-content: space-around;
            }
        }

        /* Mobile View (max-width: 768px) */
        @media (max-width: 768px) {
            .about-wrapper {
                padding: 60px 20px;
            }
            h1 {
                font-size: 2.2rem;
                letter-spacing: -1px;
            }
            .description {
                font-size: 1rem;
            }
            .stats-row {
                flex-direction: column; /* ස්ටැට්ස් එක යටට හැරවීම */
                align-items: center;
                gap: 30px;
                padding-top: 30px;
            }
            .stat-item {
                text-align: center; /* අකුරු මැදට කිරීම */
            }
            .stat-number {
                font-size: 2.3rem;
            }
        }
    </style>
</head>
<body>

<div class="about-wrapper">
    <div class="main-grid">
        
        <div class="image-container">
            <img src="assets/uploads/products/gitar.png" alt="Melody Masters Professionals">
        </div>

        <div class="about-content">
            
            <h1>The Rhythm of <span>Innovation</span>.</h1>
            
            <div class="description">
                Found by <strong>Mr. Vibuddha Vibodha (CEO)</strong>, Melody Masters is more than just a music store; it’s a premium ecosystem designed for creators. We bring you unique and high-quality musical instruments and digital assets to add a professional touch to your sound!
            </div>

            <div class="description">
                Our aim is to provide a seamless online and offline experience for musicians worldwide. Whether you are looking for physical instruments delivered to your doorstep or digital libraries to expand your sound, Melody Masters ensures security, quality, and speed.
            </div>

            <div class="description" style="font-style: italic; color: #fff; border-left: 3px solid var(--primary); padding-left: 15px;">
                "We don't just sell music products; we empower your creative legacy."
            </div>

            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number">20</span>
                    <span class="stat-label">Employees</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100+</span>
                    <span class="stat-label">Total Products</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">99%</span>
                    <span class="stat-label">Happy Creators</span>
                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>