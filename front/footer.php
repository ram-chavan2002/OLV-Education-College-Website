<?php
// college/front/footer.php

// Helper function for active link highlighting
if (!function_exists('olv_active')) {
    function olv_active($file) {
        $current_page = basename($_SERVER['PHP_SELF']);
        return ($current_page === $file) ? 'active' : '';
    }
}
?>

<style>
/* Footer - WHITE LINKS + Perfect Header Match */
:root {
    --olv-gold: #d4a34d;
    --olv-dark: #0a0a0a;
    --olv-footer-bg: #050505;
}

.olv-footer {
    background-color: var(--olv-footer-bg);
    color: #e0e0e0;
    font-family: 'Inter', sans-serif;
    line-height: 1.6;
    margin-top: auto;
    position: relative;
    z-index: 100;
    border-top: 1px solid #1a1a1a;
}

.footer-top {
    padding: 80px 0 50px;
}

.footer-primary {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr;
    gap: 40px;
}

/* Brand Section */
.footer-brand-section {
    max-width: 320px;
}

.olv-footer-brand {
    display: flex;
    align-items: center;
    gap: 15px;
    text-decoration: none;
    margin-bottom: 25px;
}

.olv-footer-brand .olv-brand-badge {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--olv-gold), #b88a3a);
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 1.2rem;
    color: #fff;
    font-weight: 900;
    box-shadow: 0 10px 25px rgba(212,163,77,0.2);
}

.olv-footer-brand .olv-brand-text strong {
    display: block;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.2rem;
    color: #fff;
    letter-spacing: 1px;
    line-height: 1.1;
}

.olv-footer-brand .olv-brand-text span {
    font-size: 0.75rem;
    color: var(--olv-gold);
    letter-spacing: 2px;
    text-transform: uppercase;
}

.footer-description {
    font-size: 0.95rem;
    color: #a0a0a0;
    margin-bottom: 30px;
    line-height: 1.7;
}

.footer-social {
    display: flex;
    gap: 15px;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.social-link:hover {
    background: var(--olv-gold);
    color: #fff;
    transform: translateY(-3px);
    border-color: var(--olv-gold);
}

/* Footer Columns */
.footer-column h3 {
    color: #fff;
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    letter-spacing: 1px;
    margin-bottom: 25px;
    position: relative;
    display: inline-block;
}

.footer-column h3::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 30px;
    height: 2px;
    background: var(--olv-gold);
}

.footer-nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-nav-list li {
    margin-bottom: 12px;
}

.footer-nav-list a {
    color: #b0b0b0;
    text-decoration: none;
    font-size: 0.95rem;
    transition: 0.3s;
    display: inline-block;
}

.footer-nav-list a:hover {
    color: var(--olv-gold);
    transform: translateX(5px);
}

/* Contact Details */
.contact-details {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.contact-item {
    display: flex;
    gap: 15px;
    align-items: flex-start;
}

.contact-icon {
    color: var(--olv-gold);
    font-size: 1.2rem;
    margin-top: 2px;
}

.contact-item div {
    font-size: 0.9rem;
    color: #b0b0b0;
    line-height: 1.5;
}

/* Footer Bottom */
.footer-bottom {
    background: #000;
    padding: 25px 0;
    border-top: 1px solid rgba(255,255,255,0.05);
}

.bottom-simple {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.copyright-text {
    font-size: 0.9rem;
    color: #777;
}

.footer-logo-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #888;
    background: rgba(255,255,255,0.05);
    padding: 6px 15px;
    border-radius: 20px;
}

/* Responsive */
@media (max-width: 1024px) {
    .footer-primary {
        grid-template-columns: 1fr 1fr;
        gap: 50px;
    }
    .footer-brand-section { grid-column: 1 / -1; max-width: 100%; text-align: center; }
    .olv-footer-brand { justify-content: center; }
    .footer-social { justify-content: center; }
    .footer-column h3::after { left: 50%; transform: translateX(-50%); }
    .footer-column { text-align: center; }
    .contact-item { justify-content: center; }
}

@media (max-width: 600px) {
    .footer-primary { grid-template-columns: 1fr; gap: 40px; }
    .bottom-simple { flex-direction: column; text-align: center; }
}
</style>

<footer class="olv-footer">
    <!-- Footer Top Section -->
    <section class="footer-top">
        <div class="container">
            <div class="footer-primary">
                <!-- Brand & Description -->
                <div class="footer-brand-section">
                    <a href="olv-school.php" class="olv-footer-brand">
                        <div class="olv-brand-badge">🎓</div>
                        <div class="olv-brand-text">
                            <strong>OLV EDUCATION</strong>
                            <span>Education Ecosystem</span>
                        </div>
                    </a>
                    <p class="footer-description">
                        Asia's Largest Integrated Educational Ecosystem. 
                        50-Acre Smart Campus with World-Class Infrastructure.
                    </p>
                    <!-- Social Icons -->
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="Facebook">📘</a>
                        <a href="#" class="social-link" aria-label="Twitter">🐦</a>
                        <a href="#" class="social-link" aria-label="Instagram">📷</a>
                        <a href="#" class="social-link" aria-label="YouTube">🎥</a>
                        <a href="#" class="social-link" aria-label="LinkedIn">💼</a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul class="footer-nav-list">
                        <li><a href="olv-school.php" class="<?php echo olv_active('olv-school.php'); ?>">Home</a></li>
                        <li><a href="about.php" class="<?php echo olv_active('about.php'); ?>">About Us</a></li>
                        <li><a href="courses.php" class="<?php echo olv_active('courses.php'); ?>">Courses</a></li>
                        <li><a href="gallery.php" class="<?php echo olv_active('gallery.php'); ?>">Gallery</a></li>
                    </ul>
                </div>

                <!-- Academic -->
                <div class="footer-column">
                    <h3>Academic</h3>
                    <ul class="footer-nav-list">
                        <li><a href="teachers.php" class="<?php echo olv_active('teachers.php'); ?>">Faculty</a></li>
                        <li><a href="register.php" class="<?php echo olv_active('register.php'); ?>">Admissions</a></li>
                        <li><a href="#">Scholarships</a></li>
                        <li><a href="complain.php" class="<?php echo olv_active('complain.php'); ?>">Feedback</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div class="footer-column">
                    <h3>Support</h3>
                    <ul class="footer-nav-list">
                        <li><a href="donate.php">Donate</a></li>
                        <li><a href="contact.php" class="<?php echo olv_active('contact.php'); ?>">Contact</a></li>
                        <li><a href="#">Events</a></li>
                        <li><a href="#">News</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-column contact-column">
                    <h3>Get In Touch</h3>
                    <div class="contact-details">
                        <div class="contact-item">
                            <span class="contact-icon">📍</span>
                            <div>MIttal Educational Building, Near Citizen  Complex, Naigaon East
                            Tq. Vasai  Dist. Palghar  State : Maharashtra</div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">📞</span>
                            <div>+91 7378579000 <br>+91 8530575999</div>
                        </div>
                        <div class="contact-item">
                            <span class="contact-icon">✉️</span>
                            <div>Olvsjcnaigaon@olveducation.com</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Bottom Bar -->
    <section class="footer-bottom">
        <div class="container">
            <div class="bottom-simple">
                <div class="copyright-text">
                    &copy; 2026 OLV Education. All Rights Reserved.
                </div>
                <div class="footer-logo-badge">
                    <span>⚔️</span>
                    <span>OLV Education Ecosystem</span>
                </div>
            </div>
        </div>
    </section>
</footer>
