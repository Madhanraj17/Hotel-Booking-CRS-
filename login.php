<?php
/**
 * login.php — StayPro / GrandHorizon CRS
 * Styled login page with session guard
 */
session_start();
 
// Already logged in? Redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
 
// Capture error from login_process redirect
$error = '';
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — GrandHorizon ERP</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(2, 132, 199, 0.15) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(79, 70, 229, 0.15) 0%, transparent 60%);
        }
 
        .login-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
 
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
 
        .login-logo .logo-icon {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 0.5rem;
        }
 
        .login-logo h1 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #f8fafc;
            letter-spacing: -0.02em;
        }
 
        .login-logo h1 span {
            color: #38bdf8;
            font-size: 0.75rem;
            font-weight: 500;
            display: block;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 2px;
        }
 
        .login-subtitle {
            text-align: center;
            color: #94a3b8;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
 
        .form-group {
            margin-bottom: 1.25rem;
        }
 
        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 0.5rem;
        }
 
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #f8fafc;
            font-size: 0.9375rem;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
 
        .form-input::placeholder { color: #475569; }
 
        .form-input:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }
 
        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }
 
        .error-banner {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #fca5a5;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
 
        .btn-login {
            width: 100%;
            padding: 0.8125rem 1rem;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 0.9375rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 0.5rem;
            letter-spacing: 0.01em;
        }
 
        .btn-login:hover { opacity: 0.9; }
        .btn-login:active { transform: scale(0.99); }
 
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #475569;
            font-size: 0.8rem;
        }
 
        @media (max-width: 480px) {
            .login-card { padding: 2rem 1.25rem; }
        }
    </style>
</head>
<body>
 
<div class="login-card">
    <div class="login-logo">
        <span class="logo-icon">🏨</span>
        <h1>GrandHorizon <span>Enterprise ERP Suite</span></h1>
    </div>
    <p class="login-subtitle">Sign in to access the CRS Booking Engine</p>
 
    <?php if ($error): ?>
        <div class="error-banner" role="alert">
            ⚠️ <?= $error ?>
        </div>
    <?php endif; ?>
 
    <form method="POST" action="login_process.php" novalidate>
        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input
                type="text"
                id="username"
                name="username"
                class="form-input <?= $error ? 'error' : '' ?>"
                placeholder="Enter your username"
                required
                autocomplete="username"
                autofocus>
        </div>
 
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-input <?= $error ? 'error' : '' ?>"
                placeholder="Enter your password"
                required
                autocomplete="current-password">
        </div>
 
        <button type="submit" class="btn-login">Sign In to Dashboard</button>
    </form>
 
    <p class="login-footer">© 2026 GrandHorizon ERP — Authorized access only</p>
</div>
 
</body>
</html>