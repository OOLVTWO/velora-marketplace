<?php
/**
 * VELORA Marketplace — Landing Page (index.php)
 * Developer: Tom
 */
session_start();

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

/* ── RESPONSIVE ── */
@media(max-width:900px){
    .cat-grid { grid-template-columns: 1fr 1fr; }
    .cat-card:first-child { grid-row: auto; }
    .featured-grid, .potw-wrap { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2,1fr); }
    .journal-grid { grid-template-columns: 1fr; }
}
@media(max-width:640px){
    .cat-grid { grid-template-columns: 1fr; }
    .hero-cta-row { flex-direction: column; align-items: flex-start; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="nx-navbar" id="navbar">
    <a href="index.php" class="nx-brand">VELORA<span class="dot">.</span></a>
    <ul class="nx-nav-links">
        <li><a href="index.php" class="active">Home</a></li>
        <li><a href="#categories">Categories</a></li>
        <li><a href="#featured">Featured</a></li>
        <li><a href="#journal">Journal</a></li>
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
            <a href="auth/logout.php" class="btn-outline" style="padding:8px 20px;font-size:13px;">Sign Out</a>
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
<section class="nx-section" id="categories">
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
<section class="nx-section" id="featured" style="background:var(--surface);">
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
<section class="nx-section" id="potw">
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
<section class="nx-section" id="journal" style="background:var(--surface);">
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

<!-- FOOTER -->
<footer class="nx-footer" id="about">
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
<?php if ($flash_msg): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    window.showToast(<?= json_encode($flash_msg) ?>, <?= json_encode($flash_type) ?>);
});
</script>
<?php endif; ?>
</body>
</html>
