<?php
/**
 * VELORA — Sign Up Page
 * Developer: Felysia
 */
session_start();

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$error   = '';
$success = '';

// Simpan nilai lama form supaya tidak hilang jika error
$old = [
    'firstName' => '',
    'lastName'  => '',
    'email'     => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName  = trim($_POST['lastName']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']        ?? '';
    $terms     = isset($_POST['terms']);

    $old = [
        'firstName' => htmlspecialchars($firstName),
        'lastName'  => htmlspecialchars($lastName),
        'email'     => htmlspecialchars($email),
    ];

    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $error = 'Mohon isi semua field yang diperlukan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif (!$terms) {
        $error = 'Kamu harus menyetujui Terms of Service terlebih dahulu.';
    } else {
        // TODO: Simpan ke database
        // Simulasi registrasi berhasil (UI demo)
        $_SESSION['user_name']  = $firstName . ' ' . $lastName;
        $_SESSION['user_email'] = $email;
        $success = 'Akun berhasil dibuat! Selamat datang di VELORA 🎉';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — VELORA Marketplace</title>
<meta name="description" content="Join VELORA — the premium curated marketplace. Create your free account today.">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { min-height: 100vh; display: flex; overflow: hidden; }

/* ── DECORATIVE PANEL (right side for signup) ── */
.auth-panel {
    width: 42%;
    order: 2;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background: linear-gradient(135deg, #0d0b1f 0%, #1a0533 40%, #2d1057 100%);
    padding: 60px 48px;
    flex-direction: column;
    text-align: center;
}
.auth-panel::before {
    content: '';
    position: absolute;
    width: 500px; height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(91,63,248,.4) 0%, transparent 70%);
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 4s ease-in-out infinite;
}
.auth-panel::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 40px 40px;
}
@keyframes pulse {
    0%,100% { transform: translate(-50%,-50%) scale(1); opacity: 1; }
    50%      { transform: translate(-50%,-50%) scale(1.1); opacity: .7; }
}
.panel-content { position: relative; z-index: 1; }
.panel-logo { font-family: 'DM Serif Display', serif; font-size: 2.4rem; color: #fff; margin-bottom: 8px; }
.panel-logo span { color: #a78bfa; }
.panel-tagline { color: rgba(255,255,255,.55); font-size: 14px; margin-bottom: 48px; }
.panel-steps { display: flex; flex-direction: column; gap: 0; text-align: left; max-width: 260px; }
.ps-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid rgba(255,255,255,.08);
    position: relative;
}
.ps-item:last-child { border-bottom: none; }
.ps-num {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: rgba(91,63,248,.5);
    border: 1.5px solid rgba(167,139,250,.4);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: #a78bfa;
    flex-shrink: 0;
}
.ps-text strong { display: block; color: #fff; font-size: 13px; margin-bottom: 2px; }
.ps-text span   { color: rgba(255,255,255,.45); font-size: 12px; }

/* ── FORM SIDE ── */
.auth-form-side {
    flex: 1;
    order: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg);
    padding: 48px;
    overflow-y: auto;
}
.auth-form-wrap { width: 100%; max-width: 440px; }
.auth-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 36px; }
.auth-topbar a { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
.auth-topbar a:hover { color: var(--primary); }
.auth-header { margin-bottom: 28px; }
.auth-header h1 { font-size: 2rem; color: var(--text-main); margin-bottom: 6px; }
.auth-header p  { color: var(--text-muted); font-size: 14px; }

/* Password strength */
.pass-wrap { position: relative; }
.pass-toggle {
    position: absolute;
    right: 14px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: var(--text-soft); cursor: pointer;
    font-size: 1rem; padding: 4px;
}
.pass-toggle:hover { color: var(--primary); }
.strength-bar { display: flex; gap: 4px; margin-top: 8px; }
.strength-segment {
    flex: 1; height: 3px;
    border-radius: 4px;
    background: var(--border);
    transition: background .3s;
}
.strength-label { font-size: 11px; color: var(--text-soft); margin-top: 4px; }

/* Name row */
.name-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

/* Social */
.social-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; }
.btn-social {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-main);
    font-size: 13px; font-weight: 500;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    text-decoration: none;
}
.btn-social:hover { border-color: var(--primary); background: var(--primary-light); color: var(--primary); }

/* Divider */
.divider { display: flex; align-items: center; gap: 12px; margin: 16px 0; }
.divider::before,.divider::after { content:''; flex:1; height:1px; background: var(--border); }
.divider span { font-size: 12px; color: var(--text-soft); white-space: nowrap; }

/* Terms */
.terms-check { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; }
.terms-check input[type="checkbox"] { width: 16px; height: 16px; margin-top: 2px; accent-color: var(--primary); cursor: pointer; flex-shrink: 0; }
.terms-check label { font-size: 13px; color: var(--text-muted); line-height: 1.5; cursor: pointer; }
.terms-check a { color: var(--primary); font-weight: 600; }

/* Submit */
.auth-submit {
    width: 100%; padding: 13px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 15px; font-weight: 600;
    cursor: pointer;
    transition: background .2s, transform .2s, box-shadow .2s;
}
.auth-submit:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--primary-glow); }
.auth-submit:active { transform: none; }

.auth-footer { text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-muted); }
.auth-footer a { color: var(--primary); font-weight: 600; }

@media(max-width:768px){
    body { flex-direction: column; overflow: auto; }
    .auth-panel { display: none; order: 0; }
    .auth-form-side { padding: 40px 24px; order: 1; }
    .name-row { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- Form Side -->
<div class="auth-form-side">
    <div class="auth-form-wrap">
        <div class="auth-topbar">
            <a href="../index.php"><i class="bi bi-arrow-left"></i> Back to VELORA</a>
            <button class="theme-toggle" title="Toggle theme"><i class="bi bi-moon-fill"></i></button>
        </div>

        <div class="auth-header">
            <h1 class="serif">Create your account</h1>
            <p>Join thousands of curators. It's free forever.</p>
        </div>

        <!-- PHP: Tampilkan pesan error/success dari server -->
        <?php if ($error): ?>
        <div role="alert" style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;margin-bottom:20px;font-size:13px;color:#ef4444;">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div role="alert" style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:8px;margin-bottom:20px;font-size:13px;color:#10b981;">
            <i class="bi bi-check-circle-fill"></i>
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- Social -->
        <div class="social-row">
            <a href="#" class="btn-social" id="googleBtn"><i class="bi bi-google"></i> Google</a>
            <a href="#" class="btn-social" id="fbBtn"><i class="bi bi-facebook"></i> Facebook</a>
        </div>
        <div class="divider"><span>or sign up with email</span></div>

        <!-- Signup Form -->
        <form action="" method="POST" id="signupForm">
            <div class="name-row">
                <div class="nx-form-group">
                    <label for="firstName">First Name</label>
                    <input class="nx-input" type="text" id="firstName" name="firstName"
                           placeholder="John" required autocomplete="given-name"
                           value="<?= $old['firstName'] ?>">
                </div>
                <div class="nx-form-group">
                    <label for="lastName">Last Name</label>
                    <input class="nx-input" type="text" id="lastName" name="lastName"
                           placeholder="Doe" required autocomplete="family-name"
                           value="<?= $old['lastName'] ?>">
                </div>
            </div>

            <div class="nx-form-group">
                <label for="email">Email Address</label>
                <input class="nx-input" type="email" id="email" name="email"
                       placeholder="you@example.com" required autocomplete="email"
                       value="<?= $old['email'] ?>">
            </div>

            <div class="nx-form-group">
                <label for="password">Password</label>
                <div class="pass-wrap">
                    <input class="nx-input" type="password" id="password" name="password" placeholder="Min. 8 characters" required autocomplete="new-password" style="padding-right:44px;" oninput="checkStrength(this.value)">
                    <button type="button" class="pass-toggle" id="passToggle"><i class="bi bi-eye" id="passIcon"></i></button>
                </div>
                <div class="strength-bar">
                    <div class="strength-segment" id="s1"></div>
                    <div class="strength-segment" id="s2"></div>
                    <div class="strength-segment" id="s3"></div>
                    <div class="strength-segment" id="s4"></div>
                </div>
                <div class="strength-label" id="strengthLabel">Enter a password</div>
            </div>

            <div class="terms-check">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</label>
            </div>

            <button type="submit" class="auth-submit" id="signupBtn">
                Create Free Account <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign in</a>
        </div>
    </div>
</div>

<!-- Decorative Panel -->
<div class="auth-panel">
    <div class="panel-content">
        <div class="panel-logo">VELORA<span>.</span></div>
        <div class="panel-tagline">Your curated journey starts here.</div>
        <div class="panel-steps">
            <div class="ps-item">
                <div class="ps-num">1</div>
                <div class="ps-text">
                    <strong>Create Your Account</strong>
                    <span>Quick, free, no card needed</span>
                </div>
            </div>
            <div class="ps-item">
                <div class="ps-num">2</div>
                <div class="ps-text">
                    <strong>Browse the Collection</strong>
                    <span>12,400+ curated products</span>
                </div>
            </div>
            <div class="ps-item">
                <div class="ps-num">3</div>
                <div class="ps-text">
                    <strong>Checkout Seamlessly</strong>
                    <span>Secure, fast &amp; tracked delivery</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
// Password toggle
const passToggle = document.getElementById('passToggle');
const passInput  = document.getElementById('password');
const passIcon   = document.getElementById('passIcon');
passToggle.addEventListener('click', () => {
    const show = passInput.type === 'password';
    passInput.type = show ? 'text' : 'password';
    passIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});

// Strength checker
function checkStrength(val) {
    const segs = [document.getElementById('s1'), document.getElementById('s2'),
                  document.getElementById('s3'), document.getElementById('s4')];
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8)        score++;
    if (/[A-Z]/.test(val))      score++;
    if (/[0-9]/.test(val))      score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['', '#EF4444', '#F59E0B', '#3B82F6', '#10B981'];
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    segs.forEach((s, i) => { s.style.background = i < score ? colors[score] : 'var(--border)'; });
    label.textContent = val.length === 0 ? 'Enter a password' : labels[score];
    label.style.color = colors[score] || 'var(--text-soft)';
}

// Social login → landing page
document.getElementById('googleBtn').addEventListener('click', e => {
    e.preventDefault();
    window.showToast('Signing up with Google…', 'default');
    setTimeout(() => { window.location.href = '../index.php'; }, 1200);
});
document.getElementById('fbBtn').addEventListener('click', e => {
    e.preventDefault();
    window.showToast('Signing up with Facebook…', 'default');
    setTimeout(() => { window.location.href = '../index.php'; }, 1200);
});

// Submit feedback → success toast then redirect
document.getElementById('signupForm').addEventListener('submit', e => {
    e.preventDefault();
    const btn = document.getElementById('signupBtn');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating account…';
    btn.style.opacity = '.75';
    btn.disabled = true;
    window.showToast('Account created! Welcome to VELORA 🎉', 'success');
    setTimeout(() => { window.location.href = '../index.php'; }, 1800);
});
</script>
</body>
</html>
