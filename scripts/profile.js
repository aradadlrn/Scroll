document.addEventListener('DOMContentLoaded', function () {
    // Check if the user is logged in
    const loginData = localStorage.getItem('loginData');

    if (loginData) {
        try {
            const data = JSON.parse(loginData);

            // Only proceed if we have valid user data
            if (data?.username) {
                // Update profile elements with user data
                const displayNameElement = document.querySelector('.display-name2');
                const usernameElement = document.querySelector('.username2');

                if (displayNameElement) {
                    displayNameElement.textContent = data.displayName || data.username;
                }
                if (usernameElement) {
                    usernameElement.textContent = `@${data.username}`;
                }
            }
        } catch (error) {
            console.error('Error parsing loginData:', error);
            localStorage.removeItem('loginData');  // Clear corrupted data
        }
    }
    
    // Removed all redirect logic - stays on current page regardless of auth status
});