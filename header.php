<?php ; // Make sure session is started ?>
<header class="navbar">
    <link rel="stylesheet" href="css/styles.css">
    <nav class="nav">
        <a href="home.php">Home</a>
        <a href="about.php">About</a>
        <a href="home.php">Portfolio</a>
        <a href="contact.php">Contact</a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php"><button class="login-btn"><?php echo $_SESSION['full_name']; ?></button></a>
            <a href="logout.php"><button class="signup-btn">Logout</button></a>
        <?php endif; ?>
    </nav>
</header>
