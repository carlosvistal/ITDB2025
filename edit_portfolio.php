<?php
include('config.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid portfolio ID.");
}

$portfolio_id = $_GET['id'];
$portfolio = null;

$stmt = $conn->prepare("SELECT * FROM portfolios WHERE id = ?");
$stmt->bind_param("i", $portfolio_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Portfolio not found.");
}

$portfolio = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_title = $_POST['project_title'];
    $new_description = $_POST['project_description'];

    $update_stmt = $conn->prepare("UPDATE portfolios SET project_title = ?, project_description = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $new_title, $new_description, $portfolio_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('Portfolio updated successfully!'); window.location='home.php';</script>";
    } else {
        echo "Error updating portfolio: " . $conn->error;
    }

    $update_stmt->close();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Portfolio - Canva Style</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }
        .edit-container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            display: flex;
            overflow: hidden;
        }
        .edit-sidebar {
            width: 250px;
            background-color: #3b5998;
            color: #fff;
            padding: 20px;
        }
        .edit-sidebar h3 {
            margin-top: 0;
        }
        .edit-sidebar ul {
            list-style: none;
            padding: 0;
        }
        .edit-sidebar ul li {
            margin: 15px 0;
        }
        .edit-sidebar ul li a {
            color: #fff;
            text-decoration: none;
        }
        .edit-sidebar ul li a:hover {
            text-decoration: underline;
        }
        .edit-form {
            flex: 1;
            padding: 30px;
        }
        .edit-form h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .edit-form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        .edit-form input[type="text"],
        .edit-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 5px;
        }
        .edit-form button {
            margin-top: 20px;
            background-color: #3b5998;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .edit-form button:hover {
            background-color: #2d4373;
        }
    </style>
</head>
<body>
    <div class="edit-container">
        <aside class="edit-sidebar">
            <h3>Tools</h3>
            <ul>
                <li><a href="home.php">Dashboard</a></li>
                <li><a href="manage_portfolio.php">Manage Portfolios</a></li>
                <li><a href="create_portfolio.php">Create New</a></li>
            </ul>
        </aside>
        <div class="edit-form">
            <h2>Edit Your Portfolio</h2>
            <form method="POST">
                <label for="project_title">Project Title:</label>
                <input type="text" name="project_title" id="project_title" value="<?= htmlspecialchars($portfolio['project_title']) ?>" required>

                <label for="project_description">Project Description:</label>
                <textarea name="project_description" id="project_description" rows="8" required><?= htmlspecialchars($portfolio['project_description']) ?></textarea>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
