<?php
// checkout.php - Tommy Marketplace | Checkout & Payment Page
// Developer: Jody

session_start();

// Dummy cart data (in real app, this comes from session/database)
$cart_items = [
    [
        'id' => 1,
        'name' => 'Ergonomic Oak Chair',
        'variant' => 'Natural / Walnut',
        'price' => 3200000,  
        'qty' => 1,
        'image' => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=120&q=80'
    ],
    [
        'id' => 2,
        'name' => 'Minimal Desk Lamp',
        'variant' => 'Matte Black',
        'price' => 780000,
        'qty' => 2,
        'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=120&q=80'
    ],
];

$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cart_items));
$shipping = 45000;
$tax = round($subtotal * 0.11);
$total = $subtotal + $shipping + $tax;

function formatRp($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Handle form submission
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process payment here
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — VELORA Marketplace</title>

    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="description" content="Secure checkout for VELORA Marketplace — shipping, payment, and confirmation.">

    <style>
        /* VELORA tokens are inherited — add only extras */

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            font-size: 15px;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            height: 64px;
        }
        .navbar-brand {
            font-family: 'DM Serif Display', serif;
            font-size: 1.25rem;
            color: var(--text-main) !important;
            letter-spacing: -0.3px;
        }
        .nav-step {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }
        .nav-step .step {
            width: 24px; height: 24px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600;
        }
        .nav-step .step.done { background: var(--success); border-color: var(--success); color: white; }
        .nav-step .step.active { background: var(--primary); border-color: var(--primary); color: white; }
        .nav-step .divider { width: 32px; height: 1px; background: var(--border); }

        /* ── MAIN LAYOUT ── */
        .checkout-wrap {
            max-width: 1080px;
            margin: 80px auto 60px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 28px;
            align-items: start;
        }

        /* ── CARDS ── */
        .card-section {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 32px;
            margin-bottom: 20px;
        }
        .card-section:last-child { margin-bottom: 0; }
        .section-title {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        /* ── FORM ── */
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 6px;
        }
        .form-control, .form-select {
            display: block;
            width: 100%;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            transition: border-color .2s, box-shadow .2s;
            background: var(--surface);
            color: var(--text-main);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(91,63,248,.12);
            outline: none;
        }

        /* ── PAYMENT METHOD ── */
        .pay-option {
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
            position: relative;
        }
        .pay-option:last-child { margin-bottom: 0; }
        .pay-option:hover { border-color: var(--primary); background: var(--primary-light); }
        .pay-option.selected { border-color: var(--primary); background: var(--primary-light); }
        .pay-option input[type="radio"] { accent-color: var(--primary); width: 16px; height: 16px; }
        .pay-option .pay-label { font-weight: 500; font-size: 14px; flex: 1; }
        .pay-option .pay-sub { font-size: 12px; color: var(--text-muted); }
        .pay-icon {
            width: 38px; height: 28px;
            background: var(--bg);
            border-radius: 5px;
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }

        /* Card detail sub-form */
        .card-detail-form {
            display: none;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }
        .card-detail-form.show { display: block; }
        .card-number-wrap { position: relative; }
        .card-number-wrap .card-type-icon {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            font-size: 18px; color: var(--text-muted);
        }

        /* ── ORDER SUMMARY ── */
        .summary-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 28px 28px;
            position: sticky;
            top: 24px;
        }
        .summary-title {
            font-family: 'DM Serif Display', serif;
            font-size: 1.15rem;
            margin-bottom: 20px;
        }
        .cart-item {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        .cart-item:last-child { margin-bottom: 20px; }
        .cart-img {
            width: 56px; height: 56px;
            border-radius: 8px;
            object-fit: cover;
            background: var(--bg);
            flex-shrink: 0;
            border: 1px solid var(--border);
        }
        .cart-info .name { font-size: 14px; font-weight: 500; line-height: 1.3; }
        .cart-info .variant { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .cart-info .price { font-size: 14px; font-weight: 600; margin-top: 4px; color: var(--text-main); }
        .qty-badge {
            display: inline-block;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 0 6px;
            font-size: 11px;
            color: var(--text-muted);
            margin-left: 6px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
            color: var(--text-muted);
        }
        .summary-row .val { color: var(--text-main); font-weight: 500; }
        .summary-row.total {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
            padding-top: 12px;
            margin-top: 4px;
            border-top: 1.5px solid var(--text-main);
        }
        .summary-row.total .val { color: var(--primary); }

        /* Promo */
        .promo-wrap {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }
        .promo-wrap .form-control { font-size: 13px; padding: 9px 12px; }
        .btn-promo {
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0 16px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: all .2s;
        }
        .btn-promo:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }

        /* CTA */
        .btn-pay {
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }
        .btn-pay:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-pay:active { transform: translateY(0); }

        .secure-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 12px;
        }

        /* ── SUCCESS MODAL ── */
        .success-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(14,14,14,.45);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }
        .success-overlay.show { display: flex; }
        .success-box {
            background: white;
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            animation: popIn .35s cubic-bezier(.34,1.56,.64,1);
        }
        @keyframes popIn {
            from { opacity: 0; transform: scale(.85); }
            to   { opacity: 1; transform: scale(1); }
        }
        .success-icon {
            width: 72px; height: 72px;
            background: #ECFDF5;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            color: var(--success);
            margin: 0 auto 20px;
        }
        .success-box h4 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        .success-box p { font-size: 14px; color: var(--text-muted); margin-bottom: 28px; }
        .order-id {
            background: var(--bg);
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 13px;
            font-family: monospace;
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 24px;
            letter-spacing: .5px;
        }
        .btn-done {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            padding: 12px 32px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
        }
        .btn-done:hover { background: var(--primary-dark); }

        @media (max-width: 768px) {
            .checkout-wrap { grid-template-columns: 1fr; }
            .summary-card { position: static; order: -1; }
            .nav-step { display: none; }
            .card-section { padding: 20px 18px; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR (VELORA shared) ── -->
<nav class="nx-navbar" id="navbar">
    <a href="../index.php" class="nx-brand">VELORA<span class="dot">.</span></a>
    <ul class="nx-nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="shoppingCart.php">Cart</a></li>
    </ul>
    <div class="nx-nav-actions">
        <button class="theme-toggle" title="Toggle theme"><i class="bi bi-moon-fill"></i></button>
        <span style="font-size:13px;color:var(--text-muted);"><i class="bi bi-lock me-1"></i>Secure Checkout</span>
    </div>
    <button class="nx-hamburger" id="hamburger" aria-label="Menu"><span></span><span></span><span></span></button>
</nav>
<div class="nx-mobile-menu" id="mobileMenu">
    <a href="../index.php">Home</a>
    <a href="shoppingCart.php">Cart</a>
</div>

<!-- ── MAIN ── -->
<div class="checkout-wrap">

    <!-- LEFT COLUMN -->
    <div>

        <!-- Shipping Address -->
        <div class="card-section">
            <div class="section-title">Shipping Address</div>
            <form id="checkoutForm" method="POST" action="">
            <div class="row g-3" style="grid-template-columns:1fr 1fr;">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" placeholder="Jody" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Santoso" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="jody@email.com" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+62 812 0000 0000" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Street Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Jl. Kemang Raya No. 12" required>
                </div>
                <div class="col-12" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" placeholder="Jakarta" required>
                    </div>
                    <div>
                        <label class="form-label">Province</label>
                        <select name="province" class="form-select" required>
                            <option value="">Select...</option>
                            <option>DKI Jakarta</option>
                            <option>Jawa Barat</option>
                            <option>Jawa Tengah</option>
                            <option>Jawa Timur</option>
                            <option>Banten</option>
                            <option>Bali</option>
                            <option>Sumatera Utara</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal" class="form-control" placeholder="12160" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Method -->
        <div class="card-section">
            <div class="section-title">Shipping Method</div>
            <div class="row g-2 three-col">
                <?php
                $shippings = [
                    ['id'=>'reg', 'name'=>'Regular', 'desc'=>'3-5 business days', 'price'=>'Rp 45.000', 'icon'=>'bi-box'],
                    ['id'=>'exp', 'name'=>'Express', 'desc'=>'1-2 business days',  'price'=>'Rp 85.000', 'icon'=>'bi-lightning-charge'],
                    ['id'=>'same','name'=>'Same Day', 'desc'=>'Within 12 hours',   'price'=>'Rp 150.000','icon'=>'bi-rocket-takeoff'],
                ];
                foreach ($shippings as $s): ?>
                <div class="col-md-4">
                    <label class="pay-option <?= $s['id']==='reg' ? 'selected' : '' ?>" style="flex-direction:column; align-items:flex-start; gap:6px;">
                        <div class="d-flex align-items-center gap-2 w-100">
                            <input type="radio" name="shipping_method" value="<?= $s['id'] ?>"
                                   <?= $s['id']==='reg' ? 'checked' : '' ?> onchange="selectShipping(this)">
                            <span class="pay-label"><i class="bi <?= $s['icon'] ?> me-1"></i><?= $s['name'] ?></span>
                        </div>
                        <div style="padding-left:24px;">
                            <div class="pay-sub"><?= $s['desc'] ?></div>
                            <div style="font-size:13px;font-weight:600;color:var(--primary);margin-top:2px"><?= $s['price'] ?></div>
                        </div>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="card-section">
            <div class="section-title">Payment Method</div>

            <!-- Credit / Debit Card -->
            <div class="pay-option selected" onclick="selectPayment('card', this)">
                <input type="radio" name="payment" id="pay_card" value="card" checked>
                <div class="pay-icon">💳</div>
                <div>
                    <div class="pay-label">Credit / Debit Card</div>
                    <div class="pay-sub">Visa, Mastercard, JCB</div>
                </div>
            </div>

            <!-- Card Detail Form -->
            <div class="card-detail-form show" id="cardForm">
                <div class="row g-3" style="grid-template-columns:1fr 1fr;">
                    <div class="col-12">
                        <label class="form-label">Card Number</label>
                        <div class="card-number-wrap">
                            <input type="text" name="card_number" class="form-control"
                                   placeholder="0000  0000  0000  0000" maxlength="19"
                                   oninput="formatCard(this)">
                            <span class="card-type-icon" id="cardTypeIcon">💳</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Cardholder Name</label>
                        <input type="text" name="card_name" class="form-control" placeholder="JODY SANTOSO">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Expiry Date</label>
                        <input type="text" name="expiry" class="form-control" placeholder="MM / YY"
                               maxlength="7" oninput="formatExpiry(this)">
                    </div>
                    <div class="col-6">
                        <label class="form-label">CVV</label>
                        <input type="password" name="cvv" class="form-control" placeholder="•••" maxlength="4">
                    </div>
                </div>
            </div>

            <!-- Transfer Bank -->
            <div class="pay-option" onclick="selectPayment('bank', this)">
                <input type="radio" name="payment" id="pay_bank" value="bank">
                <div class="pay-icon">🏦</div>
                <div>
                    <div class="pay-label">Bank Transfer</div>
                    <div class="pay-sub">BCA, Mandiri, BNI, BRI</div>
                </div>
            </div>

            <!-- QRIS -->
            <div class="pay-option" onclick="selectPayment('qris', this)">
                <input type="radio" name="payment" id="pay_qris" value="qris">
                <div class="pay-icon">📱</div>
                <div>
                    <div class="pay-label">QRIS</div>
                    <div class="pay-sub">GoPay, OVO, DANA, ShopeePay</div>
                </div>
            </div>

            <!-- COD -->
            <div class="pay-option" onclick="selectPayment('cod', this)">
                <input type="radio" name="payment" id="pay_cod" value="cod">
                <div class="pay-icon">💵</div>
                <div>
                    <div class="pay-label">Cash on Delivery</div>
                    <div class="pay-sub">Pay when delivered</div>
                </div>
            </div>

            </form><!-- end form -->
        </div>

    </div><!-- end left -->

    <!-- RIGHT COLUMN — ORDER SUMMARY -->
    <div>
        <div class="summary-card">
            <div class="summary-title">Order Summary</div>

            <!-- Cart Items -->
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <img src="<?= $item['image'] ?>" class="cart-img" alt="<?= $item['name'] ?>">
                <div class="cart-info flex-grow-1">
                    <div class="name">
                        <?= $item['name'] ?>
                        <span class="qty-badge">×<?= $item['qty'] ?></span>
                    </div>
                    <div class="variant"><?= $item['variant'] ?></div>
                    <div class="price"><?= formatRp($item['price'] * $item['qty']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Promo -->
            <div class="promo-wrap">
                <input type="text" class="form-control" placeholder="Promo code" id="promoInput">
                <button class="btn-promo" type="button" onclick="applyPromo()">Apply</button>
            </div>

            <!-- Summary Rows -->
            <div class="summary-row">
                <span>Subtotal</span>
                <span class="val"><?= formatRp($subtotal) ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span class="val" id="shippingCost"><?= formatRp($shipping) ?></span>
            </div>
            <div class="summary-row">
                <span>Tax (11%)</span>
                <span class="val"><?= formatRp($tax) ?></span>
            </div>
            <div class="summary-row" id="promoRow" style="display:none; color:var(--success)">
                <span>Promo</span>
                <span class="val" style="color:var(--success)">− Rp 0</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span class="val" id="totalAmount"><?= formatRp($total) ?></span>
            </div>

            <button class="btn-pay" type="button" onclick="submitOrder()">
                <i class="bi bi-shield-lock-fill"></i>
                Pay Now · <?= formatRp($total) ?>
            </button>

            <div class="secure-note">
                <i class="bi bi-shield-check text-success"></i>
                <span>256-bit SSL Encryption · Secured Payment</span>
            </div>
        </div>
    </div>

</div><!-- end checkout-wrap -->

<!-- ── SUCCESS OVERLAY ── -->
<div class="success-overlay" id="successOverlay">
    <div class="success-box">
        <div class="success-icon">✓</div>
        <h4>Payment Successful!</h4>
        <p>Your order has been placed and is being processed. You'll receive a confirmation email shortly.</p>
        <div class="order-id" id="orderId">#TM-<?= strtoupper(substr(uniqid(), -6)) ?></div>
        <button class="btn-done" onclick="window.location.href='../index.php'">Back to Home</button>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
// ── Payment selection
function selectPayment(type, el) {
    document.querySelectorAll('.pay-option').forEach(o => {
        o.classList.remove('selected');
        o.querySelector('input[type="radio"]').checked = false;
    });
    el.classList.add('selected');
    el.querySelector('input[type="radio"]').checked = true;

    const cardForm = document.getElementById('cardForm');
    if (type === 'card') {
        cardForm.classList.add('show');
    } else {
        cardForm.classList.remove('show');
    }
}

// ── Shipping selection
const shippingPrices = { reg: 45000, exp: 85000, same: 150000 };
const subtotal = <?= $subtotal ?>;
const tax = <?= $tax ?>;

function selectShipping(radio) {
    document.querySelectorAll('.pay-option').forEach(o => {
        if (o.querySelector('input[name="shipping_method"]')) o.classList.remove('selected');
    });
    radio.closest('.pay-option').classList.add('selected');

    const cost = shippingPrices[radio.value] || 45000;
    const total = subtotal + cost + tax;
    document.getElementById('shippingCost').textContent = formatRp(cost);
    document.getElementById('totalAmount').textContent = formatRp(total);
    document.querySelector('.btn-pay').innerHTML =
        `<i class="bi bi-shield-lock-fill"></i> Pay Now · ${formatRp(total)}`;
}

// ── Card number formatting
function formatCard(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = v.match(/.{1,4}/g)?.join('  ') || v;

    const icon = document.getElementById('cardTypeIcon');
    if (v.startsWith('4')) icon.textContent = '💳'; // Visa
    else if (v.startsWith('5')) icon.textContent = '💳'; // MC
    else icon.textContent = '💳';
}

// ── Expiry formatting
function formatExpiry(input) {
    let v = input.value.replace(/\D/g, '').substring(0, 4);
    if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2);
    input.value = v;
}

// ── Promo
function applyPromo() {
    const code = document.getElementById('promoInput').value.trim().toUpperCase();
    if (code === 'VELORA10') {
        document.getElementById('promoRow').style.display = 'flex';
        document.getElementById('promoRow').querySelector('.val').textContent = '− Rp 50.000';
        window.showToast('✅ Promo VELORA10 applied! Discount Rp 50.000', 'success');
    } else if (code) {
        window.showToast('❌ Invalid promo code. Try VELORA10', 'error');
    }
}

// ── Submit
function submitOrder() {
    const form = document.getElementById('checkoutForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let valid = true;
    inputs.forEach(i => {
        if (!i.value.trim()) {
            i.classList.add('is-invalid');
            valid = false;
        } else {
            i.classList.remove('is-invalid');
        }
    });
    if (!valid) {
        window.showToast('⚠️ Please fill all required fields', 'error');
        return;
    }
    document.getElementById('successOverlay').classList.add('show');
}

// ── Helpers
function formatRp(n) {
    return 'Rp ' + n.toLocaleString('id-ID');
}
</script>
</body>
</html>
