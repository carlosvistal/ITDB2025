document.addEventListener('DOMContentLoaded', function() {
    const loggedIn = localStorage.getItem('loggedIn');
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const content = document.getElementById('content');
    const loginFormContainer = document.getElementById('loginFormContainer');
    const signupFormContainer = document.getElementById('signupFormContainer');
    const authBtns = document.getElementById('authBtns');

    if (loggedIn) {
        // If user is logged in, show the portfolio or homepage content
        content.innerHTML = `<h2>Welcome Back!</h2><p>Your Portfolio</p>`;
        authBtns.style.display = 'none'; // Hide login/signup buttons
    } else {
        // If not logged in, show login/signup buttons
        loginBtn.addEventListener('click', function() {
            authBtns.style.display = 'none';
            loginFormContainer.style.display = 'block';
        });
        
        signupBtn.addEventListener('click', function() {
            authBtns.style.display = 'none';
            signupFormContainer.style.display = 'block';
        });

        document.getElementById('goToSignup').addEventListener('click', function() {
            loginFormContainer.style.display = 'none';
            signupFormContainer.style.display = 'block';
        });

        document.getElementById('goToLogin').addEventListener('click', function() {
            signupFormContainer.style.display = 'none';
            loginFormContainer.style.display = 'block';
        });
    }

    // Handle Signup form submission
    document.getElementById('signupForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const email = document.getElementById('email').value;

        // Simulate saving the account in localStorage
        localStorage.setItem('user', JSON.stringify({ username, password, email }));
        alert('Account created successfully! Please log in.');
        signupFormContainer.style.display = 'none';
        loginFormContainer.style.display = 'block';
    });

    // Handle Login form submission
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const usernameLogin = document.getElementById('usernameLogin').value;
        const passwordLogin = document.getElementById('passwordLogin').value;

        const user = JSON.parse(localStorage.getItem('user'));
        
        if (user && user.username === usernameLogin && user.password === passwordLogin) {
            localStorage.setItem('loggedIn', true);
            window.location.reload();
        } else {
            alert('Invalid credentials');
        }
    });
});
