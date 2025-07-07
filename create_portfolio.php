<?php
session_start();
include('config.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: create_portfolio.php");
    exit();
}

// Handle portfolio creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $project_title = $_POST['project_title'];
    $project_description = $_POST['project_description'];

    $sql = "INSERT INTO portfolios (user_id, project_title, project_description) VALUES ('$user_id', '$project_title', '$project_description')";

    if ($conn->query($sql) === TRUE) {
        header("Location: home.php?success=Portfolio created successfully");
        exit();
    } else {
        $error_message = "Error: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css"> <!-- Assuming this contains your global styles -->
    <title>Create Portfolio</title>
</head>

   <style>
    .navbar{
            background-color: rgba(59, 89, 152, 0.9);
            padding: 10px, 20px;
        }
        .navbar .nav a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
        }
        .navbar .logo{
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
        }


    
   </style>
<body>
    <div class="navbar">
        <div class="container">
        <div class="logo">Portfolio System</div>
        <nav class="nav">
             <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="logout.php?logout=true">Logout</a>
                </nav>
        </div>
    </div>
    </header>

    <section class="create-portfolio-page">
        <div class="form-container">
            <h2>Create a New Portfolio</h2>

            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="create_portfolio.php" class="portfolio-form">
                <input type="text" name="project_title" placeholder="Project Title" required class="input-field">
                <textarea name="project_description" placeholder="Project Description" required class="textarea-field"></textarea>
                <button type="submit" class="submit-btn">Create Portfolio</button>
            </form>
        </div>
            </section>
</body>
</html>
