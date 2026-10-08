<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'auth.php';

// Handle email/password sign-in for presentation demo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $name = !empty($email) ? explode('@', $email)[0] : 'Demo User';
    $_SESSION['user'] = [
        'id' => 1,
        'google_id' => 'email_' . md5($email ?: 'demo'),
        'name' => ucwords(str_replace(['.', '_', '-'], ' ', $name)),
        'email' => $email ?: 'demo.user@furfaimily.org',
        'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=ff6b6b&color=ffffff&bold=true'
    ];
    header('Location: index.php');
    exit();
}

$authUrl = $client ? $client->createAuthUrl() : 'callback.php?mock_login=1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - FurFaimily | Pet Adoption Platform</title>
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/sign.css">
    <link rel="stylesheet" href="CSS/premium.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
</head>
<body>
    <div class="split-container">
        <!-- Left Side Hero Banner -->
        <div class="split-left">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="badge-dot"></span>
                    <span>🐾 India's Trusted Pet Adoption Platform</span>
                </div>
                <h1 class="hero-title">Adopt, Love, Transform Lives.</h1>
                <p class="hero-desc">Join thousands of families giving shelter pets a loving second chance. Every adoption creates a lifelong bond.</p>
                
                <div class="hero-features">
                    <div class="feature-item">
                        <div class="feature-icon">🛡️</div>
                        <div class="feature-text">
                            <strong>100% Verified Shelters</strong>
                            <span>Strict vetting for animal safety</span>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">⚡</div>
                        <div class="feature-text">
                            <strong>1-Click Google Access</strong>
                            <span>Instant onboarding with OAuth 2.0</span>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">📲</div>
                        <div class="feature-text">
                            <strong>Smart QR Adoptions</strong>
                            <span>Instant match on mobile scan</span>
                        </div>
                    </div>
                </div>
                
                <div class="hero-quote">
                    "Saving one pet won't change the world, but surely for that pet, the world will change forever."
                </div>
            </div>
        </div>

        <!-- Right Side Login Box -->
        <div class="split-right">
            <div class="top-nav-actions">
                <a href="index.php" class="nav-back-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back to Home</span>
                </a>
                <button id="dark-mode-toggle" class="dark-toggle pill-toggle">🌙 Dark Mode</button>
            </div>
            
            <div class="login-wrapper">
                <div class="login-card">
                    <!-- Brand Header -->
                    <div class="login-header">
                        <div class="logo-wrapper">
                            <img src="Img/Img/Gemini_Generated_Image_2vj2pb2vj2pb2vj2 (1).jpeg" alt="FurFaimily Logo" class="login-logo">
                        </div>
                        <h2>Welcome to FurFaimily</h2>
                        <p>Sign in to manage adoptions and discover pets</p>
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="error-msg">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Authentication failed. Please try again.
                        </div>
                    <?php endif; ?>

                    <!-- Presentation Helper Badge -->
                    <div class="demo-callout">
                        <span class="callout-badge">Demo</span>
                        <span>Click Google Sign-In for 1-click verified login</span>
                    </div>

                    <!-- Official Google Sign-In Button (GIS Standard) -->
                    <a href="<?php echo htmlspecialchars($authUrl); ?>" class="google-gis-btn" id="google-signin-btn" aria-label="Sign in with Google">
                        <div class="google-icon-box">
                            <!-- Official 4-Color Google 'G' Vector Icon -->
                            <svg class="google-svg-logo" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                        </div>
                        <span class="google-gis-text">Sign in with Google</span>
                    </a>

                    <div class="divider">
                        <span>or continue with email</span>
                    </div>

                    <!-- Email Fallback Form -->
                    <form method="POST" action="sign.php" class="email-login-form">
                        <div class="form-group">
                            <label for="login-email">Email Address</label>
                            <input type="email" id="login-email" name="email" placeholder="name@furfaimily.org" value="demo.adopter@gmail.com" required>
                        </div>
                        <div class="form-group">
                            <div class="label-row">
                                <label for="login-password">Password</label>
                                <span class="forgot-hint">Demo: any password</span>
                            </div>
                            <input type="password" id="login-password" name="password" placeholder="••••••••" value="demo1234" required>
                        </div>
                        <button type="submit" class="submit-btn">
                            <span>Sign In with Email</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    </form>

                    <!-- Footer meta / security note -->
                    <div class="login-footer">
                        <div class="security-note">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            <span>OAuth 2.0 256-bit SSL encrypted login</span>
                        </div>
                        <div class="admin-link-row">
                            <span>Staff / Reviewer?</span>
                            <a href="admin.php">Access Admin Portal →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
