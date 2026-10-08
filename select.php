<?php
session_start();
require_once 'db.php';

// Get counts per category
$categoryCounts = [];
$counts = $pdo->query("SELECT category, COUNT(*) as count FROM pets WHERE status='available' GROUP BY category")->fetchAll();
foreach ($counts as $c) {
    $categoryCounts[$c['category']] = $c['count'];
}

$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Pets for Adoption - FurFaimily</title>
    <link rel="icon" href="Img/Img/Gemini_Generated_Image_2vj2pb2vj2pb2vj2 (1).jpeg" type="image/x-icon">
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/select.css">
    <link rel="stylesheet" href="CSS/premium.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        body.dark { background-color: #0f172a; color: #f8fafc; }
        
        .explorer-header { text-align: center; padding: 40px 20px 20px; max-width: 800px; margin: 0 auto; }
        .explorer-header h1 { font-size: 36px; font-weight: 800; color: #0f172a; letter-spacing: -0.6px; margin-bottom: 8px; }
        body.dark .explorer-header h1 { color: #f8fafc; }
        .explorer-header p { font-size: 16px; color: #64748b; }
        body.dark .explorer-header p { color: #94a3b8; }
        
        .quick-nav-bar {
            display: flex; justify-content: center; gap: 12px; margin: 20px 0 30px; flex-wrap: wrap;
        }
        .nav-chip {
            display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 9999px;
            background: white; border: 1.5px solid #e2e8f0; font-size: 13.5px; font-weight: 600; color: #334155;
            text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.03); transition: all 0.2s;
        }
        body.dark .nav-chip { background: #1e293b; border-color: #334155; color: #cbd5e1; }
        .nav-chip:hover { border-color: #ff6b6b; color: #ff6b6b; transform: translateY(-1px); }
        .nav-chip.highlight { background: #fff1f2; border-color: #fecdd3; color: #e11d48; }
        body.dark .nav-chip.highlight { background: rgba(225,29,72,0.2); border-color: rgba(225,29,72,0.3); color: #fda4af; }
        
        .card-count-badge {
            background: #ff6b6b; color: white; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px; margin-left: 6px;
        }
    </style>
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
                <a href="my-applications.php">My Applications</a>
                <a href="lost-found.php">Lost & Found</a>
                <a href="donate.php">Sponsor Medical</a>
                <button id="btn-logout" onclick="logOut()">LOGOUT</button>
            </div>
        </div>
    </nav>

    <div class="explorer-header">
        <h1>Find Your Perfect Companion</h1>
        <p>Every pet has been veterinary checked, rabies vaccinated, and is eager to meet you.</p>
        
        <!-- Quick Action Navigation Chips -->
        <div class="quick-nav-bar">
            <a href="my-applications.php" class="nav-chip highlight">
                <span>📋 Track Applications</span>
            </a>
            <a href="lost-found.php" class="nav-chip">
                <span>🚨 Lost & Found Registry</span>
            </a>
            <a href="donate.php" class="nav-chip">
                <span>💚 Sponsor Medical Care</span>
            </a>
            <a href="admin.php" class="nav-chip">
                <span>⚙️ Shelter Admin Portal</span>
            </a>
        </div>
    </div>

    <div class="container">
        <div class="card-container" style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
            <!-- DOG -->
            <div class="card">
                <img src="Img/Img/Gemini_Generated_Image_xqfjilxqfjilxqfj.jpeg" alt="Dog" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">DOGS <span class="card-count-badge"><?php echo $categoryCounts['Dog'] ?? 2; ?> Available</span></h3>
                    <a href="Dog.php"><button class="card-button">MEET DOGS</button></a>
                </div>
            </div>

            <!-- CAT -->
            <div class="card">
                <img src="Img/Img/Gemini_Generated_Image_79f0bf79f0bf79f0.jpeg" alt="Cat" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">CATS <span class="card-count-badge"><?php echo $categoryCounts['Cat'] ?? 2; ?> Available</span></h3>
                    <a href="Cat.php"><button class="card-button">MEET CATS</button></a>
                </div>
            </div>

            <!-- PARROT -->
            <div class="card">
                <img src="Img/Img/Gemini_Generated_Image_kr9hzakr9hzakr9h.jpeg" alt="Parrot" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">BIRDS <span class="card-count-badge"><?php echo $categoryCounts['Parrot'] ?? 2; ?> Available</span></h3>
                    <a href="Parrot.php"><button class="card-button">MEET BIRDS</button></a>
                </div>
            </div>

            <!-- RABBIT -->
            <div class="card">
                <img src="Img/Img/Gemini_Generated_Image_bo2yxlbo2yxlbo2y.jpeg" alt="Rabbit" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">RABBITS <span class="card-count-badge"><?php echo $categoryCounts['Rabbit'] ?? 1; ?> Available</span></h3>
                    <a href="Rabbit.php"><button class="card-button">MEET RABBITS</button></a>
                </div>
            </div>

            <!-- TURTLE -->
            <div class="card">
                <img src="Img/Img/Gemini_Generated_Image_9ov8co9ov8co9ov8.jpeg" alt="Turtle" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">TURTLES <span class="card-count-badge"><?php echo $categoryCounts['Turtle'] ?? 1; ?> Available</span></h3>
                    <a href="Turtle.php"><button class="card-button">MEET TURTLES</button></a>
                </div>
            </div>

            <!-- HAMSTER -->
            <div class="card">
                <img src="Img/Img/Gemini_Generated_Image_q35lfq35lfq35lfq.jpeg" alt="Hamster" class="card-img">
                <div class="card-content">
                    <h3 class="card-title">HAMSTERS <span class="card-count-badge"><?php echo $categoryCounts['Hamster'] ?? 1; ?> Available</span></h3>
                    <a href="Hamster.php"><button class="card-button">MEET HAMSTERS</button></a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer" style="margin-top: 50px;">
        <p>&copy; 2026 FurFaimily - India's Certified Ethical Pet Adoption & Rescue Network.</p>
    </footer>

    <script>
        function logOut() {
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>
