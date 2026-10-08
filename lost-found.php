<?php
session_start();
require_once 'db.php';

$message = '';
// Handle Report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_pet'])) {
    $pet_name = trim($_POST['pet_name'] ?? '');
    $pet_type = trim($_POST['pet_type'] ?? 'Dog');
    $status = trim($_POST['status'] ?? 'Lost');
    $city = trim($_POST['city'] ?? '');
    $last_seen = trim($_POST['last_seen'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');
    $reward = trim($_POST['reward'] ?? '');
    
    // Default image if none uploaded
    $image = ($pet_type === 'Cat') ? 'Img/Img/persian.jpeg' : 'Img/Img/Labrador Retriever.jpeg';

    if (!empty($pet_name) && !empty($city) && !empty($contact_phone)) {
        $stmt = $pdo->prepare("INSERT INTO lost_found (pet_name, pet_type, status, city, last_seen, contact_phone, reward, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$pet_name, $pet_type, $status, $city, $last_seen, $contact_phone, $reward, $image]);
        $message = "Pet report broadcasted to FurFaimily community successfully!";
    }
}

// Fetch all lost & found pets
$stmt = $pdo->query("SELECT * FROM lost_found ORDER BY id DESC");
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Pets Community - FurFaimily</title>
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
        .lf-container { max-width: 1040px; margin: 30px auto; padding: 0 20px 60px; }
        
        .hero-banner-lf {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border-radius: 20px; padding: 36px 40px; color: white; margin-bottom: 30px;
            display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 25px rgba(239,68,68,0.25);
        }
        @media (max-width: 768px) { .hero-banner-lf { flex-direction: column; text-align: center; gap: 20px; } }
        
        .lf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 20px; }
        .lf-card {
            background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: all 0.3s;
        }
        body.dark .lf-card { background: #1e293b; border-color: #334155; }
        .lf-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        
        .lf-card-img { width: 100%; height: 200px; object-fit: cover; }
        .lf-card-body { padding: 20px; }
        
        .badge-lost { background: #fee2e2; color: #b91c1c; font-size: 11.5px; font-weight: 800; padding: 3px 10px; border-radius: 9999px; text-transform: uppercase; }
        .badge-found { background: #dcfce7; color: #15803d; font-size: 11.5px; font-weight: 800; padding: 3px 10px; border-radius: 9999px; text-transform: uppercase; }
        
        .btn-report {
            background: #ffffff; color: #dc2626; border: none; padding: 12px 24px; border-radius: 12px;
            font-size: 14.5px; font-weight: 700; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .btn-report:hover { transform: scale(1.03); background: #fff5f5; }
        
        /* Modal */
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; padding: 20px; backdrop-filter: blur(4px); }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 20px; max-width: 520px; width: 100%; padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); position: relative; }
        body.dark .modal-content { background: #1e293b; color: white; }
    </style>
</head>
<body>
    <div class="lf-container">
        <!-- Top Nav -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
            <a href="index.php" class="nav-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Home</span>
            </a>
            <div style="display:flex; gap:12px; align-items:center;">
                <a href="select.php" style="font-size:13px; font-weight:600; color:#ff6b6b; text-decoration:none;">Adopt a Pet</a>
                <button id="dark-mode-toggle" class="pill-toggle">🌙 Dark Mode</button>
            </div>
        </div>

        <?php if ($message): ?>
            <div style="background:#22c55e; color:white; padding:14px 20px; border-radius:12px; margin-bottom:20px; font-weight:600;">
                ✓ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Hero Section -->
        <div class="hero-banner-lf">
            <div>
                <span style="background:rgba(255,255,255,0.2); padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:700; text-transform:uppercase;">Community Rescue Network</span>
                <h1 style="font-size:32px; font-weight:800; margin: 8px 0;">Lost & Found Pet Registry</h1>
                <p style="opacity:0.95; font-size:15px; max-width:520px;">Help reunite lost pets with their worried families across India. Broadcast an alert or notify when you find a rescue.</p>
            </div>
            <button class="btn-report" onclick="openModal()">+ Report Lost / Found Pet</button>
        </div>

        <!-- Cards Grid -->
        <div class="lf-grid">
            <?php foreach ($reports as $r): ?>
                <div class="lf-card">
                    <img src="<?php echo htmlspecialchars($r['image']); ?>" alt="Pet" class="lf-card-img">
                    <div class="lf-card-body">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                            <span class="<?php echo ($r['status'] === 'Lost') ? 'badge-lost' : 'badge-found'; ?>">
                                🚨 <?php echo htmlspecialchars($r['status']); ?>
                            </span>
                            <?php if ($r['reward']): ?>
                                <span style="font-size:12px; font-weight:700; color:#b45309; background:#fef3c7; padding:2px 8px; border-radius:6px;">Reward: <?php echo htmlspecialchars($r['reward']); ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 style="font-size:19px; font-weight:700; margin-bottom:6px;"><?php echo htmlspecialchars($r['pet_name']); ?></h3>
                        <p style="font-size:13px; color:#64748b; margin-bottom:6px;">📍 <b>City:</b> <?php echo htmlspecialchars($r['city']); ?></p>
                        <p style="font-size:13px; color:#64748b; margin-bottom:14px;">🕒 <b>Last Seen:</b> <?php echo htmlspecialchars($r['last_seen']); ?></p>
                        
                        <div style="background:#f8fafc; padding:12px; border-radius:10px; border:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span style="font-size:11px; color:#64748b; display:block;">Emergency Contact</span>
                                <b style="font-size:14px; color:#0f172a;"><?php echo htmlspecialchars($r['contact_phone']); ?></b>
                            </div>
                            <a href="tel:<?php echo htmlspecialchars($r['contact_phone']); ?>" style="background:#22c55e; color:white; padding:6px 14px; border-radius:8px; text-decoration:none; font-size:12px; font-weight:700;">Call Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="report-modal" class="modal">
        <div class="modal-content">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
                <h2 style="font-size:20px; font-weight:700;">Report a Lost / Found Pet</h2>
                <button onclick="closeModal()" style="background:none; border:none; font-size:22px; cursor:pointer; color:#64748b;">&times;</button>
            </div>
            <form method="POST">
                <div class="field-group">
                    <label>Report Type</label>
                    <select name="status">
                        <option value="Lost">I Lost My Pet (Missing)</option>
                        <option value="Found">I Found a Stray / Lost Pet</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Pet Species</label>
                    <select name="pet_type">
                        <option value="Dog">Dog</option>
                        <option value="Cat">Cat</option>
                        <option value="Bird">Bird</option>
                        <option value="Other">Other Pet</option>
                    </select>
                </div>
                <div class="field-group">
                    <label>Pet Name & Identifying Features</label>
                    <input type="text" name="pet_name" placeholder="e.g. Bruno (Brown Indie with red collar)" required>
                </div>
                <div class="field-group">
                    <label>City & Neighborhood</label>
                    <input type="text" name="city" placeholder="e.g. Bangalore, Koramangala" required>
                </div>
                <div class="field-group">
                    <label>Last Seen Details (Date & Spot)</label>
                    <input type="text" name="last_seen" placeholder="e.g. Near 5th Block Park yesterday at 6 PM" required>
                </div>
                <div class="field-group">
                    <label>Emergency Contact Phone Number</label>
                    <input type="tel" name="contact_phone" placeholder="+91 9876543210" required>
                </div>
                <div class="field-group">
                    <label>Reward (Optional)</label>
                    <input type="text" name="reward" placeholder="e.g. ₹2,000">
                </div>
                <button type="submit" name="report_pet" class="btn-submit-app" style="margin-top:10px;">Broadcast Report</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('report-modal').classList.add('active'); }
        function closeModal() { document.getElementById('report-modal').classList.remove('active'); }
    </script>
</body>
</html>
