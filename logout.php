<?php
if (isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header('Location: portfolio.html');
    exit;
}
?>
