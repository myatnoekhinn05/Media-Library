<?php

$pageTitle = 'Register';
$section = 'register';
$hideSearch = true;

require BASE_PATH . '/view/Layout/header.php';

?>

<link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">

<div class="section page">

    <div class="wrapper">

        <div class="auth-container">

            <h2>Register</h2>

            <!-- SUCCESS MESSAGE -->
            <?php if (!empty($_SESSION['success'])): ?>

                <div class="auth-message auth-success">
                    <?= htmlspecialchars($_SESSION['success']); ?>
                </div>

                <?php unset($_SESSION['success']); ?>

            <?php endif; ?>

            <!-- GENERAL ERROR MESSAGE -->
            <?php if (!empty($_SESSION['error'])): ?>

                <div class="auth-message auth-error">
                    <?= htmlspecialchars($_SESSION['error']); ?>
                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

            <form
                method="POST"
                action="<?= BASE_URL ?>/Public/index.php?page=register-submit">

                <!-- USERNAME -->
                <div class="auth-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        placeholder="Enter your username"
                        value="<?= htmlspecialchars(
                                    $_SESSION['old']['username'] ?? ''
                                ); ?>">

                    <!-- USERNAME ERROR -->
                    <?php if (!empty($_SESSION['errors']['username'])): ?>

                        <small class="error">
                            <?= htmlspecialchars($_SESSION['errors']['username']); ?>
                        </small>

                    <?php endif; ?>

                </div>

                <!-- EMAIL -->
                <div class="auth-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input
                        type="text"
                        name="email"
                        id="email"
                        placeholder="Enter your email"
                        value="<?= htmlspecialchars(
                                    $_SESSION['old']['email'] ?? ''
                                ); ?>">

                    <!-- EMAIL ERROR -->
                    <?php if (!empty($_SESSION['errors']['email'])): ?>

                        <small class="error">
                            <?= htmlspecialchars($_SESSION['errors']['email']); ?>
                        </small>

                    <?php endif; ?>

                </div>

                <!-- PASSWORD -->
                <div class="auth-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Enter your password">

                    <!-- PASSWORD ERROR -->
                    <?php if (!empty($_SESSION['errors']['password'])): ?>

                        <small class="error">
                            <?= htmlspecialchars($_SESSION['errors']['password']); ?>
                        </small>

                    <?php endif; ?>

                </div>

                <!-- CONFIRM PASSWORD -->
                <div class="auth-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        name="confirm_password"
                        id="confirm_password"
                        placeholder="Confirm your password">

                    <!-- CONFIRM PASSWORD ERROR -->
                    <?php if (!empty($_SESSION['errors']['confirm_password'])): ?>

                        <small class="error">
                            <?= htmlspecialchars($_SESSION['errors']['confirm_password']); ?>
                        </small>

                    <?php endif; ?>

                </div>

                <!-- SUBMIT -->
                <button
                    type="submit"
                    class="auth-button">

                    Register

                </button>

            </form>

            <!-- LOGIN LINK -->
            <div class="auth-footer">

                Already have an account?

                <a href="<?= BASE_URL ?>/Public/index.php?page=login">
                    Login here
                </a>

            </div>

        </div>

    </div>

</div>

<?php

unset($_SESSION['errors']);
unset($_SESSION['old']);

?>

<?php require BASE_PATH . '/view/Layout/footer.php'; ?>