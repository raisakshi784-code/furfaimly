<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->execute([$id]);
$pet = $stmt->fetch();

if (!$pet) {
    die("Pet not found!");
}

// In a real app, you would insert into an adoptions table here.
// For the prototype, we just show a success message.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adopt <?php echo htmlspecialchars($pet['name']); ?></title>
    <link rel="stylesheet" href="CSS/sign.css">
    <style>
        .success-box {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-box h1 { color: #ff6b6b; font-size: 32px; }
        .pet-name { font-weight: bold; font-size: 24px; color: #333; margin: 15px 0; }
        .btn-home { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #333; color: white; text-decoration: none; border-radius: 4px; }
    </style>
    <link rel="stylesheet" href="CSS/premium.css">
</head>
<body>
    <div class="success-box">
        <h1>🎉 Congratulations!</h1>
        <p>You have started the adoption process for:</p>
        <div class="pet-name"><?php echo htmlspecialchars($pet['name']); ?> (<?php echo htmlspecialchars($pet['category']); ?>)</div>
        <p>We have received your request. Our shelter team will contact your registered email shortly to schedule a meet-and-greet.</p>
        
        <a href="index.php" class="btn-home">Back to Home</a>
    </div>
</body>
</html>
