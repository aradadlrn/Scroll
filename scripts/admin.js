document.addEventListener('DOMContentLoaded', function () {
    fetchAdminData();
});

function fetchAdminData() {
    fetch('./php-scripts/dashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update totals
                document.getElementById('users-count').textContent = data.data.total_users;
                document.getElementById('posts-count').textContent = data.data.total_posts;

                // Update top 3 active users
                const topUsersList = document.getElementById('topThreeUsers');
                topUsersList.innerHTML = ''; // Clear existing content
                data.data.top_users.forEach(user => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${user.Username} (${user.post_count} posts)`;
                    topUsersList.appendChild(listItem);
                });

                // Update top 3 popular books
                const topBooksList = document.getElementById('topThreeBooks');
                topBooksList.innerHTML = ''; // Clear existing content
                data.data.top_books.forEach(book => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${book.TitleBook} (${book.book_count} posts)`;
                    topBooksList.appendChild(listItem);
                });
            } else {
                console.error('Error fetching data:', data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Logout functionality
document.getElementById('logoutButton').addEventListener('click', function () {
    document.getElementById('logoutModal').style.display = 'block';
});

document.getElementById('confirmLogout').addEventListener('click', function () {
    window.location.href = 'logout.php'; // Redirect to logout script
});

document.getElementById('cancelLogout').addEventListener('click', function () {
    document.getElementById('logoutModal').style.display = 'none';
});

// LOGOUT
