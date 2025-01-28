// Dynamically load comments (simulated for now)
const commentsList = document.getElementById('comments-list');

// Add comment to the list
function addComment() {
    const commentInput = document.getElementById('comment-input');
    const commentText = commentInput.value.trim();

    if (commentText) {
        addCommentToList('You', commentText);
        commentInput.value = ''; // Clear the input
    }
}

// Utility function to append a comment
function addCommentToList(username, text) {
    const commentItem = document.createElement('div');
    commentItem.className = 'comment-item';

    commentItem.innerHTML = `
        <div class="comment-avatar"></div>
        <div class="comment-content">
            <p class="comment-username">${username}</p>
            <p class="comment-text">${text}</p>
        </div>
    `;

    commentsList.appendChild(commentItem);
    commentsList.scrollTop = commentsList.scrollHeight; // Scroll to the latest comment
}

// Close the comments section
function closeComments() {
    const commentsSection = document.getElementById('comments-container');

    commentsSection.classList.remove('open');
    realPostContainer.classList.remove('moveLeft'); // Remove the moveLeft class
}

// For the comments to slide over
function toggleComments() {
    const commentsSection = document.getElementById('comments-container');
    const realPostContainer = document.querySelector('.realPostContainer');

    commentsSection.classList.toggle('open');
    realPostContainer.classList.toggle('moveLeft');
}

// Load initial comments on page load
loadComments();
