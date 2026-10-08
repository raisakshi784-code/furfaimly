<?php
session_start();
require_once 'db.php';

// Auth check
if (!isset($_SESSION['user'])) {
    header("Location: sign.php");
    exit();
}

$message = '';

// Handle Application Status Update
if (isset($_POST['update_app_status'])) {
    $app_id = intval($_POST['app_id']);
    $new_status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $app_id]);
    
    // If approved, mark pet as adopted
    if ($new_status === 'Approved') {
        $pet_id = intval($_POST['pet_id']);
        $pdo->prepare("UPDATE pets SET status = 'adopted' WHERE id = ?")->execute([$pet_id]);
    }
    $message = "Application #APP-2026-" . str_pad($app_id, 3, '0', STR_PAD_LEFT) . " status updated to " . htmlspecialchars($new_status);
}

// Handle Add Pet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pet'])) {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Dog');
    $breed = trim($_POST['breed'] ?? 'Indie Mix');
    $age = trim($_POST['age'] ?? '1 Year');
    $city = trim($_POST['city'] ?? 'Delhi NCR');
    $trait = trim($_POST['trait_tag'] ?? 'family');
    $vaccinated = isset($_POST['vaccinated']) ? 1 : 0;
    $neutered = isset($_POST['neutered']) ? 1 : 0;
    
    // Image fallback based on category
    $imageMap = [
        'Dog' => 'Img/Img/Labrador Retriever.jpeg',
        'Cat' => 'Img/Img/persian.jpeg',
        'Parrot' => 'Img/Img/Blue-and-gold Macaw.jpeg',
        'Rabbit' => 'Img/Img/Lionhead.jpeg',
        'Turtle' => 'Img/Img/Red-Eared Slider.jpeg',
        'Hamster' => 'Img/Img/Golden Hamster .jpeg',
    ];
    $image = $imageMap[$category] ?? 'Img/Img/persian.jpeg';

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO pets (name, category, breed, age, city, trait_tag, vaccinated, neutered, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')");
        $stmt->execute([$name, $category, $breed, $age, $city, $trait, $vaccinated, $neutered, $image]);
        $message = "New pet '$name' added to shelter catalog successfully!";
    }
}

// Handle Delete Pet
if (isset($_GET['delete_pet'])) {
    $pet_id = intval($_GET['delete_pet']);
    $pdo->prepare("DELETE FROM pets WHERE id = ?")->execute([$pet_id]);
    $message = "Pet record deleted.";
}

// Fetch Data
$applications = $pdo->query("SELECT a.*, p.name AS pet_name, p.category, p.breed FROM applications a JOIN pets p ON a.pet_id = p.id ORDER BY a.id DESC")->fetchAll();
$pets = $pdo->query("SELECT * FROM pets ORDER BY id DESC")->fetchAll();
$donations = $pdo->query("SELECT * FROM donations ORDER BY id DESC LIMIT 15")->fetchAll();
$totalDonations = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shelter Operations Portal - FurFaimily Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/sign.css">
    <link rel="stylesheet" href="CSS/premium.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        body.dark { background-color: #0f172a; color: #f8fafc; }
        .admin-wrap { max-width: 1100px; margin: 30px auto; padding: 0 20px 60px; }
        
        .admin-nav {
            background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 16px 24px;
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
        }
        body.dark .admin-nav { background: #1e293b; border-color: #334155; }
        
        .tab-btn-row { display: flex; gap: 8px; margin-bottom: 20px; }
        .tab-btn {
            background: white; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 18px;
            font-size: 13.5px; font-weight: 600; color: #64748b; cursor: pointer; transition: all 0.2s;
        }
        body.dark .tab-btn { background: #1e293b; border-color: #334155; color: #94a3b8; }
        .tab-btn.active { background: #ff6b6b; border-color: #ff6b6b; color: white; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .admin-card {
            background: white; border-radius: 16px; border: 1px solid #e2e8f0; padding: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 24px;
        }
        body.dark .admin-card { background: #1e293b; border-color: #334155; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13.5px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        body.dark th, body.dark td { border-bottom-color: #334155; }
        th { font-weight: 700; color: #64748b; font-size: 12px; text-transform: uppercase; }
        body.dark th { color: #94a3b8; }
        
        .action-select { padding: 6px 10px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 12.5px; font-family: inherit; }
        .btn-update { background: #0284c7; color: white; border: none; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; }
        
        .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 20px; }
        body.dark .stat-card { background: #1e293b; border-color: #334155; }
        .stat-card h3 { font-size: 24px; font-weight: 800; margin-top: 4px; color: #0f172a; }
        body.dark .stat-card h3 { color: #f8fafc; }
        .stat-card span { font-size: 12.5px; color: #64748b; }
    </style>
</head>
<body>
    <div class="admin-wrap">
        <!-- Admin Navigation -->
        <div class="admin-nav">
            <div style="display:flex; align-items:center; gap:12px;">
                <span style="font-size:24px;">🛡️</span>
                <div>
                    <h2 style="font-size:18px; font-weight:800;">Shelter Operations Command Center</h2>
                    <span style="font-size:12px; color:#64748b;">Logged in as: <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Admin'); ?></span>
                </div>
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <a href="index.php" style="font-size:13px; font-weight:600; color:#64748b; text-decoration:none;">View Site</a>
                <button id="dark-mode-toggle" class="pill-toggle">🌙 Dark Mode</button>
                <a href="logout.php" style="font-size:13px; font-weight:600; color:#ef4444; text-decoration:none; margin-left:8px;">Logout</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="background:#22c55e; color:white; padding:12px 18px; border-radius:10px; margin-bottom:20px; font-weight:600;">
                ✓ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Key Stats -->
        <div class="stat-grid">
            <div class="stat-card">
                <span>Pending Applications</span>
                <h3><?php echo count($applications); ?></h3>
            </div>
            <div class="stat-card">
                <span>Shelter Animals</span>
                <h3><?php echo count($pets); ?></h3>
            </div>
            <div class="stat-card">
                <span>Medical Funds Raised</span>
                <h3 style="color:#059669;">₹<?php echo number_format($totalDonations); ?></h3>
            </div>
            <div class="stat-card">
                <span>Shelter Security</span>
                <h3 style="color:#2563eb;">Active</h3>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="tab-btn-row">
            <button class="tab-btn active" onclick="switchTab('tab-apps', this)">📋 Adoption Applications (<?php echo count($applications); ?>)</button>
            <button class="tab-btn" onclick="switchTab('tab-pets', this)">🐾 Pet Catalog Management</button>
            <button class="tab-btn" onclick="switchTab('tab-funds', this)">💚 Donations & Medical Fund</button>
        </div>

        <!-- TAB 1: APPLICATIONS -->
        <div id="tab-apps" class="tab-content active">
            <div class="admin-card">
                <h2 style="font-size:18px; font-weight:700; margin-bottom:14px;">Adoption Screening Queue</h2>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>App ID</th>
                                <th>Pet</th>
                                <th>Applicant</th>
                                <th>Housing / Exp</th>
                                <th>Contact</th>
                                <th>Current Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><b>#APP-<?php echo str_pad($app['id'], 3, '0', STR_PAD_LEFT); ?></b></td>
                                    <td>
                                        <b><?php echo htmlspecialchars($app['pet_name']); ?></b><br>
                                        <small style="color:#64748b;"><?php echo htmlspecialchars($app['breed']); ?></small>
                                    </td>
                                    <td>
                                        <b><?php echo htmlspecialchars($app['applicant_name']); ?></b><br>
                                        <small style="color:#64748b;"><?php echo htmlspecialchars($app['applicant_city']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($app['residence_type']); ?></small><br>
                                        <small style="color:#64748b;">Exp: <?php echo htmlspecialchars($app['has_experience']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($app['applicant_phone']); ?></small><br>
                                        <small style="color:#64748b;"><?php echo htmlspecialchars($app['applicant_email']); ?></small>
                                    </td>
                                    <td>
                                        <span style="font-weight:700; font-size:12px; padding:3px 8px; border-radius:9999px; background:#eff6ff; color:#1d4ed8;">
                                            <?php echo htmlspecialchars($app['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                            <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                            <input type="hidden" name="pet_id" value="<?php echo $app['pet_id']; ?>">
                                            <select name="new_status" class="action-select">
                                                <option value="Under Review" <?php if ($app['status'] == 'Under Review') echo 'selected'; ?>>Under Review</option>
                                                <option value="Home Check Scheduled" <?php if ($app['status'] == 'Home Check Scheduled') echo 'selected'; ?>>Home Check</option>
                                                <option value="Approved" <?php if ($app['status'] == 'Approved') echo 'selected'; ?>>Approve</option>
                                                <option value="Rejected" <?php if ($app['status'] == 'Rejected') echo 'selected'; ?>>Reject</option>
                                            </select>
                                            <button type="submit" name="update_app_status" class="btn-update">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: PETS INVENTORY -->
        <div id="tab-pets" class="tab-content">
            <div class="admin-card">
                <h2 style="font-size:18px; font-weight:700; margin-bottom:16px;">Add New Shelter Animal</h2>
                <form method="POST" style="display:grid; grid-template-columns:repeat(3, 1fr); gap:14px;">
                    <div>
                        <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:4px;">Pet Name</label>
                        <input type="text" name="name" placeholder="e.g. Simba" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:4px;">Category</label>
                        <select name="category" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Parrot">Parrot</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Turtle">Turtle</option>
                            <option value="Hamster">Hamster</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:4px;">Breed</label>
                        <input type="text" name="breed" placeholder="e.g. Golden Retriever" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:4px;">Age</label>
                        <input type="text" name="age" placeholder="e.g. 2 Years" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:4px;">City / Shelter</label>
                        <input type="text" name="city" placeholder="e.g. Mumbai" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                    </div>
                    <div>
                        <label style="font-size:12.5px; font-weight:600; display:block; margin-bottom:4px;">Temperament</label>
                        <select name="trait_tag" style="width:100%; padding:10px; border-radius:8px; border:1px solid #cbd5e1;">
                            <option value="family">Family Friendly</option>
                            <option value="calm">Calm & Gentle</option>
                            <option value="active">High Energy / Active</option>
                        </select>
                    </div>
                    <div style="grid-column: span 3; display:flex; gap:20px; align-items:center; margin-top:6px;">
                        <label style="font-size:13px; font-weight:600;"><input type="checkbox" name="vaccinated" checked> Fully Vaccinated</label>
                        <label style="font-size:13px; font-weight:600;"><input type="checkbox" name="neutered" checked> Neutered / Spayed</label>
                        <button type="submit" name="add_pet" class="btn-update" style="padding:10px 24px; font-size:14px; margin-left:auto; background:#ff6b6b;">Add to Live Catalog</button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <h2 style="font-size:18px; font-weight:700; margin-bottom:14px;">Current Shelter Catalog</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Breed & Age</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pets as $p): ?>
                            <tr>
                                <td>#<?php echo $p['id']; ?></td>
                                <td><b><?php echo htmlspecialchars($p['name']); ?></b></td>
                                <td><?php echo htmlspecialchars($p['category']); ?></td>
                                <td><?php echo htmlspecialchars($p['breed'] ?? 'Mixed'); ?> (<?php echo htmlspecialchars($p['age'] ?? '1Y'); ?>)</td>
                                <td><?php echo htmlspecialchars($p['city'] ?? 'Delhi'); ?></td>
                                <td>
                                    <span style="font-size:11px; font-weight:700; padding:3px 8px; border-radius:9999px; background:<?php echo ($p['status'] == 'available') ? '#dcfce7; color:#15803d;' : '#fee2e2; color:#b91c1c;'; ?>">
                                        <?php echo strtoupper($p['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="admin.php?delete_pet=<?php echo $p['id']; ?>" onclick="return confirm('Delete this pet record?');" style="color:#ef4444; font-size:12px; font-weight:600; text-decoration:none;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: DONATIONS -->
        <div id="tab-funds" class="tab-content">
            <div class="admin-card">
                <h2 style="font-size:18px; font-weight:700; margin-bottom:14px;">Verified Contributions & 80G Receipts</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Receipt #</th>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donations as $d): ?>
                            <tr>
                                <td><b><?php echo htmlspecialchars($d['receipt_no']); ?></b></td>
                                <td>
                                    <b><?php echo htmlspecialchars($d['donor_name']); ?></b><br>
                                    <small style="color:#64748b;"><?php echo htmlspecialchars($d['donor_email']); ?></small>
                                </td>
                                <td><b style="color:#059669;">₹<?php echo number_format($d['amount']); ?></b></td>
                                <td><?php echo htmlspecialchars($d['payment_method']); ?></td>
                                <td><?php echo date('d M Y', strtotime($d['created_at'])); ?></td>
                                <td><span style="color:#15803d; font-weight:700; font-size:12px;">✓ Verified</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');
        }
    </script>
</body>
</html>
