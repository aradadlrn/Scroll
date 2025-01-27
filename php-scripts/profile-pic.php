<?php
session_start();
include 'connect.php'; // Include your database connection

header('Content-Type: application/json');

// Handle different actions
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'upload':
            handleUpload();
            break;
            
        case 'save':
            saveProfile();
            break;
            
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

function handleUpload() {
    // Validate uploaded file
    if (!isset($_FILES['profilePic']) || $_FILES['profilePic']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed');
    }

    $file = $_FILES['profilePic'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and GIF are allowed.');
    }

    // Validate file size (max 2MB)
    if ($file['size'] > 2097152) {
        throw new Exception('File size too large. Maximum 2MB allowed.');
    }

    // Create upload directory if not exists
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Sanitize filename
    $filename = preg_replace('/[^a-zA-Z0-9\-_.]/', '_', $file['name']);
    $filename = time() . '_' . $filename;
    $targetPath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to save uploaded file');
    }

    // Return relative path
    echo json_encode([
        'filePath' => '/' . $targetPath
    ]);
}

function saveProfile() {
    global $pdo; // From your db_connect.php
    
    // Validate user session
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not authenticated');
    }

    // Get input data
    $avatarPath = $_POST['avatarPath'] ?? '';
    $userId = $_SESSION['user_id'];

    // Validate avatar path
    if (!preg_match('/^\/uploads\/[a-zA-Z0-9\-_.]+$/', $avatarPath)) {
        throw new Exception('Invalid avatar path');
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE userregister SET profile_picture = ? WHERE id = ?");
    $stmt->execute([$avatarPath, $userId]);

    echo json_encode([
        'success' => true,
        'newPath' => $avatarPath
    ]);
}
?>