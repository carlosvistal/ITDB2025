<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $gender = $_POST['gender'];

    //  Check if the email already exists
    $check_query = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email exists, alert user
        echo "<script>alert('Email already registered. Please use a different one.'); window.location='create_account.php';</script>";
    } else {
        //   Proceed with inserting the user
        $insert_query = "INSERT INTO users (full_name, email, password, gender) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $full_name, $email, $password, $gender);
       
        

        if ($stmt->execute()) {
            echo "<script>alert('Account created successfully!'); window.location='login.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            margin: 100px auto;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #3b5998;
        }
        .form-container .input-field {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container .btn {
            width: 100%;
            padding: 12px;
            background-color: #3b5998;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .form-container .btn:hover {
            background-color: #2d4373;
        }
        .form-container p {
            margin-top: 15px;
            color: #555;
        }
        .form-container a {
            color: #3b5998;
            text-decoration: none;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Account</h2>
        <form method="POST" action="create_account.php">
            <input type="text" name="full_name" placeholder="Full Name" required class="input-field">
            <input type="email" name="email" placeholder="Email" required class="input-field">
            <input type="password" name="password" placeholder="Password" required class="input-field">
            <label for="gender">Gender</label>
            <select name="gender" id="gender" required class="input-field">
                <option value="">--select--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Others">Others</option>
            </select>
            <button type="submit" class="btn">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>
