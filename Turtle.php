<?php
session_start();
require_once "db.php";
$category = "Turtle";
$stmt = $pdo->prepare("SELECT * FROM pets WHERE category=?");
$stmt->execute([$category]);
$pets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FurFaimily - Turtle Selection</title>
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
                            <img src="<?php echo htmlspecialchars(!empty($pet['image']) ? $pet['image'] : 'Img/Img/Red-Eared Slider.jpeg'); ?>" alt="<?php echo htmlspecialchars($pet["name"]); ?>" class="card-img" style="object-fit:cover;">
                            <div class="card-content">
                                <h3 style="font-size:17px; margin-bottom:4px;"><?php echo htmlspecialchars($pet["name"]); ?></h3>
                                <p style="font-size:12px; color:#666; margin-bottom:10px;"><?php echo htmlspecialchars($pet["breed"] ?? 'Turtle'); ?> &bull; <?php echo htmlspecialchars($pet["city"] ?? 'Delhi'); ?></p>
                                <button class="card-button" style="padding:8px 14px; font-size:13px;">CLICK TO VIEW</button>
                            </div>
                        </div>
                        <div class="card-back" style="padding:15px;">
                            <h3 style="font-size:18px; margin-bottom:4px;"><?php echo htmlspecialchars($pet["name"]); ?></h3>
                            <p style="font-size:12px; opacity:0.9;"><?php echo htmlspecialchars($pet["breed"] ?? 'Turtle'); ?> (<?php echo htmlspecialchars($pet["age"] ?? '1Y'); ?>)</p>
                            <span style="background:rgba(255,255,255,0.2); padding:2px 8px; border-radius:10px; font-size:11px; margin: 4px 0;">✓ Vet Checked & Healthy</span>
                            <p style="font-size:12px; margin:6px 0;"><?php echo htmlspecialchars($pet["trait_tag"] ?? "Calm Aquatic"); ?></p>
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=85x85&data=http://localhost:8000/adopt.php?id=<?php echo $pet["id"]; ?>" alt="QR Code" style="border-radius:4px; background:white; padding:2px;">
                            <a href="adopt.php?id=<?php echo $pet["id"]; ?>" onclick="event.stopPropagation();" style="display:inline-block; margin-top:8px; padding:6px 14px; background:#ff6b6b; color:#fff; border-radius:6px; text-decoration:none; font-weight:bold; font-size:12px;">Adopt Me</a>
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