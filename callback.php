<?php
require_once 'auth.php';
require_once 'db.php';

if (isset($_GET['mock_login'])) {
    // Mock login for presentation without real API credentials
    $_SESSION['user'] = [
        'id' => 1,
        'google_id' => 'mock_google_id_123',
        'name' => 'Demo User',
        'email' => 'demo@example.com',
        'avatar' => 'https://ui-avatars.com/api/?name=Demo+User&background=random'
    ];
    header('Location: index.php');
    exit();
}

if (isset($_GET['code']) && $client) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        // Get profile info
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        $google_id = $google_account_info->id;
        $email = $google_account_info->email;
        $name = $google_account_info->name;
        $avatar = $google_account_info->picture;

        // Check if user exists in DB
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $user = $stmt->fetch();

        if (!$user) {
            // Register new user
            $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, avatar) VALUES (?, ?, ?, ?)");
            $stmt->execute([$google_id, $name, $email, $avatar]);
            $user_id = $pdo->lastInsertId();
            
            $user = [
                'id' => $user_id,
                'google_id' => $google_id,
                'name' => $name,
                'email' => $email,
                'avatar' => $avatar
            ];
        }

        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();

    } catch (Exception $e) {
        header('Location: sign.php?error=auth_failed');
        exit();
    }
} else {
    header('Location: sign.php?error=auth_failed');
    exit();
}
?>
