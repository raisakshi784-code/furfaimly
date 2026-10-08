<?php
session_start();
require_once 'db.php';

$user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 1;

// Fetch all applications
$stmt = $pdo->prepare("SELECT a.*, p.name AS pet_name, p.category, p.breed, p.image, p.city AS pet_city 
                       FROM applications a 
                       JOIN pets p ON a.pet_id = p.id 
                       ORDER BY a.id DESC");
$stmt->execute();
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Adoption Applications - FurFaimily</title>
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
        .dashboard-container { max-width: 960px; margin: 30px auto; padding: 0 20px 60px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-header h1 { font-size: 28px; font-weight: 800; }
        
        .app-card {
            background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px 24px; margin-bottom: 16px;
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            transition: all 0.2s;
        }
        body.dark .app-card { background: #1e293b; border-color: #334155; }
        .app-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        
        .app-pet-info { display: flex; align-items: center; gap: 18px; }
        .app-avatar { width: 70px; height: 70px; border-radius: 14px; object-fit: cover; }
        .app-details h3 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .app-details p { font-size: 13px; color: #64748b; margin-bottom: 6px; }
        body.dark .app-details p { color: #94a3b8; }
        
        .status-badge {
            display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; font-weight: 700;
            padding: 5px 12px; border-radius: 9999px;
        }
        .status-review { background: #fef3c7; color: #b45309; }
        .status-homecheck { background: #e0f2fe; color: #0284c7; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #b91c1c; }
        
        .btn-view-tracker {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 10px;
            background: #ff6b6b; color: white; text-decoration: none; font-size: 13.5px; font-weight: 600;
            transition: all 0.2s;
        }
        .btn-view-tracker:hover { background: #ff5252; transform: translateY(-1px); }
        
        .empty-state { text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; }
        body.dark .empty-state { background: #1e293b; border-color: #334155; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="page-header">
            <div>
                <a href="select.php" style="color:#64748b; font-size:13px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:4px; margin-bottom:8px;">← Back to Pets</a>
                <h1>📋 My Adoption Applications</h1>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <button id="dark-mode-toggle" class="pill-toggle">🌙 Dark Mode</button>
            </div>
        </div>

        <?php if (count($applications) > 0): ?>
            <?php foreach ($applications as $app): ?>
                <?php 
                    $st = strtolower($app['status']);
                    $badgeClass = 'status-review';
                    if (strpos($st, 'approved') !== false) $badgeClass = 'status-approved';
                    elseif (strpos($st, 'home') !== false) $badgeClass = 'status-homecheck';
                    elseif (strpos($st, 'reject') !== false) $badgeClass = 'status-rejected';
                ?>
                <div class="app-card">
                    <div class="app-pet-info">
                        <img src="<?php echo htmlspecialchars($app['image'] ?? 'Img/Img/persian.jpeg'); ?>" alt="Pet" class="app-avatar">
                        <div class="app-details">
                            <h3><?php echo htmlspecialchars($app['pet_name']); ?> (<?php echo htmlspecialchars($app['breed'] ?? $app['category']); ?>)</h3>
                            <p>Application ID: <b>#APP-2026-<?php echo str_pad($app['id'], 3, '0', STR_PAD_LEFT); ?></b> &bull; Applied: <?php echo date('d M Y', strtotime($app['applied_at'])); ?></p>
                            <span class="status-badge <?php echo $badgeClass; ?>">● <?php echo htmlspecialchars($app['status']); ?></span>
                        </div>
                    </div>
                    <div>
                        <a href="adopt.php?app_id=<?php echo $app['id']; ?>" class="btn-view-tracker">
                            <span>View Live Tracker</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <span style="font-size:48px;">🐾</span>
                <h2 style="font-size:22px; margin: 12px 0 6px;">No Applications Yet</h2>
                <p style="color:#64748b; margin-bottom:20px;">Browse our shelter pets and submit your first adoption request!</p>
                <a href="select.php" class="btn-view-tracker">Browse Pets for Adoption</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
