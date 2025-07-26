<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/index.css"> <!-- Include your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Include jQuery -->
    <title>Login</title>
</head>
<body>
    <div class="container">
        <div id="loginFormContainer">
            <form id="loginForm" action="controller/auth.php" method="POST">
                <h2>Login</h2>
                <div class="form-group">
                    <label for="username">Username (Email)</label>
                    <input type="email" id="username" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="form-group">
                    <p>Don't have an account? <a href="#" id="showRegisterForm">Register</a></p>
                </div>
            </form>
        </div>

        <div id="registerFormContainer" style="display: none;">
            <form id="registerForm" action="controller/auth.php" method="POST">
                <h2>Register</h2>
                <div class="form-group">
                    <label for="register_email">Email</label>
                    <input type="email" id="register_email" name="register_email" required>
                </div>
                <div class="form-group">
                    <label for="register_password">Password</label>
                    <input type="password" id="register_password" name="register_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label for="client">Transport Company Name</label>
                    <input type="text" id="client" name="transport_company" required>
                </div>
                <button type="submit" class="btn">Register</button>
                <div class="form-group">
                    <p>Already have an account? <a href="#" id="showLoginForm">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show the registration form
        document.getElementById('showRegisterForm').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            document.getElementById('loginFormContainer').style.display = 'none';
            document.getElementById('registerFormContainer').style.display = 'block';
        });

        // Show the login form
        document.getElementById('showLoginForm').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default link behavior
            document.getElementById('registerFormContainer').style.display = 'none';
            document.getElementById('loginFormContainer').style.display = 'block';
        });

        // Handle registration via AJAX
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission
            const formData = new FormData(this); // Get form data

            // Send AJAX request to register
            fetch('controller/auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data); // Display success/error message
                if (data.includes("Registration successful")) {
                    // Optionally, redirect to the login page or automatically log in
                    document.getElementById('registerFormContainer').style.display = 'none';
                    document.getElementById('loginFormContainer').style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
<footer class="footer">
  <p>Developed by <strong>Renato Silva</strong> – <em>Test Dock Booking System</em></p>
</footer>
</html>
