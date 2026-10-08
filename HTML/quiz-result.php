<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home = $_POST['home'] ?? '';
    $activity = $_POST['activity'] ?? '';
    $kids = $_POST['kids'] ?? '';

    // Simple algorithm mapping answers to trait_tag
    $target_trait = 'family'; // default
    if ($activity === 'active') {
        $target_trait = 'active';
    } elseif ($home === 'small' && $activity === 'calm') {
        $target_trait = 'calm';
    }

    $stmt = $pdo->prepare("SELECT * FROM pets WHERE trait_tag = ? LIMIT 1");
    $stmt->execute([$target_trait]);
    $match = $stmt->fetch();

    if (!$match) {
        // Fallback to random
        $stmt = $pdo->prepare("SELECT * FROM pets ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $match = $stmt->fetch();
    }
} else {
    header('Location: quiz.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Perfect Match!</title>
    <link rel="stylesheet" href="../CSS/sign.css">
    <style>
        .result-container { max-width: 600px; margin: 50px auto; padding: 20px; text-align: center; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .match-name { font-size: 28px; color: #ff6b6b; margin: 10px 0; }
        .btn-adopt { display: inline-block; padding: 12px 24px; background: #ff6b6b; color: white; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
    </style>
    <link rel="stylesheet" href="../CSS/premium.css">
</head>
<body>
    <div class="result-container">
        <h2>We Found Your Perfect Match!</h2>
        
        <?php if ($match): ?>
            <div class="match-name"><?php echo htmlspecialchars(strtoupper($match['name'])); ?></div>
            <p>Category: <?php echo htmlspecialchars($match['category']); ?></p>
            <p>Trait: <?php echo htmlspecialchars($match['trait_tag']); ?></p>
            
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=http://localhost/FURFAIMILY/HTML/adopt.php?id=<?php echo $match['id']; ?>" alt="Scan to Adopt" style="margin-top:20px; border-radius:8px;">
            <p style="font-size:14px; color:#666;">Scan QR code with your phone!</p>
            
            <a href="adopt.php?id=<?php echo $match['id']; ?>" class="btn-adopt">ADOPT NOW</a>
        <?php else: ?>
            <p>Sorry, no pets available right now.</p>
        <?php endif; ?>
        
        <br><br>
        <a href="quiz.php">Retake Quiz</a> | <a href="index.php">Back to Home</a>
    </div>
</body>
</html>
