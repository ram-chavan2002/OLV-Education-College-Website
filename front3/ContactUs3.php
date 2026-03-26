<?php
session_start();

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Phone: exactly 10 digits only
    if (!preg_match('/^\d{10}$/', $phone)) {
        $error = "❌ Phone must be exactly 10 digits.";
    } elseif ($name == '' || $email == '' || $subject == '' || $message == '') {
        $error = "❌ All * fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=sai7755_college;charset=utf8mb4",
                "sai7755_college",
                "Admin_66666"
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("INSERT INTO mum_contact 
                (name, phone, email, subject, message, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $phone, $email, $subject, $message]);

            $_SESSION['success'] = "✅ Thank you! Your message has been sent successfully.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;

        } catch (PDOException $e) {
            $error = "⚠️ Database error. Please try again.";
        }
    }
}

if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<?php require_once(__DIR__ . '/header3.php'); ?>

<style>
:root {
    --gold: #F59E0B;
    --light-bg: #f8fafc;
    --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
    --border-light: #e2e8f0;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--light-bg);
}

/* CARDS */
.contact-cards { padding: 4rem 0 2rem; background: #fff; }
.contact-card {
    border-radius: 16px;
    border: 1px solid var(--border-light);
    padding: 2rem;
    text-align: center;
    height: 100%;
    transition: all 0.3s ease;
    box-shadow: var(--card-shadow);
}
.contact-card:hover { transform: translateY(-4px); border-color: var(--gold); }
.contact-icon {
    width: 70px; height: 70px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; margin: 0 auto 1rem; color: #fff;
}
.contact-card h5 { font-size: 1.2rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; }
.contact-card p { color: #64748b; font-size: 0.95rem; margin: 0; }

/* FORM */
.contact-main { padding: 4rem 0; background: #fff; }
.contact-form {
    border-radius: 16px; border: 1px solid var(--border-light);
    padding: 2.5rem; box-shadow: var(--card-shadow);
}
.form-label { font-weight: 600; color: #374151; margin-bottom: 0.75rem; }
.form-control {
    border: 2px solid var(--border-light); border-radius: 10px;
    padding: 12px 16px; transition: all 0.3s ease;
}
.form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 0.2rem rgba(245,158,11,0.15); }
#phone { font-family: monospace; font-size: 1.1rem; letter-spacing: 2px; }
textarea.form-control { height: 120px; resize: vertical; }
.btn-submit {
    background: var(--gold); border: none; border-radius: 10px; color: #fff;
    font-weight: 700; padding: 14px; width: 100%; text-transform: uppercase;
    transition: all 0.3s ease; box-shadow: 0 4px 12px rgba(245,158,11,0.3);
}
.btn-submit:hover:not(:disabled) {
    background: #d97706; transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(245,158,11,0.4);
}
.alert { border-radius: 10px; border: none; padding: 1rem; margin-bottom: 1.5rem; font-weight: 500; }

/* MAP - PROPER HEIGHT/WIDTH */
.contact-map {
    height: 450px;
    border-radius: 16px;
    border: 1px solid var(--border-light);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    position: relative;
}
.contact-map iframe {
    width: 100% !important;
    height: 100% !important;
    border: 0 !important;
    display: block;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .contact-map { height: 400px; margin-bottom: 2rem; }
}
@media (max-width: 768px) {
    .contact-main, .contact-cards { padding: 2rem 1rem; }
    .contact-form { padding: 2rem; margin: 0 1rem; }
    .contact-map { height: 350px; }
}
</style>

<!-- CONTACT CARDS -->
<section class="contact-cards">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-6">
                <div class="contact-card h-100">
                    <div class="contact-icon bg-primary mb-3"><i class="bi bi-geo-alt"></i></div>
                    <h5>Campus Address</h5>
                    <p>Bandra Kurla Complex<br><strong>BKC, Mumbai MH 400051</strong></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="contact-card h-100">
                    <div class="contact-icon bg-success mb-3"><i class="bi bi-telephone"></i></div>
                    <h5>Phone Number</h5>
                    <p><strong>+91 98765 43210</strong><br>8AM - 8PM Daily</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="contact-card h-100">
                    <div class="contact-icon bg-warning mb-3"><i class="bi bi-envelope"></i></div>
                    <h5>Email Us</h5>
                    <p><strong>admissions@olvacademy.in</strong><br>Reply within 24h</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="contact-card h-100">
                    <div class="contact-icon bg-info mb-3"><i class="bi bi-clock"></i></div>
                    <h5>Working Hours</h5>
                    <p><strong>Mon-Sat:</strong> 9AM-9PM<br><strong>Sunday:</strong> 10AM-6PM</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAP & FORM -->
<section class="contact-main">
    <div class="container">
        <div class="row g-5 align-items-stretch">
            <div class="col-lg-6">
                <div class="contact-map h-100">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3770.449140228614!2d72.86497897601284!3d19.06690288657989!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c8f94f3a6d2d%3A0x5e7c8f5c0e5b5b5b!2sBandra%20Kurla%20Complex%2C%20Bandra%20East%2C%20Mumbai%2C%20Maharashtra%20400051!5e0!3m2!1sen!2sin!4v1737000000000!5m2!1sen!2sin" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-form h-100 d-flex flex-column justify-content-center">
                    <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <?php if($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>

                    <h3 class="mb-4 fw-bold text-center mb-5" style="color:#1e293b;">
                        <i class="bi bi-chat-text me-2 text-warning"></i>Get In Touch
                    </h3>
                    
                    <form method="POST" class="needs-validation flex-grow-1" novalidate>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required placeholder="Enter your full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                <div class="invalid-feedback">Name required</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" id="phone" maxlength="10" required 
                                       placeholder="9876543210" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                <div class="invalid-feedback">Exactly 10 digits required</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required placeholder="your@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                <div class="invalid-feedback">Valid email required</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="subject" required placeholder="What is this about?" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>">
                                <div class="invalid-feedback">Subject required</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="message" required placeholder="Tell us more..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                <div class="invalid-feedback">Message required</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-submit mt-3" id="submitBtn">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('.needs-validation');
    const phone = document.getElementById('phone');
    const submitBtn = document.getElementById('submitBtn');
    
    // Phone: DIGITS ONLY + exactly 10
    phone.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, ''); // Only digits
        if (this.value.length > 10) this.value = this.value.slice(0,10);
    });
    
    phone.addEventListener('blur', function() {
        if (this.value && this.value.length !== 10) {
            this.classList.add('is-invalid');
        }
    });
    
    form.addEventListener('submit', function(e) {
        let valid = true;
        
        // Phone check
        if (phone.value && phone.value.length !== 10) {
            phone.classList.add('is-invalid');
            valid = false;
        }
        
        // Required fields
        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            }
        });
        
        if (!valid) {
            e.preventDefault();
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass me-2"></i>Sending...';
    });
});
</script>

<?php if (isset($pdo)) $pdo = null; ?>
<?php include(__DIR__ . '/footer3.php'); ?>
