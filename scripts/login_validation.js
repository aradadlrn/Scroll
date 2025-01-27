document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signInForm');
    const errorMessage = document.getElementById('error-message');
    const passwordInput = document.getElementById('password');

    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Get the form inputs
        const email = document.getElementById('email').value.trim();
        const password = passwordInput.value.trim();

        // Clear any previous error message
        errorMessage.textContent = '';

        // Validate the form inputs
        if (!email && !password) {
            errorMessage.textContent = 'Please fill in all fields.';
            return;
        }
        if (!email) {
            errorMessage.textContent = 'Please fill in your email.';
            return;
        }
        if (!password) {
            errorMessage.textContent = 'Please fill in your password.';
            return;
        }

        // Send the form data to login.php using fetch
        fetch('php-scripts/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                Username: email,
                user_password: password,
            }),
        })
        .then(response => {
            console.log('Response status:', response.status); // Debugging
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text(); // First, get the response as text
        })
        .then(text => {
            console.log('Response text:', text); // Debugging
            try {
                const data = JSON.parse(text); // Try to parse the text as JSON
                console.log('Parsed data:', data); // Debugging
                if (data.success) {
                    // Store user data in localStorage
                    localStorage.setItem('loginData', JSON.stringify({
                        username: data.username,
                        displayName: data.displayName,
                        registerId: data.registerId
                    }));

                    // Redirect to the homepage
                    window.location.href = data.redirect;
                } else {
                    // Display the error message
                    errorMessage.textContent = data.error;

                    // Clear the password field if the error is related to the password
                    if (data.error === "Incorrect password.") {
                        passwordInput.value = '';
                    }
                }
            } catch (error) {
                console.error('Error parsing JSON:', error); // Debugging
                errorMessage.textContent = 'An error occurred. Please try again.';
            }
        })
        .catch(error => {
            console.error('Error:', error); // Debugging
            errorMessage.textContent = 'An error occurred. Please try again.';
        });
    });
});