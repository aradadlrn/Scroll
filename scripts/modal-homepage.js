// Get references to elements
const openModal = document.getElementById('openModal');
const closeModal = document.getElementById('closeModal');
const modal = document.getElementById('modal');

// Function to show the modal
openModal.addEventListener('click', () => {
    modal.style.display = 'flex'; // Display the modal as a flexbox
});

// Function to hide the modal when the close button is clicked
closeModal.addEventListener('click', () => {
    modal.style.display = 'none'; // Hide the modal
});

// Function to close the modal when clicking outside of it
window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none'; // Hide the modal
    }
});

// Set RegisterID from localStorage when the page loads
window.addEventListener('DOMContentLoaded', () => {
    // Fetch loginData from localStorage
    const loginData = localStorage.getItem('loginData');
    console.log('loginData from localStorage:', loginData);  // Debugging

    // Check if loginData exists
    if (!loginData) {
        alert('No login data found. Please log in again.');
        window.location.href = 'login.html';
        return;
    }

    // Parse loginData to extract RegisterID
    let registerId;
    try {
        const parsedLoginData = JSON.parse(loginData);
        console.log('Parsed loginData:', parsedLoginData);  // Debugging
        
        // CASE-SENSITIVE: Ensure "registerID" matches your localStorage key
        registerId = parsedLoginData.registerId; 
        console.log('Extracted registerID:', registerId);  // Debugging
    } catch (error) {
        console.error('Error parsing loginData:', error);
        alert('Invalid login data. Please log in again.');
        window.location.href = 'login.html';
        return;
    }

    // Check if registerID exists
    if (!registerId) {
        alert('No registerID found in login data. Please log in again.');
        window.location.href = 'login.html';
        return;
    }

    // Populate hidden form field
    document.getElementById('registeredId').value = registerId;
});