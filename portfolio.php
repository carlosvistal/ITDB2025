<?php
session_start();
include('config.php'); // Database connection file

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

// Handle portfolio edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_portfolio'])) {
    $portfolio_id = $_POST['portfolio_id'];
    $project_title = $_POST['project_title'];
    $project_description = $_POST['project_description'];

    // Update the portfolio in the database
    $update_query = $conn->prepare("UPDATE portfolios SET project_title = ?, project_description = ? WHERE id = ? AND user_id = ?");
    $update_query->bind_param('ssii', $project_title, $project_description, $portfolio_id, $user_id);
    $update_query->execute();
    $update_query->close();

    header('Location: portfolio.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Portfolio - Portfolio System</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
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

        .my-portfolios {
            margin-top: 20px;
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

        .edit-profile-btn {
            padding: 10px 15px;
            background-color: #3b5998;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-profile-btn:hover {
            background-color: #2e477a;
        }

        .edit-port-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .edit-port-btn:hover {
            background-color: #0056b3;
        }

        .edit-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .edit-form input, .edit-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .edit-form button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .edit-form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

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

        <!-- Portfolios -->
        <section class="my-portfolios">
            <h3>My Portfolios</h3>
            <div class="portfolio-list">
                <?php if ($portfolios->num_rows > 0): ?>
                    <?php while ($portfolio = $portfolios->fetch_assoc()): ?>
                        <div class="portfolio-card">
                            <h4><?= htmlspecialchars($portfolio['project_title']); ?></h4>
                            <p><?= nl2br(htmlspecialchars($portfolio['project_description'])); ?></p>
                            <button class="edit-port-btn" onclick="editPortfolio(<?= $portfolio['id']; ?>, '<?= htmlspecialchars($portfolio['project_title']); ?>', '<?= htmlspecialchars($portfolio['project_description']); ?>')">Edit</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No portfolios found.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Portfolio Edit Form -->
        <section id="edit-portfolio-section" style="display:none;">
            <h3>Edit Portfolio</h3>
            <form method="POST" class="edit-form">
                <input type="hidden" name="portfolio_id" id="portfolio_id">
                <input type="text" name="project_title" id="project_title" required>
                <textarea name="project_description" id="project_description" rows="5" required></textarea>
                <button type="submit" name="edit_portfolio">Save Changes</button>
            </form>
        </section>
    </div>

    <script>
        function editPortfolio(id, title, description) {
            document.getElementById('portfolio_id').value = id;
            document.getElementById('project_title').value = title;
            document.getElementById('project_description').value = description;
            document.getElementById('edit-portfolio-section').style.display = 'block';
        }
    </script>
</body>
</html>
