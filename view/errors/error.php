<?php

declare(strict_types=1);

$title ??= 'Application Error';
$message ??= 'Something went wrong.';
$errorList ??= [];
$statusCode ??= 500;

http_response_code($statusCode);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 700px;
            margin: auto;
        }

        .box {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #d32f2f;
            margin-top: 0;
        }

        p {
            color: #444;
            line-height: 1.6;
        }

        ul {
            text-align: left;
            margin-top: 20px;
        }

        li {
            margin-bottom: 10px;
            color: #555;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn:hover {
            background: #125ea7;
        }
    </style>

</head>

<body>

    <div class="container">

        <div class="box">

            <h1><?= htmlspecialchars($title) ?></h1>

            <!-- ONLY ONE MESSAGE (NO DUPLICATION) -->
            <p><?= htmlspecialchars($message) ?></p>

            <?php if (!empty($errorList)): ?>
                <ul>
                    <?php foreach ($errorList as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <a class="btn"
                href="<?= BASE_URL ?>/Public/index.php?page=home">
                Go Home
            </a>

        </div>

    </div>

</body>

</html>