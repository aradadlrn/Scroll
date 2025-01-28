<?php
// Start the session
session_start();

include 'php-scripts/connect.php'; // Database connection

try {
    // Fetch bookmarked posts
    $stmt = $db->prepare("
        SELECT p.idUpload, r.DisplayName, r.Username, p.TitleBook, p.Caption, p.Picture 
        FROM userpost p 
        JOIN userregister r ON p.RegisterId = r.RegisterId 
        WHERE p.RegisterId != :registerId
        ORDER BY p.idUpload DESC
    ");
    $stmt->execute(['registerId' => $_SESSION['registerId']]);
    $bookmarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database errors
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarks</title>
    <link rel="stylesheet" href="styles/bookmark-layout.css">
    <link rel="stylesheet" href="styles/side-nav-layout.css">
    <script src="./scripts/modal-homepage.js" defer></script>
    <script src="./scripts/Homepage.js" defer></script>
    <script src="./scripts/comments.js" defer></script>
</head>
<body>
    <header>
    <h2 id="bookmarks-heading">Bookmarks</h2>
    </header>
    <div class="main-container bookmark">
        <!-- ===== LEFT SIDEBAR ===== -->
        <aside class="sidebar">
            <!-- LOGO at the top -->
            <div class="logo">
                <img src="./assets/logo0.png" alt="Scroll Logo">
            </div>
            <!-- Nav links -->
            <nav class="nav-links">
                <a href="/Scroll/Homepage.php" class="nav-item">
                    <img class="nav-icon" src="./assets/home-icon0.svg" />
                    <span>For you</span>
                </a>
                <a href="/Scroll/profilev3.html" style="text-decoration: none;" class="nav-item">
                    <img class="nav-icon" src="./assets/icon-user0.svg" />
                    <span>Profile</span>
                </a>
                <a href="/Scroll/bookmarks.php" style="text-decoration: none;" class="nav-item">
                    <img class="nav-icon" src="./assets/icon-bookmark0.svg" />
                    <span>Bookmarks</span>
                </a>
                <a href="/Scroll/settings.html" style="text-decoration: none;" class="nav-item">
                    <img class="nav-icon" src="./assets/icon-settings0.svg" />
                    <span>Settings</span>
                </a>
            </nav>
            <!-- Theme toggle container -->
            <div class="dark-mode-toggle" id="themeToggle">
                <img src="./assets/moon0.svg" class="theme-icon" alt="Toggle theme" />
            </div>
            <!-- PROFILE CARD at the very bottom -->
            <div class="profile-card">
                <div class="profile-avatar">
                    <img id="profileAvatarImg" src="assets/profilepic.png" alt="Profile Avatar" />
                </div>
                <div class="profile-text">
                    <div class="display-name">Display Name</div>
                    <div class="username">@username</div>
                </div>
                <div class="profile-options">
                    <img src="./assets/more-horizontal.png" alt="options" id="profileOptions">
                    <div class="dropdown-content" id="dropdownContent">
                        <div class="dropdown-item" id="logoutButton">Log out</div>
                    </div>
                </div>
            </div>
        </aside>
        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" class="lModal">
            <div class="lModal-content">
                <h2>Logout Confirmation</h2>
                <p>Are you sure you want to log out?</p>
                <div class="lModal-buttons">
                    <button id="confirmLogout">Yes, Logout</button>
                    <button id="cancelLogout">Cancel</button>
                </div>
            </div>
        </div>
        <!--- BOOK MARK PARTS -->
        <div class="bookmarks-container">
            <?php if (empty($bookmarks)): ?>
                <p>No bookmarks yet.</p>
            <?php else: ?>
                <?php foreach ($bookmarks as $post): ?>
                    <div class="actualPost" id="post-<?= $post['idUpload'] ?>">
                        <div class="postLeftContainer">
                            <div class="postCreator">
                                <?= htmlspecialchars($post['DisplayName']) ?>&emsp;
                                <text class="postUsername">@<?= htmlspecialchars($post['Username']) ?></text>
                            </div>
                            <text class="postTitle"><?= htmlspecialchars($post['TitleBook']) ?></text>
                            <text class="postDesc"><?= htmlspecialchars($post['Caption']) ?></text>
                            <div class="postIcons">
                                <img class="likeBtn" src="./assets/icon-heart0.svg">
                                <button class="commentBtn" onclick="toggleComments(<?= $post['idUpload'] ?>)">
                                    <img class="commentBtnImg" src="./assets/icon-message0.svg">
                                </button>
                                <img class="bookmarkBtn" src="./assets/icon-bookmark0.svg">
                            </div>
                        </div>
                        <div class="postRightContainer">
                            <div class="postImage">
                                <img src="data:image/jpeg;base64,<?= $post['Picture'] ?>" alt="Post Image">
                            </div>
                        </div>
                        <!-- Comments Section -->
                        <div id="comments-<?= $post['idUpload'] ?>" class="comments-section" style="display: none;">
                            <!-- Comments will be loaded here -->
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        <!-- ACTUAL COMMENTS -->
        <div class="comments-container" id="comments-container">
            <!-- Close Button -->
            <button class="close-btn" onclick="closeComments()">×</button>
            <!-- Comments Header -->
            <div class="comments-header">
                <h2>Comments</h2>
            </div>
            <!-- Comments List -->
            <div class="comments-list" id="comments-list">
                <!-- Where comments show -->
            </div>
            <!-- Add Comment Section -->
            <div class="comment-input-container">
                <input type="text" id="comment-input" placeholder="Add a comment..." />
                <button onclick="addComment()">Post</button>
            </div>
        </div>
    </div>
    <script src="scripts/ui-mode-toggle.js"></script>
</body>
</html>