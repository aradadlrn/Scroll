<?php
include 'connect.php';

header('Content-Type: application/json'); // Ensure the response is JSON
session_start();

try {
    $postId = isset($_GET['postId']) ? (int)$_GET['postId'] : 0;

    if ($postId > 0) {
        // Get the latest comment count
        $stmt = $db->prepare("SELECT COUNT(*) AS comment_count FROM comments WHERE idUpload = :postId");
        $stmt->execute(['postId' => $postId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'commentCount' => $result['comment_count']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid postId']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>
