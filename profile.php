<?php
session_start();
require 'config.php'; // Database connection file

// Initialize variables
$upload_dir = 'uploads/'; // Directory to store uploaded images
$success = $error = null;
$user = null;  // Initialize user variable to avoid undefined warning

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);

    // Profile picture upload handling
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validate file extension
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            // Ensure the uploads directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($file_tmp, $file_path)) {
                // Update the database with the new profile picture
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ?, full_name = ? WHERE id = ?");
                $stmt->bind_param('ssi', $new_file_name, $full_name, $_SESSION['user_id']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['profile_picture'] = $new_file_name;
                    $success = "Profile updated successfully.";
                } else {
                    $error = "No changes were made or an error occurred.";
                }
                $stmt->close();
            } else {
                $error = "Failed to upload the profile picture.";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        // Update only the full name if no file is uploaded
        $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
        $stmt->bind_param('si', $full_name, $_SESSION['user_id']);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['full_name'] = $full_name;
            $success = "Profile updated successfully.";
        } else {
            $error = "No changes were made or an error occurred.";
        }
        $stmt->close();
    }
}

// Fetch current user details
$stmt = $conn->prepare("SELECT full_name, profile_picture FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Check if the user exists and fetch data
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
}

$stmt->close();

// Set the profile picture or default image
$profile_picture = isset($user['profile_picture']) ? $upload_dir . $user['profile_picture'] : 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        /* Body and Background */
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
        }

        /* Main Container */
        .container {
            max-width: 500px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            color: #333;
        }

        /* Header Styling */
        h1 {
            text-align: center;
            color: #3b5998;
        }

        /* Profile Picture Section */
        .profile-picture-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-picture-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #3b5998;
        }

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #3b5998;
        }

        input[type="text"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background-color: #3b5998;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #2e477a;
        }

        /* Back to Dashboard Link */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #3b5998;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* Success/Error Messages */
        .success {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        .error {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        
        <?php if ($success): ?>
            <p class="success"><?= htmlspecialchars($success); ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <div class="profile-picture-container">
            <img src="<?= htmlspecialchars($profile_picture); ?>" alt="Profile Picture">
        </div>
            
        <form method="POST" enctype="multipart/form-data">
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']); ?>" required>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

            <button type="submit">Save Changes</button>
        </form>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
