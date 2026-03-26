<?php
// ==========================================
// FIX: HEADER MUST BE FIRST LINE
// Path updated to: font/header.php
// ==========================================
include 'font/header.php'; 
?>

<!-- Custom CSS for Donation Page -->
<style>
    :root {
        /* ===== Logo-based palette (purple + gold) ===== */
        --purple-dark:   #3e145f;
        --purple:        #6d449f;
        --purple-mid:    #875db8;
        --gold:          #c8a22c;
        --gold-deep:     #b8901f;
        --gold-light:    #e1c05c;
        --white:         #fefefd;
        --off-white:     #fbf7ff;
        --text-dark:     #1a1a2e;
        --text-muted:    #6b6b8a;
        --border:        #e2d9f3;
        --shadow-soft:   0 2px 20px rgba(62, 20, 95, 0.10);
        --shadow-card:   0 20px 60px rgba(62, 20, 95, 0.16);
    }

    .donation-page-wrapper {
        font-family: 'Raleway', sans-serif;
        background: var(--off-white);
        color: var(--text-dark);
        overflow-x: hidden;
    }
    
    .donation-page-wrapper * {
        box-sizing: border-box;
    }

    /* ======= HERO SECTION ======= */
    .hero {
        background: linear-gradient(135deg, var(--purple-dark) 0%, var(--purple) 55%, var(--purple-mid) 100%);
        padding: 90px 40px 70px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(200, 162, 44, 0.25);
    }

    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.045'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .hero-badge {
        display: inline-block;
        background: rgba(200, 162, 44, 0.18);
        border: 1px solid rgba(225, 192, 92, 0.75);
        color: var(--gold-light);
        padding: 7px 20px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 24px;
        animation: fadeDown 0.6s ease both;
    }

    .hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.2rem, 5vw, 3.6rem);
        font-weight: 900;
        color: #ffffff;
        line-height: 1.2;
        margin-bottom: 16px;
        animation: fadeDown 0.7s ease 0.1s both;
    }

    .hero h1 span { color: var(--gold-light); }

    .hero p {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.84);
        max-width: 640px;
        margin: 0 auto 30px;
        line-height: 1.7;
        animation: fadeDown 0.7s ease 0.2s both;
    }

    .hero p strong { color: #ffffff; }

    .hero-quote {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        color: rgba(225, 192, 92, 0.95);
        font-size: 1rem;
        opacity: 0.95;
        animation: fadeDown 0.7s ease 0.3s both;
    }

    /* ======= STATS STRIP ======= */
    .stats-strip {
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        display: flex;
        justify-content: center;
        gap: 0;
    }

    .stat-item {
        flex: 1;
        max-width: 220px;
        text-align: center;
        padding: 22px 20px;
        border-right: 1px solid rgba(255, 255, 255, 0.35);
    }
    .stat-item:last-child { border-right: none; }

    .stat-item h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.9rem;
        font-weight: 900;
        color: var(--purple-dark);
        margin: 0;
    }

    .stat-item p {
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(62, 20, 95, 0.92);
        margin-top: 2px;
        margin-bottom: 0;
    }

    /* ======= MAIN LAYOUT ======= */
    .main-content {
        max-width: 1200px;
        margin: 60px auto;
        padding: 0 24px;
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 40px;
        align-items: start;
    }

    /* ======= LEFT COLUMN ======= */
    .section-tag {
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 3px;
        text-transform: uppercase;
        color: var(--gold-deep);
        margin-bottom: 10px;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 800;
        color: var(--purple-dark);
        margin-bottom: 16px;
    }

    .section-desc {
        color: var(--text-muted);
        line-height: 1.75;
        font-size: 0.95rem;
        margin-bottom: 32px;
    }

    /* Impact cards */
    .impact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 40px;
    }

    .impact-card {
        background: var(--white);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid var(--border);
        display: flex;
        gap: 16px;
        align-items: flex-start;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    }

    .impact-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 34px rgba(62, 20, 95, 0.12);
        border-color: rgba(200, 162, 44, 0.35);
    }

    .impact-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--purple-dark), var(--purple));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 18px;
        flex-shrink: 0;
        box-shadow: 0 10px 18px rgba(62, 20, 95, 0.18);
    }

    .impact-card h4 {
        font-weight: 900;
        font-size: 0.9rem;
        color: var(--text-dark);
        margin-bottom: 4px;
    }

    .impact-card p {
        font-size: 0.8rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin: 0;
    }

    /* Trust badges */
    .trust-section {
        background: var(--white);
        border-radius: 20px;
        padding: 28px;
        border: 1px solid var(--border);
        margin-bottom: 32px;
    }

    .trust-section h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        color: var(--purple-dark);
        margin-bottom: 18px;
    }

    .trust-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 7px;
        background: var(--off-white);
        border: 1px solid var(--border);
        border-radius: 50px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 800;
        color: var(--purple-dark);
    }
    .trust-badge i { color: #27ae60; font-size: 13px; }

    /* Testimonial */
    .testimonial {
        background: linear-gradient(135deg, var(--purple-dark), var(--purple));
        border-radius: 20px;
        padding: 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(200, 162, 44, 0.15);
    }

    .testimonial::before {
        content: '"';
        position: absolute;
        top: -10px; left: 20px;
        font-size: 120px;
        font-family: 'Playfair Display', serif;
        color: rgba(255, 255, 255, 0.09);
        line-height: 1;
    }

    .testimonial p {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        font-size: 1rem;
        line-height: 1.7;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
        color: rgba(255, 255, 255, 0.92);
    }

    .testimonial-author {
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 1px;
        color: var(--gold-light);
        text-transform: uppercase;
    }

    /* ======= RIGHT COLUMN — DONATION FORM ======= */
    .donation-card {
        background: var(--white);
        border-radius: 24px;
        box-shadow: var(--shadow-card);
        padding: 36px 32px;
        border: 1px solid rgba(200, 162, 44, 0.20);
        position: sticky;
        top: 90px;
    }

    .card-header { text-align: center; margin-bottom: 28px; }

    .card-header .heart-icon {
        width: 56px; height: 56px;
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        font-size: 22px;
        color: var(--purple-dark);
        box-shadow: 0 10px 24px rgba(200, 162, 44, 0.30);
        animation: heartbeat 1.6s ease-in-out infinite;
        border: 1px solid rgba(62, 20, 95, 0.10);
    }

    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        15% { transform: scale(1.12); }
        30% { transform: scale(1); }
        45% { transform: scale(1.07); }
    }

    .card-header h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.5rem;
        color: var(--purple-dark);
        margin-bottom: 6px;
    }

    .card-header p { font-size: 0.82rem; color: var(--text-muted); margin: 0; }

    /* Frequency tabs */
    .freq-tabs {
        display: flex;
        background: var(--off-white);
        border-radius: 10px;
        padding: 4px;
        margin-bottom: 24px;
        border: 1px solid rgba(200, 162, 44, 0.18);
    }

    .freq-tab {
        flex: 1;
        text-align: center;
        padding: 9px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-muted);
        user-select: none;
    }

    .freq-tab.active {
        background: linear-gradient(135deg, var(--purple-dark), var(--purple));
        color: #ffffff;
        box-shadow: 0 6px 16px rgba(62, 20, 95, 0.22);
    }

    /* Amount buttons */
    .form-label {
        font-size: 12px;
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 10px;
        display: block;
    }

    .amount-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .amount-btn {
        border: 2px solid var(--border);
        background: var(--white);
        border-radius: 12px;
        padding: 12px 10px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        font-family: 'Raleway', sans-serif;
    }

    .amount-btn:hover {
        border-color: rgba(200, 162, 44, 0.75);
        background: var(--off-white);
    }

    .amount-btn.selected {
        border-color: rgba(200, 162, 44, 0.85);
        background: linear-gradient(135deg, var(--purple-dark), var(--purple));
        color: #ffffff;
        box-shadow: 0 12px 26px rgba(62, 20, 95, 0.20);
    }

    .amount-btn .amt { font-size: 1.1rem; font-weight: 900; display: block; }
    .amount-btn .desc { font-size: 10px; opacity: 0.78; display: block; margin-top: 2px; }
    .amount-btn.selected .desc { opacity: 0.88; }

    /* Custom amount */
    .custom-amount-wrap {
        position: relative;
        margin-bottom: 22px;
    }

    .custom-amount-wrap .rupee {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 900;
        color: var(--purple-dark);
        font-size: 1rem;
    }

    .custom-amount-wrap input {
        width: 100%;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 13px 14px 13px 34px;
        font-family: 'Raleway', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: var(--white);
    }

    .custom-amount-wrap input:focus {
        border-color: rgba(200, 162, 44, 0.80);
        box-shadow: 0 0 0 4px rgba(200, 162, 44, 0.14);
    }

    .custom-amount-wrap input::placeholder { color: #bbb; font-weight: 400; }

    /* Divider */
    .divider {
        height: 1px;
        background: var(--border);
        margin: 22px 0;
    }

    /* Form fields */
    .form-group { margin-bottom: 14px; }

    .form-group input, .form-group select {
        width: 100%;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 12px 14px;
        font-family: 'Raleway', sans-serif;
        font-size: 0.9rem;
        color: var(--text-dark);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        background: var(--white);
    }

    .form-group input:focus, .form-group select:focus {
        border-color: rgba(200, 162, 44, 0.80);
        box-shadow: 0 0 0 4px rgba(200, 162, 44, 0.14);
    }

    .form-group input::placeholder { color: #bbb; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

    /* Donate button */
    .donate-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--purple-dark), var(--purple));
        color: #ffffff;
        border: none;
        border-radius: 14px;
        font-family: 'Raleway', sans-serif;
        font-size: 1rem;
        font-weight: 900;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }

    .donate-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        opacity: 0;
        transition: opacity 0.28s;
    }

    .donate-btn:hover::after { opacity: 1; }
    .donate-btn span { position: relative; z-index: 1; }

    .donate-btn:hover {
        box-shadow: 0 10px 30px rgba(62, 20, 95, 0.30);
        transform: translateY(-1px);
        color: var(--purple-dark);
    }
    .donate-btn:active { transform: translateY(0); }

    /* Security note */
    .security-note {
        text-align: center;
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .security-note i { color: #27ae60; }

    /* Tax info */
    .tax-info {
        background: linear-gradient(135deg, #f0f9f4, #e8f5e9);
        border: 1px solid #a5d6a7;
        border-radius: 12px;
        padding: 14px 16px;
        margin-top: 16px;
        font-size: 12px;
        color: #2e7d32;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tax-info i { font-size: 18px; }

    /* ======= HOW IT WORKS ======= */
    .how-section {
        background: linear-gradient(135deg, var(--purple-dark), #2f0f49);
        padding: 70px 40px;
        text-align: center;
        border-top: 1px solid rgba(200, 162, 44, 0.25);
    }

    .how-section .section-tag { color: var(--gold-light); }
    .how-section .section-title { color: #ffffff; }

    .steps-grid {
        display: flex;
        justify-content: center;
        gap: 0;
        max-width: 900px;
        margin: 40px auto 0;
    }

    .step {
        flex: 1;
        padding: 0 30px;
        position: relative;
    }

    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 28px;
        width: 1px; height: 30px;
        background: rgba(255, 255, 255, 0.20);
    }

    .step-num {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        color: var(--purple-dark);
        font-family: 'Playfair Display', serif;
        font-size: 1.4rem;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        box-shadow: 0 10px 22px rgba(200, 162, 44, 0.30);
        border: 1px solid rgba(62, 20, 95, 0.12);
    }

    .step h4 {
        font-weight: 900;
        font-size: 0.95rem;
        color: #ffffff;
        margin-bottom: 8px;
    }

    .step p {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.68);
        line-height: 1.6;
    }

    /* ======= FOOTER ======= */
    footer {
        background: #141424;
        color: rgba(255, 255, 255, 0.70);
        text-align: center;
        padding: 32px 20px;
        font-size: 13px;
        border-top: 1px solid rgba(200, 162, 44, 0.20);
    }
    footer strong { color: var(--gold-light); }

    /* ======= SUCCESS MODAL WITH PAYMENT DETAILS ======= */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(6px);
    }

    .modal-overlay.show { display: flex; }

    .modal {
        background: var(--white);
        border-radius: 24px;
        padding: 40px 36px;
        max-width: 540px;
        width: 92%;
        text-align: center;
        animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) both;
        border: 1px solid rgba(200, 162, 44, 0.22);
        box-shadow: 0 22px 70px rgba(0, 0, 0, 0.30);
        max-height: 90vh;
        overflow-y: auto;
    }

    @keyframes popIn {
        from { transform: scale(0.85); opacity: 0; }
        to   { transform: scale(1); opacity: 1; }
    }

    .modal-icon {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, var(--gold), var(--gold-light));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 18px;
        font-size: 30px;
        color: var(--purple-dark);
        box-shadow: 0 10px 26px rgba(200, 162, 44, 0.35);
        border: 1px solid rgba(62, 20, 95, 0.12);
    }

    .modal h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.7rem;
        color: var(--purple-dark);
        margin-bottom: 8px;
    }

    .modal-intro {
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 16px;
        font-size: 0.9rem;
    }

    .modal-amount {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 800;
        color: var(--purple-dark);
        margin-bottom: 20px;
    }

    /* QR CODE + BANK DETAILS */
    .payment-details {
        background: var(--off-white);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        text-align: left;
    }

    .payment-details h4 {
        font-family: 'Raleway', sans-serif;
        font-weight: 900;
        font-size: 0.95rem;
        color: var(--purple-dark);
        margin-bottom: 14px;
        text-align: center;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .qr-section {
        background: #ffffff;
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 18px;
        text-align: center;
    }

    .qr-placeholder {
        width: 180px; height: 180px;
        background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
        border: 2px solid var(--border);
        border-radius: 12px;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 700;
        overflow: hidden;
    }
    
    .qr-placeholder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .qr-note { font-size: 11px; color: var(--text-muted); font-style: italic; }

    .bank-details {
        background: #ffffff;
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 16px;
    }

    .bank-details table { width: 100%; border-collapse: collapse; }
    .bank-details td { padding: 7px 0; font-size: 0.85rem; border-bottom: 1px dashed var(--border); }
    .bank-details tr:last-child td { border-bottom: none; }
    .bank-details td:first-child { color: var(--text-muted); font-weight: 700; width: 42%; }
    .bank-details td:last-child { color: var(--text-dark); font-weight: 800; text-align: right; }

    .modal-footer-note {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 18px;
        padding: 12px;
        background: linear-gradient(135deg, #f0f9f4, #e8f5e9);
        border: 1px solid #a5d6a7;
        border-radius: 10px;
        color: #2e7d32;
    }

    .modal-close {
        background: linear-gradient(135deg, var(--purple-dark), var(--purple));
        color: #ffffff;
        border: none;
        padding: 13px 36px;
        border-radius: 50px;
        font-family: 'Raleway', sans-serif;
        font-weight: 900;
        cursor: pointer;
        font-size: 0.9rem;
        letter-spacing: 1px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .modal-close:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(62, 20, 95, 0.28);
    }

    /* ======= RESPONSIVE ======= */
    @media (max-width: 900px) {
        .main-content { grid-template-columns: 1fr; }
        .donation-card { position: static; }
        .steps-grid { flex-direction: column; gap: 30px; }
        .step::after { display: none; }
        .stats-strip { flex-wrap: wrap; }
    }

    @media (max-width: 500px) {
        .hero { padding: 70px 16px 55px; }
        .impact-grid { grid-template-columns: 1fr; }
        .amount-grid { grid-template-columns: 1fr 1fr; }
        .donation-card { padding: 28px 18px; }
        .modal { padding: 28px 20px; }
        .qr-placeholder { width: 160px; height: 160px; }
    }
</style>

<!-- WRAPPER DIV START (To apply font/color correctly inside body) -->
<div class="donation-page-wrapper">

    <!-- ======= HERO ======= -->
    <section class="hero">
        <div class="hero-badge">
            <i class="fas fa-hands-holding-heart"></i>
            &nbsp; Make a Difference Today
        </div>

        <h1>
            Support a Child.<br>
            <span>Transform a Future.</span>
        </h1>

        <p>
            At <strong>Our Lady of Good Health Trust</strong>, we believe every child deserves quality education — regardless of their financial background.
        </p>

        <div class="hero-quote">
            "Education is the most powerful weapon which you can use to change the world."
        </div>
    </section>

    <!-- ======= STATS STRIP ======= -->
    <div class="stats-strip">
        <div class="stat-item">
            <h3>500+</h3>
            <p>Students Supported</p>
        </div>
        <div class="stat-item">
            <h3>₹12L+</h3>
            <p>Funds Raised</p>
        </div>
        <div class="stat-item">
            <h3>10+</h3>
            <p>Years of Service</p>
        </div>
        <div class="stat-item">
            <h3>100%</h3>
            <p>Transparent Funds</p>
        </div>
    </div>

    <!-- ======= MAIN CONTENT ======= -->
    <div class="main-content">

        <!-- LEFT COLUMN -->
        <div>

            <div class="section-tag">Why Donate</div>

            <h2 class="section-title">
                Your Donation Directly Impacts a Child's Life
            </h2>

            <p class="section-desc">
                Many children at Our Lady of Vailankanni School come from financially struggling families.
                Every rupee you donate goes towards keeping them in school, giving them a chance at a brighter future.
            </p>

            <div class="impact-grid">
                <div class="impact-card">
                    <div class="impact-icon"><i class="fas fa-book-open"></i></div>
                    <div>
                        <h4>Books &amp; Stationery</h4>
                        <p>Ensure every child has the materials they need to learn effectively.</p>
                    </div>
                </div>
                <div class="impact-card">
                    <div class="impact-icon"><i class="fas fa-tshirt"></i></div>
                    <div>
                        <h4>School Uniforms</h4>
                        <p>Provide uniforms so every child can attend with dignity and pride.</p>
                    </div>
                </div>
                <div class="impact-card">
                    <div class="impact-icon"><i class="fas fa-laptop"></i></div>
                    <div>
                        <h4>Digital Learning</h4>
                        <p>Support access to digital tools bridging the technology gap.</p>
                    </div>
                </div>
                <div class="impact-card">
                    <div class="impact-icon"><i class="fas fa-award"></i></div>
                    <div>
                        <h4>Scholarship Support</h4>
                        <p>Fund exam fees and scholarships for deserving bright students.</p>
                    </div>
                </div>
                <div class="impact-card">
                    <div class="impact-icon"><i class="fas fa-utensils"></i></div>
                    <div>
                        <h4>Midday Support</h4>
                        <p>Ensure no child studies on an empty stomach during school hours.</p>
                    </div>
                </div>
                <div class="impact-card">
                    <div class="impact-icon"><i class="fas fa-file-invoice"></i></div>
                    <div>
                        <h4>Fee Sponsorship</h4>
                        <p>Sponsor partial or full annual school fees for needy children.</p>
                    </div>
                </div>
            </div>

            <!-- Trust Certifications -->
            <div class="trust-section">
                <h3>Our Certifications &amp; Registrations</h3>
                <div class="trust-badges">
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> 80G Certified</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> 12A Registration</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> CSR Compliant</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> ISO Certified</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> NITI Aayog</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> E-Anudaan Portal</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> Annual Audit Reports</div>
                    <div class="trust-badge"><i class="fas fa-check-circle"></i> Receipts Issued</div>
                </div>
            </div>

            <!-- Testimonial -->
            <div class="testimonial">
                <p>
                    Because of the generous support from donors like you, I was able to continue my studies.
                    Today I am in 10th standard and my dream is to become a doctor. Thank you from the bottom of my heart.
                </p>
                <div class="testimonial-author">
                    — Priya S., Scholarship Beneficiary, OLV School
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN — DONATION FORM -->
        <div>
            <div class="donation-card">
                <div class="card-header">
                    <div class="heart-icon"><i class="fas fa-heart"></i></div>
                    <h3>Make a Donation</h3>
                    <p>Our Lady of Good Health Trust</p>
                </div>

                <!-- Frequency Tabs -->
                <div class="freq-tabs">
                    <div class="freq-tab active" onclick="setFreq(this, 'one-time')">One-Time</div>
                    <div class="freq-tab" onclick="setFreq(this, 'monthly')">Monthly</div>
                    <div class="freq-tab" onclick="setFreq(this, 'annual')">Annual</div>
                </div>

                <!-- Amount Selection -->
                <label class="form-label">Select Amount</label>
                <div class="amount-grid">
                    <div class="amount-btn" onclick="selectAmount(this, 3000)">
                        <span class="amt">₹3,000</span>
                        <span class="desc">Books &amp; Stationery</span>
                    </div>
                    <div class="amount-btn" onclick="selectAmount(this, 6000)">
                        <span class="amt">₹6,000</span>
                        <span class="desc">Uniform + Materials</span>
                    </div>
                    <div class="amount-btn selected" onclick="selectAmount(this, 12000)">
                        <span class="amt">₹12,000</span>
                        <span class="desc">Partial Fee Support</span>
                    </div>
                    <div class="amount-btn" onclick="selectAmount(this, 25000)">
                        <span class="amt">₹25,000</span>
                        <span class="desc">Full Year Support</span>
                    </div>
                </div>

                <div class="custom-amount-wrap">
                    <span class="rupee">₹</span>
                    <input type="number" id="customAmount" placeholder="Enter custom amount" min="100" value="12000" oninput="onCustomInput(this)">
                </div>

                <div class="divider"></div>

                <!-- Donor Details -->
                <label class="form-label">Your Details</label>
                <div class="form-group">
                    <input type="text" placeholder="Full Name *" id="donorName">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <input type="email" placeholder="Email Address *" id="donorEmail">
                    </div>
                    <div class="form-group">
                        <input type="tel" placeholder="Phone Number *" id="donorPhone">
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="PAN Number (for 80G receipt)" id="donorPan">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="City / State" id="donorCity">
                </div>

                <div class="divider"></div>

                <!-- Donate Button -->
                <button class="donate-btn" onclick="submitDonation()">
                    <span><i class="fas fa-heart"></i> &nbsp; PROCEED TO DONATE</span>
                </button>

                <div class="security-note">
                    <i class="fas fa-lock"></i>
                    All transactions are secure &bull; 80G Receipt provided
                </div>
                <div class="tax-info">
                    <i class="fas fa-receipt"></i>
                    <span>Your donation qualifies for <strong>80G Tax Exemption</strong>. Receipt will be emailed after payment confirmation.</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ======= HOW IT WORKS ======= -->
    <section class="how-section">
        <div class="section-tag">Simple Process</div>
        <h2 class="section-title">How Your Donation Works</h2>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <h4>Choose an Amount</h4>
                <p>Pick a preset sponsorship tier or enter a custom amount that works for you.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h4>Enter Your Details</h4>
                <p>Fill in your contact information for donation receipt and updates.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h4>Complete Payment</h4>
                <p>Use our QR code or bank transfer to complete your secure donation.</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <h4>Impact Report</h4>
                <p>We share how your funds are utilized with full transparency and annual reports.</p>
            </div>
        </div>
    </section>

    <!-- ======= FOOTER ======= -->
    <footer>
        <p><strong>Our Lady of Good Health Trust</strong> &bull; Managing: Our Lady of Vailankanni School</p>
        <p style="margin-top:8px;">📞 [Your Phone] &nbsp;|&nbsp; ✉ [Your Email] &nbsp;|&nbsp; 🌐 [Your Website]</p>
        <p style="margin-top:12px; font-size:11px; opacity:0.5;">© <?php echo date('Y'); ?> Our Lady of Good Health Trust. All Rights Reserved.</p>
    </footer>

</div> <!-- WRAPPER DIV END -->

<!-- ======= SUCCESS MODAL WITH QR + BANK DETAILS ======= -->
<div class="modal-overlay" id="successModal">
    <div class="modal">
        <div class="modal-icon"><i class="fas fa-heart"></i></div>
        <h3>Complete Your Donation</h3>
        <p class="modal-intro">Thank you for choosing to support our children's education!</p>
        <div class="modal-amount" id="modalAmount">₹12,000</div>

        <!-- PAYMENT DETAILS SECTION -->
        <div class="payment-details">
            <h4><i class="fas fa-qrcode"></i> Scan QR Code to Pay</h4>
            <div class="qr-section">
                <div class="qr-placeholder">
                    <!-- REPLACE WITH ACTUAL QR CODE -->
                    [QR Code Image Here]
                    <!-- <img src="images/qr.png" alt="QR Code"> -->
                </div>
                <p class="qr-note">Scan using any UPI app (Google Pay, PhonePe, Paytm, etc.)</p>
            </div>

            <h4 style="margin-top:20px;"><i class="fas fa-university"></i> Or Transfer Directly</h4>
            <div class="bank-details">
                <table>
                    <tr><td>Account Name</td><td>Our Lady of Good Health Trust</td></tr>
                    <tr><td>Account Number</td><td>1234567890</td></tr>
                    <tr><td>IFSC Code</td><td>ABCD0123456</td></tr>
                    <tr><td>Bank Name</td><td>XYZ Bank</td></tr>
                    <tr><td>Branch</td><td>Mumbai Main Branch</td></tr>
                    <tr><td>UPI ID</td><td>olvtrust@upi</td></tr>
                </table>
            </div>
        </div>

        <div class="modal-footer-note">
            <i class="fas fa-info-circle"></i>
            <strong>Important:</strong> After payment, please WhatsApp/Email your transaction screenshot to us for confirmation and 80G receipt.
        </div>
        <button class="modal-close" onclick="closeModal()">Close</button>
    </div>
</div>

<script>
    let selectedAmount = 12000;
    let donationFrequency = 'one-time';

    function setFreq(el, freq) {
        document.querySelectorAll('.freq-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        donationFrequency = freq;
    }

    function selectAmount(el, amount) {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        el.classList.add('selected');
        selectedAmount = amount;
        document.getElementById('customAmount').value = amount;
    }

    function onCustomInput(input) {
        document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('selected'));
        selectedAmount = parseInt(input.value) || 0;
    }

    function submitDonation() {
        const name  = document.getElementById('donorName').value.trim();
        const email = document.getElementById('donorEmail').value.trim();
        const phone = document.getElementById('donorPhone').value.trim();
        const amount = parseInt(document.getElementById('customAmount').value) || selectedAmount;

        if (!name)  { alert('Please enter your full name.'); return; }
        if (!email) { alert('Please enter your email address.'); return; }
        if (!phone) { alert('Please enter your phone number.'); return; }
        if (!amount || amount < 100) { alert('Minimum donation amount is ₹100.'); return; }

        document.getElementById('modalAmount').textContent = '₹' + amount.toLocaleString('en-IN');
        document.getElementById('successModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('successModal').classList.remove('show');
    }

    document.getElementById('successModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
