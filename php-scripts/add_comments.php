<?php
include 'connect.php';

header('Content-Type: application/json');
session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = isset($data['postId']) ? (int)$data['postId'] : 0;
    $content = isset($data['Comment']) ? trim($data['Comment']) : '';
    $userId = isset($_SESSION['registerId']) ? (int)$_SESSION['registerId'] : 0;

    if ($postId > 0 && $userId > 0 && !empty($content)) {
        // Add the comment
        $stmt = $db->prepare("INSERT INTO comments (idUpload, RegisterId, Comment) VALUES (:postId, :userId, :content)");
        $stmt->execute(['postId' => $postId, 'userId' => $userId, 'content' => $content]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>
