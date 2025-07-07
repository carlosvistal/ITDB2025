<?php
include('config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $location = $_POST['location'];

    // Insert user into the database
    $sql = "INSERT INTO users (full_name, email, password, location) VALUES ('$full_name', '$email', '$password', '$location')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
        header("Location: login.php"); // Redirect to login page
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<form method="POST" action="signup.php">
    <input type="text" name="full_name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="location" placeholder="Location" required>
    <button type="submit">Sign Up</button>
</form>
