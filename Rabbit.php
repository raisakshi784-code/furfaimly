<?php
session_start();
require_once "db.php";
$category = "Rabbit";
$stmt = $pdo->prepare("SELECT * FROM pets WHERE category=?");
$stmt->execute([$category]);
$pets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurFaimily - Rabbit Selection</title>
    <link rel="icon" href="Img/Img/Gemini_Generated_Image_2vj2pb2vj2pb2vj2 (1).jpeg" type="image/x-icon">
    <link rel="stylesheet" href="CSS/Cat.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
    <link rel="stylesheet" href="CSS/premium.css">
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-left">
            <img src="Img/Img/Gemini_Generated_Image_2vj2pb2vj2pb2vj2 (1).jpeg" alt="FurFaimily Logo" class="nav-logo">
            <h1>FurFaimily</h1>
        </div>
        <div class="nav-right">
            <div class="nav-links">
                <button id="dark-mode-toggle" class="dark-toggle">🌙 Dark Mode</button>
                <a href="index.php">Home</a>
                <select id="user-type" onchange="navigateToPage(this.value)">
                    <option value="" disabled selected>Select Role</option>
                    <option value="dog.php">Dog</option>
                    <option value="cat.php">Cat</option>
                    <option value="parrot.php">Parrot</option>
                    <option value="hamster.php">Hamster</option>
                    <option value="rabbit.php">Rabbit</option>
                    <option value="turtle.php">Turtle</option>
                </select>
                <button id="btn-logout" onclick="logOut()">LOGOUT</button>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card-container">
            <?php if (count($pets) > 0): ?>
                <?php foreach ($pets as $pet): ?>
                <div class="card" onclick="flipCard(this)">
                    <div class="card-inner">
                        <div class="card-front">
                            <img src="Img/Img/persian.jpeg" alt="<?php echo htmlspecialchars($pet["name"]); ?>" class="card-img">
                            <div class="card-content">
                                <button class="card-button"><?php echo htmlspecialchars(strtoupper($pet["name"])); ?></button>
                            </div>
                        </div>
                        <div class="card-back">
                            <p><?php echo htmlspecialchars($pet["name"]); ?>: <?php echo htmlspecialchars($pet["trait_tag"] ?? "A wonderful companion!"); ?></p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=http://localhost/FURFAIMILY/adopt.php?id=<?php echo $pet["id"]; ?>" alt="QR Code" style="margin-top: 10px; border-radius: 4px;">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; width:100%; font-size:18px;">No pets available right now.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2024 FurFaimily. All rights reserved.</p>
    </footer>

    <script>
        function flipCard(card) {
            card.classList.toggle("flipped");
        }
        function logOut() {
            window.location.href = "logout.php"; 
        }
        function navigateToPage(page) {
            if (page) { window.location.href = page; }
        }
    </script>
</body>
</html>