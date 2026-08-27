<?php
$ip = $_GET['user'] ?? '';
$index = $_GET['download'] ?? '';

if (empty($ip) || $index === '' || !isset($index)) {
    die('Invalid request');
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

if (!$user_data || !isset($user_data['captures'][$index])) {
    die('Capture not found');
}

$capture = $user_data['captures'][$index];
$file = $capture['file'] ?? '';

if (empty($file) || !file_exists($file)) {
    die('File not found');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
?>
