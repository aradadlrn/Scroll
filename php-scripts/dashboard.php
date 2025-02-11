<?php
session_start();
include 'connect.php';

$response = ['success' => false, 'error' => ''];

try {
    // Fetch total number of users
    $sql = "SELECT COUNT(*) AS total_users FROM userregister";
    $stmt = $db->query($sql);
    $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

    // Fetch total number of posts
    $sql = "SELECT COUNT(*) AS total_posts FROM userpost";
    $stmt = $db->query($sql);
    $totalPosts = $stmt->fetch(PDO::FETCH_ASSOC)['total_posts'];

    // Fetch total number of comments
    $sql = "SELECT COUNT(*) AS total_comments FROM comments";
    $stmt = $db->query($sql);
    $totalComments = $stmt->fetch(PDO::FETCH_ASSOC)['total_comments'];

    // Fetch top 3 active users (based on number of posts)
    $sql = "SELECT u.Username, COUNT(*) AS post_count 
    FROM userpost p 
    INNER JOIN userregister u ON p.RegisterId = u.RegisterId 
    GROUP BY p.RegisterId 
    ORDER BY post_count DESC 
    LIMIT 3";
    $stmt = $db->query($sql);
    $topUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch top 3 popular books (based on number of posts)
    $sql = "SELECT TitleBook, COUNT(*) AS book_count 
            FROM userpost 
            GROUP BY TitleBook 
            ORDER BY book_count DESC 
            LIMIT 3";
    $stmt = $db->query($sql);
    $topBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch top 3 active commenters (based on number of comments)
    $sql = "SELECT u.Username, COUNT(*) AS comment_count 
            FROM comments c JOIN userregister u ON c.RegisterId = u.RegisterId
            GROUP BY u.Username
            ORDER BY comment_count DESC 
            LIMIT 3";
    $stmt = $db->query($sql);
    $topCommenters = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch user registrations per day for time series chart
    $sql = "SELECT  DATE(dateCreated) AS date, COUNT(*) AS count 
            FROM userregister 
            GROUP BY DATE(dateCreated)
            ORDER BY dateCreated ASC";
    $stmt = $db->query($sql);
    $userRegistrations = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Prepare response
    $response['success'] = true;
    $response['data'] = [
        'total_users' => $totalUsers,
        'total_posts' => $totalPosts,
        'total_comments' => $totalComments,
        'top_users' => $topUsers,
        'top_books' => $topBooks,
        'top_commenters' => $topCommenters,
        'user_registrations'=> $userRegistrations
    ];
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $response['error'] = "A database error occurred. Please try again later.";
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>