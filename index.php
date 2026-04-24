<?php
/**
 * VELORA Marketplace — Landing Page (index.php)
 * Developer: Tom
 */
session_start();

// ── Logout handler (replaces broken logout.php) ──
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php?msg=logout');
    exit;
}

// ── Page config ──
$page_title   = 'VELORA — The Curated Marketplace';
$page_desc    = 'VELORA is a premium curated marketplace for unique products and expert services.';
$current_year = date('Y');

// ── Cart item count ─ dibaca dari session yang di-set oleh shoppingCart.php ──
$cart_count = $_SESSION['cart_count'] ?? 3; // default 3 (demo dummy cart)

// ── User session ──
$is_logged_in  = isset($_SESSION['user_id']);
$user_name     = $is_logged_in ? htmlspecialchars($_SESSION['user_name'] ?? '') : '';

// ── Flash message (misal: setelah logout) ──
$flash_msg  = '';
$flash_type = 'default';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'logout') {
        $flash_msg  = 'You have been signed out. See you next time!';
        $flash_type = 'success';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
<link rel="stylesheet" href="assets/css/style.css">
<style>
/* ── HERO ── */
.hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    background: var(--bg);
    padding-top: var(--navbar-h);
}
.hero-bg {
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 80% 60% at 60% 40%, rgba(91,63,248,.13) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 20% 80%, rgba(91,63,248,.08) 0%, transparent 60%);
    z-index: 0;
}
.hero-grid {
    position: absolute;
    inset: 0;
    background-image: linear-gradient(var(--border) 1px, transparent 1px),
                      linear-gradient(90deg, var(--border) 1px, transparent 1px);
    background-size: 48px 48px;
    opacity: .4;
    z-index: 0;
}
.hero-inner {
    position: relative;
    z-index: 1;
    max-width: 760px;
}
.hero-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary-light);
    color: var(--primary);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: var(--radius-full);
    margin-bottom: 28px;
}
.hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(3rem, 7vw, 5.5rem);
    line-height: 1.05;
    color: var(--text-main);
    margin-bottom: 24px;
}
.hero-title em { color: var(--primary); font-style: italic; }
.hero-sub {
    font-size: 1.1rem;
    color: var(--text-muted);
    max-width: 520px;
    margin-bottom: 40px;
    line-height: 1.75;
}
.hero-search {
    display: flex;
    align-items: center;
    gap: 0;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-full);
    padding: 6px 6px 6px 20px;
    box-shadow: var(--shadow-lg);
    max-width: 520px;
    transition: border-color .2s, box-shadow .2s;
}
.hero-search:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px var(--primary-glow), var(--shadow-lg);
}
.hero-search input {
    flex: 1;
    border: none;
    background: transparent;
    color: var(--text-main);
    font-size: 14px;
    outline: none;
}
.hero-search input::placeholder { color: var(--text-soft); }
.hero-cta-row { display: flex; align-items: center; gap: 16px; margin-top: 24px; }
.hero-trust { font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }

/* ── HERO CHARACTERS ── */
@keyframes charFloat  { 0%,100%{transform:translateY(0) rotate(-3deg)} 50%{transform:translateY(-20px) rotate(3deg)} }
@keyframes charBounce { 0%,100%{transform:translateY(0) scale(1)} 38%{transform:translateY(-24px) scale(1.07)} 58%{transform:translateY(-10px) scale(1.02)} }
@keyframes twinkle    { 0%,100%{opacity:.45;transform:scale(1) rotate(0deg)} 50%{opacity:1;transform:scale(1.4) rotate(20deg)} }
.hero-chars { position:absolute; inset:0; pointer-events:none; z-index:1; overflow:hidden; }
@media(max-width:900px){ .hero-chars { display:none; } }
.hc-spark { position:absolute; color:var(--primary); font-style:normal; }
.hc-spark-1 { top:16%; right:19%; font-size:1.3rem; animation:twinkle 2.4s ease-in-out infinite 0s; }
.hc-spark-2 { top:31%; right:9%;  font-size:1rem;   animation:twinkle 3.2s ease-in-out infinite .6s; }
.hc-spark-3 { top:61%; right:27%; font-size:1.1rem; animation:twinkle 2.8s ease-in-out infinite 1.1s; }
.hc-spark-4 { top:11%; right:41%; font-size:.8rem;  animation:twinkle 3.6s ease-in-out infinite .3s; }
.hc-spark-5 { bottom:19%; right:11%; font-size:.9rem; animation:twinkle 2.6s ease-in-out infinite .8s; }
.hc-mascot { position:absolute; top:50%; right:10%; transform:translateY(-55%); animation:charFloat 5s ease-in-out infinite; display:flex; flex-direction:column; align-items:center; gap:8px; }
.hc-mascot-emoji { font-size:6.5rem; line-height:1; filter:drop-shadow(0 16px 36px rgba(91,63,248,.35)); display:block; }
.hc-mascot-shadow { width:55px; height:10px; background:radial-gradient(ellipse, rgba(91,63,248,.2), transparent 70%); border-radius:50%; }
.hc-sec { position:absolute; filter:drop-shadow(0 6px 18px rgba(91,63,248,.2)); }
.hc-sec-1 { font-size:2.8rem; top:19%; right:27%; animation:charBounce 4.5s ease-in-out infinite .4s; }
.hc-sec-2 { font-size:2.2rem; bottom:24%; right:22%; animation:charFloat 6.5s ease-in-out infinite 1s; }
.hc-sec-3 { font-size:1.9rem; top:42%; right:39%; animation:twinkle 4s ease-in-out infinite .7s; }
.hc-bubble { position:absolute; background:var(--surface); border:1.5px solid var(--border); border-radius:14px; padding:10px 14px; box-shadow:var(--shadow-lg); display:flex; align-items:center; gap:10px; backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); white-space:nowrap; }
.hc-bubble-icon { font-size:1.4rem; }
.hc-bubble-name { font-size:13px; font-weight:600; color:var(--text-main); line-height:1.2; }
.hc-bubble-sub  { font-size:11px; color:var(--text-muted); }
.hc-b1 { top:20%; right:41%; animation:charFloat 7s ease-in-out infinite 0s; }
.hc-b2 { bottom:24%; right:5%; animation:charFloat 5.5s ease-in-out infinite 1.8s; }

/* ── STATS ── */
.stats-bar {
    background: var(--primary);
    padding: 28px 0;
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: rgba(255,255,255,.15);
}
.stat-item {
    background: var(--primary);
    text-align: center;
    padding: 16px;
}
.stat-num {
    font-family: 'DM Serif Display', serif;
    font-size: 2.2rem;
    color: #fff;
    line-height: 1;
}
.stat-label { font-size: 12px; color: rgba(255,255,255,.7); margin-top: 4px; }

/* ── CATEGORIES ── */
.cat-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 260px 260px;
    gap: 16px;
}
.cat-card {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
    cursor: pointer;
}
.cat-card:first-child { grid-row: span 2; }
.cat-card-bg {
    position: absolute;
    inset: 0;
    transition: transform .6s var(--ease);
}
.cat-card:hover .cat-card-bg { transform: scale(1.07); }
.cat-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.65) 0%, transparent 55%);
}
.cat-card-label {
    position: absolute;
    bottom: 20px;
    left: 20px;
    color: #fff;
}
.cat-card-label h3 { font-family: 'DM Serif Display', serif; font-size: 1.5rem; }
.cat-card-label span { font-size: 12px; opacity: .75; }

/* Category card image covers */
.cat-card-bg { position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .6s var(--ease); }
.cat-card:hover .cat-card-bg { transform:scale(1.07); }

/* ── FEATURED ── */
.featured-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}
.featured-img-wrap {
    position: relative;
}
.featured-img-wrap::before {
    content: '';
    position: absolute;
    inset: -16px -16px 16px 16px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-lg);
    z-index: 0;
}
.featured-img {
    position: relative;
    z-index: 1;
    background: linear-gradient(135deg,#1a0533,#5B3FF8,#a78bfa);
    border-radius: var(--radius-lg);
    aspect-ratio: 4/5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 5rem;
    overflow: hidden;
}
.featured-tag {
    display: inline-block;
    background: var(--primary);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: var(--radius-full);
    margin-bottom: 16px;
}
.featured-title {
    font-size: 2.6rem;
    margin-bottom: 16px;
    color: var(--text-main);
}
.featured-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    margin-bottom: 20px;
}
.featured-meta span { font-size: 13px; color: var(--text-muted); }
.featured-price { font-size: 2rem; font-weight: 700; color: var(--primary); margin: 16px 0; }
.check-list { list-style: none; display: flex; flex-direction: column; gap: 10px; margin-bottom: 28px; }
.check-list li { display: flex; align-items: center; gap: 10px; font-size: 14px; color: var(--text-muted); }
.check-list li::before { content: '✓'; color: var(--primary); font-weight: 700; }

/* ── PRODUCT OF WEEK ── */
.potw-wrap {
    background: var(--surface);
    border-radius: var(--radius-lg);
    padding: 48px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
    align-items: center;
    border: 1px solid var(--border);
}
.potw-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg,#fbbf24,#f59e0b);
    color: #000;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: var(--radius-full);
    margin-bottom: 16px;
}
.potw-img {
    background: linear-gradient(135deg,#0f0c29,#302b63,#24243e);
    border-radius: var(--radius);
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
    position: relative;
    overflow: hidden;
}
.potw-img::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 30% 30%,rgba(91,63,248,.3),transparent 60%);
}
.potw-title { font-size: 2.2rem; margin-bottom: 12px; }
.potw-avail { color: var(--success); font-size: 13px; font-weight: 600; margin-bottom: 20px; }

/* ── JOURNAL ── */
.journal-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
.journal-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; transition: transform .3s var(--ease), box-shadow .3s var(--ease); }
.journal-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.journal-thumb { aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; }
.jt1 { background: linear-gradient(135deg,#1a1a2e,#16213e); }
.jt2 { background: linear-gradient(135deg,#0d0d0d,#434343); }
.jt3 { background: linear-gradient(135deg,#200122,#6f0000); }
.journal-body { padding: 20px; }
.journal-cat { font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--primary); margin-bottom: 8px; }
.journal-title { font-size: 1rem; font-weight: 600; color: var(--text-main); line-height: 1.4; }

/* ── GLOBAL ANIMATIONS ── */
@keyframes floatA { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(35px,-25px) scale(1.07)} }
@keyframes floatB { 0%,100%{transform:translate(0,0) scale(1)} 50%{transform:translate(-25px,20px) scale(0.93)} }
@keyframes ringPulse { 0%,100%{transform:scale(1);opacity:.45} 50%{transform:scale(1.14);opacity:.85} }
@keyframes glowPulse { 0%,100%{box-shadow:0 0 22px rgba(91,63,248,.3),0 0 0 6px rgba(91,63,248,.08)} 50%{box-shadow:0 0 44px rgba(91,63,248,.55),0 0 0 10px rgba(91,63,248,.16)} }
@keyframes shimmerBorder { 0%{background-position:0% 50%} 100%{background-position:300% 50%} }
@keyframes scrollProgress { from{width:0} to{width:100%} }

/* ── SCROLL PROGRESS BAR ── */
.scroll-prog {
    position: fixed; top:0; left:0; height:3px; z-index:10000;
    background: linear-gradient(90deg, var(--primary), #a78bfa, var(--primary));
    background-size: 200% 100%;
    border-radius: 0 2px 2px 0;
    width: 0%; pointer-events: none;
    animation: shimmerBorder 3s linear infinite;
    transition: width .05s linear;
}

/* ── HERO ANIMATED ORBS ── */
.hero-orb {
    position: absolute; border-radius: 50%; filter: blur(70px);
    pointer-events: none; z-index: 0;
}
.hero-orb-a {
    width: 480px; height: 480px;
    background: radial-gradient(circle, rgba(91,63,248,.2), transparent 70%);
    top: -120px; right: -80px;
    animation: floatA 11s ease-in-out infinite;
}
.hero-orb-b {
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(167,139,250,.14), transparent 70%);
    bottom: 40px; left: -60px;
    animation: floatB 14s ease-in-out infinite;
}

/* ── TEAM SECTION — PREMIUM DARK ── */
.team-section {
    background: #09081a;
    position: relative; overflow: hidden;
    padding: 96px 0 112px;
}
.team-blob {
    position: absolute; border-radius: 50%; filter: blur(90px); pointer-events: none;
}
.team-blob-1 {
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(91,63,248,.12), transparent 70%);
    top: -200px; right: -200px; animation: floatA 13s ease-in-out infinite;
}
.team-blob-2 {
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(167,139,250,.09), transparent 70%);
    bottom: -150px; left: -100px; animation: floatB 11s ease-in-out infinite;
}
.team-header { text-align:center; margin-bottom:64px; position:relative; z-index:1; }
.team-header .section-eyebrow { color:#a78bfa; }
.team-header .section-eyebrow::before { background:#a78bfa; }
.team-header h2 { color:#fff; font-size:clamp(2rem,5vw,3rem); margin-bottom:16px; }
.team-header p { color:rgba(255,255,255,.5); max-width:480px; margin:0 auto; line-height:1.75; }

.team-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    position: relative; z-index: 1;
}

/* Animated gradient border via wrapper */
.team-card-wrap {
    background: linear-gradient(135deg, rgba(91,63,248,.55), rgba(167,139,250,.35), rgba(91,63,248,.55));
    background-size: 300% 300%;
    border-radius: 26px; padding: 1.5px;
    animation: shimmerBorder 4s linear infinite;
    transition: transform .4s cubic-bezier(.2,0,.1,1), box-shadow .4s;
}
.team-card-wrap:hover {
    transform: translateY(-14px) scale(1.02);
    box-shadow: 0 32px 80px rgba(91,63,248,.28), 0 0 60px rgba(91,63,248,.15);
}
.team-card {
    background: linear-gradient(160deg, #151228 0%, #0e0c1e 100%);
    border-radius: 25px; overflow: hidden;
    position: relative; display: flex; flex-direction: column;
    height: 100%;
}
/* Number watermark */
.team-card-num {
    position: absolute; top:14px; right:20px;
    font-family:'DM Serif Display',serif; font-size:4rem; line-height:1;
    color:rgba(255,255,255,.04); pointer-events:none; user-select:none;
}
/* Photo area */
.team-photo-area {
    background: linear-gradient(180deg, rgba(91,63,248,.18) 0%, rgba(91,63,248,.04) 100%);
    padding: 36px 0 30px;
    display: flex; justify-content: center; align-items: center;
    position: relative;
}
.team-photo-area::after {
    content:''; position:absolute; bottom:0; left:0; right:0; height:48px;
    background: linear-gradient(to bottom, transparent, #0e0c1e);
}
/* Pulsing rings */
.t-ring {
    position: absolute; border-radius: 50%; animation: ringPulse 3s ease-in-out infinite;
}
.t-ring-1 { width:140px; height:140px; border:1.5px solid rgba(91,63,248,.4);  animation-delay:0s;   }
.t-ring-2 { width:172px; height:172px; border:1px solid rgba(167,139,250,.2);   animation-delay:.8s;  }
.t-ring-3 { width:206px; height:206px; border:1px solid rgba(91,63,248,.09);    animation-delay:1.6s; }
/* Avatar */
.team-avatar {
    width: 110px; height: 110px; border-radius: 50%; object-fit: cover;
    border: 3px solid rgba(91,63,248,.7);
    display: block; position: relative; z-index: 2;
    animation: glowPulse 4s ease-in-out infinite;
    transition: transform .4s;
}
.team-card-wrap:hover .team-avatar { transform: scale(1.07); }
/* Card body */
.team-card-body {
    padding: 20px 22px 28px; flex: 1;
    display: flex; flex-direction: column; align-items: center; text-align: center;
}
.team-name { font-family:'DM Serif Display',serif; font-size:1.3rem; color:#fff; margin-bottom:10px; letter-spacing:-.2px; }
.team-role {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(91,63,248,.2); border: 1px solid rgba(91,63,248,.35);
    color: #a78bfa; font-size:10px; font-weight:700;
    letter-spacing:.12em; text-transform:uppercase;
    padding:5px 14px; border-radius:100px; margin-bottom:16px;
}
.team-role::before { content:'◈'; font-size:8px; }
.team-desc { font-size:13px; color:rgba(255,255,255,.45); line-height:1.7; flex:1; margin-bottom:20px; }
.team-links { display:flex; gap:8px; justify-content:center; }
.team-links a {
    width:32px; height:32px; border-radius:50%;
    background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1);
    display:flex; align-items:center; justify-content:center;
    color:rgba(255,255,255,.4); font-size:.85rem; transition:all .25s;
}
.team-links a:hover { background:rgba(91,63,248,.3); border-color:rgba(91,63,248,.5); color:#a78bfa; transform:translateY(-3px); }

/* ── TEAM SECTION — LIGHT MODE OVERRIDES ── */
[data-theme="light"] .team-section { background: linear-gradient(135deg,#f5f3ff 0%,#ede9fe 100%); }
[data-theme="light"] .team-card { background: linear-gradient(160deg,#ffffff 0%,#f0ebff 100%); }
[data-theme="light"] .team-photo-area::after { background: linear-gradient(to bottom,transparent,#f0ebff); }
[data-theme="light"] .team-photo-area { background: linear-gradient(180deg,rgba(91,63,248,.1) 0%,rgba(91,63,248,.03) 100%); }
[data-theme="light"] .team-card-num { color:rgba(91,63,248,.07); }
[data-theme="light"] .team-header h2 { color:#2D1B69; }
[data-theme="light"] .team-header p { color:rgba(76,29,149,.65); }
[data-theme="light"] .team-name { color:#2D1B69; }
[data-theme="light"] .team-desc { color:rgba(76,29,149,.7); }
[data-theme="light"] .team-links a { background:rgba(91,63,248,.08); border-color:rgba(91,63,248,.2); color:#6D28D9; }
[data-theme="light"] .team-links a:hover { background:rgba(91,63,248,.2); color:#5B3FD4; }
[data-theme="light"] .team-card-wrap { background:linear-gradient(135deg,rgba(91,63,248,.45),rgba(167,139,250,.28),rgba(91,63,248,.45)); }

/* ── TEAM CARD CHARACTERS ── */
.team-card-wrap { position: relative; }
.team-grid { overflow: visible; }
.tc-char-wrap { position:absolute; top:-58px; left:50%; transform:translateX(-50%); z-index:10; pointer-events:none; }
.tc-char { width:76px; height:auto; animation:charFloat 4.5s ease-in-out infinite; filter:drop-shadow(0 8px 24px rgba(91,63,248,.45)); }
.team-card-wrap:nth-child(2) .tc-char { animation-delay:.6s; }
.team-card-wrap:nth-child(3) .tc-char { animation-delay:1.2s; }
.team-card-wrap:nth-child(4) .tc-char { animation-delay:1.8s; }

/* ── RESPONSIVE ── */
@media(max-width:900px){
    .cat-grid { grid-template-columns:1fr 1fr; }
    .cat-card:first-child { grid-row:auto; }
    .featured-grid,.potw-wrap { grid-template-columns:1fr; }
    .stats-grid { grid-template-columns:repeat(2,1fr); }
    .journal-grid { grid-template-columns:1fr 1fr; }
    .team-grid { grid-template-columns:repeat(2,1fr); }
    .potw-wrap { padding:36px 28px; }
}
@media(max-width:640px){
    .cat-grid { grid-template-columns:1fr; }
    .hero-cta-row { flex-direction:column; align-items:flex-start; }
    .journal-grid { grid-template-columns:1fr; }
    .potw-wrap { padding:24px 18px; gap:32px; }
    .hero-search { flex-wrap:wrap; gap:8px; padding:10px 14px; }
    .hero-search input { min-width:0; }
    .stats-grid { grid-template-columns:repeat(2,1fr); }
    .featured-title { font-size:2rem; }
}
@media(max-width:480px){
    .team-grid { grid-template-columns:1fr; gap:14px; }
    .potw-title { font-size:1.8rem; }
    .hero-orb-a,.hero-orb-b { display:none; }
}

/* ── SECTION CHARACTERS ── */
.sc { position:absolute; pointer-events:none; user-select:none; line-height:1; z-index:0; }
.sc-fl  { animation:charFloat  5.5s ease-in-out infinite; }
.sc-fl2 { animation:charFloat  7.5s ease-in-out infinite 1.3s; }
.sc-bo  { animation:charBounce 4.5s ease-in-out infinite 0.5s; }
.sc-tw  { animation:twinkle   3s   ease-in-out infinite 0.4s; }
.sc-tw2 { animation:twinkle   2.5s ease-in-out infinite 1.1s; }
.sc-fl3 { animation:charFloat  6s   ease-in-out infinite 2s; }
/* Team card unique emoji badge */
.tc-badge {
    position:absolute; top:14px; left:16px; z-index:4;
    font-size:2.2rem; line-height:1;
    filter:drop-shadow(0 4px 14px rgba(91,63,248,.45));
    animation:charFloat 4.5s ease-in-out infinite;
}
/* Give stats-bar and footer a stacking context */
.stats-bar { position:relative; overflow:hidden; }
.nx-footer { position:relative; overflow:hidden; }
/* ── ILLUSTRATED CHARACTER ── */
.hero-char-img {
    position: absolute; right: 0; bottom: 0;
    width: 300px; max-width: 32vw;
    animation: heroCharAnim 10s ease-in-out infinite;
    pointer-events: none; z-index: 1;
    filter: drop-shadow(0 20px 50px rgba(91,63,248,.3));
}
@keyframes heroCharAnim {
    0%   { transform: translateY(0px)   rotate(0deg); }
    25%  { transform: translateY(-16px) rotate(90deg); }
    50%  { transform: translateY(-6px)  rotate(180deg); }
    75%  { transform: translateY(-20px) rotate(270deg); }
    100% { transform: translateY(0px)   rotate(360deg); }
}
@media(max-width:900px) { .hero-char-img { display:none; } }
/* Static section mascots (no animation) */
.sec-mascot { position:absolute; pointer-events:none; user-select:none; z-index:0; }
.tc-icon-badge {
    position: absolute; top: 12px; left: 12px; z-index: 4;
    width: 42px; height: 42px;
    background: rgba(91,63,248,.2); border: 1.5px solid rgba(91,63,248,.4);
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: #a78bfa; font-size: 1.25rem;
    animation: glowPulse 3s ease-in-out infinite;
}

</style>
</head>
<body>
<div class="scroll-prog" id="scrollProg"></div>

<!-- NAVBAR -->
<nav class="nx-navbar" id="navbar">
    <a href="index.php" class="nx-brand">VELORA<span class="dot">.</span></a>
    <ul class="nx-nav-links">
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="#categories">Categories</a></li>
        <li><a href="#featured">Featured</a></li>
        <li><a href="#journal">Journal</a></li>
        <li><a href="#team">Team</a></li>
        <li><a href="#about">About</a></li>
    </ul>
    <div class="nx-nav-actions">
        <button class="theme-toggle" title="Toggle theme"><i class="bi bi-moon-fill"></i></button>
        <a href="store/shoppingCart.php" class="nx-icon-btn" title="Cart"><i class="bi bi-bag"></i><span class="nx-badge"><?= $cart_count ?></span></a>
        <?php if ($is_logged_in): ?>
            <span style="font-size:13px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                <i class="bi bi-person-circle" style="color:var(--primary);"></i>
                <?= $user_name ?>
            </span>
            <a href="index.php?logout=1" class="btn-outline" style="padding:8px 20px;font-size:13px;">Sign Out</a>
        <?php else: ?>
            <a href="auth/login.php" class="btn-outline" style="padding:8px 20px;font-size:13px;">Sign In</a>
            <a href="auth/signUp.php" class="btn-primary" style="padding:8px 20px;font-size:13px;">Get Started</a>
        <?php endif; ?>
    </div>
    <button class="nx-hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>
<div class="nx-mobile-menu" id="mobileMenu">
    <a href="index.php">Home</a>
    <a href="#categories">Categories</a>
    <a href="#featured">Featured</a>
    <a href="#journal">Journal</a>
    <a href="store/shoppingCart.php">Cart</a>
    <a href="auth/login.php">Sign In</a>
    <a href="auth/signUp.php" style="color:var(--primary);font-weight:700;">Get Started →</a>
</div>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb hero-orb-a"></div>
    <div class="hero-orb hero-orb-b"></div>
    <div class="nx-container">
        <div class="hero-inner">
            <div class="hero-tag reveal"><i class="bi bi-stars"></i> VELORA Vol. 01 — Now Live</div>
            <h1 class="hero-title reveal delay-1">
                Where <em>design</em><br>meets desire.
            </h1>
            <p class="hero-sub reveal delay-2">
                A curated marketplace of premium products and specialist services — handpicked for those who demand more than ordinary.
            </p>
            <div class="hero-search reveal delay-3">
                <i class="bi bi-search" style="color:var(--text-soft);font-size:1rem;margin-right:4px;"></i>
                <input type="text" placeholder="Search products, services, specialists…" id="heroSearch">
                <a href="store/shoppingCart.php" class="btn-primary" style="padding:10px 22px;">Explore</a>
            </div>
            <div class="hero-cta-row reveal delay-4">
                <a href="auth/signUp.php" class="btn-outline">Create Free Account</a>
                <span class="hero-trust"><i class="bi bi-shield-check" style="color:var(--success)"></i> No credit card required</span>
            </div>
        </div>
    </div>
    <div class="hero-char-img" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400" fill="none" style="width:100%;height:100%;">
        <defs>
          <radialGradient id="bg1" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="#a78bfa" stop-opacity=".18"/><stop offset="100%" stop-color="#5b3fd4" stop-opacity="0"/></radialGradient>
          <radialGradient id="headG" cx="40%" cy="35%"><stop offset="0%" stop-color="#c4b5fd"/><stop offset="100%" stop-color="#7c3aed"/></radialGradient>
          <radialGradient id="bodyG" cx="35%" cy="30%"><stop offset="0%" stop-color="#a78bfa"/><stop offset="100%" stop-color="#5b21b6"/></radialGradient>
          <radialGradient id="faceG" cx="40%" cy="35%"><stop offset="0%" stop-color="#f5f3ff"/><stop offset="100%" stop-color="#ede9fe"/></radialGradient>
          <filter id="glow"><feGaussianBlur stdDeviation="4" result="blur"/><feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
        </defs>
        <!-- ambient glow -->
        <ellipse cx="150" cy="200" rx="140" ry="140" fill="url(#bg1)"/>
        <!-- shadow -->
        <ellipse cx="150" cy="392" rx="52" ry="8" fill="rgba(91,63,248,.13)"/>
        <!-- feet -->
        <ellipse cx="122" cy="358" rx="22" ry="13" fill="#6d28d9"/><ellipse cx="122" cy="354" rx="20" ry="12" fill="#7c3aed"/>
        <ellipse cx="178" cy="358" rx="22" ry="13" fill="#6d28d9"/><ellipse cx="178" cy="354" rx="20" ry="12" fill="#7c3aed"/>
        <!-- body -->
        <ellipse cx="150" cy="268" rx="72" ry="86" fill="url(#bodyG)"/>
        <ellipse cx="146" cy="255" rx="58" ry="68" fill="rgba(196,181,253,.22)"/>
        <!-- neck -->
        <ellipse cx="150" cy="185" rx="28" ry="16" fill="#7c3aed"/>
        <!-- head (helmet-style) -->
        <circle cx="150" cy="118" r="78" fill="url(#headG)"/>
        <circle cx="150" cy="118" r="70" fill="#f0ebff"/>
        <!-- face visor -->
        <ellipse cx="150" cy="122" rx="50" ry="46" fill="url(#faceG)"/>
        <!-- blush -->
        <ellipse cx="110" cy="138" rx="14" ry="9" fill="rgba(236,72,153,.2)"/>
        <ellipse cx="190" cy="138" rx="14" ry="9" fill="rgba(236,72,153,.2)"/>
        <!-- eyes -->
        <ellipse cx="130" cy="112" rx="12" ry="14" fill="#3b0764"/>
        <ellipse cx="170" cy="112" rx="12" ry="14" fill="#3b0764"/>
        <!-- eye shine -->
        <circle cx="134" cy="107" r="4" fill="white"/>
        <circle cx="174" cy="107" r="4" fill="white"/>
        <circle cx="128" cy="116" r="2" fill="rgba(255,255,255,.6)"/>
        <circle cx="168" cy="116" r="2" fill="rgba(255,255,255,.6)"/>
        <!-- smile -->
        <path d="M120 142 Q150 162 180 142" stroke="#3b0764" stroke-width="3.5" stroke-linecap="round" fill="none"/>
        <!-- left arm -->
        <path d="M84 248 Q62 222 68 200" stroke="#7c3aed" stroke-width="20" stroke-linecap="round" fill="none"/>
        <circle cx="68" cy="193" r="15" fill="#a78bfa"/>
        <!-- right arm -->
        <path d="M216 248 Q234 218 228 196" stroke="#7c3aed" stroke-width="20" stroke-linecap="round" fill="none"/>
        <circle cx="228" cy="188" r="15" fill="#a78bfa"/>
        <!-- shopping bag -->
        <rect x="230" y="146" width="58" height="68" rx="10" fill="#7c3aed"/>
        <rect x="235" y="151" width="48" height="58" rx="8" fill="#c4b5fd"/>
        <path d="M244 146 C244 130 274 130 274 146" stroke="#5b21b6" stroke-width="4" stroke-linecap="round" fill="none"/>
        <!-- bag label -->
        <rect x="246" y="168" width="26" height="18" rx="4" fill="rgba(91,63,248,.35)"/>
        <text x="259" y="181" text-anchor="middle" font-size="11" fill="white" font-weight="bold">V</text>
        <!-- star decorations -->
        <path d="M44 95 L46 102 L53 104 L46 106 L44 113 L42 106 L35 104 L42 102 Z" fill="#c4b5fd" opacity=".75" filter="url(#glow)"/>
        <path d="M256 58 L258 63 L263 65 L258 67 L256 72 L254 67 L249 65 L254 63 Z" fill="#ddd6fe" opacity=".7"/>
        <path d="M22 175 L23.5 179 L28 181 L23.5 183 L22 187 L20.5 183 L16 181 L20.5 179 Z" fill="#a78bfa" opacity=".6"/>
        <circle cx="270" cy="290" r="4" fill="#c4b5fd" opacity=".5"/>
        <circle cx="40" cy="310" r="3" fill="#ddd6fe" opacity=".5"/>
        <circle cx="275" cy="170" r="3" fill="#a78bfa" opacity=".55"/>
      </svg>
    </div>
</section>

<!-- STATS BAR -->
<section class="stats-bar">
    <div class="nx-container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-num" data-count="12400" data-suffix="+">0</div>
                <div class="stat-label">Products Listed</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" data-count="3800" data-suffix="+">0</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" data-count="98" data-suffix="%">0</div>
                <div class="stat-label">Satisfaction Rate</div>
            </div>
            <div class="stat-item">
                <div class="stat-num" data-count="24" data-suffix="/7">0</div>
                <div class="stat-label">Expert Support</div>
            </div>
        </div>
    </div>
</section>

<!-- CATEGORIES -->
<section class="nx-section" id="categories" style="position:relative;">
    <div class="sec-mascot" style="bottom:6%;left:1%;width:80px;transform:rotate(90deg);opacity:.7;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="top:6%;right:1.5%;width:65px;transform:rotate(-22deg);opacity:.65;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="top:42%;right:0.5%;width:52px;transform:rotate(180deg);opacity:.55;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="nx-container">
        <div class="reveal">
            <span class="section-eyebrow">Browse</span>
            <h2 class="serif" style="font-size:2.4rem;margin-bottom:8px;">Curated Categories</h2>
            <p style="color:var(--text-muted);margin-bottom:40px;">Hand-picked selection of premium physical goods and digital expertise.</p>
        </div>
        <div class="cat-grid reveal delay-1">
            <div class="cat-card">
                <img class="cat-card-bg" src="https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=700&q=80" alt="Living & Home">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-label">
                    <h3>Living &amp; Home</h3>
                    <span>Elevated environments</span>
                </div>
            </div>
            <div class="cat-card">
                <img class="cat-card-bg" src="https://images.unsplash.com/photo-1498049794561-7780e7231661?w=500&q=80" alt="Tech">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-label"><h3>Tech</h3><span>Precision gear</span></div>
            </div>
            <div class="cat-card">
                <img class="cat-card-bg" src="https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=500&q=80" alt="Wellness">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-label"><h3>Wellness</h3><span>Body &amp; mind</span></div>
            </div>
            <div class="cat-card">
                <img class="cat-card-bg" src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=700&q=80" alt="Fashion">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-label"><h3>Fashion</h3><span>Timeless style</span></div>
            </div>
            <div class="cat-card">
                <img class="cat-card-bg" src="https://images.unsplash.com/photo-1558655146-9f40138edfeb?w=700&q=80" alt="Design">
                <div class="cat-card-overlay"></div>
                <div class="cat-card-label"><h3>Design</h3><span>Conceptual excellence</span></div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURED -->
<section class="nx-section" id="featured" style="background:var(--surface);position:relative;">
    <div class="sec-mascot" style="bottom:8%;right:1%;width:75px;transform:rotate(-90deg);opacity:.65;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="top:8%;left:1%;width:62px;transform:rotate(25deg);opacity:.6;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="nx-container">
        <div class="featured-grid">
            <div class="featured-img-wrap reveal left">
                <div class="featured-img" style="padding:0;">
                    <img src="https://images.unsplash.com/photo-1485846234645-a62644f84728?w=600&q=80" alt="Visual Storytelling Masterclass" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-lg);">
                </div>
            </div>
            <div class="reveal right">
                <span class="featured-tag">Featured Exhibit</span>
                <h2 class="serif featured-title">Visual Storytelling Masterclass</h2>
                <div class="featured-meta">
                    <div style="width:36px;height:36px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:1.1rem;">👤</div>
                    <span><strong>Julian Voss</strong> — Director of Photography</span>
                </div>
                <p style="color:var(--text-muted);font-style:italic;line-height:1.75;margin-bottom:16px;">"True visual storytelling is the ability to see the soul of a subject before you ever press the shutter."</p>
                <ul class="check-list">
                    <li>4-week immersive digital curriculum</li>
                    <li>1-on-1 portfolio review with industry leaders</li>
                    <li>Lifetime access to session recordings</li>
                </ul>
                <div class="featured-price">$299<span style="font-size:1rem;color:var(--text-muted);font-weight:400;">/session</span></div>
                <div style="display:flex;gap:12px;">
                    <a href="store/shoppingCart.php" class="btn-primary"><i class="bi bi-bag-plus"></i> Reserve Seat</a>
                    <a href="#" class="btn-ghost">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCT OF THE WEEK -->
<section class="nx-section" id="potw" style="position:relative;">
    <div class="nx-container">
        <div class="reveal" style="margin-bottom:36px;">
            <span class="section-eyebrow">Weekly Pick</span>
            <h2 class="serif" style="font-size:2.4rem;">Product of the Week</h2>
        </div>
        <div class="potw-wrap reveal delay-1">
            <div class="potw-img" style="padding:0;overflow:hidden;">
                <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&q=80" alt="Acoustic Prism Headphones" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div>
                <span class="potw-badge">⭐ Limited Edition</span>
                <h3 class="serif potw-title">The Acoustic Prism v.02</h3>
                <p style="color:var(--text-muted);line-height:1.75;margin-bottom:16px;">Engineered for those who hear the nuances in silence. Featuring a custom titanium driver and carbon fiber housing for the discerning audiophile.</p>
                <div class="potw-avail"><i class="bi bi-check-circle-fill"></i> 12 Units Left — Ships in 2 days</div>
                <div style="font-size:2.2rem;font-weight:700;color:var(--primary);margin-bottom:20px;">$1,450<span style="font-size:1rem;font-weight:400;color:var(--text-muted);">.00</span></div>
                <div style="display:flex;gap:12px;">
                    <a href="store/shoppingCart.php" class="btn-primary" style="flex:1;justify-content:center;"><i class="bi bi-bag"></i> Acquire Now</a>
                    <a href="#" class="btn-ghost">Details</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JOURNAL -->
<section class="nx-section" id="journal" style="background:var(--surface);position:relative;">
    <div class="sec-mascot" style="bottom:8%;left:1%;width:78px;transform:rotate(90deg);opacity:.65;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="top:6%;right:1.5%;width:58px;transform:rotate(-18deg);opacity:.6;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="top:45%;left:0.5%;width:50px;transform:rotate(-90deg);opacity:.5;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(91,63,248,.1)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#6d28d9"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#7c3aed"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.22)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#7c3aed"/><circle cx="25" cy="15" r="13" fill="#8b5cf6"/><circle cx="25" cy="15" r="11" fill="#f0ebff"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#3b0764"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#3b0764" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="nx-container">
        <div class="reveal" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:36px;flex-wrap:wrap;gap:16px;">
            <div>
                <span class="section-eyebrow">Journal</span>
                <h2 class="serif" style="font-size:2.4rem;">From the Editorial</h2>
            </div>
            <a href="#" class="btn-ghost" style="padding:8px 20px;font-size:13px;">View All <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="journal-grid">
            <div class="journal-card reveal delay-1">
                <div class="journal-thumb" style="padding:0;">
                    <img src="https://images.unsplash.com/photo-1484788984921-03950022c9ef?w=500&q=80" alt="Minimalist Spaces" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div class="journal-body">
                    <div class="journal-cat">Editorial</div>
                    <div class="journal-title">The Psychology of Minimalist Spaces</div>
                </div>
            </div>
            <div class="journal-card reveal delay-2">
                <div class="journal-thumb" style="padding:0;">
                    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=500&q=80" alt="Analog Mechanics" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div class="journal-body">
                    <div class="journal-cat">Industry</div>
                    <div class="journal-title">Future of Analog Mechanics in a Digital Age</div>
                </div>
            </div>
            <div class="journal-card reveal delay-3">
                <div class="journal-thumb" style="padding:0;">
                    <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=500&q=80" alt="Mental Clarity" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div class="journal-body">
                    <div class="journal-cat">Wellness</div>
                    <div class="journal-title">Designing for Mental Clarity: A New Approach</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- TEAM -->
<section class="team-section" id="team">
    <div class="team-blob team-blob-1"></div>
    <div class="team-blob team-blob-2"></div>
    <div class="sec-mascot" style="top:8%;left:1.5%;width:70px;transform:rotate(-15deg);opacity:.45;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(167,139,250,.15)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#a78bfa"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.3)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#a78bfa"/><circle cx="25" cy="15" r="13" fill="#c4b5fd"/><circle cx="25" cy="15" r="11" fill="#ede9fe"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#4c1d95" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="bottom:8%;right:1.5%;width:65px;transform:rotate(20deg);opacity:.4;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(167,139,250,.15)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#a78bfa"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.3)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#a78bfa"/><circle cx="25" cy="15" r="13" fill="#c4b5fd"/><circle cx="25" cy="15" r="11" fill="#ede9fe"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#4c1d95" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="nx-container">
        <div class="team-header">
            <div class="section-eyebrow reveal">The Builders</div>
            <h2 class="reveal delay-1">Meet the minds behind <em style="font-style:italic;">VELORA</em></h2>
            <p class="reveal delay-2">A passionate team of student creators who designed and built VELORA as a premium curated marketplace experience.</p>
        </div>
        <div class="team-grid">

            <div class="team-card-wrap reveal">
                <div class="tc-char-wrap"><svg class="tc-char" viewBox="0 0 80 112" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="40" cy="109" rx="16" ry="3" fill="rgba(91,63,248,.12)"/><ellipse cx="32" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="32" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="48" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="48" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="40" cy="68" rx="19" ry="24" fill="#7c3aed"/><ellipse cx="39" cy="60" rx="14" ry="17" fill="rgba(196,181,253,.2)"/><ellipse cx="40" cy="46" rx="8" ry="5" fill="#7c3aed"/><circle cx="40" cy="26" r="20" fill="#8b5cf6"/><circle cx="40" cy="26" r="17" fill="#f0ebff"/><ellipse cx="40" cy="28" rx="12" ry="11" fill="#f5f3ff"/><ellipse cx="30" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="50" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="35" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><ellipse cx="45" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><circle cx="36.5" cy="23" r="1.5" fill="white"/><circle cx="46.5" cy="23" r="1.5" fill="white"/><path d="M33 34 Q40 41 47 34" stroke="#3b0764" stroke-width="1.5" stroke-linecap="round" fill="none"/><path d="M23 66 Q11 57 16 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="15" cy="44" r="6" fill="#a78bfa"/><path d="M57 66 Q69 57 64 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="65" cy="44" r="6" fill="#a78bfa"/><rect x="4" y="37" width="26" height="16" rx="2" fill="#c4b5fd"/><rect x="6" y="39" width="22" height="11" rx="1.5" fill="#4c1d95"/><rect x="8" y="41" width="5" height="2" rx=".5" fill="#a78bfa"/><rect x="15" y="41" width="9" height="2" rx=".5" fill="#7c3aed" opacity=".6"/><rect x="8" y="45" width="14" height="1.5" rx=".5" fill="#a78bfa" opacity=".5"/></svg></div>
                <div class="team-card">
                    <div class="team-card-num">01</div>
                    <div class="tc-icon-badge"><i class="bi bi-window-fullscreen"></i></div>
                    <div class="team-photo-area">
                        <div class="t-ring t-ring-1"></div>
                        <div class="t-ring t-ring-2"></div>
                        <div class="t-ring t-ring-3"></div>
                        <img class="team-avatar" src="assets/css/image/tom.jpeg" alt="Tom" loading="lazy">
                    </div>
                    <div class="team-card-body">
                        <div class="team-name">Tom</div>
                        <div class="team-role">Landing Page</div>
                        <div class="team-desc">Designed and built the VELORA landing experience — from the hero section all the way to the journal.</div>
                        <div class="team-links">
                            <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="team-card-wrap reveal delay-1">
                <div class="tc-char-wrap"><svg class="tc-char" viewBox="0 0 80 112" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="40" cy="109" rx="16" ry="3" fill="rgba(91,63,248,.12)"/><ellipse cx="32" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="32" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="48" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="48" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="40" cy="68" rx="19" ry="24" fill="#7c3aed"/><ellipse cx="39" cy="60" rx="14" ry="17" fill="rgba(196,181,253,.2)"/><ellipse cx="40" cy="46" rx="8" ry="5" fill="#7c3aed"/><circle cx="40" cy="26" r="20" fill="#8b5cf6"/><circle cx="40" cy="26" r="17" fill="#f0ebff"/><ellipse cx="40" cy="28" rx="12" ry="11" fill="#f5f3ff"/><ellipse cx="30" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="50" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="35" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><ellipse cx="45" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><circle cx="36.5" cy="23" r="1.5" fill="white"/><circle cx="46.5" cy="23" r="1.5" fill="white"/><path d="M33 34 Q40 41 47 34" stroke="#3b0764" stroke-width="1.5" stroke-linecap="round" fill="none"/><path d="M23 66 Q11 57 16 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="15" cy="44" r="6" fill="#a78bfa"/><path d="M57 66 Q69 57 64 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="65" cy="44" r="6" fill="#a78bfa"/><circle cx="22" cy="47" r="8" stroke="#fbbf24" stroke-width="2.5" fill="rgba(251,191,36,.15)"/><circle cx="22" cy="47" r="4" fill="#fbbf24"/><rect x="26" y="45.5" width="17" height="3" rx="1.5" fill="#fbbf24"/><rect x="38" y="48.5" width="4" height="3.5" rx="1" fill="#fbbf24"/><rect x="42" y="48.5" width="4" height="3.5" rx="1" fill="#fbbf24"/></svg></div>
                <div class="team-card">
                    <div class="team-card-num">02</div>
                    <div class="tc-icon-badge"><i class="bi bi-shield-lock-fill"></i></div>
                    <div class="team-photo-area">
                        <div class="t-ring t-ring-1"></div>
                        <div class="t-ring t-ring-2"></div>
                        <div class="t-ring t-ring-3"></div>
                        <img class="team-avatar" src="assets/css/image/felysia.jpeg" alt="Felysia" loading="lazy">
                    </div>
                    <div class="team-card-body">
                        <div class="team-name">Felysia</div>
                        <div class="team-role">Authentication</div>
                        <div class="team-desc">Crafted the sign in and register flows with secure PHP session handling and validation.</div>
                        <div class="team-links">
                            <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="team-card-wrap reveal delay-2">
                <div class="tc-char-wrap"><svg class="tc-char" viewBox="0 0 80 112" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="40" cy="109" rx="16" ry="3" fill="rgba(91,63,248,.12)"/><ellipse cx="32" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="32" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="48" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="48" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="40" cy="68" rx="19" ry="24" fill="#7c3aed"/><ellipse cx="39" cy="60" rx="14" ry="17" fill="rgba(196,181,253,.2)"/><ellipse cx="40" cy="46" rx="8" ry="5" fill="#7c3aed"/><circle cx="40" cy="26" r="20" fill="#8b5cf6"/><circle cx="40" cy="26" r="17" fill="#f0ebff"/><ellipse cx="40" cy="28" rx="12" ry="11" fill="#f5f3ff"/><ellipse cx="30" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="50" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="35" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><ellipse cx="45" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><circle cx="36.5" cy="23" r="1.5" fill="white"/><circle cx="46.5" cy="23" r="1.5" fill="white"/><path d="M33 34 Q40 41 47 34" stroke="#3b0764" stroke-width="1.5" stroke-linecap="round" fill="none"/><path d="M23 66 Q11 57 16 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="15" cy="44" r="6" fill="#a78bfa"/><path d="M57 66 Q69 57 64 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="65" cy="44" r="6" fill="#a78bfa"/><rect x="5" y="38" width="22" height="20" rx="3" fill="#7c3aed"/><rect x="7" y="40" width="18" height="16" rx="2" fill="#c4b5fd"/><path d="M11 38 C11 32 21 32 21 38" stroke="#5b21b6" stroke-width="2.5" stroke-linecap="round" fill="none"/><rect x="10" y="44" width="10" height="6" rx="1.5" fill="rgba(91,63,248,.3)"/></svg></div>
                <div class="team-card">
                    <div class="team-card-num">03</div>
                    <div class="tc-icon-badge"><i class="bi bi-bag-heart-fill"></i></div>
                    <div class="team-photo-area">
                        <div class="t-ring t-ring-1"></div>
                        <div class="t-ring t-ring-2"></div>
                        <div class="t-ring t-ring-3"></div>
                        <img class="team-avatar" src="assets/css/image/jodi.jpeg" alt="Jodi" loading="lazy">
                    </div>
                    <div class="team-card-body">
                        <div class="team-name">Jodi</div>
                        <div class="team-role">Store &amp; Checkout</div>
                        <div class="team-desc">Built the shopping cart and checkout with a seamless and intuitive payment experience.</div>
                        <div class="team-links">
                            <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="team-card-wrap reveal delay-3">
                <div class="tc-char-wrap"><svg class="tc-char" viewBox="0 0 80 112" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="40" cy="109" rx="16" ry="3" fill="rgba(91,63,248,.12)"/><ellipse cx="32" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="32" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="48" cy="98" rx="7" ry="4" fill="#6d28d9"/><ellipse cx="48" cy="95" rx="6" ry="3.5" fill="#7c3aed"/><ellipse cx="40" cy="68" rx="19" ry="24" fill="#7c3aed"/><ellipse cx="39" cy="60" rx="14" ry="17" fill="rgba(196,181,253,.2)"/><ellipse cx="40" cy="46" rx="8" ry="5" fill="#7c3aed"/><circle cx="40" cy="26" r="20" fill="#8b5cf6"/><circle cx="40" cy="26" r="17" fill="#f0ebff"/><ellipse cx="40" cy="28" rx="12" ry="11" fill="#f5f3ff"/><ellipse cx="30" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="50" cy="32" rx="4" ry="3" fill="rgba(236,72,153,.22)"/><ellipse cx="35" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><ellipse cx="45" cy="25" rx="3.5" ry="4.5" fill="#3b0764"/><circle cx="36.5" cy="23" r="1.5" fill="white"/><circle cx="46.5" cy="23" r="1.5" fill="white"/><path d="M33 34 Q40 41 47 34" stroke="#3b0764" stroke-width="1.5" stroke-linecap="round" fill="none"/><path d="M23 66 Q11 57 16 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="15" cy="44" r="6" fill="#a78bfa"/><path d="M57 66 Q69 57 64 47" stroke="#7c3aed" stroke-width="7" stroke-linecap="round" fill="none"/><circle cx="65" cy="44" r="6" fill="#a78bfa"/><ellipse cx="17" cy="52" rx="12" ry="8" fill="#f0ebff" stroke="#7c3aed" stroke-width="1.5"/><circle cx="11" cy="52" r="3" fill="#ef4444"/><circle cx="17" cy="49" r="3" fill="#fbbf24"/><circle cx="23" cy="52" r="3" fill="#34d399"/><circle cx="17" cy="55" r="3" fill="#a78bfa"/><rect x="25" y="35" width="3" height="20" rx="1.5" fill="#92400e" transform="rotate(-30 25 35)"/><ellipse cx="20" cy="34" rx="4" ry="2.5" fill="#7c3aed" transform="rotate(-30 20 34)"/></svg></div>
                <div class="team-card">
                    <div class="team-card-num">04</div>
                    <div class="tc-icon-badge"><i class="bi bi-palette-fill"></i></div>
                    <div class="team-photo-area">
                        <div class="t-ring t-ring-1"></div>
                        <div class="t-ring t-ring-2"></div>
                        <div class="t-ring t-ring-3"></div>
                        <img class="team-avatar" src="assets/css/image/bagus.jpeg" alt="Bagus" loading="lazy">
                    </div>
                    <div class="team-card-body">
                        <div class="team-name">Bagus</div>
                        <div class="team-role">Design &amp; Vision</div>
                        <div class="team-desc">Shaped the overall design vision and ensured a premium user experience throughout VELORA.</div>
                        <div class="team-links">
                            <a href="#" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="nx-footer" id="about">
    <div class="sec-mascot" style="top:6%;right:2%;width:72px;transform:rotate(-25deg);opacity:.35;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(167,139,250,.12)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#a78bfa"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.25)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#a78bfa"/><circle cx="25" cy="15" r="13" fill="#c4b5fd"/><circle cx="25" cy="15" r="11" fill="#ede9fe"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#4c1d95" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="bottom:10%;left:1%;width:80px;transform:rotate(90deg);opacity:.3;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(167,139,250,.12)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#a78bfa"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.25)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#a78bfa"/><circle cx="25" cy="15" r="13" fill="#c4b5fd"/><circle cx="25" cy="15" r="11" fill="#ede9fe"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#4c1d95" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="top:40%;left:2%;width:55px;transform:rotate(15deg);opacity:.28;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(167,139,250,.12)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#a78bfa"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.25)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#a78bfa"/><circle cx="25" cy="15" r="13" fill="#c4b5fd"/><circle cx="25" cy="15" r="11" fill="#ede9fe"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#4c1d95" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="sec-mascot" style="bottom:6%;right:3%;width:60px;transform:rotate(180deg);opacity:.3;"><svg viewBox="0 0 50 70" fill="none" xmlns="http://www.w3.org/2000/svg"><ellipse cx="25" cy="65" rx="10" ry="3" fill="rgba(167,139,250,.12)"/><ellipse cx="19" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="31" cy="57" rx="5" ry="3" fill="#a78bfa"/><ellipse cx="25" cy="43" rx="13" ry="16" fill="#a78bfa"/><ellipse cx="24" cy="36" rx="10" ry="12" fill="rgba(196,181,253,.25)"/><ellipse cx="25" cy="28" rx="6" ry="4" fill="#a78bfa"/><circle cx="25" cy="15" r="13" fill="#c4b5fd"/><circle cx="25" cy="15" r="11" fill="#ede9fe"/><ellipse cx="25" cy="16" rx="8" ry="7.5" fill="#f5f3ff"/><ellipse cx="19" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><ellipse cx="31" cy="14" rx="2.8" ry="3.5" fill="#4c1d95"/><circle cx="20" cy="12" r="1" fill="white"/><circle cx="32" cy="12" r="1" fill="white"/><path d="M18 22 Q25 28 32 22" stroke="#4c1d95" stroke-width="1.2" stroke-linecap="round" fill="none"/></svg></div>
    <div class="nx-container">
        <div class="nx-footer-grid">
            <div class="nx-footer-brand">
                <div class="nx-brand" style="font-size:1.6rem;">VELORA<span class="dot">.</span></div>
                <p>A curated marketplace bridging the gap between premium products and specialized human expertise. Crafted with care by our team.</p>
                <div class="nx-footer-newsletter" style="margin-top:20px;">
                    <input type="email" placeholder="Enter your email…" id="newsletterEmail">
                    <button onclick="window.showToast('Subscribed! Welcome to VELORA.','success')">Subscribe</button>
                </div>
            </div>
            <div>
                <h5>Explore</h5>
                <ul>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="#featured">Featured Exhibit</a></li>
                    <li><a href="#potw">Product of Week</a></li>
                    <li><a href="#journal">Journal</a></li>
                </ul>
            </div>
            <div>
                <h5>Account</h5>
                <ul>
                    <li><a href="auth/login.php">Sign In</a></li>
                    <li><a href="auth/signUp.php">Create Account</a></li>
                    <li><a href="store/shoppingCart.php">Shopping Cart</a></li>
                    <li><a href="store/checkOut.php">Checkout</a></li>
                </ul>
            </div>
            <div>
                <h5>Company</h5>
                <ul>
                    <li><a href="#">About VELORA</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
        </div>
        <div class="nx-footer-bottom">
            <p>&copy; <?= $current_year ?> VELORA Marketplace. All rights reserved.</p>
            <div class="nx-social">
                <a href="#" title="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" title="Twitter/X"><i class="bi bi-twitter-x"></i></a>
                <a href="#" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                <a href="#" title="Facebook"><i class="bi bi-facebook"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
<script>
// Scroll progress bar
(function(){
    var prog = document.getElementById('scrollProg');
    if(!prog) return;
    window.addEventListener('scroll', function(){
        var s = document.documentElement.scrollTop || document.body.scrollTop;
        var h = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        prog.style.width = (h > 0 ? (s/h*100) : 0) + '%';
    }, {passive:true});
})();
</script>
<?php if ($flash_msg): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.showToast(<?= json_encode($flash_msg) ?>, <?= json_encode($flash_type) ?>);
});
</script>
<?php endif; ?>
</body>
</html>
