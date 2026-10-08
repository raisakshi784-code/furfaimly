<?php
require_once 'auth.php';
$authUrl = $client ? $client->createAuthUrl() : 'callback.php?mock_login=1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - FurFaimily</title>
    <link rel="stylesheet" href="CSS/sign.css">
    <link rel="stylesheet" href="CSS/premium.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
</head>
<body>
    <div class="split-container">
        <!-- Left Side Image -->
        <div class="split-left">
            <div class="overlay-text">
                <h1>Adopt, Love, Transform Lives.</h1>
                <p>Join thousands of families giving pets a second chance.</p>
            </div>
        </div>

        <!-- Right Side Login -->
        <div class="split-right">
            <button id="dark-mode-toggle" class="dark-toggle absolute-toggle">🌙</button>
            
            <div class="login-box">
                <div class="login-header">
                    <img src="Img/Img/Gemini_Generated_Image_2vj2pb2vj2pb2vj2 (1).jpeg" alt="FurFaimily Logo" class="login-logo">
                    <h2>Welcome Back</h2>
                    <p>Please sign in to continue to FurFaimily.</p>
                </div>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="error-msg">Authentication failed. Please try again.</div>
                <?php endif; ?>

                <!-- Official Google Sign-In Button Styling -->
                <a href="<?php echo htmlspecialchars($authUrl); ?>" class="google-btn-official">
                    <div class="google-icon-wrapper">
                        <img class="google-icon" src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg"/>
                    </div>
                    <p class="btn-text"><b>Sign in with Google</b></p>
                </a>
                
                <div class="divider">
                    <span>or</span>
                </div>
                
                <a href="index.php" class="back-link">← Back to Homepage</a>
            </div>
        </div>
    </div>
</body>
</html>
