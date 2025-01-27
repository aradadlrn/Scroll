// Session Handling
document.addEventListener('DOMContentLoaded', function () {
  // Check if the user is logged in
  const loginData = localStorage.getItem('loginData');

  if (loginData) {
    try {
      const data = JSON.parse(loginData);

      // Update profile card with user data
      const displayNameElement = document.querySelector('.display-name');
      const usernameElement = document.querySelector('.username');

      if (displayNameElement) {
        displayNameElement.textContent = data.displayName || data.username;
      }
      if (usernameElement) {
        usernameElement.textContent = `@${data.username}`;
      }
    } catch (error) {
      console.error('Error parsing loginData:', error);
      localStorage.removeItem('loginData'); // Clear invalid login data
      window.location.href = 'login.html'; // Redirect to login page
    }
  } else {
    // Redirect to login if no session data is found
    window.location.href = 'login.html';
  }

  // Logout Handling
  const logoutButton = document.getElementById('logoutButton');
  const logoutModal = document.getElementById('logoutModal');
  const confirmLogoutButton = document.getElementById('confirmLogout');
  const cancelLogoutButton = document.getElementById('cancelLogout');

  if (logoutButton) {
    logoutButton.addEventListener('click', function () {
      // Show the custom modal
      logoutModal.style.display = 'flex';
    });
  }

  if (confirmLogoutButton) {
    confirmLogoutButton.addEventListener('click', function () {
      // Perform logout
      fetch('php-scripts/logout.php', {
        method: 'POST',
        credentials: 'include',
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          localStorage.removeItem('loginData');
          window.location.href = 'login.html';
        } else {
          console.error('Logout failed:', data.error);
          alert('Logout failed: ' + data.error);
        }
      })
      .catch(error => {
        console.error('Error during logout:', error);
        alert('An error occurred during logout. Please try again.');
      });

      // Hide the modal
      logoutModal.style.display = 'none';
    });
  }

  if (cancelLogoutButton) {
    cancelLogoutButton.addEventListener('click', function () {
      // Hide the modal
      logoutModal.style.display = 'none';
    });
  }

  // Close the modal if the user clicks outside of it
  window.addEventListener('click', function (event) {
    if (event.target === logoutModal) {
      logoutModal.style.display = 'none';
    }
  });

  // Dropdown Handling
  const profileOptions = document.getElementById('profileOptions');
  const dropdownContent = document.getElementById('dropdownContent');

  if (profileOptions && dropdownContent) {
    profileOptions.addEventListener('click', function (event) {
      event.stopPropagation(); // Prevent the click from closing the dropdown immediately
      dropdownContent.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    window.addEventListener('click', function () {
      if (dropdownContent.classList.contains('show')) {
        dropdownContent.classList.remove('show');
      }
    });
  }
});