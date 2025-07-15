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
        .navbar { background-color: rgba(59, 89, 152, 0.9); padding: 10px 20px; }
        .navbar .nav a { color: #fff; margin: 0 10px; text-decoration: none; }
        .navbar .logo { color: #fff; font-size: 24px; font-weight: bold; display: inline-block; }

        .dashboard { max-width: 1200px; margin: auto; padding: 20px; }

        .profile-overview { display: flex; align-items: center; margin-bottom: 20px; }
        .profile-overview .profile-picture img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-right: 20px; }
        .profile-details { flex: 1; }

        .portfolio-list { display: flex; flex-wrap: wrap; gap: 20px; }

        .portfolio-card { background-color: #f4f4f4; padding: 15px; border-radius: 10px; flex: 1 1 calc(33.333% - 20px); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); position: relative; }
        .portfolio-card h4 { margin: 0 0 10px; }
        .portfolio-card p { margin: 0; color: #555; }

        .dots-button { position: absolute; top: 10px; right: 10px; background: #fff; border: 1px solid #ccc; border-radius: 5px; font-size: 18px; cursor: pointer; padding: 4px 8px; }
        .dropdown-content { display: none; position: absolute; top: 40px; right: 10px; background-color: #fff; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); z-index: 100; min-width: 140px; }
        .dropdown-content a { display: block; padding: 10px 15px; text-decoration: none; color: #333; transition: background 0.3s ease; }
        .dropdown-content a:hover { background-color: #f2f2f2; }

        .form-popup { background-color: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); margin: 20px 0; }
        .form-popup input, .form-popup textarea { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .form-popup button { margin-top: 15px; padding: 10px 15px; background-color: #3b5998; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .form-popup button:hover { background-color: #2e477a; }
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
    <section class="profile-overview">
        <div class="profile-picture">
            <img src="<?= $profile_picture; ?>" alt="Profile Picture">
        </div>
        <div class="profile-details">
            <h2>Welcome, <?= htmlspecialchars($user_data['full_name']); ?></h2>
            <p>Portfolio User | Designer</p>
            <a href="profile.php"><button class="edit-profile-btn">Edit Profile</button></a>
        </div>
    </section>

    <section class="add-portfolio">
        <h3>Add New Portfolio</h3>
        <?php if (isset($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form method="POST" class="form-popup">
            <input type="text" name="project_title" placeholder="Project Title" required>
            <textarea name="project_description" placeholder="Project Description" rows="4" required></textarea>
            <button type="submit" name="add_portfolio">Add Portfolio</button>
        </form>
    </section>

    <section class="my-portfolios">
        <h3>My Portfolios</h3>
        <div class="portfolio-list">
            <?php if ($portfolios->num_rows > 0): ?>
                <?php while ($portfolio = $portfolios->fetch_assoc()): ?>
                    <div class="portfolio-card">
                        <button class="dots-button" onclick="toggleDropdown(this)">⋮</button>
                        <div class="dropdown-content">
                            <a href="manage_portfolio.php?edit=<?= $portfolio['id']; ?>">✏️ Edit</a>
                            <a href="manage_portfolio.php?delete=<?= $portfolio['id']; ?>" onclick="return confirm('Are you sure?')">🗑 Delete</a>
                            <a href="download_portfolio.php?id=<?= $portfolio['id']; ?>">📥 Download</a>
                        </div>
                        <h4><?= htmlspecialchars($portfolio['project_title']); ?></h4>
                        <p><?= nl2br(htmlspecialchars($portfolio['project_description'])); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No portfolios found. Add your first portfolio!</p>
            <?php endif; ?>
        </div>
    </section>

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
        <form method="POST" class="form-popup">
            <input type="hidden" name="portfolio_id" value="<?= $portfolio_to_edit['id']; ?>">
            <input type="text" name="project_title" value="<?= htmlspecialchars($portfolio_to_edit['project_title']); ?>" required>
            <textarea name="project_description" rows="4" required><?= htmlspecialchars($portfolio_to_edit['project_description']); ?></textarea>
            <button type="submit" name="edit_portfolio">Update Portfolio</button>
        </form>
    </section>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>

<script>
function toggleDropdown(button) {
    document.querySelectorAll('.dropdown-content').forEach(menu => {
        if (menu !== button.nextElementSibling) menu.style.display = 'none';
    });
    const dropdown = button.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('.portfolio-card')) {
        document.querySelectorAll('.dropdown-content').forEach(menu => menu.style.display = 'none');
    }
});
</script>
</body>
</html>
