<?php
// shoppingCart.php - VELORA Marketplace | Shopping Cart Page
// Developer: Jody

session_start();

// Dummy cart data (in real app, from session/database)
$cart_items = [
    [
        'id'      => 1,
        'name'    => 'Ergonomic Oak Chair',
        'variant' => 'Natural / Walnut',
        'price'   => 3200000,
        'qty'     => 1,
        'stock'   => 5,
        'image'   => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=300&q=80',
        'category'=> 'Furniture',
    ],
    [
        'id'      => 2,
        'name'    => 'Minimal Desk Lamp',
        'variant' => 'Matte Black',
        'price'   => 780000,
        'qty'     => 2,
        'stock'   => 10,
        'image'   => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=300&q=80',
        'category'=> 'Lighting',
    ],
    [
        'id'      => 3,
        'name'    => 'Ceramic Planter Set',
        'variant' => 'Off-White / Set of 3',
        'price'   => 420000,
        'qty'     => 1,
        'stock'   => 8,
        'image'   => 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=300&q=80',
        'category'=> 'Decor',
    ],
];

$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart_items));
$shipping = 45000;
$tax      = round($subtotal * 0.11);
$total    = $subtotal + $shipping + $tax;

// Sync cart count ke session agar badge di halaman lain akurat
$_SESSION['cart_count'] = count($cart_items);

function formatRp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart — VELORA Marketplace</title>
    <meta name="description" content="Your VELORA shopping cart — review and checkout your curated selection.">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        /* ── Inherit VELORA tokens — extras only ── */
        .white { background: var(--surface); }

        /* ── NAVBAR ── */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 2.5rem;
            height: 64px;
        }
        .navbar-brand {
            font-family: 'DM Serif Display', serif;
            font-size: 1.25rem;
            color: var(--text-main) !important;
        }
        .nav-links a {
            font-size: 14px;
            color: var(--text-muted);
            text-decoration: none;
            margin: 0 14px;
            transition: color .2s;
        }
        .nav-links a:hover, .nav-links a.active { color: var(--text-main); }
        .nav-links a.active {
            color: var(--primary);
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 2px;
        }
        .nav-icons a {
            color: var(--text-muted);
            font-size: 1.1rem;
            margin-left: 14px;
            text-decoration: none;
            transition: color .2s;
            position: relative;
        }
        .nav-icons a:hover { color: var(--text-main); }
        .cart-badge {
            position: absolute;
            top: -6px; right: -8px;
            background: var(--primary);
            color: white;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50%;
            width: 16px; height: 16px;
            display: flex; align-items: center; justify-content: center;
        }
        .btn-signin {
            background: var(--primary);
            color: white !important;
            border-radius: 100px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .btn-signin:hover { background: var(--primary-dark); }

        /* ── BREADCRUMB ── */
        .breadcrumb-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 18px 20px 0;
        }
        .breadcrumb-item, .breadcrumb-item a {
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
        }
        .breadcrumb-item.active { color: var(--text-main); font-weight: 500; }
        .breadcrumb-item + .breadcrumb-item::before { color: var(--border); }

        /* ── STEP INDICATOR ── */
        .steps-wrap {
            max-width: 460px;
            margin: 20px auto 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            flex: 1;
            position: relative;
        }
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px; left: 50%;
            width: 100%; height: 2px;
            background: var(--border);
            z-index: 0;
        }
        .step-item.done::after { background: var(--primary); }
        .step-circle {
            width: 30px; height: 30px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--white);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
            color: var(--text-muted);
            z-index: 1;
            transition: all .3s;
        }
        .step-item.active .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .step-item.done .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .step-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .step-item.active .step-label { color: var(--primary); }

        /* ── MAIN LAYOUT ── */
        .cart-wrap {
            max-width: 1100px;
            margin: 16px auto 60px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 28px;
            align-items: start;
        }

        /* ── PAGE TITLE ── */
        .page-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.75rem;
            margin-bottom: 4px;
        }
        .page-sub { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; }

        /* ── SELECT ALL BAR ── */
        .select-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius) var(--radius) 0 0;
            padding: 12px 20px;
            border-bottom: none;
        }
        .select-bar label {
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 500; cursor: pointer;
        }
        .select-bar input[type="checkbox"] { accent-color: var(--primary); width: 16px; height: 16px; }
        .btn-delete-selected {
            font-size: 13px;
            color: var(--danger);
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: flex; align-items: center; gap: 5px;
            opacity: 0;
            transition: opacity .2s;
            pointer-events: none;
        }
        .btn-delete-selected.visible { opacity: 1; pointer-events: all; }

        /* ── CART ITEM CARD ── */
        .cart-list {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 0 0 var(--radius) var(--radius);
            overflow: hidden;
        }
        .cart-row {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            border-bottom: 1px solid var(--border);
            transition: background .2s;
            position: relative;
        }
        .cart-row:last-child { border-bottom: none; }
        .cart-row:hover { background: #FAFAFA; }
        .cart-row.removing {
            animation: slideOut .3s forwards;
        }
        @keyframes slideOut {
            to { opacity: 0; transform: translateX(30px); max-height: 0; padding: 0; overflow: hidden; }
        }

        .row-check input[type="checkbox"] { accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer; }

        .item-img {
            width: 88px; height: 88px;
            border-radius: 10px;
            object-fit: cover;
            background: var(--bg);
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .item-info { flex: 1; min-width: 0; }
        .item-category {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--primary);
            margin-bottom: 3px;
        }
        .item-name {
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .item-variant {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        .item-price-unit {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        /* QTY CONTROL */
        .qty-control {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            overflow: hidden;
            width: fit-content;
        }
        .qty-btn {
            width: 32px; height: 32px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: var(--text-muted);
            transition: background .15s, color .15s;
            display: flex; align-items: center; justify-content: center;
        }
        .qty-btn:hover { background: var(--primary-light); color: var(--primary); }
        .qty-val {
            width: 36px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-left: 1.5px solid var(--border);
            border-right: 1.5px solid var(--border);
            background: none;
            outline: none;
            padding: 0;
            height: 32px;
        }

        /* ITEM SUBTOTAL */
        .item-subtotal {
            text-align: right;
            min-width: 110px;
        }
        .item-subtotal .price {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }
        .item-subtotal .save {
            font-size: 11px;
            color: var(--success);
            margin-top: 2px;
        }

        /* DELETE BTN */
        .btn-remove {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 16px;
            padding: 4px;
            border-radius: 6px;
            transition: color .2s, background .2s;
            flex-shrink: 0;
        }
        .btn-remove:hover { color: var(--danger); background: #FEF2F2; }

        /* ── EMPTY STATE ── */
        .empty-cart {
            display: none;
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }
        .empty-cart .empty-icon { font-size: 3.5rem; margin-bottom: 16px; }
        .empty-cart h5 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.3rem;
            margin-bottom: 8px;
        }
        .empty-cart p { font-size: 14px; color: var(--text-muted); margin-bottom: 24px; }
        .btn-shop {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 100px;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }
        .btn-shop:hover { background: var(--primary-dark); color: white; }

        /* ── ORDER SUMMARY ── */
        .summary-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px;
            position: sticky;
            top: 24px;
        }
        .summary-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }

        .promo-wrap {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }
        .promo-wrap .form-control {
            font-size: 13px;
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif;
        }
        .promo-wrap .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91,63,248,.1);
        }
        .btn-promo {
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0 16px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: all .2s;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-promo:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
            color: var(--text-muted);
        }
        .summary-row .val { color: var(--text-main); font-weight: 500; }
        .summary-row.total {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-main);
            padding-top: 14px;
            margin-top: 4px;
            border-top: 1.5px solid var(--text-main);
        }
        .summary-row.total .val { color: var(--primary); }
        .summary-row.promo-active { color: var(--success); }
        .summary-row.promo-active .val { color: var(--success); }

        .btn-checkout {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            width: 100%;
            padding: 14px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background .2s, transform .15s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-top: 16px;
            text-decoration: none;
        }
        .btn-checkout:hover { background: var(--primary-dark); color: white; transform: translateY(-1px); }
        .btn-checkout:active { transform: translateY(0); }

        .btn-continue {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            width: 100%;
            padding: 11px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
            background: none;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all .2s;
            text-decoration: none;
            margin-top: 10px;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-continue:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

        .secure-note {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            font-size: 12px; color: var(--text-muted);
            margin-top: 14px;
        }

        /* ── YOU MAY ALSO LIKE ── */
        .also-like {
            margin-top: 32px;
        }
        .also-like-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.15rem;
            margin-bottom: 16px;
        }
        .suggest-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            cursor: pointer;
        }
        .suggest-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
        .suggest-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: var(--bg);
        }
        .suggest-body { padding: 12px 14px; }
        .suggest-name { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
        .suggest-price { font-size: 13px; color: var(--primary); font-weight: 700; }
        .btn-add-suggest {
            width: 28px; height: 28px;
            background: var(--primary);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 16px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            transition: background .2s;
            flex-shrink: 0;
        }
        .btn-add-suggest:hover { background: var(--primary-dark); }

        /* ── TOAST ── */
        .toast-wrap {
            position: fixed;
            bottom: 28px; right: 28px;
            z-index: 9999;
        }
        .toast-item {
            background: var(--text-main);
            color: white;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 14px;
            display: flex; align-items: center; gap: 10px;
            margin-top: 10px;
            animation: toastIn .3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .cart-wrap { grid-template-columns: 1fr; }
            .summary-card { position: static; }
            .item-img { width: 68px; height: 68px; }
            .item-subtotal { min-width: 80px; }
            .nav-links { display: none; }
            .also-like { display: none; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR (VELORA shared) ── -->
<nav class="nx-navbar" id="navbar">
    <a href="../index.php" class="nx-brand">VELORA<span class="dot">.</span></a>
        <ul class="nx-nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="../index.php#categories">Categories</a></li>
        <li><a href="../index.php#featured">Featured</a></li>
        <li><a href="../index.php#journal">Journal</a></li>
    </ul>
    <div class="nx-nav-actions">
        <button class="theme-toggle" title="Toggle theme"><i class="bi bi-moon-fill"></i></button>
        <a href="shoppingCart.php" class="nx-icon-btn" title="Cart" style="color:var(--primary)">
            <i class="bi bi-bag"></i>
            <span class="nx-badge" id="cartBadge"><?= count($cart_items) ?></span>
        </a>
        <a href="../auth/login.php" class="btn-outline" style="padding:8px 20px;font-size:13px;">Sign In</a>
        <a href="../auth/signUp.php" class="btn-primary" style="padding:8px 20px;font-size:13px;">Get Started</a>
    </div>
    <button class="nx-hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
</nav>
<div class="nx-mobile-menu" id="mobileMenu">
    <a href="../index.php">Home</a>
    <a href="shoppingCart.php">Cart</a>
    <a href="../auth/login.php">Sign In</a>
    <a href="../auth/signUp.php" style="color:var(--primary);font-weight:700;">Get Started →</a>
</div>

<!-- ── BREADCRUMB ── -->
<div class="breadcrumb-wrap" style="max-width:1100px;margin:80px auto 0;padding:18px 20px 0;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="list-style:none;display:flex;gap:8px;font-size:13px;color:var(--text-muted);">
            <li><a href="../index.php" style="color:var(--text-muted);">Home</a></li>
            <li style="color:var(--border);">/</li>
            <li style="color:var(--text-main);font-weight:500;">Shopping Cart</li>
        </ol>
    </nav>
</div>

<!-- ── STEP INDICATOR ── -->
<div class="steps-wrap d-none d-md-flex">
    <div class="step-item active">
        <div class="step-circle">1</div>
        <span class="step-label">Cart</span>
    </div>
    <div class="step-item">
        <div class="step-circle">2</div>
        <span class="step-label">Shipping</span>
    </div>
    <div class="step-item">
        <div class="step-circle">3</div>
        <span class="step-label">Payment</span>
    </div>
</div>

<!-- ── MAIN ── -->
<div class="cart-wrap">

    <!-- LEFT: CART LIST -->
    <div>
        <h1 class="page-title">Your Cart</h1>
        <p class="page-sub" id="itemCount"><?= count($cart_items) ?> items in your cart</p>

        <!-- Select All Bar -->
        <div class="select-bar">
            <label>
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
                Select All
            </label>
            <button class="btn-delete-selected" id="btnDeleteSelected" onclick="deleteSelected()">
                <i class="bi bi-trash3"></i> Remove Selected
            </button>
        </div>

        <!-- Cart Items -->
        <div class="cart-list" id="cartList">
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-row" id="row-<?= $item['id'] ?>" data-price="<?= $item['price'] ?>" data-qty="<?= $item['qty'] ?>">
                <div class="row-check">
                    <input type="checkbox" class="item-check" onchange="onCheckChange()" data-id="<?= $item['id'] ?>">
                </div>
                <img src="<?= $item['image'] ?>" class="item-img" alt="<?= $item['name'] ?>">
                <div class="item-info">
                    <div class="item-category"><?= $item['category'] ?></div>
                    <div class="item-name"><?= $item['name'] ?></div>
                    <div class="item-variant"><?= $item['variant'] ?></div>
                    <div class="item-price-unit"><?= formatRp($item['price']) ?> / item</div>
                    <div class="mt-2">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, -1)">−</button>
                            <input class="qty-val" type="number"
                                   id="qty-<?= $item['id'] ?>"
                                   value="<?= $item['qty'] ?>"
                                   min="1" max="<?= $item['stock'] ?>"
                                   onchange="onQtyInput(<?= $item['id'] ?>, this.value)">
                            <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, 1)">+</button>
                        </div>
                    </div>
                </div>
                <div class="item-subtotal">
                    <div class="price" id="sub-<?= $item['id'] ?>"><?= formatRp($item['price'] * $item['qty']) ?></div>
                </div>
                <button class="btn-remove" title="Remove" onclick="removeItem(<?= $item['id'] ?>)">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div class="empty-cart" id="emptyCart">
            <div class="empty-icon">🛍️</div>
            <h5>Your cart is empty</h5>
            <p>Looks like you haven't added anything yet.<br>Explore our curated collection.</p>
            <a href="../index.php" class="btn-shop">Start Shopping</a>
        </div>

        <!-- You May Also Like -->
        <div class="also-like">
            <div class="also-like-title">You may also like</div>
            <div class="row g-3 suggest-row">
                <?php
                $suggestions = [
                    ['name'=>'Woven Rattan Shelf','price'=>1850000,'img'=>'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&q=80'],
                    ['name'=>'Stone Candle Holder','price'=>290000,'img'=>'https://images.unsplash.com/photo-1603905757960-3a38abb6e3bc?w=300&q=80'],
                    ['name'=>'Linen Throw Pillow','price'=>345000,'img'=>'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=300&q=80'],
                ];
                foreach ($suggestions as $s): ?>
                <div class="col-4">
                    <div class="suggest-card">
                        <img src="<?= $s['img'] ?>" class="suggest-img" alt="<?= $s['name'] ?>">
                        <div class="suggest-body d-flex align-items-center justify-content-between">
                            <div>
                                <div class="suggest-name"><?= $s['name'] ?></div>
                                <div class="suggest-price"><?= formatRp($s['price']) ?></div>
                            </div>
                            <button class="btn-add-suggest" onclick="window.showToast('<?= $s['name'] ?> added to cart')">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- RIGHT: SUMMARY -->
    <div>
        <div class="summary-card">
            <div class="summary-title">Order Summary</div>

            <!-- Promo -->
            <div class="promo-wrap">
                <input type="text" class="form-control" placeholder="Promo code" id="promoInput">
                <button class="btn-promo" onclick="applyPromo()">Apply</button>
            </div>

            <div class="summary-row">
                <span>Subtotal (<span id="summaryQty"><?= array_sum(array_column($cart_items,'qty')) ?></span> items)</span>
                <span class="val" id="summarySubtotal"><?= formatRp($subtotal) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span class="val"><?= formatRp($shipping) ?></span>
            </div>
            <div class="summary-row">
                <span>Tax (11%)</span>
                <span class="val" id="summaryTax"><?= formatRp($tax) ?></span>
            </div>
            <div class="summary-row promo-active" id="promoRow" style="display:none">
                <span><i class="bi bi-tag-fill me-1"></i>Promo TOMMY10</span>
                <span class="val">− Rp 50.000</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span class="val" id="summaryTotal"><?= formatRp($total) ?></span>
            </div>

            <a href="checkOut.php" class="btn-checkout">
                <i class="bi bi-shield-lock-fill"></i>
                Proceed to Checkout
            </a>
            <a href="../index.php" class="btn-continue">
                <i class="bi bi-arrow-left"></i>
                Continue Shopping
            </a>

            <div class="secure-note">
                <i class="bi bi-shield-check text-success"></i>
                <span>256-bit SSL · Secure Checkout</span>
            </div>
        </div>
    </div>

</div><!-- end cart-wrap -->

<!-- ── TOAST CONTAINER ── -->
<div class="toast-wrap" id="toastWrap"></div>

<script src="../assets/js/main.js"></script>
<script>
// ── Cart state from PHP
const SHIPPING = <?= $shipping ?>;
const TAX_RATE = 0.11;
let promoDiscount = 0;

let cartData = {
    <?php foreach ($cart_items as $item): ?>
    <?= $item['id'] ?>: { price: <?= $item['price'] ?>, qty: <?= $item['qty'] ?>, max: <?= $item['stock'] ?> },
    <?php endforeach; ?>
};

// ── QTY
function changeQty(id, delta) {
    const item = cartData[id];
    if (!item) return;
    const newQty = item.qty + delta;
    if (newQty < 1 || newQty > item.max) return;
    item.qty = newQty;
    document.getElementById('qty-' + id).value = newQty;
    updateRowSubtotal(id);
    updateSummary();
}

function onQtyInput(id, val) {
    const item = cartData[id];
    if (!item) return;
    let qty = parseInt(val) || 1;
    qty = Math.max(1, Math.min(qty, item.max));
    item.qty = qty;
    document.getElementById('qty-' + id).value = qty;
    updateRowSubtotal(id);
    updateSummary();
}

function updateRowSubtotal(id) {
    const item = cartData[id];
    const el = document.getElementById('sub-' + id);
    if (el) el.textContent = formatRp(item.price * item.qty);
}

// ── REMOVE
function removeItem(id) {
    const row = document.getElementById('row-' + id);
    if (!row) return;
    row.classList.add('removing');
    setTimeout(() => {
        row.remove();
        delete cartData[id];
        updateSummary();
        updateItemCount();
        checkEmpty();
    }, 300);
    window.showToast('Item removed from cart');
}

// ── SELECT ALL / DELETE SELECTED
function toggleSelectAll(cb) {
    document.querySelectorAll('.item-check').forEach(c => c.checked = cb.checked);
    onCheckChange();
}

function onCheckChange() {
    const anyChecked = [...document.querySelectorAll('.item-check')].some(c => c.checked);
    const btn = document.getElementById('btnDeleteSelected');
    btn.classList.toggle('visible', anyChecked);
    // sync select all
    const all = document.querySelectorAll('.item-check');
    document.getElementById('selectAll').checked = all.length > 0 && [...all].every(c => c.checked);
}

function deleteSelected() {
    const checked = [...document.querySelectorAll('.item-check:checked')];
    checked.forEach(cb => {
        const id = parseInt(cb.dataset.id);
        removeItem(id);
    });
    document.getElementById('btnDeleteSelected').classList.remove('visible');
}

// ── SUMMARY RECALC
function updateSummary() {
    let subtotal = 0, totalQty = 0;
    Object.values(cartData).forEach(item => {
        subtotal += item.price * item.qty;
        totalQty += item.qty;
    });
    const tax = Math.round(subtotal * TAX_RATE);
    const total = subtotal + SHIPPING + tax - promoDiscount;

    document.getElementById('summarySubtotal').textContent = formatRp(subtotal);
    document.getElementById('summaryTax').textContent = formatRp(tax);
    document.getElementById('summaryTotal').textContent = formatRp(total);
    document.getElementById('summaryQty').textContent = totalQty;
    document.getElementById('cartBadge').textContent = Object.keys(cartData).length;
}

function updateItemCount() {
    const n = Object.keys(cartData).length;
    document.getElementById('itemCount').textContent = n + ' item' + (n !== 1 ? 's' : '') + ' in your cart';
}

function checkEmpty() {
    const empty = Object.keys(cartData).length === 0;
    document.getElementById('emptyCart').style.display = empty ? 'block' : 'none';
    document.querySelector('.select-bar').style.display = empty ? 'none' : 'flex';
    document.getElementById('cartList').style.display = empty ? 'none' : 'block';
}

// ── PROMO
function applyPromo() {
    const code = document.getElementById('promoInput').value.trim().toUpperCase();
    if (code === 'VELORA10') {
        promoDiscount = 50000;
        document.getElementById('promoRow').style.display = 'flex';
        updateSummary();
        window.showToast('✅ Promo VELORA10 applied! − Rp 50.000', 'success');
    } else if (code) {
        window.showToast('❌ Invalid promo code. Try VELORA10', 'error');
    }
}

// showToast — delegates to shared main.js implementation
function showToast(msg, type) {
    if (window.showToast) window.showToast(msg, type);
}

// ── HELPERS
function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}
</script>
</body>
</html>
