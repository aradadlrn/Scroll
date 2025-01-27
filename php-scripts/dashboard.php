<?php
session_start();
include 'connect.php';

$response = ['success' => false, 'error' => ''];

try {
    // Fetch total number of users
    $sql = "SELECT COUNT(*) AS total_users FROM scroll.userregister";
    $stmt = $db->query($sql);
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Fetch total number of posts
    $sql = "SELECT COUNT(*) AS total_posts FROM scroll.userpost";
    $stmt = $db->query($sql);
    $totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total_posts'];

    // Fetch top 3 active users (based on number of posts)
    $sql = "SELECT u.Username, COUNT(*) AS post_count 
    FROM scroll.userpost p 
    INNER JOIN scroll.userregister u ON p.RegisterId = u.RegisterId 
    GROUP BY p.RegisterId 
    ORDER BY post_count DESC 
    LIMIT 3";
    $stmt = $db->query($sql);
    $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch top 3 popular books (based on number of posts)
    $sql = "SELECT TitleBook, COUNT(*) AS book_count 
            FROM scroll.userpost 
            GROUP BY TitleBook 
            ORDER BY book_count DESC 
            LIMIT 3";
    $stmt = $db->query($sql);
    $topBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare response
    $response['success'] = true;
    $response['data'] = [
        'total_users' => $totalUsers,
        'total_posts' => $totalPosts,
        'top_users' => $topUsers,
        'top_books' => $topBooks,
    ];
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response['error'] = "A database error occurred. Please try again later.";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>