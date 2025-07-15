<?php include('header.php'); ?>


<main class="about-page">
    <section class="about-content">
        <h2>About Us</h2>
        <p>Welcome to the Portfolio System! Our platform is designed to help you manage, create, and showcase your professional work. Whether you're a developer, designer, or creative professional, this system offers an intuitive interface to organize your projects, update your profile, and track your portfolio progress.</p>

        <p>Our goal is to provide you with an easy-to-use tool for presenting your skills and achievements in a professional manner. With our system, you can:</p>
        <ul>
            <li>Create multiple portfolios to display different projects.</li>
            <li>Update and personalize your profile with custom information.</li>
            <li>Easily share your portfolio with potential employers, clients, or collaborators.</li>
        </ul>

        <p>We believe in the power of showcasing your work online, and our system aims to make it as simple and effective as possible.</p>
    </section>
    <?php include('footer.php'); ?>
</main>



<style>
    body {
        background: url('background.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: Arial, sans-serif;
        color: #fff;
        margin: 0;
    }

    .about-page {
        padding: 40px 20px;
        text-align: center;
    }

    .about-page h2 {
        font-size: 2.5em;
        margin-bottom: 20px;
        color: #fff;
    }

    .about-page p {
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .about-page ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .about-page ul li {
        font-size: 1.2em;
        margin: 10px 0;
        padding-left: 20px;
        text-align: left;
        display: inline-block;
    }

    .about-content {
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 10px;
        padding: 30px;
        max-width: 800px;
        margin: 40px auto;
    }

    .about-page h2, .about-page p, .about-page ul li {
        color: #fff;
    }

    .about-page a {
        color: #fff;
        text-decoration: none;
        background-color: #3b5998;
        padding: 10px 15px;
        border-radius: 5px;
        display: inline-block;
        margin-top: 20px;
    }

    .about-page a:hover {
        background-color: #2e477a;
    }
</style>
