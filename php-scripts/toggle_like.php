<?php
include 'connect.php';


header('Content-Type: application/json'); // Ensure the response is JSON
session_start();

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = isset($data['postId']) ? (int) $data['postId'] : 0;
    $userId = isset($_SESSION['registerId']) ? (int) $_SESSION['registerId'] : 0;

    if ($postId > 0 && $userId > 0) {
        $stmt = $db->prepare("SELECT * FROM likes WHERE idUpload = :postId AND RegisterId = :userId");
        $stmt->execute(['postId' => $postId, 'userId' => $userId]);
        $like = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($like) {
            // Remove the like
            $stmt = $db->prepare("DELETE FROM likes WHERE idUpload = :postId AND RegisterId = :userId");
            $stmt->execute(['postId' => $postId, 'userId' => $userId]);
        } else {
            // Add the like
            $stmt = $db->prepare("INSERT INTO likes (idUpload, RegisterId) VALUES (:postId, :userId)");
            $stmt->execute(['postId' => $postId, 'userId' => $userId]);
        }

        // Get the new like count
        $stmt = $db->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE idUpload = :postId");
        $stmt->execute(['postId' => $postId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'newLikeCount' => $result['like_count']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid postId or userId']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>
