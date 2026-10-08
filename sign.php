<?php
require_once 'auth.php';

// Generate Google Auth URL
$authUrl = $client ? $client->createAuthUrl() : 'callback.php?mock_login=1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In / Sign Up</title>
    <link rel="stylesheet" href="CSS/sign.css">
    <style>
        .google-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            color: #757575;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 16px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: background-color 0.2s, box-shadow 0.2s;
            margin: 20px auto;
            max-width: 300px;
        }
        .google-btn:hover {
            background-color: #f8f8f8;
            box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        }
        .google-btn img {
            width: 24px;
            height: 24px;
            margin-right: 12px;
        }
        .container {
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="CSS/premium.css">
</head>
<body>
    <div class="container">
        <h2>Welcome to FurFaimily</h2>
        <p>Adopt, Love, Transform Lives!</p>
        
        <?php if (isset($_GET['error'])): ?>
            <p style="color: red; margin: 10px 0;">Authentication failed. Please try again.</p>
        <?php endif; ?>

        <a href="<?php echo htmlspecialchars($authUrl); ?>" class="google-btn">
            <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google Logo">
            Continue with Google
        </a>
        <br>
        <a href="index.php" style="color: #666; text-decoration: none; font-size: 14px;">Back to Home</a>
    </div>
</body>
</html>
