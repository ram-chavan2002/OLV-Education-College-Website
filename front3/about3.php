<?php
$page_title = "About Us | OLV Academy";
include __DIR__ . '/header.php';
?>
<?php
session_start();

$success = '';
$error = '';

$form_data = [
    'name'        => '',
    'email'       => '',
    'phone'       => '',
    'child_class' => '',
    'message'     => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $form_data['name']        = trim($_POST['name']        ?? '');
    $form_data['phone']       = trim($_POST['phone']       ?? '');
    $form_data['email']       = trim($_POST['email']       ?? '');
    $form_data['child_class'] = trim($_POST['child_class'] ?? '');
    $form_data['message']     = trim($_POST['message']     ?? '');

    $subject = 'OLV Academy - Parent Enquiry (' . $form_data['child_class'] . ')';

    if (
        empty($form_data['name'])        ||
        empty($form_data['email'])       ||
        empty($form_data['phone'])       ||
        empty($form_data['child_class']) ||
        empty($form_data['message'])
    ) {
        $error = "❌ All fields are required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $form_data['phone'])) {
        $error = "❌ Phone must be exactly 10 digits only.";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Invalid email format.";
    } else {
        try {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=sai7755_college;charset=utf8mb4",
                "sai7755_college",
                "Admin_66666"
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("INSERT INTO college_contact
                (name, phone, email, subject, message, child_class, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())");

            $stmt->execute([
                $form_data['name'],
                $form_data['phone'],
                $form_data['email'],
                $subject,
                $form_data['message'],
                $form_data['child_class']
            ]);

            $success = "✅ Thank you! Your enquiry has been submitted successfully.";

            $form_data = ['name' => '', 'email' => '', 'phone' => '', 'child_class' => '', 'message' => ''];

        } catch (PDOException $e) {
            $error = "⚠️ Database error. Please try again later.";
        }
    }
}
?>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<style>
    body         { font-family: 'Inter', sans-serif; }
    .text-gold   { color: #B38B3F; }
    .bg-gold     { background-color: #B38B3F; }
    .border-gold { border-color: #B38B3F; }
    .gradient-text {
        background: linear-gradient(to right, #B38B3F, #FF0000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* ── Professional Principal Photo Styling ── */
    .principal-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0;
    }
    .principal-img-outer {
        position: relative;
        padding: 5px;
        background: linear-gradient(135deg, #B38B3F 0%, #ffe9a0 50%, #B38B3F 100%);
        border-radius: 24px;
        box-shadow: 0 12px 40px rgba(179,139,63,0.30);
    }
    .principal-img-outer img {
        width: 230px;
        height: 280px;
        object-fit: cover;
        object-position: top center;
        border-radius: 20px;
        display: block;
        border: 5px solid #fff;
    }
    .principal-badge {
        margin-top: 16px;
        background: linear-gradient(90deg, #B38B3F, #d4a84b);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        padding: 8px 28px;
        border-radius: 999px;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        box-shadow: 0 4px 18px rgba(179,139,63,0.40);
        text-align: center;
    }
</style>

<body class="bg-white text-gray-900 overflow-x-hidden">

<!-- HERO -->
<section class="relative h-[70vh] flex items-center justify-center text-center px-6 bg-slate-50">
    <div class="absolute inset-0 z-0 opacity-15">
        <img src="https://images.unsplash.com/photo-1523050853063-913894d92f5f?auto=format&fit=crop&q=80"
             class="w-full h-full object-cover" alt="School Campus">
    </div>
    <div class="relative z-10 max-w-4xl">
        <span class="tracking-widest uppercase text-sm font-bold text-gold mb-4 block">About</span>
        <h1 class="text-4xl md:text-6xl font-extrabold uppercase tracking-tighter mb-5">
            OUR LADY OF <span class="gradient-text">VAILANKANNI</span>
        </h1>
        <p class="text-lg md:text-xl text-gray-700 font-light mb-4 max-w-3xl mx-auto">
            OLV School &amp; Junior College &nbsp;|&nbsp; OLV Orchid International School &nbsp;|&nbsp; Orchid Degree College
        </p>
        <p class="text-gray-600 max-w-2xl mx-auto">
            Kindling the spirit of learning among the youth — pursuing excellence and meeting the challenges of the globalized world.
        </p>
    </div>
</section>

<!-- MISSION, VISION & GOALS -->
<section class="py-12 px-6 max-w-7xl mx-auto">
    <div class="text-center mb-8">
        <span class="tracking-widest uppercase text-sm font-bold text-gold mb-3 block">Our Foundation</span>
        <h2 class="text-3xl md:text-4xl font-extrabold uppercase mb-3">
            Mission, Vision <span class="gradient-text">&amp; Goals</span>
        </h2>
        <p class="text-gray-500 max-w-xl mx-auto">The core principles that shape every student's journey at OLV.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8 mb-8">

        <div class="bg-orange-50 p-6 rounded-2xl border border-orange-100 shadow-sm">
            <h3 class="text-xl font-extrabold uppercase mb-4 flex items-center gap-3 border-b-2 border-gold pb-3">
                <i data-lucide="eye" class="text-gold w-5 h-5"></i> Our Vision
            </h3>
            <p class="text-gray-700 leading-relaxed">
                To kindle the spirit of learning among youth, irrespective of socioeconomic differences, while consistently pursuing excellence and meeting the challenges of the globalized world.
            </p>
        </div>

        <div class="bg-orange-50 p-6 rounded-2xl border border-orange-100 shadow-sm">
            <h3 class="text-xl font-extrabold uppercase mb-4 flex items-center gap-3 border-b-2 border-gold pb-3">
                <i data-lucide="target" class="text-gold w-5 h-5"></i> Our Mission
            </h3>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle" class="text-gold mt-1 w-4 h-4 shrink-0"></i>
                    Equal opportunities for education
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle" class="text-gold mt-1 w-4 h-4 shrink-0"></i>
                    Holistic education for all-round development
                </li>
                <li class="flex items-start gap-2">
                    <i data-lucide="check-circle" class="text-gold mt-1 w-4 h-4 shrink-0"></i>
                    Creating human capital as an asset to the nation
                </li>
            </ul>
        </div>

    </div>

    <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6 md:p-8">
        <h3 class="text-xl font-extrabold uppercase mb-6 flex items-center gap-3 border-b-2 border-gold pb-3">
            <i data-lucide="flag" class="text-gold w-5 h-5"></i> Our Goals
        </h3>
        <div class="grid md:grid-cols-2 gap-4 text-gray-700">
            <div class="flex items-start gap-3"><i data-lucide="star" class="text-gold mt-1 w-4 h-4 shrink-0"></i><p>Quality and affordable education for all sections of society.</p></div>
            <div class="flex items-start gap-3"><i data-lucide="star" class="text-gold mt-1 w-4 h-4 shrink-0"></i><p>Overall personality development through festivals, fitness, and sports.</p></div>
            <div class="flex items-start gap-3"><i data-lucide="star" class="text-gold mt-1 w-4 h-4 shrink-0"></i><p>Flexible subject combinations for appropriate student choice.</p></div>
            <div class="flex items-start gap-3"><i data-lucide="star" class="text-gold mt-1 w-4 h-4 shrink-0"></i><p>Skill development to enhance learning and job orientation.</p></div>
            <div class="flex items-start gap-3"><i data-lucide="star" class="text-gold mt-1 w-4 h-4 shrink-0"></i><p>State-of-the-art infrastructure for enriched learning.</p></div>
            <div class="flex items-start gap-3"><i data-lucide="star" class="text-gold mt-1 w-4 h-4 shrink-0"></i><p>Creativity, research, entrepreneurship, and sports excellence.</p></div>
        </div>
    </div>
</section>

<!-- ORCHID DEGREE COLLEGE -->
<section class="py-12 bg-gray-50 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <span class="tracking-widest uppercase text-sm font-bold text-gold mb-3 block">About Us</span>
            <h2 class="text-3xl md:text-4xl font-extrabold uppercase mb-2">
                Orchid <span class="gradient-text">Degree College</span>
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Mumbai University · Established 2023</p>
        </div>

        <div class="grid md:grid-cols-2 gap-10 items-start">

            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold uppercase mb-5 tracking-wide">College Highlights</h3>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="graduation-cap" class="mt-1 text-gold shrink-0"></i> Higher education aligned with global careers.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="globe" class="mt-1 text-gold shrink-0"></i> Inclusive environment with ethics and mutual respect.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="cpu" class="mt-1 text-gold shrink-0"></i> Innovation-driven curriculum and analytical thinking.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="activity" class="mt-1 text-gold shrink-0"></i> Academic forums, cultural &amp; leadership activities.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="briefcase" class="mt-1 text-gold shrink-0"></i> Blend of academics, ethics &amp; experiential learning.</li>
                </ul>
            </div>

            <div>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Established in <strong>2023</strong> under the guidance of <strong>Mr. Melwyn Sequiera</strong>, <strong>Orchid Degree College</strong> is a center of higher learning designed to meet the evolving needs of today's students and tomorrow's professionals.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Our philosophy is rooted in adaptability — empowering students with knowledge, confidence, and the ability to respond effectively to real-world challenges through a balanced blend of academics, ethics, and experiential learning.
                </p>
                <p class="text-gray-600 leading-relaxed italic border-l-4 border-gold pl-4">
                    "Inspiring knowledge, shaping futures."
                </p>
            </div>

        </div>
    </div>
</section>


<!-- PRINCIPAL MESSAGE -->
<section class="py-12 px-6">
    <div class="max-w-6xl mx-auto mb-8 text-center">
        <span class="text-xs font-semibold tracking-[0.3em] uppercase text-gold">School Leadership</span>
        <h2 class="text-3xl md:text-4xl font-extrabold uppercase mt-3 mb-2">
            From the Desk of the <span class="text-gold">Principal</span>
        </h2>
        <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-base">Guiding students to become responsible global citizens.</p>
    </div>
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-10 items-start">

        <!-- ✅ Professional Principal Photo -->
        <div class="md:col-span-1 flex justify-center">
            <div class="principal-card">
                <div class="principal-img-outer">
                    <img
                        src="uploads/images/principlee.jpeg"
                        alt="Principal"
                    >
                </div>
                <div class="principal-badge">✦ Principal ✦</div>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 md:p-8 relative">
                <div class="absolute -top-4 left-6 text-4xl text-gold opacity-30 select-none">"</div>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    At <strong>Our Lady of Valainkanni School and Junior College</strong>, we believe true education is a blend of intelligence and character. We educate students to maximize their potential, develop positive social behaviour, and grow into responsible global citizens.
                </p>
                <p class="text-gray-700 mb-4 leading-relaxed italic border-l-4 border-gold pl-5">
                    "Educating the mind without educating the heart is not education." — <strong>Aristotle</strong>
                </p>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    We invite you to be a part of our family and step into the future with confidence.
                </p>
                <p class="text-gray-800 font-bold mb-3">JAI HIND</p>
                <div>
                    <div class="font-semibold text-sm text-gray-900">Principal</div>
                    <div class="text-[12px] text-gray-500 uppercase tracking-[0.2em]">Our Lady of Valainkanni School &amp; Junior College</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CO-FOUNDER MESSAGE -->
<section class="py-12 bg-gray-50 px-6">
    <div class="max-w-6xl mx-auto mb-8 text-center">
        <span class="text-xs font-semibold tracking-[0.3em] uppercase text-gold">Leadership</span>
        <h2 class="text-3xl md:text-4xl font-extrabold uppercase mt-3 mb-2">
            Co-Founder's <span class="text-gold">Message</span>
        </h2>
        <p class="text-gray-500 max-w-2xl mx-auto text-sm md:text-base">Shaping confident, compassionate individuals for tomorrow.</p>
    </div>
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-10 items-start">
        <div class="md:col-span-1 flex justify-center">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80"
                     alt="Co-Founder" class="w-64 h-64 object-cover rounded-3xl shadow-xl border-[6px] border-white">
                <div class="absolute -bottom-4 -right-4 bg-gold text-white text-[10px] font-bold px-4 py-2 rounded-full uppercase tracking-[0.2em] shadow-lg">Co-Founder</div>
            </div>
        </div>
        <div class="md:col-span-2">
            <div class="bg-white shadow-sm border border-gray-100 rounded-2xl p-6 md:p-8 relative">
                <div class="absolute -top-4 left-6 text-4xl text-gold opacity-30 select-none">"</div>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Education is not merely the acquisition of facts — it is the nurturing of values. At <strong>Our Lady of Valainkanni School and Junior College</strong>, we shape each learner into a balanced human being through meaningful experiences rooted in strong socio-cultural values.
                </p>
                <p class="text-gray-700 mb-4 leading-relaxed italic border-l-4 border-gold pl-5">
                    "We didn't come this far to come this far only."
                </p>
                <p class="text-gray-700 mb-4 leading-relaxed">
                    Co-curricular activities instill discipline, social responsibility, and pride in Indian culture — shaping students into responsible citizens and compassionate human beings.
                </p>
                <div>
                    <div class="font-semibold text-sm text-gray-900">Lavina Melwyn Sequiera</div>
                    <div class="text-[12px] text-gray-500 uppercase tracking-[0.2em]">Co-Founder, Our Lady of Valainkanni School &amp; Junior College</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ORCHID DEGREE COLLEGE -->
<section class="py-12 bg-gray-50 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <span class="tracking-widest uppercase text-sm font-bold text-gold mb-3 block">About Us</span>
            <h2 class="text-3xl md:text-4xl font-extrabold uppercase mb-2">
                Orchid <span class="gradient-text">Degree College</span>
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Mumbai University · Established 2023</p>
        </div>

        <div class="grid md:grid-cols-2 gap-10 items-start">

            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-2xl font-bold uppercase mb-5 tracking-wide">College Highlights</h3>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="graduation-cap" class="mt-1 text-gold shrink-0"></i> Higher education aligned with global careers.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="globe" class="mt-1 text-gold shrink-0"></i> Inclusive environment with ethics and mutual respect.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="cpu" class="mt-1 text-gold shrink-0"></i> Innovation-driven curriculum and analytical thinking.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="activity" class="mt-1 text-gold shrink-0"></i> Academic forums, cultural &amp; leadership activities.</li>
                    <li class="flex items-start gap-3 font-medium"><i data-lucide="briefcase" class="mt-1 text-gold shrink-0"></i> Blend of academics, ethics &amp; experiential learning.</li>
                </ul>
            </div>

            <div>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Established in <strong>2023</strong> under the guidance of <strong>Mr. Melwyn Sequiera</strong>, <strong>Orchid Degree College</strong> is a center of higher learning designed to meet the evolving needs of today's students and tomorrow's professionals.
                </p>
                <p class="text-gray-600 mb-4 leading-relaxed">
                    Our philosophy is rooted in adaptability — empowering students with knowledge, confidence, and the ability to respond effectively to real-world challenges through a balanced blend of academics, ethics, and experiential learning.
                </p>
                <p class="text-gray-600 leading-relaxed italic border-l-4 border-gold pl-4">
                    "Inspiring knowledge, shaping futures."
                </p>
            </div>

        </div>
    </div>
</section>

<!-- CONTACT FORM -->
<section class="py-12 px-6 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-start">

        <div>
            <h2 class="text-3xl md:text-4xl font-extrabold uppercase mb-4">Contact &amp; Location</h2>
            <p class="text-gray-300 mb-6">Connect with us for admissions, visits, or queries.</p>
            <div class="space-y-4 text-gray-200 text-sm">
                <p class="flex gap-3">
                    <span class="mt-1 text-gold shrink-0"><i data-lucide="map-pin"></i></span>
                    OLV Academy Campus, Hyderabad, Telangana — (Refer brochure for complete address)
                </p>
                <p class="flex gap-3">
                    <span class="mt-1 text-gold shrink-0"><i data-lucide="phone"></i></span>
                    Office: Refer brochure &nbsp;|&nbsp; Admissions: Refer brochure
                </p>
                <p class="flex gap-3">
                    <span class="mt-1 text-gold shrink-0"><i data-lucide="mail"></i></span>
                    Refer school brochure for email addresses
                </p>
                <p class="flex gap-3">
                    <span class="mt-1 text-gold shrink-0"><i data-lucide="clock"></i></span>
                    Mon–Sat, 8:00 AM to 3:30 PM
                </p>
            </div>
        </div>

        <div class="bg-white/5 rounded-2xl border border-white/10 p-6 backdrop-blur">
            <h3 class="text-xl font-bold mb-4">Quick Contact Form</h3>

            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-red-500/20 text-red-400 rounded-md border border-red-400/30"><?= $error ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="mb-4 p-3 bg-green-500/20 text-green-400 rounded-md border border-green-400/30"><?= $success ?></div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label class="text-sm text-gray-300 block mb-1">Parent / Guardian Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="<?= htmlspecialchars($form_data['name']) ?>" required
                           class="w-full px-3 py-2 rounded-md bg-black/40 border border-white/10 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/50">
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-gray-300 block mb-1">Email <span class="text-red-400">*</span></label>
                        <input type="email" name="email" value="<?= htmlspecialchars($form_data['email']) ?>" required
                               class="w-full px-3 py-2 rounded-md bg-black/40 border border-white/10 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/50">
                    </div>
                    <div>
                        <label class="text-sm text-gray-300 block mb-1">Phone <span class="text-red-400">*</span></label>
                        <input type="text" name="phone" maxlength="10" pattern="[0-9]{10}" inputmode="numeric"
                               value="<?= htmlspecialchars($form_data['phone']) ?>" required
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               class="w-full px-3 py-2 rounded-md bg-black/40 border border-white/10 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/50">
                    </div>
                </div>

                <div>
                    <label class="text-sm text-gray-300 block mb-1">Child's Class / Grade <span class="text-red-400">*</span></label>
                    <input type="text" name="child_class" value="<?= htmlspecialchars($form_data['child_class']) ?>" required
                           class="w-full px-3 py-2 rounded-md bg-black/40 border border-white/10 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/50">
                </div>

                <div>
                    <label class="text-sm text-gray-300 block mb-1">Message <span class="text-red-400">*</span></label>
                    <textarea rows="3" name="message" required
                              class="w-full px-3 py-2 rounded-md bg-black/40 border border-white/10 text-sm focus:outline-none focus:border-gold focus:ring-2 focus:ring-gold/50"><?= htmlspecialchars($form_data['message']) ?></textarea>
                </div>

                <button type="submit"
                        class="mt-2 inline-flex items-center justify-center px-6 py-3 rounded-lg bg-gold text-black font-semibold text-sm uppercase tracking-wide hover:opacity-90 hover:shadow-lg transition-all duration-200">
                    <i data-lucide="send" class="w-4 h-4 mr-2"></i> Submit Enquiry
                </button>
            </form>
        </div>

    </div>
</section>

<!-- CAMPUS HIGHLIGHTS -->
<section class="py-12 bg-gray-50 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl md:text-4xl font-extrabold uppercase mb-3">Campus <span class="text-gold">Highlights</span></h2>
            <p class="text-gray-500 max-w-2xl mx-auto">A safe, vibrant campus designed for every dimension of student growth.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all">
                <h4 class="font-bold mb-2 flex items-center gap-2"><i data-lucide="library" class="text-gold"></i> Digital Library</h4>
                <p class="text-sm text-gray-600">Thousands of books, e-resources, and reference materials.</p>
            </div>
            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all">
                <h4 class="font-bold mb-2 flex items-center gap-2"><i data-lucide="microscope" class="text-gold"></i> Science Labs</h4>
                <p class="text-sm text-gray-600">Physics, Chemistry, Biology, and Robotics labs for experimentation.</p>
            </div>
            <div class="bg-white p-7 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all">
                <h4 class="font-bold mb-2 flex items-center gap-2"><i data-lucide="trophy" class="text-gold"></i> Sports Arena</h4>
                <p class="text-sm text-gray-600">Grounds, courts, and structured fitness programs.</p>
            </div>
        </div>
    </div>
</section>

<script>
    lucide.createIcons();
</script>

<?php include __DIR__ . '/footer.php'; ?>
</body>
</html>