<?php
include 'connect.php';

header('Content-Type: application/json');
session_start();

try {
    $postId = isset($_GET['postId']) ? (int)$_GET['postId'] : 0;

    if ($postId > 0) {
        // Fetch comments for the post
        $stmt = $db->prepare("SELECT 
            c.Comment, 
            c.dateCreated, 
            r.DisplayName AS username
        FROM comments c
        JOIN userregister r ON c.RegisterId = r.RegisterId
        WHERE c.idUpload = :postId
        ORDER BY c.dateCreated ASC");
        $stmt->execute(['postId' => $postId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'comments' => $comments]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid postId']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;
?>
