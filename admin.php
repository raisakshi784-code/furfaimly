<?php
session_start();
require_once 'db.php';

// Simple admin check (in a real app, use roles)
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$message = '';

// Handle Add Pet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pet'])) {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $trait = $_POST['trait_tag'];
    
    $stmt = $pdo->prepare("INSERT INTO pets (name, category, trait_tag) VALUES (?, ?, ?)");
    if ($stmt->execute([$name, $category, $trait])) {
        $message = "Pet added successfully!";
    }
}

// Fetch all pets
$stmt = $pdo->query("SELECT * FROM pets ORDER BY id DESC");
$all_pets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FurFaimily</title>
    <link rel="stylesheet" href="CSS/sign.css">
    <link rel="stylesheet" href="CSS/premium.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
    <style>
        .admin-container { max-width: 900px; margin: 40px auto; padding: 20px; }
        .admin-box { padding: 30px; border-radius: 12px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        body.dark th, body.dark td { border-bottom: 1px solid #444; }
        .form-group { margin-bottom: 15px; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <nav class="nav-bar" style="display:flex; justify-content:space-between; padding:15px 30px;">
        <h2>Admin Dashboard</h2>
        <div>
            <button id="dark-mode-toggle" class="dark-toggle">🌙 Dark Mode</button>
            <a href="index.php" style="text-decoration:none; color:inherit; font-weight:bold;">Home</a>
        </div>
    </nav>

    <div class="admin-container">
        <?php if ($message): ?>
            <div style="background: #4caf50; color: white; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="admin-box">
            <h3>➕ Add New Pet</h3>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Pet Name (e.g. Fluffy)" required>
                </div>
                <div class="form-group">
                    <select name="category" required>
                        <option value="Cat">Cat</option>
                        <option value="Dog">Dog</option>
                        <option value="Parrot">Parrot</option>
                        <option value="Hamster">Hamster</option>
                        <option value="Rabbit">Rabbit</option>
                        <option value="Turtle">Turtle</option>
                    </select>
                </div>
                <div class="form-group">
                    <select name="trait_tag" required>
                        <option value="calm">Calm</option>
                        <option value="active">Active</option>
                        <option value="family">Family Friendly</option>
                    </select>
                </div>
                <button type="submit" name="add_pet" class="btn">Add Pet</button>
            </form>
        </div>

        <div class="admin-box">
            <h3>🐾 Current Pets in Shelter</h3>
            <table>
                <tr><th>ID</th><th>Name</th><th>Category</th><th>Trait</th><th>Status</th></tr>
                <?php foreach($all_pets as $pet): ?>
                <tr>
                    <td>#<?php echo $pet['id']; ?></td>
                    <td><?php echo htmlspecialchars($pet['name']); ?></td>
                    <td><?php echo htmlspecialchars($pet['category']); ?></td>
                    <td><?php echo htmlspecialchars($pet['trait_tag']); ?></td>
                    <td><span style="background:#e3fce1; color:#2d8a26; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;"><?php echo htmlspecialchars($pet['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
