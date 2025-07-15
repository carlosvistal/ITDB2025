<?php
session_start();
require 'config.php'; // Database connection file

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user details from the database
$stmt = $conn->prepare("SELECT full_name, email, location, profile_picture FROM users WHERE id = ?");
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch user portfolios from the database
$portfolio_stmt = $conn->prepare("SELECT * FROM portfolios WHERE user_id = ?");
$portfolio_stmt->bind_param('i', $_SESSION['user_id']);
$portfolio_stmt->execute();
$portfolios_result = $portfolio_stmt->get_result();
$portfolio_stmt->close();

// Set profile picture or use default
$profile_picture = isset($user['profile_picture']) && !empty($user['profile_picture']) 
    ? 'uploads/' . htmlspecialchars($user['profile_picture']) 
    : 'default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Portfolio System</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: rgba(59, 89, 152, 0.9);
            padding: 10px 20px;
        }

        .navbar .logo {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            display: inline-block;
        }

        .navbar .nav a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
        }

        .navbar .nav a:hover {
            text-decoration: underline;
        }

        .advertisement {
            background-color: rgba(51, 51, 51, 0.8);
            padding: 10px 20px;
            text-align: center;
        }

        .dashboard {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #333;
            border-radius: 10px;
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
        }

        .footer {
            background-color: rgba(51, 51, 51, 0.9);
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .profile-overview,
        .portfolio-tools,
        .my-portfolios {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .profile-overview img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .tools a button {
            margin: 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #3b5998;
            color: #fff;
            cursor: pointer;
        }

        .tools a button:hover {
            background-color: #2e477a;
        }

        .portfolio-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .portfolio-card {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 15px;
            border-radius: 10px;
            width: 30%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .portfolio-card h4 {
            margin: 0 0 10px;
        }

        .portfolio-card p {
            margin: 0;
            color: #555;
        }

        .portfolio-card .portfolio-btns {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .portfolio-card .portfolio-btns a button {
            padding: 5px 10px;
            background-color: #3b5998;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        .portfolio-card .portfolio-btns a button:hover {
            background-color: #2e477a;
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="container">
            <div class="logo">Portfolio System</div>
            <nav class="nav">
                <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="contact.php">Contact</a>
                <a href="logout.php?logout=true">Logout</a>
            </nav>
        </div>
    </header>

    <div class="advertisement">
        <marquee>🚀 Welcome back, <?= htmlspecialchars($user['full_name']); ?>! Manage your portfolio with ease. 🚀</marquee>
    </div>

    <main class="dashboard">
        <section class="profile-overview">
            <div class="profile-picture">
                <img src="<?= $profile_picture; ?>" alt="Profile Picture">
            </div>
            <div class="profile-details">
                <h2>Welcome, <?= htmlspecialchars($user['full_name']); ?></h2>
                <p>Email: <?= htmlspecialchars($user['email']); ?></p>
                <p>Location: <?= htmlspecialchars($user['location']); ?></p>

                <button class="edit-profile-btn" onclick="location.href='profile.php'">Edit Profile</button>
            </div>
        </section>

        <section class="portfolio-tools">
            <h3>Portfolio Tools</h3>
            <div class="tools">
                <a href="create_portfolio.php"><button>Create Portfolio</button></a>
                <a href="manage_portfolio.php"><button>Manage Portfolio</button></a>
                <a href="settings.php"><button>Settings</button></a>
            </div>
        </section>

        <section class="my-portfolios">
        
    <h3>My Portfolios</h3>
    <div class="portfolio-list">
        <?php if ($portfolios_result->num_rows > 0): ?>
            <?php while ($portfolio = $portfolios_result->fetch_assoc()): ?>
                <div class="portfolio-card" style="position: relative;">
    
    <div class="dots-wrapper">
        <button class="dots-button">⋮</button>
        <div class="dropdown-content">
            <a href="edit_portfolio.php?id=<?= $portfolio['id']; ?>">✏️ Edit</a>
            <a href="delete_portfolio.php?id=<?= $portfolio['id']; ?>" onclick="return confirm('Delete this?')">🗑️ Delete</a>
            <a href="download_portfolio.php?id=<?= $portfolio['id']; ?>">⬇️ Download</a>
        </div>
    </div>

    <!-- Portfolio Content -->
    <h4><?= htmlspecialchars($portfolio['project_title']); ?></h4>
    <p><?= nl2br(htmlspecialchars($portfolio['project_description'])); ?></p>
</div>

            <?php endwhile; ?>
        <?php else: ?>
            <p>You haven't created any portfolios yet. <a href="create_portfolio.php">Create one now!</a></p>
        <?php endif; ?>
    </div>
</section>





    <footer class="footer">
        <p>&copy; 2025 Portfolio System | All Rights Reserved</p>
    </footer>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Handle dots button click
    document.querySelectorAll('.dots-button').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation(); // Don’t close immediately
            const dropdown = this.nextElementSibling;

            // Close other dropdowns
            document.querySelectorAll('.dropdown-content').forEach(menu => {
                if (menu !== dropdown) {
                    menu.style.display = 'none';
                }
            });

            // Toggle this one
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        });
    });

    // Close dropdowns on outside click
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-content').forEach(menu => {
            menu.style.display = 'none';
        });
    });
});
</script>

