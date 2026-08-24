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
║              ███████╗██╗   ██╗███████╗                       ║
║              ██╔════╝╚██╗ ██╔╝██╔════╝                       ║
║              █████╗   ╚████╔╝ █████╗                         ║
║              ██╔══╝    ╚██╔╝  ██╔══╝                         ║
║              ███████╗   ██║   ███████╗                       ║
║              ╚══════╝   ╚═╝   ╚══════╝                       ║
║                                                               ║
║           🔥 ROOTX EYE — TERMUX CINEMATIC EDITION 🔥        ║
║                                                               ║
║   📦 6 MODULES | 📊 LIVE PANEL | 🚀 RENDER INTEGRATED       ║
║   💀 MATRIX RAIN | GLITCH EFFECTS | NO COPYRIGHT             ║
╚═══════════════════════════════════════════════════════════════╝
"""

import os
import sys
import time
import random
import requests
import json
import subprocess

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
REV = '\033[7m'

# ================= CONFIG =================
RENDER_URL = "https://rootx-eye-1.onrender.com"

# ================= CINEMATIC EFFECTS =================

def matrix_rain(duration=3):
    """Matrix rain animation"""
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
    """Animated loading bar"""
    bar_len = 40
    steps = 40
    
    sys.stdout.write(f"\n{Y}{text}{W}\n")
    for i in range(steps + 1):
        percent = int((i / steps) * 100)
        filled = int((i / steps) * bar_len)
        bar = '█' * filled + '░' * (bar_len - filled)
        
        if percent < 33:
            color = R
        elif percent < 66:
            color = Y
        else:
            color = G
        
        sys.stdout.write(f"\r[{color}{bar}{W}] {percent}%")
        sys.stdout.flush()
        time.sleep(duration / steps)
    sys.stdout.write("\n")
    sys.stdout.flush()

def glitch_effect(text):
    """Glitch text effect"""
    chars = "!@#$%^&*()_+{}|:<>?~"
    for _ in range(3):
        glitched = ''.join(random.choice(chars) if random.random() < 0.3 else c for c in text)
        sys.stdout.write(f"\r{R}{glitched}{W}")
        sys.stdout.flush()
        time.sleep(0.05)
    sys.stdout.write(f"\r{G}{text}{W}\n")
    sys.stdout.flush()

def typewriter(text, delay=0.02):
    """Typewriter effect"""
    for char in text:
        sys.stdout.write(char)
        sys.stdout.flush()
        time.sleep(delay)
    sys.stdout.write("\n")
    sys.stdout.flush()

# ================= ANIMATED BANNER =================

def animated_banner():
    """Full animated banner with matrix, glitch, and loading effects"""
    os.system('clear')
    
    # Matrix intro
    print(f"{DIM}Initializing ROOTX EYE engine...{W}")
    time.sleep(0.5)
    matrix_rain(2)
    
    # Loading
    loading_bar("Loading ROOTX EYE modules", 1.5)
    
    # Glitch logo
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
    
    # Status box
    border = "╔" + "═" * 54 + "╗"
    print(f"\n{R}{border}{W}")
    print(f"{R}║{W}  {G}[✓] ROOTX EYE engine initialized{W}                     {R}║{W}")
    print(f"{R}║{W}  {C}[✓] Interface loaded{W}                                  {R}║{W}")
    print(f"{R}║{W}  {Y}[✓] Modules detected: 06{W}                             {R}║{W}")
    print(f"{R}║{W}  {G}[✓] Render integrated{W}                                {R}║{W}")
    print(f"{R}╚" + "═" * 54 + "╝{W}")
    
    time.sleep(0.5)

# ================= BANNER =================

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
    print(f"{BOLD}{O}         🔥 ROOTX EYE — TERMUX CINEMATIC EDITION 🔥{W}")
    print(f"{C}═══════════════════════════════════════════════════════════════{W}")
    print(f"{G}   📦 6 MODULES | 📊 LIVE PANEL | 🚀 RENDER INTEGRATED{W}")
    print(f"{G}   💀 MATRIX RAIN | GLITCH EFFECTS | NO COPYRIGHT{W}")
    print(f"{C}═══════════════════════════════════════════════════════════════{W}")

# ================= FUNCTIONS =================

def show_links():
    print(f"\n{G}[+] VICTIM LINKS:{W}")
    print(f"{C}═══════════════════════════════════════════{W}")
    
    modules = {
        "📷 Front Camera": "cf",
        "📹 Back Camera": "cb",
        "📍 GPS Location": "loc",
        "🎤 Voice Recorder": "vo",
        "🎥 Front Video": "vf",
        "📼 Back Video": "vb"
    }
    
    for name, code in modules.items():
        print(f"{Y}{name}:{W}")
        print(f"{B}{RENDER_URL}/captcha.php?target={code}&id=123456789{W}\n")
    
    print(f"{C}═══════════════════════════════════════════{W}")
    print(f"{G}[✓] Copy any link and share with victim!{W}")

def show_admin():
    print(f"\n{G}[+] ADMIN PANEL:{W}")
    print(f"{C}═══════════════════════════════════════════{W}")
    print(f"{B}{RENDER_URL}/admin.php{W}")
    print(f"{C}═══════════════════════════════════════════{W}")
    print(f"{Y}📊 Username: rootx{W}")
    print(f"{Y}🔑 Password: rootx123{W}")
    print(f"{C}═══════════════════════════════════════════{W}")

def check_captures():
    try:
        resp = requests.get(f"{RENDER_URL}/api/status", timeout=5)
        data = resp.json()
        print(f"\n{G}[+] CAPTURES STATUS:{W}")
        print(f"{C}═══════════════════════════════════════════{W}")
        print(f"{B}📦 Total Captures: {data.get('total', 0)}{W}")
        types = data.get('types', {})
        for t, c in types.items():
            print(f"{P}📱 {t}: {c}{W}")
        print(f"{C}═══════════════════════════════════════════{W}")
    except:
        print(f"{R}[!] Could not fetch status.{W}")
        print(f"{Y}[!] Check Render URL: {RENDER_URL}{W}")

# ================= MAIN MENU =================

def main():
    # Show animated banner once
    animated_banner()
    
    while True:
        banner()
        print(f"\n{C}═══════════════════════════════════════════════════════════════{W}")
        print(f"{B}🌐 RENDER URL: {RENDER_URL}{W}")
        print(f"{C}═══════════════════════════════════════════════════════════════{W}")
        
        print(f"\n{Y}[1] 🔗 GENERATE VICTIM LINKS{W}")
        print(f"{B}[2] 📊 SHOW ADMIN PANEL{W}")
        print(f"{P}[3] 📦 CHECK CAPTURES COUNT{W}")
        print(f"{R}[4] 🚪 EXIT{W}")
        print(f"{C}═══════════════════════════════════════════════════════════════{W}")
        
        choice = input(f"{Y}➜ CHOOSE: {W}")
        
        if choice == '1':
            loading_bar("Generating Victim Links", 1)
            show_links()
            input(f"\n{Y}Press Enter...{W}")
        elif choice == '2':
            loading_bar("Loading Admin Panel", 1)
            show_admin()
            input(f"\n{Y}Press Enter...{W}")
        elif choice == '3':
            loading_bar("Fetching Captures", 1)
            check_captures()
            input(f"\n{Y}Press Enter...{W}")
        elif choice == '4':
            print(f"\n{R}[!] Exiting ROOTX EYE...{W}")
            matrix_rain(2)
            print(f"\n{G}🔥 ROOTX EYE terminated successfully{W}")
            sys.exit(0)
        else:
            print(f"{R}[!] Invalid choice!{W}")
            input(f"{Y}Press Enter...{W}")

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print(f"\n{R}[!] Interrupted by user{W}")
        sys.exit(0)
