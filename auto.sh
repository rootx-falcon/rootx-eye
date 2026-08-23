#!/bin/bash

# ============================================
# ROOTX-EYE — AUTO DEPLOY (Cloudflare)
# ============================================

echo ""
echo "🔥 ROOTX-EYE — AUTO DEPLOY STARTING..."
echo ""

# 1. Install cloudflared
echo "[1] Installing Cloudflared..."
pkg install cloudflared -y

# 2. Start server
echo "[2] Starting ROOTX-EYE Server..."
cd ~/ROOTX-EYE
python3 root.py &
sleep 5

# 3. Start Cloudflare Tunnel
echo "[3] Starting Cloudflare Tunnel..."
cloudflared tunnel --url http://localhost:8081

echo ""
echo "✅ DONE! COPY THE LINK ABOVE!"
echo ""
