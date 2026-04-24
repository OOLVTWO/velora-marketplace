# VELORA Marketplace

> A premium curated marketplace for unique products and specialist services.

## 🚀 How to Run

Open in your browser:
```
http://localhost/Website/product&services/
```
This will automatically open the **Landing Page** (`index.php`).

---

## 📁 Folder Structure

```
product&services/
│
├── index.php                 ← Entry point (Landing Page) — Tommy
│
├── assets/
│   ├── css/style.css         ← Shared design system (all pages)
│   └── js/main.js            ← Shared JS (dark mode, toast, animations)
│
├── auth/
│   ├── login.php             ← Sign In page — Felysia
│   ├── signUp.php            ← Register page — Felysia
│   └── logout.php            ← Logout handler — Felysia
│
└── store/
    ├── shoppingCart.php      ← Shopping Cart — Jody
    └── checkOut.php          ← Checkout & Payment — Jody
```

---

## 👥 Team

| Name | Page | Role |
|------|------|------|
| Tommy | `index.php` | Landing Page |
| Jody | `store/shoppingCart.php`, `store/checkOut.php` | Store & Checkout |
| Felysia | `auth/login.php`, `auth/signUp.php` | Authentication |

---

## 🔑 Demo Credentials

| Feature | Value |
|---------|-------|
| Promo Code | `VELORA10` (discount Rp 50.000) |

---

## 🛒 User Flow

```
Landing Page → Shopping Cart → Checkout → Payment Success → Back to Home
```

1. Klik **"Explore"** / **"Reserve Seat"** / **"Acquire Now"** di landing page
2. Review produk di **Shopping Cart**
3. Klik **"Proceed to Checkout"**
4. Isi alamat pengiriman & pilih metode pembayaran
5. Klik **"Pay Now"** → muncul success overlay
6. Klik **"Back to Home"** → kembali ke landing page

---

## ⚙️ Tech Stack

- **PHP** — Server-side logic (session, form handling, data rendering)
- **HTML5 + CSS3** — Struktur dan styling
- **Vanilla JS** — Interaktivitas (toast, dark mode, animasi)
- **XAMPP** — Local development server
