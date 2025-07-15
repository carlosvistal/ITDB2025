<?php
   
      include("header.php");

// Define variables and set to empty
$name = $email = $message = $success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST["name"]));
    $email   = htmlspecialchars(trim($_POST["email"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $to = "vistalcarlosfidel@gmail.com"; // Change this to your email
        $subject = "Contact Form Message";
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = "From: $email";

        if (mail($to, $subject, $body, $headers)) {
            $success = "Message sent successfully!";
            $name = $email = $message = ""; // clear form
        } else {
            $error = "Failed to send message.";
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
    
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <style>
        body { 
            font-family: Arial; padding: 20px; 
             background: url('background.jpg') no-repeat center center fixed;
        }
        form { 
            max-width: 400px; margin: auto;
               padding: 10%; 
        }
        input, textarea 
        { 
            width: 100%; margin-bottom: 10px; padding: 8px; 
        }
        .success { 
            color: green; 
        }
        .error {
             color: red;
             }
        
        h2{
        text-align: center;
        }
        
        

    </style>
</head>
<body>

<?php if ($success) echo "<p class='success'>$success</p>"; ?>
<?php if ($error) echo "<p class='error'>$error</p>"; ?>

<form method="post" action="send-email.php">
    <section class="Contact-Page">

  <h2>Contact us</h2>
    <label>Name:</label>
    <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

    <label>Message:</label>
    <textarea name="message" rows="5" required><?= htmlspecialchars($message) ?></textarea>

    <input type="submit" value="Send Message">
    </section>
</form>

</body>
</html>
<footer class="footer">
        <p>&copy; 2025 Portfolio System | All Rights Reserved</p>
    </footer>






