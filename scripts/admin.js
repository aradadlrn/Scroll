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
                document.getElementById('comments-count').textContent = data.data.total_comments;

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

                // Update top 3 commenters
                const topCommentersList = document.getElementById('topThreeCommenter');
                topCommentersList.innerHTML = ''; // Clear existing content
                data.data.top_commenters.forEach(commenter => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${commenter.Username} (${commenter.comment_count} posts)`;
                    topCommentersList.appendChild(listItem);
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

// Logout end

document.addEventListener('DOMContentLoaded', function () {
    fetch('./php-scripts/dashboard.php')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error fetching data:', data.error);
                return;
            }

            // Extract user registrations per day
            const registrations = data.data.user_registrations;
            const dates = registrations.map(item => item.date);
            const counts = registrations.map(item => item.count);

            // For styling
            const rootStyles = getComputedStyle(document.documentElement);
            const gridColor = rootStyles.getPropertyValue('--text-color').trim();
            const sidebarColor = rootStyles.getPropertyValue('--sidebar-bg').trim();

            // Render Chart.js Line Chart
            const ctx = document.getElementById('userChart').getContext('2d');
            new Chart (ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'User Registrations per Day',
                        data: counts,
                        borderColor: 'rgb(255, 90, 40)',
                        backgroundColor: 'rgba(255, 81, 0, 0.2)',
                        fill: true,
                        tension: 0.2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',  // Darker background
                            titleColor: 'rgb(255, 255, 255)',
                            bodyColor: 'rgb(255, 255, 255)',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            cornerRadius: 5,
                            padding: 10,
                            bodyFont: {
                                family: 'Poppins'
                            },
                            titleFont: {
                                family: 'Poppins'
                            },
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date',
                                font: {
                                    family: 'Poppins',
                                    weight: 'bold'
                                }
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Registrations',
                                font: {
                                    family: 'Poppins',
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins'
                                }
                            }
                        }
                    }
                }
            })
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});