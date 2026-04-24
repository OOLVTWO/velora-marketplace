<?php
/**
 * VELORA — Login Page
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
$old_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $old_email = htmlspecialchars($email);

    if (empty($email) || empty($password)) {
        $error = 'Mohon isi semua field yang diperlukan.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        // TODO: Verifikasi ke database
        // Simulasi login berhasil (UI demo)
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name']  = explode('@', $email)[0];
        $success = 'Login berhasil! Mengarahkan ke halaman utama…';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — VELORA Marketplace</title>
<meta name="description" content="Sign in to your VELORA account to access your premium curated marketplace.">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body { min-height: 100vh; display: flex; overflow: hidden; }

/* ── SPLIT PANEL ── */
.auth-panel {
    width: 46%;
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
    width: 600px; height: 600px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(91,63,248,.35) 0%, transparent 70%);
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
.panel-feature {
    display: flex;
    flex-direction: column;
    gap: 20px;
    text-align: left;
    max-width: 280px;
}
.pf-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 14px;
    backdrop-filter: blur(8px);
}
.pf-icon {
    width: 36px; height: 36px;
    background: rgba(91,63,248,.35);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.pf-text strong { display: block; color: #fff; font-size: 13px; margin-bottom: 2px; }
.pf-text span   { color: rgba(255,255,255,.5); font-size: 12px; }

/* ── FORM SIDE ── */
.auth-form-side {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg);
    padding: 60px 48px;
    overflow-y: auto;
}
.auth-form-wrap { width: 100%; max-width: 420px; }
.auth-header { margin-bottom: 36px; }
.auth-header h1 { font-size: 2rem; color: var(--text-main); margin-bottom: 8px; }
.auth-header p  { color: var(--text-muted); font-size: 14px; }

/* Password toggle */
.pass-wrap { position: relative; }
.pass-toggle {
    position: absolute;
    right: 14px; top: 50%;
    transform: translateY(-50%);
    background: none; border: none;
    color: var(--text-soft);
    cursor: pointer;
    font-size: 1rem;
    line-height: 1;
    padding: 4px;
}
.pass-toggle:hover { color: var(--primary); }

/* Social login */
.social-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px; }
.btn-social {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-main);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    text-decoration: none;
}
.btn-social:hover { border-color: var(--primary); background: var(--primary-light); color: var(--primary); }

/* Divider */
.divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
.divider::before,.divider::after { content:''; flex:1; height:1px; background: var(--border); }
.divider span { font-size: 12px; color: var(--text-soft); white-space: nowrap; }

/* Submit */
.auth-submit {
    width: 100%;
    padding: 13px;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s, transform .2s, box-shadow .2s;
    margin-top: 6px;
}
.auth-submit:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 6px 20px var(--primary-glow); }
.auth-submit:active { transform: none; }

.auth-footer { text-align: center; margin-top: 24px; font-size: 13px; color: var(--text-muted); }
.auth-footer a { color: var(--primary); font-weight: 600; }

.form-extras { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px; }
.form-extras label { display: flex; align-items: center; gap: 6px; color: var(--text-muted); cursor: pointer; }
.form-extras a { color: var(--primary); text-decoration: none; }
.form-extras a:hover { text-decoration: underline; }

/* Dark mode toggle on form side */
.auth-topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 48px; }
.auth-topbar a { font-size: 13px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
.auth-topbar a:hover { color: var(--primary); }

@media(max-width:768px){
    body { flex-direction: column; overflow: auto; }
    .auth-panel { display: none; }
    .auth-form-side { padding: 48px 24px; }
}
</style>
</head>
<body>
<!-- Decorative Panel -->
<div class="auth-panel">
    <div class="panel-content">
        <div class="panel-logo">VELORA<span>.</span></div>
        <div class="panel-tagline">The curated marketplace for those who demand more.</div>
        <div class="panel-feature">
            <div class="pf-item">
                <div class="pf-icon">🛍️</div>
                <div class="pf-text">
                    <strong>12,400+ Products</strong>
                    <span>Curated &amp; verified by experts</span>
                </div>
            </div>
            <div class="pf-item">
                <div class="pf-icon">⚡</div>
                <div class="pf-text">
                    <strong>Instant Access</strong>
                    <span>Sign in once, shop everywhere</span>
                </div>
            </div>
            <div class="pf-item">
                <div class="pf-icon">🔒</div>
                <div class="pf-text">
                    <strong>Secure &amp; Private</strong>
                    <span>Your data is always protected</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form Side -->
<div class="auth-form-side">
    <div class="auth-form-wrap">
        <div class="auth-topbar">
            <a href="../index.php"><i class="bi bi-arrow-left"></i> Back to VELORA</a>
            <button class="theme-toggle" title="Toggle theme"><i class="bi bi-moon-fill"></i></button>
        </div>

        <div class="auth-header">
            <h1 class="serif">Welcome back</h1>
            <p>Sign in to continue your curated experience.</p>
        </div>

        <!-- PHP: Tampilkan pesan error/success dari server -->
        <?php if ($error): ?>
        <div class="nx-alert nx-alert-error" role="alert" style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;margin-bottom:20px;font-size:13px;color:#ef4444;">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="nx-alert nx-alert-success" role="alert" style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);border-radius:8px;margin-bottom:20px;font-size:13px;color:#10b981;">
            <i class="bi bi-check-circle-fill"></i>
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <!-- Social Login -->
        <div class="social-row">
            <a href="#" class="btn-social" id="googleBtn"><i class="bi bi-google"></i> Google</a>
            <a href="#" class="btn-social" id="fbBtn"><i class="bi bi-facebook"></i> Facebook</a>
        </div>

        <div class="divider"><span>or continue with email</span></div>

        <!-- Login Form -->
        <form action="" method="POST" id="loginForm">
            <div class="nx-form-group">
                <label for="email">Email Address</label>
                <input class="nx-input" type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
            </div>
            <div class="nx-form-group">
                <label for="password">Password</label>
                <div class="pass-wrap">
                    <input class="nx-input" type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" style="padding-right:44px;">
                    <button type="button" class="pass-toggle" id="passToggle" aria-label="Show password">
                        <i class="bi bi-eye" id="passIcon"></i>
                    </button>
                </div>
            </div>
            <div class="form-extras">
                <label><input type="checkbox" name="remember"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" class="auth-submit" id="loginBtn">
                Sign In <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="signUp.php">Create one — it's free</a>
        </div>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
// Password visibility toggle
const passToggle = document.getElementById('passToggle');
const passInput  = document.getElementById('password');
const passIcon   = document.getElementById('passIcon');
passToggle.addEventListener('click', () => {
    const show = passInput.type === 'password';
    passInput.type = show ? 'text' : 'password';
    passIcon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});

// Social login → go to landing page (UI demo)
document.getElementById('googleBtn').addEventListener('click', e => {
    e.preventDefault();
    window.showToast('Signing in with Google…', 'default');
    setTimeout(() => { window.location.href = '../index.php'; }, 1200);
});
document.getElementById('fbBtn').addEventListener('click', e => {
    e.preventDefault();
    window.showToast('Signing in with Facebook…', 'default');
    setTimeout(() => { window.location.href = '../index.php'; }, 1200);
});

// Form submit — show success toast then redirect
document.getElementById('loginForm').addEventListener('submit', e => {
    e.preventDefault();
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Signing in…';
    btn.style.opacity = '.75';
    btn.disabled = true;
    window.showToast('Welcome back! Redirecting…', 'success');
    setTimeout(() => { window.location.href = '../index.php'; }, 1500);
});
</script>
</body>
</html>
