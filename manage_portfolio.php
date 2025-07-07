<?php
// Include necessary files and start session
session_start();
include('config.php');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details for profile
$user_query = $conn->prepare("SELECT full_name, profile_picture FROM users WHERE id = ?");
$user_query->bind_param('i', $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user_data = $user_result->fetch_assoc();
$user_query->close();

// Fetch user portfolios
$portfolio_query = $conn->prepare("SELECT * FROM portfolios WHERE user_id = ?");
$portfolio_query->bind_param('i', $user_id);
$portfolio_query->execute();
$portfolios = $portfolio_query->get_result();
$portfolio_query->close();

// Set profile picture or default
$profile_picture = isset($user_data['profile_picture']) && !empty($user_data['profile_picture']) 
    ? 'uploads/' . htmlspecialchars($user_data['profile_picture']) 
    : 'default.png';

// Add portfolio if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_portfolio'])) {
    $project_title = trim($_POST['project_title']);
    $project_description = trim($_POST['project_description']);

    // Insert new portfolio into the database
    $insert_query = $conn->prepare("INSERT INTO portfolios (user_id, project_title, project_description) VALUES (?, ?, ?)");
    $insert_query->bind_param('iss', $user_id, $project_title, $project_description);
    if ($insert_query->execute()) {
        header('Location: manage_portfolio.php');
        exit();
    } else {
        $error_message = "Error adding portfolio. Please try again.";
    }
}

// Edit portfolio if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_portfolio'])) {
    $portfolio_id = $_POST['portfolio_id'];
    $project_title = trim($_POST['project_title']);
    $project_description = trim($_POST['project_description']);

    // Update portfolio in the database
    $update_query = $conn->prepare("UPDATE portfolios SET project_title = ?, project_description = ? WHERE id = ? AND user_id = ?");
    $update_query->bind_param('ssii', $project_title, $project_description, $portfolio_id, $user_id);
    if ($update_query->execute()) {
        header('Location: manage_portfolio.php');
        exit();
    } else {
        $error_message = "Error updating portfolio. Please try again.";
    }
}

// Handle deleting portfolio
if (isset($_GET['delete'])) {
    $portfolio_id = $_GET['delete'];

    // Delete portfolio from the database
    $delete_query = $conn->prepare("DELETE FROM portfolios WHERE id = ? AND user_id = ?");
    $delete_query->bind_param('ii', $portfolio_id, $user_id);
    $delete_query->execute();
    header('Location: manage_portfolio.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Portfolio - Portfolio System</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Styles for consistency */
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

        .dashboard {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .profile-overview {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .profile-overview .profile-picture img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;
        }

        .profile-details {
            flex: 1;
        }

        .portfolio-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .portfolio-card {
            background-color: #f4f4f4;
            padding: 15px;
            border-radius: 10px;
            flex: 1 1 calc(33.333% - 20px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .portfolio-card h4 {
            margin: 0 0 10px;
        }

        .portfolio-card p {
            margin: 0;
            color: #555;
        }

        .add-portfolio-form, .edit-portfolio-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        .add-portfolio-form input, .add-portfolio-form textarea,
        .edit-portfolio-form input, .edit-portfolio-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .add-portfolio-btn, .edit-portfolio-btn {
            padding: 10px 15px;
            background-color: #3b5998;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .add-portfolio-btn:hover, .edit-portfolio-btn:hover {
            background-color: #2e477a;
        }

        .portfolio-btn-container {
            display: flex;
            justify-content: space-between;
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
        <a href="logout.php">Logout</a>     
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php"><button class="login-btn"><?php echo $_SESSION['full_name']; ?></button></a>
        <?php endif; ?>
    </nav>
    </div> 
</header>

    <div class="dashboard">
        <!-- Profile Overview -->
        <section class="profile-overview">
            <div class="profile-picture">
                <img src="<?= $profile_picture; ?>" alt="Profile Picture">
            </div>
            <div class="profile-details">
                <h2>Welcome, <?= htmlspecialchars($user_data['full_name']); ?></h2>
                <p>Frontend Developer | Designer</p>
                <a href="profile.php"><button class="edit-profile-btn">Edit Profile</button></a>
            </div>
        </section>

        <!-- Add New Portfolio -->
        <section class="add-portfolio">
            <h3>Add New Portfolio</h3>
            <?php if (isset($error_message)): ?>
                <p class="error"><?= htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form method="POST" class="add-portfolio-form">
                <label for="project_title">Project Title:</label><br>
                <input type="text" id="project_title" name="project_title" required><br>

                <label for="project_description">Project Description:</label><br>
                <textarea id="project_description" name="project_description" rows="4" required></textarea><br>

                <button type="submit" name="add_portfolio" class="add-portfolio-btn">Add Portfolio</button>
            </form>
        </section>

        <!-- Manage Portfolios -->
        <section class="my-portfolios">
            <h3>My Portfolios</h3>
            <div class="portfolio-list">
                <?php if ($portfolios->num_rows > 0): ?>
                    <?php while ($portfolio = $portfolios->fetch_assoc()): ?>
                        <div class="portfolio-card">
                            <h4><?= htmlspecialchars($portfolio['project_title']); ?></h4>
                            <p><?= nl2br(htmlspecialchars($portfolio['project_description'])); ?></p>
                            <div class="portfolio-btn-container">
                                <a href="manage_portfolio.php?edit=<?= $portfolio['id']; ?>">
                                    <button class="edit-portfolio-btn">Edit Portfolio</button>
                                </a>
                                <a href="manage_portfolio.php?delete=<?= $portfolio['id']; ?>" onclick="return confirm('Are you sure you want to delete this portfolio?')">
                                    <button class="edit-portfolio-btn">Delete Portfolio</button>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No portfolios found. Add your first portfolio!</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Edit Portfolio Form -->
        <?php if (isset($_GET['edit'])): 
            $portfolio_id = $_GET['edit'];
            $edit_query = $conn->prepare("SELECT * FROM portfolios WHERE id = ? AND user_id = ?");
            $edit_query->bind_param('ii', $portfolio_id, $user_id);
            $edit_query->execute();
            $portfolio_to_edit = $edit_query->get_result()->fetch_assoc();
            $edit_query->close();
        ?>
            <section class="edit-portfolio">
                <h3>Edit Portfolio</h3>
                <form method="POST" class="edit-portfolio-form">
                    <input type="hidden" name="portfolio_id" value="<?= $portfolio_to_edit['id']; ?>">

                    <label for="project_title">Project Title:</label><br>
                    <input type="text" id="project_title" name="project_title" value="<?= htmlspecialchars($portfolio_to_edit['project_title']); ?>" required><br>

                    <label for="project_description">Project Description:</label><br>
                    <textarea id="project_description" name="project_description" rows="4" required><?= htmlspecialchars($portfolio_to_edit['project_description']); ?></textarea><br>

                    <button type="submit" name="edit_portfolio" class="edit-portfolio-btn">Update Portfolio</button>
                </form>
            </section>
        <?php endif; ?>
    </div>

    <?php include('css/footer.php'); ?>
</body>
</html>
