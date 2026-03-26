<?php
// ============================================================
// CONFIG
// ============================================================
define('API_ID',       'APID1227');
define('API_KEY',      'a136c59d-e371-4080-b940-0e632fae572a');
define('TOKEN_ID',     's0gILlvzbZdkpI7kOcKGUHXBDHC5w8s6');
define('BASE_URL',     'https://javabackend.idspay.in/api/v1/uat');

header('X-Frame-Options: SAMEORIGIN');
$action = $_POST['action'] ?? '';

if ($action === 'prefill') {
    $mobile = preg_replace('/\D/', '', $_POST['mobile'] ?? '');
    if (strlen($mobile) !== 10) { echo json_encode(['success'=>false,'message'=>'Invalid mobile']); exit; }
    $result = callAPI('/srv3/credit-report/prefill-wn', [
        'api_id'   => API_ID,
        'api_key'  => API_KEY,
        'token_id' => TOKEN_ID,
        'mobile'   => $mobile
    ]);
    echo json_encode($result); exit;
}

if ($action === 'fetch_report') {
    $name    = trim($_POST['name']    ?? '');
    $mobile  = preg_replace('/\D/', '', $_POST['mobile'] ?? '');
    $pan     = strtoupper(trim($_POST['pan'] ?? ''));
    $gender  = strtolower(trim($_POST['gender']  ?? ''));

    if (!$name || strlen($mobile) !== 10 || !$pan) {
        echo json_encode(['success'=>false,'message'=>'Please fill all required fields']); exit;
    }

    $payload = [
        'api_id'   => API_ID,
        'api_key'  => API_KEY,
        'token_id' => TOKEN_ID,
        'name'     => $name,
        'mobile'   => $mobile,
        'pan'      => $pan,
        'gender'   => $gender,
        'consent'  => 'Y'
    ];

    $result = callAPI('/srv3/credit-report/transunion', $payload);
    echo json_encode($result); exit;
}

function callAPI(string $endpoint, array $payload): array {
    $url = BASE_URL . $endpoint;
    $jsonPayload = json_encode($payload);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $jsonPayload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($jsonPayload)
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $body     = curl_exec($ch);
    $err      = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err) return ['success'=>false,'message'=>'cURL error: '.$err,'raw'=>null];
    if (!$body) return ['success'=>false,'message'=>'Empty response from API (HTTP '.$httpCode.')','raw'=>null];

    $d = json_decode($body, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['success'=>false,'message'=>'Invalid JSON response','raw'=>$body];
    }

    $code = $d['status']['code'] ?? $d['statusCode'] ?? $d['code'] ?? 0;
    if ($code == 200 || $code === '200') {
        return ['success'=>true,'data'=>$d,'raw'=>$body];
    } else {
        return [
            'success' => false,
            'message' => $d['status']['message'] ?? $d['message'] ?? 'API Error (code: '.$code.')',
            'raw'     => $body,
            'data'    => $d
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Credit Score Check | PropGurus</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --g:#c9970f;--g2:#e8b830;--g3:#f5d060;
  --glow:rgba(201,151,15,.2);
  --ink:#080808;--ink2:#101010;--ink3:#161616;--ink4:#1e1e1e;--ink5:#272727;
  --line:#252525;--line2:#303030;
  --muted:#5a5a5a;--text:#c0c0c0;--white:#fff;
  --green:#1db954;--red:#e04848;
  --font-d:'Cinzel',serif;--font-b:'Inter',sans-serif;--font-m:'JetBrains Mono',monospace;
  --r:10px;--r2:16px;--r3:22px;
  --shadow:0 32px 80px rgba(0,0,0,.7);
  --t:.2s cubic-bezier(.4,0,.2,1);
}
html{scroll-behavior:smooth}
body{font-family:var(--font-b);background:var(--ink);color:var(--white);overflow-x:hidden;-webkit-font-smoothing:antialiased}
.ticker{background:linear-gradient(90deg,var(--g),var(--g2),var(--g));color:var(--ink);font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:9px 0;overflow:hidden;white-space:nowrap}
.ticker-track{display:flex;width:max-content;animation:ticker 40s linear infinite}
.ticker-track span{padding:0 44px}
.ticker-track span::before{content:'◈';margin-right:10px;opacity:.5}
@keyframes ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}
nav{background:rgba(8,8,8,.96);backdrop-filter:blur(20px);border-bottom:1px solid var(--line);position:sticky;top:0;z-index:999;height:68px;display:flex;align-items:center;justify-content:space-between;padding:0 clamp(20px,4vw,60px)}
.nav-logo{display:flex;align-items:center;gap:11px;text-decoration:none}
.nav-mark{width:38px;height:38px;background:linear-gradient(135deg,var(--g),var(--g2));border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:var(--font-d);font-size:18px;font-weight:900;color:var(--ink);box-shadow:0 0 20px var(--glow)}
.nav-name{font-family:var(--font-d);font-size:17px;color:var(--white);letter-spacing:.12em}
.nav-links{display:flex;gap:28px}
.nav-links a{color:var(--muted);text-decoration:none;font-size:13px;font-weight:500;letter-spacing:.03em;padding:4px 0;border-bottom:1.5px solid transparent;transition:color var(--t),border-color var(--t)}
.nav-links a:hover,.nav-links a.active{color:var(--g);border-bottom-color:var(--g)}
.nav-btn{background:linear-gradient(135deg,var(--g),var(--g2));color:var(--ink);font-weight:700;font-size:12px;letter-spacing:.08em;text-transform:uppercase;padding:10px 22px;border-radius:var(--r);text-decoration:none;transition:all var(--t);box-shadow:0 4px 16px var(--glow)}
.nav-btn:hover{transform:translateY(-1px);box-shadow:0 8px 24px var(--glow)}
@media(max-width:768px){.nav-links{display:none}}
.hero{position:relative;min-height:500px;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:100px 20px 80px}
.hero-canvas{position:absolute;inset:0;background:radial-gradient(ellipse 60% 70% at 15% 60%,rgba(201,151,15,.14) 0%,transparent 70%),radial-gradient(ellipse 50% 60% at 85% 25%,rgba(201,151,15,.08) 0%,transparent 65%),linear-gradient(175deg,var(--ink) 0%,#0d0c08 50%,var(--ink) 100%)}
.hero-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(201,151,15,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(201,151,15,.04) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,black 30%,transparent 100%)}
.hero-inner{position:relative;z-index:2;max-width:740px;text-align:center}
.hero-eyebrow{display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(201,151,15,.3);background:rgba(201,151,15,.06);color:var(--g2);font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;padding:7px 18px;border-radius:40px;margin-bottom:28px}
.hero-eyebrow::before{content:'';width:6px;height:6px;background:var(--g2);border-radius:50%;animation:pulse 2s ease-in-out infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.35;transform:scale(.7)}}
.hero h1{font-family:var(--font-d);font-size:clamp(32px,5.5vw,60px);line-height:1.08;letter-spacing:.02em;color:var(--white);margin-bottom:20px}
.hero h1 em{font-style:normal;color:var(--g2)}
.hero-sub{color:var(--muted);font-size:16px;line-height:1.75;max-width:520px;margin:0 auto 40px}
.hero-cta{display:inline-flex;align-items:center;gap:12px;background:linear-gradient(135deg,var(--g) 0%,var(--g2) 100%);color:var(--ink);font-weight:700;font-size:15px;letter-spacing:.04em;padding:17px 40px;border-radius:var(--r2);border:none;cursor:pointer;box-shadow:0 8px 32px rgba(201,151,15,.35);transition:all var(--t)}
.hero-cta:hover{transform:translateY(-2px);box-shadow:0 16px 48px rgba(201,151,15,.45)}
.cta-arr{font-size:18px;transition:transform var(--t)}
.hero-cta:hover .cta-arr{transform:translateX(4px)}
.hero-note{margin-top:16px;font-size:12px;color:var(--muted)}
.hero-note strong{color:var(--green)}
.band-sec{background:var(--ink2);border-top:1px solid var(--line);padding:72px 20px}
.container{max-width:1100px;margin:auto}
.sec-hd{text-align:center;margin-bottom:52px}
.sec-hd h2{font-family:var(--font-d);font-size:clamp(24px,3.5vw,38px);color:var(--white);margin-bottom:10px;letter-spacing:.05em}
.sec-hd h2 em{font-style:normal;color:var(--g2)}
.sec-hd p{color:var(--muted);font-size:14px}
.bands{display:flex;border:1px solid var(--line);border-radius:var(--r2);overflow:hidden}
.band{flex:1;padding:28px 16px;text-align:center;border-right:1px solid var(--line);background:var(--ink3);transition:background var(--t);position:relative;overflow:hidden}
.band::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.band:last-child{border-right:none}
.band:hover{background:var(--ink4)}
.band-r{font-family:var(--font-m);font-size:17px;font-weight:500;margin-bottom:7px}
.band-l{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;margin-bottom:5px}
.band-d{font-size:11px;color:var(--muted);line-height:1.5}
.bp .band-r,.bp .band-l{color:#e04848}.bp::before{background:#e04848}
.bf .band-r,.bf .band-l{color:#e07d30}.bf::before{background:#e07d30}
.bg .band-r,.bg .band-l{color:#e8b830}.bg::before{background:#e8b830}
.bv .band-r,.bv .band-l{color:#5ec97a}.bv::before{background:#5ec97a}
.be .band-r,.be .band-l{color:#1db954}.be::before{background:#1db954}
@media(max-width:600px){.bands{flex-direction:column}.band{border-right:none;border-bottom:1px solid var(--line)}.band:last-child{border-bottom:none}}
.form-sec{padding:80px 20px;background:var(--ink)}
.form-wrap{max-width:580px;margin:auto}
.form-card{background:var(--ink2);border:1px solid var(--line);border-radius:var(--r3);overflow:hidden;box-shadow:var(--shadow)}
.form-head{padding:28px 32px;background:linear-gradient(135deg,rgba(201,151,15,.12) 0%,rgba(201,151,15,.03) 100%);border-bottom:1px solid var(--line);display:flex;align-items:center;gap:16px}
.head-ico{width:52px;height:52px;flex-shrink:0;background:linear-gradient(145deg,var(--g),var(--g2));border-radius:13px;display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 6px 20px rgba(201,151,15,.3)}
.head-txt h2{font-family:var(--font-d);font-size:18px;color:var(--white);letter-spacing:.06em;line-height:1.2}
.head-txt p{font-size:12px;color:var(--muted);margin-top:5px}
.head-txt p span{color:rgba(201,151,15,.55)}
.steps{display:flex;background:var(--ink3);border-bottom:1px solid var(--line)}
.step{flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:14px 8px;font-size:10.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);border-bottom:2px solid transparent;transition:color var(--t),border-color var(--t);position:relative}
.step+.step::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:1px;height:38%;background:var(--line)}
.step.active{color:var(--g2);border-bottom-color:var(--g2)}
.step.done{color:var(--green);border-bottom-color:var(--green)}
.step-b{width:20px;height:20px;border-radius:50%;background:var(--ink5);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;flex-shrink:0;transition:all var(--t)}
.step.active .step-b{background:var(--g2);color:var(--ink)}
.step.done .step-b{background:var(--green);color:var(--white)}
.form-body{padding:32px}
.fp{display:none}.fp.active{display:block}
.alert{background:rgba(224,72,72,.1);border:1px solid rgba(224,72,72,.25);border-radius:var(--r);padding:12px 16px;font-size:13px;color:#e04848;font-weight:500;margin-bottom:22px;display:none;align-items:center;gap:10px}
.alert.show{display:flex}
.fg{margin-bottom:20px}
.fg label{display:block;font-size:10.5px;font-weight:700;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:.12em}
.fg label .req{color:var(--g2);margin-left:1px}
.row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}
@media(max-width:500px){.row2{grid-template-columns:1fr}}
.fc{width:100%;background:var(--ink3);border:1.5px solid var(--line2);border-radius:var(--r);color:var(--white);font-family:var(--font-b);font-size:14px;padding:13px 15px;outline:none;transition:border-color var(--t),box-shadow var(--t),background var(--t);-webkit-appearance:none;appearance:none}
.fc:focus{border-color:var(--g);box-shadow:0 0 0 3px rgba(201,151,15,.1);background:var(--ink4)}
.fc::placeholder{color:var(--muted);font-size:13px}
.fc:hover:not(:focus){background:rgba(255,255,255,.02)}
select.fc{cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='7' fill='none'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23555' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:38px}
select.fc option{background:var(--ink3);color:var(--white)}
.mob-row{display:flex}
.mob-cc{display:flex;align-items:center;gap:7px;background:var(--ink4);border:1.5px solid var(--line2);border-right:none;border-radius:var(--r) 0 0 var(--r);padding:0 14px;color:var(--text);font-size:13px;font-weight:600;white-space:nowrap;flex-shrink:0;font-family:var(--font-m)}
.mob-row .fc{border-radius:0 var(--r) var(--r) 0;font-family:var(--font-m)}
#pan{font-family:var(--font-m);letter-spacing:.08em}
.prefill-ok{margin-top:7px;font-size:11px;color:var(--green);font-weight:600;display:none;align-items:center;gap:6px}
.prefill-ok.show{display:flex}
.consent{background:var(--ink3);border:1.5px solid var(--line2);border-radius:var(--r);padding:14px 16px;display:flex;gap:12px;align-items:flex-start;cursor:pointer;transition:border-color var(--t),background var(--t)}
.consent:hover{border-color:rgba(201,151,15,.3);background:rgba(201,151,15,.03)}
.consent input{margin-top:2px;accent-color:var(--g);width:16px;height:16px;flex-shrink:0;cursor:pointer}
.consent p{font-size:12.5px;color:var(--muted);line-height:1.65}
.consent strong{color:var(--text)}
.btn-gold{width:100%;background:linear-gradient(135deg,var(--g) 0%,var(--g2) 60%,var(--g3) 100%);color:var(--ink);font-family:var(--font-b);font-size:14px;font-weight:700;letter-spacing:.06em;border:none;border-radius:var(--r);padding:15.5px;cursor:pointer;transition:all var(--t);margin-top:10px;box-shadow:0 4px 20px rgba(201,151,15,.25);position:relative;overflow:hidden}
.btn-gold::after{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(255,255,255,.14),transparent);transform:translateX(-100%);transition:transform .5s}
.btn-gold:hover::after{transform:translateX(100%)}
.btn-gold:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(201,151,15,.35)}
.btn-gold:disabled{opacity:.4;cursor:not-allowed;transform:none;box-shadow:none}
.secure-line{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:14px;font-size:11.5px;color:var(--muted)}
.secure-line .dot{width:3px;height:3px;background:var(--muted);border-radius:50%}
.secure-line strong{color:var(--green)}
.state{text-align:center;padding:52px 20px}
.state-ico{font-size:52px;display:block;margin-bottom:18px}
.state h3{font-family:var(--font-d);font-size:20px;letter-spacing:.06em;margin-bottom:10px}
.state p{color:var(--muted);font-size:13px;line-height:1.7}
.loader{width:42px;height:42px;border:3px solid var(--ink5);border-top-color:var(--g2);border-radius:50%;animation:spin .75s linear infinite;margin:24px auto 0}
@keyframes spin{to{transform:rotate(360deg)}}

/* ====== REPORT SECTION ====== */
#reportSection{display:none;padding:64px 20px;background:var(--ink)}
.report-wrap{max-width:960px;margin:auto}
.report-toolbar{display:flex;gap:12px;margin-bottom:28px;flex-wrap:wrap}
.tbtn{background:var(--ink2);border:1.5px solid var(--line2);color:var(--text);font-family:var(--font-b);font-size:13px;font-weight:500;padding:10px 22px;border-radius:var(--r);cursor:pointer;transition:all var(--t)}
.tbtn:hover{border-color:var(--g);color:var(--g)}

/* ---- Score Header Card ---- */
.report-hero{background:linear-gradient(135deg,var(--ink2) 0%,rgba(201,151,15,.06) 100%);border:1px solid var(--line);border-radius:var(--r3);padding:36px;margin-bottom:20px;display:grid;grid-template-columns:auto 1fr;gap:36px;align-items:center}
@media(max-width:600px){.report-hero{grid-template-columns:1fr;text-align:center}}
.score-dial{position:relative;width:170px;height:170px;flex-shrink:0;margin:0 auto}
.score-dial svg{position:absolute;inset:0;transform:rotate(-90deg)}
.score-dial-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.sdial-num{font-family:var(--font-d);font-size:40px;line-height:1;font-weight:700}
.sdial-max{font-size:11px;color:var(--muted);margin-top:2px}
.sdial-lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-top:8px;padding:4px 12px;border-radius:12px}
.rdetails h2{font-family:var(--font-d);font-size:24px;letter-spacing:.04em;margin-bottom:4px}
.rpan{font-size:12px;color:var(--muted);font-family:var(--font-m);margin-bottom:20px;letter-spacing:.06em}
.rmeta{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.rmeta-item{background:var(--ink3);border-radius:var(--r);padding:12px 16px;border:1px solid var(--line)}
.rmeta-key{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.1em}
.rmeta-val{font-size:14px;font-weight:600;color:var(--white);margin-top:5px;font-family:var(--font-m)}

/* ---- Score Factors Card ---- */
.factors-card{background:var(--ink2);border:1px solid var(--line);border-radius:var(--r2);padding:26px;margin-bottom:20px}
.factors-card .rcard-title{margin-bottom:16px}
.factor-chips{display:flex;flex-wrap:wrap;gap:10px}
.factor-chip{display:flex;align-items:center;gap:10px;background:var(--ink3);border:1px solid rgba(224,72,72,.2);border-radius:var(--r);padding:12px 16px;flex:1;min-width:220px}
.factor-chip-ico{width:34px;height:34px;border-radius:8px;background:rgba(224,72,72,.1);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.factor-chip-body{}
.factor-chip-title{font-size:12px;font-weight:700;color:#e04848;margin-bottom:3px}
.factor-chip-desc{font-size:11.5px;color:var(--muted);line-height:1.5}

/* ---- Report Cards Grid ---- */
.report-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-bottom:20px}
.rcard{background:var(--ink2);border:1px solid var(--line);border-radius:var(--r2);padding:26px}
.rcard.full{grid-column:1/-1}
.rcard-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--muted);margin-bottom:18px;display:flex;align-items:center;gap:8px}
.rcard-title::before{content:'';width:3px;height:14px;background:var(--g);border-radius:2px;flex-shrink:0}
.rcard-title .cnt{margin-left:auto;background:rgba(201,151,15,.1);color:var(--g2);font-size:10px;padding:2px 8px;border-radius:8px;font-family:var(--font-m)}
.row-item{display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--line);font-size:13px;gap:12px}
.row-item:last-child{border-bottom:none}
.ri-label{color:var(--muted);flex-shrink:0}
.ri-val{font-weight:600;color:var(--white);font-family:var(--font-m);font-size:13px;text-align:right}
.badge{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:3px 9px;border-radius:20px;white-space:nowrap}
.b-active{background:rgba(29,185,84,.12);color:#1db954;border:1px solid rgba(29,185,84,.25)}
.b-closed{background:rgba(120,120,120,.12);color:#888;border:1px solid rgba(120,120,120,.2)}
.b-overdue{background:rgba(224,72,72,.12);color:#e04848;border:1px solid rgba(224,72,72,.25)}
.b-written{background:rgba(224,100,30,.12);color:#e07030;border:1px solid rgba(224,100,30,.25)}

/* ---- Account Detail Cards ---- */
.acc-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px}
.acc-card{background:var(--ink3);border:1px solid var(--line2);border-radius:var(--r2);padding:20px;transition:border-color var(--t)}
.acc-card:hover{border-color:rgba(201,151,15,.25)}
.acc-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;gap:8px}
.acc-bank{font-weight:700;font-size:14px;color:var(--white);line-height:1.3}
.acc-type{font-size:10px;color:var(--muted);margin-top:3px;text-transform:uppercase;letter-spacing:.07em}
.acc-rows{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.acc-item{background:var(--ink4);border-radius:8px;padding:10px 12px}
.acc-item-k{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px}
.acc-item-v{font-size:13px;font-weight:600;color:var(--white);font-family:var(--font-m)}
.pay-history{margin-top:14px}
.ph-label{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:7px}
.ph-dots{display:flex;flex-wrap:wrap;gap:3px}
.ph-dot{width:10px;height:10px;border-radius:2px;cursor:default;flex-shrink:0}
.ph-dot.ok{background:#1db954}
.ph-dot.late{background:#e04848}
.ph-dot.na{background:#252525}
.ph-dot.written{background:#e07030}

/* ---- Inquiry Section ---- */
.inq-list{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px}
.inq-card{background:var(--ink3);border:1px solid var(--line2);border-radius:var(--r);padding:16px 18px;display:flex;justify-content:space-between;align-items:center;gap:12px}
.inq-left{}
.inq-name{font-weight:600;font-size:14px;color:var(--white)}
.inq-date{font-size:11px;color:var(--muted);margin-top:4px;font-family:var(--font-m)}
.inq-right{text-align:right;flex-shrink:0}
.inq-amt{font-family:var(--font-m);font-size:13px;font-weight:600;color:var(--g2)}
.inq-type{font-size:9px;color:var(--muted);margin-top:3px;text-transform:uppercase;letter-spacing:.08em}

/* ---- Loan Eligibility Card ---- */
.elig-card{background:linear-gradient(135deg,var(--ink2) 0%,rgba(201,151,15,.05) 100%);border:1px solid var(--line);border-radius:var(--r2);padding:26px;margin-bottom:20px}
/* Verdict Banner */
.elig-verdict{display:none;border-radius:var(--r2);padding:22px 26px;margin-bottom:20px;align-items:center;gap:20px;flex-wrap:wrap}
.elig-verdict.show{display:flex}
.elig-verdict.yes{background:rgba(29,185,84,.08);border:1.5px solid rgba(29,185,84,.3)}
.elig-verdict.no{background:rgba(224,72,72,.08);border:1.5px solid rgba(224,72,72,.3)}
.elig-verdict.maybe{background:rgba(232,184,48,.08);border:1.5px solid rgba(232,184,48,.3)}
.ev-icon{font-size:44px;flex-shrink:0}
.ev-body{flex:1;min-width:180px}
.ev-verdict{font-family:var(--font-d);font-size:22px;font-weight:700;letter-spacing:.06em;line-height:1.1;margin-bottom:6px}
.ev-sub{font-size:13px;color:var(--muted);line-height:1.6}
.ev-amt-wrap{text-align:right;flex-shrink:0}
.ev-amt-label{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:4px}
.ev-amt{font-family:var(--font-d);font-size:30px;font-weight:700;line-height:1}
/* Loan type pills */
.ev-pills{display:flex;flex-wrap:wrap;gap:7px;margin-top:14px}
.ev-pill{font-size:11px;font-weight:600;padding:5px 13px;border-radius:20px;border:1px solid}
/* Score bar */
.elig-bar-wrap{margin-bottom:20px}
.elig-bar-bg{background:var(--ink5);border-radius:6px;height:8px;overflow:hidden}
.elig-bar-fill{height:100%;border-radius:6px;transition:width 1.2s cubic-bezier(.4,0,.2,1)}
.elig-bar-labels{display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-top:6px;font-family:var(--font-m)}
/* Stats grid */
.elig-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:16px}
.elig-item{background:var(--ink3);border-radius:var(--r);padding:12px 14px;border:1px solid var(--line)}
.elig-item-k{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:5px}
.elig-item-v{font-size:14px;font-weight:700;font-family:var(--font-m)}
.elig-disclaimer{font-size:10.5px;color:var(--muted);line-height:1.6;padding:10px 14px;background:rgba(255,255,255,.02);border-radius:8px;border-left:2px solid var(--line2)}

/* ---- Income Selector inside Eligibility Card ---- */
.elig-income-row{background:rgba(201,151,15,.06);border:1.5px solid rgba(201,151,15,.25);border-radius:var(--r);padding:18px 18px 14px;margin-bottom:20px}
.elig-income-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--g2);margin-bottom:12px}
.elig-preset-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px}
@media(max-width:480px){.elig-preset-grid{grid-template-columns:repeat(2,1fr)}}
.elig-preset{background:var(--ink4);border:1.5px solid var(--line2);border-radius:var(--r);color:var(--text);font-family:var(--font-m);font-size:13px;font-weight:600;padding:10px 6px;cursor:pointer;transition:all var(--t);text-align:center}
.elig-preset:hover{border-color:rgba(201,151,15,.5);color:var(--g2);background:rgba(201,151,15,.06)}
.elig-preset.selected{background:linear-gradient(135deg,var(--g),var(--g2));color:var(--ink);border-color:var(--g2);box-shadow:0 4px 14px rgba(201,151,15,.3)}
.elig-slider-wrap{margin-bottom:8px}
.elig-slider{width:100%;-webkit-appearance:none;appearance:none;height:6px;border-radius:3px;background:var(--ink5);outline:none;cursor:pointer}
.elig-slider::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,var(--g),var(--g2));cursor:pointer;box-shadow:0 2px 8px rgba(201,151,15,.4);transition:transform var(--t)}
.elig-slider::-webkit-slider-thumb:hover{transform:scale(1.2)}
.elig-slider::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,var(--g),var(--g2));cursor:pointer;border:none;box-shadow:0 2px 8px rgba(201,151,15,.4)}
.elig-slider-labels{display:flex;justify-content:space-between;font-size:10px;color:var(--muted);margin-top:6px;font-family:var(--font-m)}
.elig-income-hint{font-size:11px;color:var(--muted);font-style:italic}
.elig-income-hint.has-income{color:var(--green);font-style:normal;font-weight:600}

/* ---- Debug Panel ---- */
.debug-section{margin-top:28px;border:1px solid var(--line2);border-radius:var(--r2);overflow:hidden}
.debug-header{background:var(--ink3);padding:14px 18px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;border-bottom:1px solid var(--line)}
.debug-header h4{font-family:var(--font-m);font-size:12px;color:var(--g2);letter-spacing:.08em}
.debug-toggle-btn{font-size:11px;background:rgba(201,151,15,.1);border:1px solid rgba(201,151,15,.2);color:var(--g);padding:5px 12px;border-radius:6px;cursor:pointer;font-family:var(--font-m)}
.debug-body{display:none;background:#050505;padding:0}
.debug-body.open{display:block}
.debug-tabs{display:flex;border-bottom:1px solid #1a1a1a;flex-wrap:wrap}
.debug-tab{padding:10px 18px;font-family:var(--font-m);font-size:11px;color:#555;cursor:pointer;border-bottom:2px solid transparent;transition:color .15s,border-color .15s}
.debug-tab.active{color:var(--g2);border-bottom-color:var(--g2)}
.debug-content{display:none;padding:16px;max-height:500px;overflow-y:auto}
.debug-content.active{display:block}
.debug-content pre{white-space:pre-wrap;word-break:break-all;color:#7a7a7a;font-family:var(--font-m);font-size:11.5px;line-height:1.7}

/* ---- How / Trust / FAQ ---- */
.how-sec{padding:80px 20px;background:var(--ink2);border-top:1px solid var(--line)}
.how-steps{max-width:640px;margin:52px auto 0;display:grid;grid-template-columns:1fr 40px 1fr;align-items:start}
.how-card{text-align:center;padding:0 8px}
.how-ico{width:70px;height:70px;margin:0 auto 18px;background:var(--ink3);border:1.5px solid var(--line2);border-radius:var(--r2);display:flex;align-items:center;justify-content:center;font-size:28px;position:relative;transition:border-color var(--t),transform var(--t)}
.how-card:hover .how-ico{border-color:rgba(201,151,15,.4);transform:translateY(-4px)}
.how-num{position:absolute;top:-9px;right:-9px;width:22px;height:22px;background:linear-gradient(135deg,var(--g),var(--g2));color:var(--ink);border-radius:50%;font-size:10px;font-weight:800;display:flex;align-items:center;justify-content:center}
.how-card h3{font-size:15px;font-weight:700;color:var(--white);margin-bottom:8px}
.how-card p{font-size:12px;color:var(--muted);line-height:1.65}
.how-arr{display:flex;align-items:center;justify-content:center;padding-top:22px;color:var(--line2);font-size:22px}
@media(max-width:560px){.how-steps{grid-template-columns:1fr;gap:32px}.how-arr{display:none}}
.trust-sec{padding:72px 20px;background:var(--ink);border-top:1px solid var(--line)}
.trust-grid{max-width:900px;margin:48px auto 0;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px}
.trust-card{background:var(--ink2);border:1px solid var(--line);border-radius:var(--r2);padding:28px 20px;text-align:center;transition:border-color var(--t),transform var(--t)}
.trust-card:hover{border-color:rgba(201,151,15,.25);transform:translateY(-3px)}
.trust-ico{width:52px;height:52px;background:rgba(201,151,15,.06);border:1px solid rgba(201,151,15,.15);border-radius:var(--r);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:22px}
.trust-card h4{font-size:14px;font-weight:700;color:var(--white);margin-bottom:7px}
.trust-card p{font-size:12px;color:var(--muted);line-height:1.6}
.faq-sec{padding:72px 20px;background:var(--ink2);border-top:1px solid var(--line)}
.faq-list{max-width:680px;margin:48px auto 0}
.faq-item{border:1px solid var(--line);border-radius:var(--r);margin-bottom:10px;overflow:hidden}
.faq-q{padding:18px 20px;font-weight:600;font-size:14px;cursor:pointer;display:flex;justify-content:space-between;align-items:center;color:var(--white);background:var(--ink3);transition:background var(--t)}
.faq-q:hover{background:var(--ink4)}
.faq-tog{width:26px;height:26px;border-radius:50%;background:var(--ink5);display:flex;align-items:center;justify-content:center;color:var(--g);font-size:16px;font-weight:700;flex-shrink:0;transition:transform var(--t),background var(--t)}
.faq-q.open .faq-tog{transform:rotate(45deg);background:var(--g);color:var(--ink)}
.faq-a{max-height:0;overflow:hidden;transition:max-height .35s ease,padding .35s;background:var(--ink3)}
.faq-a.open{max-height:200px;padding:0 20px 18px}
.faq-a p{color:var(--muted);font-size:13px;line-height:1.75}
footer{background:var(--ink);border-top:1px solid var(--line);padding:48px 20px 24px;text-align:center}
.f-brand{font-family:var(--font-d);font-size:24px;color:var(--g);letter-spacing:.12em;margin-bottom:6px}
.f-tag{font-size:12px;color:var(--muted);margin-bottom:24px}
.f-links{display:flex;justify-content:center;gap:24px;flex-wrap:wrap;margin-bottom:20px}
.f-links a{color:var(--muted);font-size:12px;text-decoration:none;transition:color var(--t)}
.f-links a:hover{color:var(--g)}
.f-legal{max-width:480px;margin:0 auto 20px;font-size:11px;color:#333;line-height:1.7}
.f-copy{font-size:11px;color:#2a2a2a;padding-top:20px;border-top:1px solid var(--line)}
@media print{nav,.ticker,.hero,.band-sec,.how-sec,.trust-sec,.faq-sec,footer,.form-sec,.report-toolbar,.debug-section{display:none!important}#reportSection{display:block!important;background:#fff;color:#000}}
</style>
</head>
<body>

<div class="ticker">
  <div class="ticker-track">
    <span>Check Your Credit Score</span><span>Transunion CIBIL Report Instantly</span><span>100% Secure & Encrypted</span><span>Trusted by 10,000+ Users</span><span>Know Your Score Before Applying for Loan</span><span>Soft Inquiry — No Score Impact</span>
    <span>Check Your Credit Score</span><span>Transunion CIBIL Report Instantly</span><span>100% Secure & Encrypted</span><span>Trusted by 10,000+ Users</span><span>Know Your Score Before Applying for Loan</span><span>Soft Inquiry — No Score Impact</span>
  </div>
</div>

<nav>
  <a href="https://propgurus.in" class="nav-logo">
    <div class="nav-mark">P</div>
    <span class="nav-name">PROPGURUS</span>
  </a>
  <div class="nav-links">
    <a href="https://propgurus.in">Home</a>
    <a href="https://propgurus.in/properties">Properties</a>
    <a href="#" class="active">Credit Score</a>
    <a href="https://propgurus.in/contact">Contact</a>
  </div>
  <a href="#formSection" class="nav-btn">Check My Score</a>
</nav>

<section class="hero">
  <div class="hero-canvas"></div>
  <div class="hero-grid"></div>
  <div class="hero-inner">
    <div class="hero-eyebrow">India's Trusted Credit Check Platform</div>
    <h1>Know Your <em>Credit Score</em><br>Instantly & Free</h1>
    <p class="hero-sub">Get your official Transunion CIBIL credit report. Just enter your details — report ready in seconds.</p>
    <button class="hero-cta" onclick="document.getElementById('formSection').scrollIntoView({behavior:'smooth'})">
      Get My Credit Report <span class="cta-arr">→</span>
    </button>
    <p class="hero-note"><strong>✓ Soft inquiry</strong> — your score is never affected</p>
  </div>
</section>

<section class="band-sec">
  <div class="container">
    <div class="sec-hd">
      <h2>What Does Your <em>Score Mean?</em></h2>
      <p>Credit scores range from 300 to 900 — the higher, the better your loan prospects</p>
    </div>
    <div class="bands">
      <div class="band bp"><div class="band-r">300–549</div><div class="band-l">Poor</div><div class="band-d">Very difficult to get approval</div></div>
      <div class="band bf"><div class="band-r">550–649</div><div class="band-l">Fair</div><div class="band-d">Limited options, higher rates</div></div>
      <div class="band bg"><div class="band-r">650–699</div><div class="band-l">Good</div><div class="band-d">Decent approval chances</div></div>
      <div class="band bv"><div class="band-r">700–749</div><div class="band-l">Very Good</div><div class="band-d">Good loan terms available</div></div>
      <div class="band be"><div class="band-r">750–900</div><div class="band-l">Excellent</div><div class="band-d">Best rates & easy approvals</div></div>
    </div>
  </div>
</section>

<section class="form-sec" id="formSection">
  <div class="form-wrap">
    <div class="sec-hd" style="margin-bottom:36px">
      <h2>Get Your <em>Credit Report</em></h2>
      <p>Fill your details — report ready in seconds</p>
    </div>
    <div class="form-card">
      <div class="form-head">
        <div class="head-ico">📋</div>
        <div class="head-txt">
          <h2>Credit Report Request</h2>
          <p>Powered by Transunion CIBIL V3 <span>·</span> End-to-End Encrypted</p>
        </div>
      </div>
      <div class="steps">
        <div class="step active" id="s1"><span class="step-b">1</span>Your Details</div>
        <div class="step" id="s2"><span class="step-b">2</span>Report</div>
      </div>
      <div class="form-body">
        <div class="alert" id="alertBox"></div>
        <div class="fp active" id="fp1">
          <div class="fg">
            <label>Mobile Number<span class="req"> *</span></label>
            <div class="mob-row">
              <div class="mob-cc">🇮🇳 +91</div>
              <input type="tel" class="fc" id="mobile" maxlength="10" placeholder="10-digit mobile number" inputmode="numeric">
            </div>
            <div class="prefill-ok" id="prefillTag">✓ Details auto-filled from your mobile</div>
          </div>
          <div class="row2">
            <div class="fg">
              <label>Full Name<span class="req"> *</span></label>
              <input type="text" class="fc" id="fullName" placeholder="As per PAN card">
            </div>
            <div class="fg">
              <label>Gender<span class="req"> *</span></label>
              <select class="fc" id="gender">
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
          </div>
          <div class="fg">
            <label>PAN Number<span class="req"> *</span></label>
            <input type="text" class="fc" id="pan" maxlength="10" placeholder="E.G. ABCDE1234F">
          </div>
          <div class="fg">
            <div class="consent" onclick="toggleConsent()">
              <input type="checkbox" id="consent" checked>
              <p>I authorise <strong>PropGurus</strong> to access my credit information from Transunion CIBIL. I understand this is a soft inquiry and will not impact my credit score.</p>
            </div>
          </div>
          <button class="btn-gold" id="fetchBtn" onclick="fetchReport()">📊 Get My Credit Report →</button>
          <div class="secure-line">
            <strong>🔒 SSL Secured</strong>
            <div class="dot"></div>
            <span>Your data is never shared</span>
          </div>
        </div>
        <div class="fp" id="fp2">
          <div class="state" id="stLoading">
            <span class="state-ico">⏳</span>
            <h3 style="color:var(--white)">Fetching Your Report</h3>
            <p>Connecting to Transunion CIBIL servers.<br>This only takes a moment.</p>
            <div class="loader"></div>
          </div>
          <div class="state" id="stDone" style="display:none">
            <span class="state-ico">✅</span>
            <h3 style="color:var(--g2)">Report Ready!</h3>
            <p>Your CIBIL credit report has been fetched successfully.</p>
            <button class="btn-gold" style="margin-top:22px" onclick="document.getElementById('reportSection').scrollIntoView({behavior:'smooth'})">View My Report ↓</button>
          </div>
          <div class="state" id="stError" style="display:none">
            <span class="state-ico">❌</span>
            <h3 style="color:var(--red)" id="errMsg">Something went wrong</h3>
            <p id="errDetail">Please try again or contact support.</p>
            <div id="errRawWrap" style="margin-top:16px;text-align:left;display:none">
              <div style="font-family:var(--font-m);font-size:10px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.1em">Raw API Response:</div>
              <pre id="errRaw" style="background:#050505;border:1px solid #1a1a1a;border-radius:8px;padding:14px;font-family:var(--font-m);font-size:11px;color:#888;white-space:pre-wrap;word-break:break-all;max-height:260px;overflow-y:auto;text-align:left"></pre>
            </div>
            <button class="btn-gold" style="margin-top:22px" onclick="resetForm()">← Try Again</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section id="reportSection">
  <div class="report-wrap">
    <div class="report-toolbar">
      <button class="tbtn" onclick="window.print()">🖨 Print Report</button>
      <button class="tbtn" onclick="resetForm()">↩ New Report</button>
    </div>
    <div id="reportContent"></div>

    <div class="debug-section">
      <div class="debug-header" onclick="toggleDebugPanel()">
        <h4>🔍 RAW API RESPONSE (DEBUG)</h4>
        <button class="debug-toggle-btn" id="debugToggleBtn">SHOW ▼</button>
      </div>
      <div class="debug-body" id="debugBody">
        <div class="debug-tabs">
          <div class="debug-tab active" onclick="switchTab('formatted')">Formatted JSON</div>
          <div class="debug-tab" onclick="switchTab('raw')">Raw String</div>
          <div class="debug-tab" onclick="switchTab('reqinfo')">Request Info</div>
        </div>
        <div class="debug-content active" id="tab-formatted"><pre id="debugFormatted"></pre></div>
        <div class="debug-content" id="tab-raw"><pre id="debugRaw"></pre></div>
        <div class="debug-content" id="tab-reqinfo"><div class="req-info" id="debugReqInfo" style="padding:16px;font-family:var(--font-m);font-size:12px;color:#555;line-height:2"></div></div>
      </div>
    </div>
  </div>
</section>

<section class="how-sec">
  <div class="container">
    <div class="sec-hd"><h2>How It <em>Works</em></h2><p>Two simple steps to your complete credit picture</p></div>
    <div class="how-steps">
      <div class="how-card"><div class="how-ico">📝<span class="how-num">1</span></div><h3>Fill Details</h3><p>Enter your mobile, name, PAN and gender. All information stays confidential and encrypted.</p></div>
      <div class="how-arr">→</div>
      <div class="how-card"><div class="how-ico">📊<span class="how-num">2</span></div><h3>View Report</h3><p>Your Transunion CIBIL report is fetched instantly and shown on screen within seconds.</p></div>
    </div>
  </div>
</section>

<section class="trust-sec">
  <div class="container">
    <div class="sec-hd"><h2>Why <em>Trust Us?</em></h2></div>
    <div class="trust-grid">
      <div class="trust-card"><div class="trust-ico">🔒</div><h4>Bank-Grade Security</h4><p>256-bit SSL encryption keeps every piece of your data protected at all times.</p></div>
      <div class="trust-card"><div class="trust-ico">🏦</div><h4>Official Data Source</h4><p>Credit data sourced directly from Transunion CIBIL — India's premier credit bureau.</p></div>
      <div class="trust-card"><div class="trust-ico">⚡</div><h4>Instant Results</h4><p>Your report appears on-screen in under 2 minutes after submitting your details.</p></div>
      <div class="trust-card"><div class="trust-ico">🛡️</div><h4>No Score Impact</h4><p>Soft inquiries only — checking here will never reduce your credit score.</p></div>
    </div>
  </div>
</section>

<section class="faq-sec">
  <div class="container">
    <div class="sec-hd"><h2>Frequently Asked <em>Questions</em></h2></div>
    <div class="faq-list">
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">Will checking my score affect it?<div class="faq-tog">+</div></div>
        <div class="faq-a"><p>No. We perform a soft inquiry which does not affect your credit score in any way.</p></div>
      </div>
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">Why does my score show N/A?<div class="faq-tog">+</div></div>
        <div class="faq-a"><p>N/A means CIBIL has not yet assigned a score to your profile. Start with a secured credit card to build your score.</p></div>
      </div>
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">Is my personal data safe?<div class="faq-tog">+</div></div>
        <div class="faq-a"><p>Absolutely. Your data is encrypted with 256-bit SSL and never shared without your explicit consent.</p></div>
      </div>
      <div class="faq-item">
        <div class="faq-q" onclick="toggleFaq(this)">What information do I need?<div class="faq-tog">+</div></div>
        <div class="faq-a"><p>Just your mobile number, full name as per PAN card, PAN number, and gender. No bank details or payment required.</p></div>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="f-brand">PROPGURUS</div>
  <div class="f-tag">Your Trusted Real Estate & Finance Partner</div>
  <div class="f-links">
    <a href="https://propgurus.in">Home</a><a href="#">Properties</a><a href="#">Credit Score</a><a href="#">Privacy Policy</a><a href="#">Terms of Service</a><a href="#">Contact</a>
  </div>
  <p class="f-legal">PropGurus is an authorized service provider for Transunion CIBIL credit information. Credit reports are for informational purposes only.</p>
  <div class="f-copy">© <?= date('Y') ?> PropGurus.in · All Rights Reserved</div>
</footer>

<script>
/* ================================================================
   PARSER — Handles the exact API structure:
   data → data → GetCustomerAssetsResponse → GetCustomerAssetsSuccess
          → Asset → TrueLinkCreditReport → Borrower / TradeLinePartition / InquiryPartition
   ================================================================ */

let formData = {};

// ── PREFILL ──────────────────────────────────────────────────────
const mobileEl = document.getElementById('mobile');
let prefillTimer;
mobileEl.addEventListener('input', function(){
  this.value = this.value.replace(/\D/g,'').slice(0,10);
  clearTimeout(prefillTimer);
  if(this.value.length === 10) prefillTimer = setTimeout(()=>doPrefill(this.value), 700);
});
async function doPrefill(m){
  try{
    const fd = new FormData();
    fd.append('action','prefill'); fd.append('mobile',m);
    const r = await fetch('',{method:'POST',body:fd});
    const j = await r.json();
    if(j.success && j.data?.data){
      const d = j.data.data;
      if(d.full_name)       document.getElementById('fullName').value = d.full_name;
      if(d.gender)          document.getElementById('gender').value   = d.gender.toLowerCase();
      if(d.pan_number?.[0]) document.getElementById('pan').value      = d.pan_number[0].toUpperCase();
      const t = document.getElementById('prefillTag');
      t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),4000);
    }
  }catch(e){console.warn('Prefill:',e);}
}
document.getElementById('pan').addEventListener('input',function(){ this.value=this.value.toUpperCase(); });
function toggleConsent(){ const c=document.getElementById('consent'); c.checked=!c.checked; }
function showAlert(m){ const el=document.getElementById('alertBox'); el.textContent='⚠ '+m; el.classList.add('show'); el.scrollIntoView({behavior:'smooth',block:'nearest'}); }
function hideAlert(){ document.getElementById('alertBox').classList.remove('show'); }
function setStep(n){
  ['fp1','fp2'].forEach((id,i)=>document.getElementById(id).classList.toggle('active',i+1===n));
  ['s1','s2'].forEach((id,i)=>{
    const el=document.getElementById(id); el.classList.remove('active','done');
    if(i+1<n){el.classList.add('done');el.querySelector('.step-b').textContent='✓';}
    else el.querySelector('.step-b').textContent=i+1;
    if(i+1===n) el.classList.add('active');
  });
}

// ── FETCH REPORT ─────────────────────────────────────────────────
async function fetchReport(){
  hideAlert();
  const mobile = mobileEl.value.trim();
  const name   = document.getElementById('fullName').value.trim();
  const pan    = document.getElementById('pan').value.trim().toUpperCase();
  const gender = document.getElementById('gender').value;
  const consent= document.getElementById('consent').checked;
  if(mobile.length!==10)                return showAlert('Enter a valid 10-digit mobile number.');
  if(!name)                             return showAlert('Enter your full name as per PAN card.');
  if(!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(pan)) return showAlert('Enter a valid PAN number (e.g. ABCDE1234F).');
  if(!gender)                           return showAlert('Please select your gender.');
  if(!consent)                          return showAlert('Please provide consent to proceed.');
  formData = {mobile,name,pan,gender};
  setStep(2); show('stLoading'); hide('stDone'); hide('stError');
  try{
    const fd=new FormData();
    fd.append('action','fetch_report'); fd.append('name',name);
    fd.append('mobile',mobile); fd.append('pan',pan);
    fd.append('gender',gender); fd.append('consent','Y');
    const res  = await fetch('',{method:'POST',body:fd});
    const text = await res.text();
    hide('stLoading');
    let json;
    try{ json=JSON.parse(text); }
    catch(e){ showErrState('Server Error','Non-JSON response from server.',text); return; }
    populateDebug(json.data||json, text, {name,mobile,pan,gender});
    if(json.success){
      renderReport(json.data);
      show('stDone');
    } else {
      showErrState(json.message||'API Error','See raw response below.',json.raw||text);
      document.getElementById('reportSection').style.display='block';
      setTimeout(()=>document.getElementById('reportSection').scrollIntoView({behavior:'smooth'}),300);
    }
  }catch(e){ hide('stLoading'); showErrState('Network Error',e.message,''); }
}

function showErrState(title,detail,raw){
  document.getElementById('errMsg').textContent=title;
  document.getElementById('errDetail').textContent=detail;
  if(raw){ document.getElementById('errRaw').textContent=raw; document.getElementById('errRawWrap').style.display='block'; }
  show('stError');
}

// ── CORE PARSER ───────────────────────────────────────────────────
function getReportRoot(apiData){
  return apiData
    ?.data
    ?.GetCustomerAssetsResponse
    ?.GetCustomerAssetsSuccess
    ?.Asset
    ?.TrueLinkCreditReport
    || null;
}

function safeInt(v, fallback=0){ const n=parseInt(v); return isNaN(n)?fallback:n; }
function safeFmt(v){ return (v && v!=='-1' && v!=='') ? v : '—'; }
function fmtAmt(v){
  const n=safeInt(v,-1);
  if(n<=0) return '—';
  return '₹'+n.toLocaleString('en-IN');
}
function fmtDate(d){
  if(!d||d==='') return '—';
  const m = d.match(/(\d{4})-(\d{2})-(\d{2})/);
  if(!m) return d;
  const months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  return `${parseInt(m[3])} ${months[parseInt(m[2])-1]} ${m[1]}`;
}
function accountTypeName(sym){
  const map={'01':'Auto Loan','02':'Home Loan','03':'Property Loan','04':'LAS','05':'Personal Loan','06':'Consumer Loan','07':'Gold Loan','08':'Education Loan','10':'OD / Overdraft','11':'Credit Card','13':'Kisan / Gold Card','15':'Business Loan','16':'Microfinance','19':'Tractor Loan','35':'Two-Wheeler Loan','51':'Commercial Vehicle'};
  return map[sym] || (sym?`Type ${sym}`:'Loan');
}
function inquiryPurpose(code){
  const map={'00':'Loan Enquiry','01':'Credit Card','05':'Home Loan','06':'Auto Loan','13':'Consumer Loan','07':'Personal Loan'};
  return map[code]||`Purpose ${code}`;
}

// ── SCORE EXTRACTION ─────────────────────────────────────────────
function extractScore(tlcr){
  const cs = tlcr?.Borrower?.CreditScore;
  if(cs){
    const rs = safeInt(cs.riskScore, -1);
    if(rs>=300 && rs<=900) return {score:rs, rank: safeInt(cs.populationRank,0), model: cs.scoreName||'CIBIL'};
  }
  return null;
}

// ── PAYMENT HISTORY DOTS ─────────────────────────────────────────
function buildPayDots(monthlyArr){
  if(!Array.isArray(monthlyArr)||!monthlyArr.length) return '';
  const dots = monthlyArr.slice(0,36).map(m=>{
    const st = String(m.status||'');
    let cls='na', title='No Data';
    if(st==='0'||st.toLowerCase()==='std'){ cls='ok'; title='On Time'; }
    else if(st==='XXX'||st==='-2'){ cls='na'; title='No Data / Closed'; }
    else if(st==='STD'){ cls='ok'; title='Standard'; }
    else if(parseInt(st)>0){ cls='late'; title=`DPD: ${st} days`; }
    else if(st==='901'||st==='902'){ cls='written'; title='Written-Off'; }
    return `<div class="ph-dot ${cls}" title="${fmtDate(m.date)} · ${title}"></div>`;
  }).join('');
  return `<div class="pay-history"><div class="ph-label">Payment History (latest → oldest)</div><div class="ph-dots">${dots}</div></div>`;
}

// ── LOAN ELIGIBILITY CALCULATOR ───────────────────────────────────
// manualIncome: monthly income in ₹ entered by user (optional)
function calcLoanEligibility(score, tradeLines, manualIncome){
  if(!score || score < 300) return null;

  // Colour & label by score band
  let eligColor, eligLabel, multiplier, foirLimit;
  if(score >= 750)      { eligColor='#1db954'; eligLabel='Excellent Eligibility'; multiplier=60; foirLimit=0.55; }
  else if(score >= 700) { eligColor='#5ec97a'; eligLabel='Good Eligibility';      multiplier=48; foirLimit=0.50; }
  else if(score >= 650) { eligColor='#e8b830'; eligLabel='Moderate Eligibility';  multiplier=36; foirLimit=0.45; }
  else if(score >= 550) { eligColor='#e07d30'; eligLabel='Limited Eligibility';   multiplier=20; foirLimit=0.35; }
  else                  { eligColor='#e04848'; eligLabel='Low Eligibility';        multiplier=8;  foirLimit=0.25; }

  // Sum existing EMIs
  const totalEMI = tradeLines.reduce((sum, tlp) => {
    const emi = safeInt(tlp.Tradeline?.GrantedTrade?.EMIAmount, 0);
    return sum + (emi > 0 ? emi : 0);
  }, 0);

  // Use manual income if provided, otherwise estimate from EMI data or score
  let estimatedMonthlyIncome;
  const hasManualIncome = manualIncome && manualIncome > 0;
  if(hasManualIncome){
    estimatedMonthlyIncome = manualIncome;
  } else if(totalEMI > 0){
    estimatedMonthlyIncome = Math.round(totalEMI / 0.40);
  } else {
    estimatedMonthlyIncome = (score - 300) * 1200;
  }

  // Eligible EMI capacity = (income × FOIR) − existing EMIs
  const maxNewEMI = Math.max((estimatedMonthlyIncome * foirLimit) - totalEMI, 0);

  // Loan amount = maxNewEMI × tenure multiplier
  const rawElig = Math.round((maxNewEMI * multiplier) / 100000) * 100000;
  const eligAmt = Math.max(rawElig, 0);

  // Format amount
  let eligAmtDisplay;
  if(eligAmt <= 0)              eligAmtDisplay = '—';
  else if(eligAmt >= 10000000)  eligAmtDisplay = '₹' + (eligAmt/10000000).toFixed(2) + ' Cr';
  else                          eligAmtDisplay = '₹' + (eligAmt/100000).toFixed(1) + ' L';

  // Score bar percentage (300–900 range)
  const barPct = Math.round(((score - 300) / 600) * 100);

  // Suggested loan types by score
  let loanTypes;
  if(score >= 750)      loanTypes = 'Home Loan, Car Loan, Personal Loan';
  else if(score >= 700) loanTypes = 'Car Loan, Personal Loan, Credit Card';
  else if(score >= 650) loanTypes = 'Secured Loan, Gold Loan';
  else if(score >= 550) loanTypes = 'Gold Loan, Small Secured Loan';
  else                  loanTypes = 'Very Limited — Improve Score First';

  // Likely interest rate range
  let rateRange;
  if(score >= 750)      rateRange = '8.5% – 10.5%';
  else if(score >= 700) rateRange = '10.5% – 13%';
  else if(score >= 650) rateRange = '13% – 16%';
  else if(score >= 550) rateRange = '16% – 22%';
  else                  rateRange = '22%+';

  return { eligAmt, eligAmtDisplay, eligColor, eligLabel, barPct, loanTypes, rateRange,
           estimatedMonthlyIncome, totalEMI, maxNewEMI, hasManualIncome, foirLimit };
}

// ── MAIN RENDER ──────────────────────────────────────────────────
function renderReport(apiData){
  const tlcr = getReportRoot(apiData);
  if(!tlcr){ document.getElementById('reportContent').innerHTML='<p style="color:var(--muted);text-align:center;padding:40px">Could not parse report structure. Check the Debug panel below.</p>'; document.getElementById('reportSection').style.display='block'; return; }

  const borrower = tlcr.Borrower || {};
  const scoreData = extractScore(tlcr);
  const score = scoreData?.score || null;
  const s = score || 0;

  // Colour & label
  let sc='#555', sl='No Score';
  if(score){ if(s>=750){sc='#1db954';sl='Excellent';}else if(s>=700){sc='#5ec97a';sl='Very Good';}else if(s>=650){sc='#e8b830';sl='Good';}else if(s>=550){sc='#e07d30';sl='Fair';}else{sc='#e04848';sl='Poor';} }

  // SVG dial
  const pct  = score ? Math.round(((s-300)/600)*283) : 0;
  const dialSVG = `<svg width="170" height="170" viewBox="0 0 170 170">
    <circle cx="85" cy="85" r="72" fill="none" stroke="#1e1e1e" stroke-width="12"/>
    <circle cx="85" cy="85" r="72" fill="none" stroke="${sc}" stroke-width="12"
      stroke-dasharray="${Math.round(pct/283*452)} 452"
      stroke-linecap="round" style="transition:stroke-dasharray .8s ease"/>
  </svg>`;

  const bn = borrower.BorrowerName?.Name;
  const fullName = [bn?.Forename, bn?.Surname].filter(Boolean).join(' ') || formData.name;
  const dob = fmtDate(borrower.Birth?.BirthDate ? `${borrower.Birth.BirthDate.year}-${String(borrower.Birth.BirthDate.month).padStart(2,'0')}-${String(borrower.Birth.BirthDate.day).padStart(2,'0')}` : '');
  const phones = Array.isArray(borrower.BorrowerTelephone) ? borrower.BorrowerTelephone : [];
  const phone  = phones[0]?.PhoneNumber?.Number || formData.mobile;
  const ids = borrower.IdentifierPartition?.Identifier || [];
  const panId = ids.find(i=>i.ID?.IdentifierName==='TaxId')?.ID?.Id || formData.pan;
  const emp = borrower.Employer;
  const occupation = emp?.OccupationCode?.description || '';

  // Score factors
  const FACTOR_MAP = {
    '53': { ico:'⏰', title:'Payment Delays Detected',  desc:'You have missed payments (>91 days) in the last 12 months. Pay on or before due dates to improve your score.' },
    '08': { ico:'💳', title:'High Credit Utilization',   desc:'Your credit card usage is too high compared to the limit. Keep utilization below 30% for a better score.' },
    '14': { ico:'📋', title:'Thin Credit History',        desc:'You have very few active credit accounts. Having a healthy mix of credit types helps your score.' },
    '21': { ico:'🔍', title:'Too Many Enquiries',         desc:'Multiple loan/card applications in a short period signal credit-hungry behaviour to lenders.' },
    '22': { ico:'⚖️', title:'Proportion of Secured Loans', desc:'Your credit mix lacks secured loans (home/auto). A balance of secured and unsecured credit is preferred.' },
    '25': { ico:'🏦', title:'No Recent Activity',          desc:'Your accounts show low or no recent credit activity. Active credit usage demonstrates repayment ability.' },
    '40': { ico:'🚨', title:'Delinquent Account',          desc:'One or more accounts have been reported delinquent. Clearing overdue amounts promptly will help.' },
  };
  const factors = (borrower.CreditScore?.CreditScoreFactor||[]).filter(f=>f.bureauCode && f.bureauCode!=='00');

  // Trade lines
  const tradeLines = Array.isArray(tlcr.TradeLinePartition) ? tlcr.TradeLinePartition : [];

  // Inquiries
  const inquiries = Array.isArray(tlcr.InquiryPartition) ? tlcr.InquiryPartition : [];

  // Account stats
  let activeCount=0, closedCount=0, overdueCount=0, totalSanctioned=0, totalBalance=0;
  tradeLines.forEach(tlp=>{
    const tl = tlp.Tradeline || {};
    const gt = tl.GrantedTrade || {};
    const amtDue = safeInt(gt.amountPastDue,0);
    const bal    = safeInt(tl.currentBalance,0);
    const sanc   = safeInt(tl.highBalance,0);
    if(amtDue>0) overdueCount++;
    if(tl.dateClosed) closedCount++; else activeCount++;
    if(bal>0) totalBalance += bal;
    if(sanc>0) totalSanctioned += sanc;
  });
  const inq6m = inquiries.filter(iq=>{ const d=new Date(iq.Inquiry?.inquiryDate); const diff=(new Date()-d)/(1000*60*60*24*30); return diff<=6; }).length;
  const totalAccounts = tradeLines.length;

  // ── Loan Eligibility ──
  // Store globals for live recalc when user types income
  window._eligScore      = score;
  window._eligTradeLines = tradeLines;

  const elig = calcLoanEligibility(score, tradeLines, 0);
  let eligCardHtml = '';
  if(elig){
    eligCardHtml = `
    <div class="elig-card">
      <div class="rcard-title">🏦 Estimated Loan Eligibility</div>

      <!-- Income Selector Row -->
      <div class="elig-income-row">
        <div class="elig-income-label">Select Your Monthly Income</div>
        <div class="elig-preset-grid">
          <button class="elig-preset" onclick="setIncome(15000,this)">₹15,000</button>
          <button class="elig-preset" onclick="setIncome(25000,this)">₹25,000</button>
          <button class="elig-preset" onclick="setIncome(35000,this)">₹35,000</button>
          <button class="elig-preset" onclick="setIncome(50000,this)">₹50,000</button>
          <button class="elig-preset" onclick="setIncome(75000,this)">₹75,000</button>
          <button class="elig-preset" onclick="setIncome(100000,this)">₹1 Lakh</button>
          <button class="elig-preset" onclick="setIncome(150000,this)">₹1.5 Lakh</button>
          <button class="elig-preset" onclick="setIncome(200000,this)">₹2 Lakh+</button>
        </div>
        <div class="elig-slider-wrap">
          <input type="range" id="incomeSlider" class="elig-slider" min="5000" max="500000" step="5000" value="50000"
            oninput="setIncomeFromSlider(this.value)">
          <div class="elig-slider-labels">
            <span>₹5,000</span>
            <span id="sliderValDisplay" style="color:var(--g2);font-weight:700;font-family:var(--font-m)">₹50,000</span>
            <span>₹5 Lakh+</span>
          </div>
        </div>
        <div class="elig-income-hint" id="incomeHint">Select a preset or drag slider to calculate eligibility</div>
      </div>

      <!-- VERDICT BANNER — shown after income selected -->
      <div class="elig-verdict" id="eligVerdict">
        <div class="ev-icon" id="evIcon">🏦</div>
        <div class="ev-body">
          <div class="ev-verdict" id="evTitle">—</div>
          <div class="ev-sub" id="evSub">Select your income above to see your loan eligibility verdict.</div>
          <div class="ev-pills" id="evPills"></div>
        </div>
        <div class="ev-amt-wrap">
          <div class="ev-amt-label">Max Loan Amount</div>
          <div class="ev-amt" id="evAmt">—</div>
          <div style="font-size:10px;color:var(--muted);margin-top:4px" id="evRate"></div>
        </div>
      </div>

      <!-- Score bar always visible -->
      <div class="elig-bar-wrap">
        <div class="elig-bar-bg">
          <div class="elig-bar-fill" id="eligBarFill" style="width:${elig.barPct}%;background:linear-gradient(90deg,${elig.eligColor}88,${elig.eligColor})"></div>
        </div>
        <div class="elig-bar-labels"><span>300 (Poor)</span><span>CIBIL Score: ${score}</span><span>900 (Excellent)</span></div>
      </div>

      <!-- Stats grid -->
      <div class="elig-grid">
        <div class="elig-item">
          <div class="elig-item-k">Monthly Income</div>
          <div class="elig-item-v" id="eligIncomeDisplay">—</div>
        </div>
        <div class="elig-item">
          <div class="elig-item-k">Existing EMI Outflow</div>
          <div class="elig-item-v">${elig.totalEMI > 0 ? '₹'+elig.totalEMI.toLocaleString('en-IN') : '—'}</div>
        </div>
        <div class="elig-item">
          <div class="elig-item-k">Available EMI Capacity</div>
          <div class="elig-item-v" id="eligEmiCapacity">—</div>
        </div>
        <div class="elig-item">
          <div class="elig-item-k">Interest Rate Range</div>
          <div class="elig-item-v" id="eligRateDisplay" style="color:var(--g2)">${elig.rateRange}</div>
        </div>
        <div class="elig-item">
          <div class="elig-item-k">FOIR Applied</div>
          <div class="elig-item-v" id="eligFoir">${Math.round(elig.foirLimit*100)}%</div>
        </div>
        <div class="elig-item">
          <div class="elig-item-k">CIBIL Score Band</div>
          <div class="elig-item-v" style="color:${elig.eligColor}">${elig.eligLabel.replace(' Eligibility','')}</div>
        </div>
      </div>
      <div class="elig-disclaimer">⚠ Indicative estimate. Actual eligibility depends on income proof, employer profile, lender policy and credit history. Contact a loan advisor for a precise assessment.</div>
    </div>\`;
  }

  // Score factor chips
  let factorHtml = '';
  if(factors.length){
    const chips = factors.map(f=>{
      const code = f.bureauCode;
      const known = FACTOR_MAP[code];
      let fallbackDesc = '';
      if(!known){
        const raw = (f.FactorText?.[0]||'').replace(/^(explain:|factor:)\s*/i,'').trim();
        const firstSentence = raw.split(/\.\s/)[0];
        fallbackDesc = firstSentence.length > 120 ? firstSentence.slice(0,117)+'…' : firstSentence;
      }
      const ico   = known?.ico   || '⚠️';
      const title = known?.title || 'Score Impact Factor';
      const desc  = known?.desc  || fallbackDesc;
      return `<div class="factor-chip">
        <div class="factor-chip-ico">${ico}</div>
        <div class="factor-chip-body">
          <div class="factor-chip-title">${title}</div>
          <div class="factor-chip-desc">${desc}</div>
        </div>
      </div>`;
    }).join('');
    factorHtml = `<div class="factors-card"><div class="rcard-title">Score Impact Factors</div><div class="factor-chips">${chips}</div></div>`;
  }

  // Account Cards
  let accCardsHtml = '';
  if(tradeLines.length){
    tradeLines.forEach(tlp=>{
      const tl  = tlp.Tradeline  || {};
      const gt  = tl.GrantedTrade || {};
      const ph  = gt.PayStatusHistory?.MonthlyPayStatus || [];
      const isClosed = !!tl.dateClosed;
      const amtDue   = safeInt(gt.amountPastDue,0);
      let badgeCls='b-closed', badgeTxt='Closed';
      if(!isClosed && amtDue>0){ badgeCls='b-overdue'; badgeTxt='Overdue'; }
      else if(!isClosed){ badgeCls='b-active'; badgeTxt='Active'; }
      const woff = safeInt(tl.writtenOffAmtTotal,-1);
      if(woff>0){ badgeCls='b-written'; badgeTxt='Written-Off'; }

      const bankName = tl.creditorName || '—';
      const accType  = accountTypeName(tlp.accountTypeSymbol || gt.AccountType?.symbol);
      const opened   = fmtDate(tl.dateOpened);
      const closed   = tl.dateClosed ? fmtDate(tl.dateClosed) : '—';
      const lastPay  = fmtDate(gt.dateLastPayment);
      const balance  = fmtAmt(tl.currentBalance);
      const sanctioned = fmtAmt(tl.highBalance);
      const emi      = fmtAmt(gt.EMIAmount);
      const rate     = (gt.interestRate && gt.interestRate!=='-1.00' && gt.interestRate!=='-1') ? gt.interestRate+'%' : '—';
      const tenure   = (gt.termMonths && safeInt(gt.termMonths)>0) ? gt.termMonths+' mo' : '—';
      const overdue  = amtDue>0 ? `<span style="color:var(--red)">₹${amtDue.toLocaleString('en-IN')}</span>` : '<span style="color:var(--green)">₹0</span>';

      accCardsHtml += `
      <div class="acc-card">
        <div class="acc-head">
          <div><div class="acc-bank">${bankName}</div><div class="acc-type">${accType}</div></div>
          <span class="badge ${badgeCls}">${badgeTxt}</span>
        </div>
        <div class="acc-rows">
          <div class="acc-item"><div class="acc-item-k">Sanctioned</div><div class="acc-item-v">${sanctioned}</div></div>
          <div class="acc-item"><div class="acc-item-k">Balance</div><div class="acc-item-v">${balance}</div></div>
          <div class="acc-item"><div class="acc-item-k">Opened</div><div class="acc-item-v">${opened}</div></div>
          <div class="acc-item"><div class="acc-item-k">${isClosed?'Closed':'Last Pay'}</div><div class="acc-item-v">${isClosed?closed:lastPay}</div></div>
          <div class="acc-item"><div class="acc-item-k">EMI</div><div class="acc-item-v">${emi}</div></div>
          <div class="acc-item"><div class="acc-item-k">Rate</div><div class="acc-item-v">${rate}</div></div>
          <div class="acc-item"><div class="acc-item-k">Tenure</div><div class="acc-item-v">${tenure}</div></div>
          <div class="acc-item"><div class="acc-item-k">Overdue</div><div class="acc-item-v">${overdue}</div></div>
        </div>
        ${buildPayDots(ph)}
      </div>`;
    });
  } else {
    accCardsHtml = `<div style="background:rgba(255,255,255,.02);border:1.5px dashed var(--line2);border-radius:var(--r2);padding:28px;text-align:center;color:var(--muted);font-size:13px;">No credit accounts found in this report.</div>`;
  }

  // Inquiry Cards
  let inqHtml = '';
  inquiries.forEach(iq=>{
    const i = iq.Inquiry || {};
    inqHtml += `
    <div class="inq-card">
      <div class="inq-left">
        <div class="inq-name">${i.subscriberName||'—'}</div>
        <div class="inq-date">${fmtDate(i.inquiryDate)}</div>
      </div>
      <div class="inq-right">
        <div class="inq-amt">${fmtAmt(i.amount)}</div>
        <div class="inq-type">${inquiryPurpose(i.inquiryType)}</div>
      </div>
    </div>`;
  });

  // Addresses
  const addrs = Array.isArray(borrower.BorrowerAddress) ? borrower.BorrowerAddress : [];
  let addrHtml = '';
  addrs.forEach((a,idx)=>{
    const ca = a.CreditAddress || {};
    const origin = a.Origin?.symbol || '';
    const street = ca.StreetAddress || '';
    const pin    = ca.PostalCode || '';
    if(!street && !pin) return;
    addrHtml += `<div class="row-item"><span class="ri-label">${origin||'Address '+(idx+1)}</span><span class="ri-val" style="font-size:12px;font-family:var(--font-b);text-align:right;max-width:260px;line-height:1.5">${street}${pin?' – '+pin:''}</span></div>`;
  });

  // ── Final HTML ──
  const html = `
  <!-- SCORE HERO -->
  <div class="report-hero">
    <div class="score-dial">
      ${dialSVG}
      <div class="score-dial-inner">
        <div class="sdial-num" style="color:${sc}">${score||'N/A'}</div>
        <div class="sdial-max">/ 900</div>
        <div class="sdial-lbl" style="background:${sc}22;color:${sc}">${sl}</div>
      </div>
    </div>
    <div class="rdetails">
      <h2>${fullName}</h2>
      <div class="rpan">PAN: ${panId}</div>
      <div class="rmeta">
        <div class="rmeta-item"><div class="rmeta-key">Date of Birth</div><div class="rmeta-val">${dob||'—'}</div></div>
        <div class="rmeta-item"><div class="rmeta-key">Gender</div><div class="rmeta-val">${(borrower.Gender||formData.gender||'—').toUpperCase()}</div></div>
        <div class="rmeta-item"><div class="rmeta-key">Mobile</div><div class="rmeta-val">+91 ${phone}</div></div>
        <div class="rmeta-item"><div class="rmeta-key">Report Date</div><div class="rmeta-val">${new Date().toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'})}</div></div>
        ${occupation?`<div class="rmeta-item" style="grid-column:1/-1"><div class="rmeta-key">Occupation</div><div class="rmeta-val" style="font-family:var(--font-b)">${occupation}</div></div>`:''}
        ${scoreData?.rank?`<div class="rmeta-item"><div class="rmeta-key">Population Rank</div><div class="rmeta-val">${scoreData.rank}%</div></div>`:''}
      </div>
    </div>
  </div>

  ${factorHtml}

  ${eligCardHtml}

  <!-- SUMMARY + EXPOSURE -->
  <div class="report-grid">
    <div class="rcard">
      <div class="rcard-title">Account Summary</div>
      <div class="row-item"><span class="ri-label">Total Accounts</span><span class="ri-val">${totalAccounts}</span></div>
      <div class="row-item"><span class="ri-label">Active</span><span class="ri-val" style="color:var(--green)">${activeCount}</span></div>
      <div class="row-item"><span class="ri-label">Closed</span><span class="ri-val" style="color:var(--muted)">${closedCount}</span></div>
      <div class="row-item"><span class="ri-label">Overdue</span><span class="ri-val" style="color:${overdueCount>0?'var(--red)':'var(--white)'}">${overdueCount}</span></div>
      <div class="row-item"><span class="ri-label">Inquiries (6 mo)</span><span class="ri-val">${inq6m}</span></div>
      <div class="row-item"><span class="ri-label">Total Inquiries</span><span class="ri-val">${inquiries.length}</span></div>
    </div>
    <div class="rcard">
      <div class="rcard-title">Credit Exposure</div>
      <div class="row-item"><span class="ri-label">Total Sanctioned</span><span class="ri-val">${fmtAmt(totalSanctioned)}</span></div>
      <div class="row-item"><span class="ri-label">Current Balance</span><span class="ri-val">${fmtAmt(totalBalance)}</span></div>
      <div class="row-item"><span class="ri-label">Utilization</span><span class="ri-val">${totalSanctioned>0?Math.round((totalBalance/totalSanctioned)*100)+'%':'—'}</span></div>
      ${addrHtml ? `</div></div><div class="rcard full"><div class="rcard-title">Addresses <span class="cnt">${addrs.length}</span></div>${addrHtml}` : ''}
    </div>
  </div>

  <!-- CREDIT ACCOUNTS -->
  <div class="rcard full" style="margin-bottom:16px">
    <div class="rcard-title">Credit Accounts <span class="cnt">${totalAccounts}</span></div>
    <div class="acc-list">${accCardsHtml}</div>
    <div style="margin-top:14px;display:flex;gap:16px;flex-wrap:wrap;font-size:11px;color:var(--muted)">
      <span><span class="ph-dot ok" style="display:inline-block;vertical-align:middle;margin-right:4px"></span>On Time</span>
      <span><span class="ph-dot late" style="display:inline-block;vertical-align:middle;margin-right:4px"></span>Late / Overdue</span>
      <span><span class="ph-dot written" style="display:inline-block;vertical-align:middle;margin-right:4px"></span>Written-Off</span>
      <span><span class="ph-dot na" style="display:inline-block;vertical-align:middle;margin-right:4px"></span>No Data</span>
    </div>
  </div>

  <!-- INQUIRIES -->
  ${inquiries.length ? `
  <div class="rcard full" style="margin-bottom:16px">
    <div class="rcard-title">Loan Enquiries <span class="cnt">${inquiries.length}</span></div>
    <div class="inq-list">${inqHtml}</div>
  </div>` : ''}
  `;

  document.getElementById('reportContent').innerHTML = html;
  document.getElementById('reportSection').style.display='block';
  setTimeout(()=>document.getElementById('reportSection').scrollIntoView({behavior:'smooth'}),300);
}

// ── DEBUG PANEL ───────────────────────────────────────────────────
function populateDebug(data, rawText, reqInfo){
  try{ document.getElementById('debugFormatted').textContent = JSON.stringify(data,null,2); }
  catch(e){ document.getElementById('debugFormatted').textContent = rawText; }
  document.getElementById('debugRaw').textContent = rawText;
  document.getElementById('debugReqInfo').innerHTML = `
    <div><strong style="color:var(--g2)">Endpoint:</strong> <span style="color:#7a7a7a">/srv3/credit-report/transunion</span></div>
    <div><strong style="color:var(--g2)">Name:</strong> <span style="color:#7a7a7a">${reqInfo.name}</span></div>
    <div><strong style="color:var(--g2)">Mobile:</strong> <span style="color:#7a7a7a">${reqInfo.mobile}</span></div>
    <div><strong style="color:var(--g2)">PAN:</strong> <span style="color:#7a7a7a">${reqInfo.pan}</span></div>
    <div><strong style="color:var(--g2)">Gender:</strong> <span style="color:#7a7a7a">${reqInfo.gender}</span></div>
    <div><strong style="color:var(--g2)">Consent:</strong> <span style="color:#7a7a7a">Y</span></div>
  `;
}
function toggleDebugPanel(){
  const body=document.getElementById('debugBody'), btn=document.getElementById('debugToggleBtn');
  const open=body.classList.toggle('open');
  btn.textContent=open?'HIDE ▲':'SHOW ▼';
}
function switchTab(name){
  const tabs=['formatted','raw','reqinfo'];
  document.querySelectorAll('.debug-tab').forEach((t,i)=>t.classList.toggle('active',tabs[i]===name));
  document.querySelectorAll('.debug-content').forEach(c=>c.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
}

// ── LIVE ELIGIBILITY RECALCULATOR ────────────────────────────────
// Loan type pill config: {label, color}
const LOAN_PILLS = {
  'Home Loan':        { color:'#5ec97a' },
  'Car Loan':         { color:'#e8b830' },
  'Personal Loan':    { color:'#e8b830' },
  'Credit Card':      { color:'#c9970f' },
  'Secured Loan':     { color:'#e07d30' },
  'Gold Loan':        { color:'#c9970f' },
  'Small Secured Loan':{ color:'#e07d30' },
};

function recalcEligibility(income){
  income = parseInt(income) || 0;
  const score = window._eligScore;
  const tl    = window._eligTradeLines || [];
  if(!score) return;

  const elig = calcLoanEligibility(score, tl, income);
  if(!elig) return;

  // ── Determine verdict ──────────────────────────────────────────
  // YES: score>=650 and eligAmt>0 and maxNewEMI>0
  // MAYBE: score>=550 and some capacity
  // NO: score<550 or no capacity
  let verdictType, icon, title, sub;
  if(income <= 0){
    // No income selected yet — don't show verdict
    const vEl = document.getElementById('eligVerdict');
    if(vEl){ vEl.classList.remove('show','yes','no','maybe'); }
    const hintEl = document.getElementById('incomeHint');
    if(hintEl){ hintEl.textContent = 'Select a preset or drag slider to calculate eligibility'; hintEl.classList.remove('has-income'); }
    const incEl = document.getElementById('eligIncomeDisplay');
    if(incEl) incEl.textContent = '—';
    return;
  }

  if(score >= 700 && elig.eligAmt >= 100000){
    verdictType='yes'; icon='✅';
    title = 'LOAN ELIGIBLE';
    sub = 'Your credit profile & income qualify you for loans. Banks are likely to approve your application.';
  } else if(score >= 650 && elig.eligAmt >= 50000){
    verdictType='yes'; icon='✅';
    title = 'LIKELY ELIGIBLE';
    sub = 'Good score with adequate income. Most lenders will consider your application favourably.';
  } else if(score >= 550 && elig.maxNewEMI > 2000){
    verdictType='maybe'; icon='⚠️';
    title = 'CONDITIONALLY ELIGIBLE';
    sub = 'Fair score — eligibility depends heavily on income proof, employer type, and lender policy.';
  } else if(score < 550){
    verdictType='no'; icon='❌';
    title = 'LOW ELIGIBILITY';
    sub = 'Score below 550 makes loan approval difficult. Focus on improving your CIBIL score first.';
  } else {
    verdictType='maybe'; icon='⚠️';
    title = 'LIMITED ELIGIBILITY';
    sub = 'Income appears insufficient for significant loan amounts after existing EMI obligations.';
  }

  // ── Update verdict banner ──────────────────────────────────────
  const vEl     = document.getElementById('eligVerdict');
  const iconEl  = document.getElementById('evIcon');
  const titleEl = document.getElementById('evTitle');
  const subEl   = document.getElementById('evSub');
  const amtEl   = document.getElementById('evAmt');
  const rateEl  = document.getElementById('evRate');
  const pillsEl = document.getElementById('evPills');

  if(vEl){
    vEl.className = 'elig-verdict show '+verdictType;
  }
  if(iconEl)  iconEl.textContent  = icon;
  if(titleEl){ titleEl.textContent = title;
    const col = verdictType==='yes'?'#1db954': verdictType==='maybe'?'#e8b830':'#e04848';
    titleEl.style.color = col; }
  if(subEl)   subEl.textContent   = sub;
  if(amtEl){
    amtEl.textContent  = elig.eligAmtDisplay;
    const col = verdictType==='yes'?'#1db954': verdictType==='maybe'?'#e8b830':'#e04848';
    amtEl.style.color = col;
  }
  if(rateEl)  rateEl.textContent  = 'Rate: '+elig.rateRange;

  // Build loan type pills
  if(pillsEl){
    const types = elig.loanTypes.split(', ');
    pillsEl.innerHTML = types.map(t => {
      const cfg = LOAN_PILLS[t.trim()] || { color:'#888' };
      if(verdictType==='no') return '';
      return `<span class="ev-pill" style="color:${cfg.color};border-color:${cfg.color}44;background:${cfg.color}11">${t.trim()}</span>`;
    }).join('');
  }

  // ── Update stats grid ──────────────────────────────────────────
  const hintEl = document.getElementById('incomeHint');
  const incEl  = document.getElementById('eligIncomeDisplay');
  const capaEl = document.getElementById('eligEmiCapacity');

  if(hintEl){
    hintEl.textContent = '✓ Showing eligibility for ₹'+income.toLocaleString('en-IN')+' / month';
    hintEl.classList.add('has-income');
  }
  if(incEl)  incEl.textContent = '₹'+income.toLocaleString('en-IN');
  if(capaEl) capaEl.textContent = elig.maxNewEMI > 0 ? '₹'+Math.round(elig.maxNewEMI).toLocaleString('en-IN') : '—';
}

// Preset button click
function setIncome(amount, btn){
  // Deselect all presets
  document.querySelectorAll('.elig-preset').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');
  // Sync slider
  const slider = document.getElementById('incomeSlider');
  const valDisp = document.getElementById('sliderValDisplay');
  if(slider){ slider.value = Math.min(amount, 500000); }
  if(valDisp){ valDisp.textContent = '₹'+amount.toLocaleString('en-IN'); }
  recalcEligibility(amount);
}

// Slider drag
function setIncomeFromSlider(val){
  const income = parseInt(val);
  const valDisp = document.getElementById('sliderValDisplay');
  if(valDisp) valDisp.textContent = '₹'+income.toLocaleString('en-IN');
  // Deselect all presets (slider overrides)
  document.querySelectorAll('.elig-preset').forEach(b => b.classList.remove('selected'));
  recalcEligibility(income);
}

// ── UTILS ────────────────────────────────────────────────────────
function show(id){ document.getElementById(id).style.display='block'; }
function hide(id){ document.getElementById(id).style.display='none'; }
function resetForm(){
  setStep(1);
  ['mobile','fullName','pan'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('gender').value='';
  document.getElementById('consent').checked=true;
  document.getElementById('reportSection').style.display='none';
  document.getElementById('errRawWrap').style.display='none';
  document.getElementById('reportContent').innerHTML='';
  hideAlert();
  window.scrollTo({top:0,behavior:'smooth'});
}
function toggleFaq(el){ el.classList.toggle('open'); el.nextElementSibling.classList.toggle('open'); }
</script>
</body>
</html>