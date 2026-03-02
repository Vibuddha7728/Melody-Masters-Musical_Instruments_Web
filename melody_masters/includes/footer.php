<footer class="footer-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <h4 class="footer-brand text-warning fw-bold mb-4">MELODY MASTERS</h4>
                <div class="contact-info">
                    <p><i class="bi bi-geo-alt-fill text-warning me-2"></i> No 123, Music Lane, Colombo 07.</p>
                    <p><i class="bi bi-envelope-fill text-warning me-2"></i> support@melodymasters.lk</p>
                    <p><i class="bi bi-telephone-fill text-warning me-2"></i> +94 11 234 5678</p>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">SERVICES</h5>
                <ul class="footer-links">
                    <li><a href="#">Home Delivery</a></li>
                    <li><a href="#">Instrument Tuning</a></li>
                    <li><a href="#">Easy Returns</a></li>
                    <li><a href="#">Warranty Claims</a></li>
                    <li><a href="#">Order Tracking</a></li>
                    <li><a href="#">Seasonal Offers</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">SHOP</h5>
                <ul class="footer-links">
                    <li><a href="#">Guitars</a></li>
                    <li><a href="#">Keyboards</a></li>
                    <li><a href="#">Drums & Percussion</a></li>
                    <li><a href="#">Recording Gear</a></li>
                    <li><a href="#">Accessories</a></li>
                    <li><a href="#">New Arrivals</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6">
                <h5 class="footer-title">SUPPORT</h5>
                <ul class="footer-links">
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Term of Service</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="footer-map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.798511765022!2d79.8519!3d6.9271!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwNTUnMzcuNiJOIDc5wrA1MScwNi44IkU!5e0!3m2!1sen!2slk!4v1625000000000!5m2!1sen!2slk" 
                        width="100%" height="180" style="border:0; border-radius: 8px;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <hr class="footer-divider mt-5 mb-4">

        <div class="row align-items-center pb-4">
            <div class="col-md-6 text-center text-md-start">
                <p class="copyright-text mb-0">Copyright 2026 | Designed by <strong>Melody Masters Dev Team</strong></p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="social-icons">
                    <a href="#" class="social-btn facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-btn instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-btn twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="social-btn youtube"><i class="bi bi-youtube"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Footer වර්ණ පද්ධතිය Global Variables වලට අනුව සකස් කිරීම */
    .footer-section {
        background-color: var(--main-bg) !important;
        color: var(--main-text) !important;
        padding-top: 80px;
        border-top: 1px solid var(--border-line);
        transition: 0.4s ease;
    }

    .footer-title {
        color: var(--accent-gold); /* Header එකේ gold color එකට සමාන කළා */
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 25px;
        letter-spacing: 1px;
    }

    .contact-info p {
        font-size: 0.9rem;
        margin-bottom: 12px;
        opacity: 0.9;
    }

    .footer-links {
        list-style: none;
        padding: 0;
    }

    .footer-links li {
        margin-bottom: 12px;
    }

    .footer-links a {
        color: var(--main-text);
        opacity: 0.7;
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.3s ease;
    }

    .footer-links a:hover {
        color: var(--accent-gold) !important;
        opacity: 1;
        padding-left: 8px;
    }

    .footer-divider {
        border-color: var(--border-line);
        opacity: 0.5;
    }

    .copyright-text {
        font-size: 0.85rem;
        opacity: 0.8;
    }

    /* Social Icons Styles */
    .social-icons .social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: var(--input-glass);
        color: var(--main-text);
        border-radius: 4px;
        margin-left: 10px;
        text-decoration: none;
        transition: 0.3s;
    }

    .social-icons .social-btn:hover {
        background: var(--accent-gold);
        color: #000 !important;
        transform: translateY(-3px);
    }

    /* Map Filter - Dark/Light අනුව මාරු වේ */
    .footer-map iframe {
        filter: grayscale(0.5) contrast(1.1);
        transition: 0.4s;
    }

    /* Dark mode එකේදී මැප් එක කරුවල කිරීමට */
    body:not(.light-mode) .footer-map iframe {
        filter: grayscale(1) invert(0.9) contrast(1.2);
    }
</style>