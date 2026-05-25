<?php

$pageTitle = 'Register';
$section = 'register';
$hideSearch = true;

require BASE_PATH . '/view/Layout/header.php';

?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/register.css">

<div class="section page">

    <div class="wrapper">

        <div class="auth-container">

            <h2>Register</h2>

            <!-- SUCCESS MESSAGE -->
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="auth-message auth-success">
                    <?= $_SESSION['success']; ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <!-- ERROR MESSAGE -->
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="auth-message auth-error">
                    <?= $_SESSION['error']; ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form
                method="POST"
                action="<?= BASE_URL ?>/Public/index.php?page=register-submit">

                <!-- USERNAME -->
                <div class="auth-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        placeholder="Enter your username"
                        required>
                </div>

                <!-- EMAIL -->
                <div class="auth-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Enter your email"
                        required>
                </div>

                <!-- PASSWORD -->
                <div class="auth-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password"
                        required>
                </div>

                <!-- SUBMIT -->
                <button type="submit" class="auth-button">Register</button>

            </form>

            <!-- LOGIN LINK -->
            <div class="auth-footer">
                Already have an account?
                <a href="<?= BASE_URL ?>/Public/index.php?page=login">Login here</a>
            </div>

        </div>

    </div>

</div>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>