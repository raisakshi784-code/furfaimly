<?php
session_start();
require_once 'db.php';

$pet_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_GET['pet_id']) ? intval($_GET['pet_id']) : 0);
$app_id = isset($_GET['app_id']) ? intval($_GET['app_id']) : 0;

$pet = null;
$application = null;
$success_msg = '';

// If viewing a submitted application
if ($app_id > 0) {
    $stmt = $pdo->prepare("SELECT a.*, p.name AS pet_name, p.category, p.breed, p.image, p.age, p.city AS pet_city 
                           FROM applications a 
                           JOIN pets p ON a.pet_id = p.id 
                           WHERE a.id = ?");
    $stmt->execute([$app_id]);
    $application = $stmt->fetch();
    if ($application) {
        $pet_id = $application['pet_id'];
    }
}

if ($pet_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM pets WHERE id = ?");
    $stmt->execute([$pet_id]);
    $pet = $stmt->fetch();
}

if (!$pet && !$application) {
    header('Location: select.php');
    exit();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 1;
    $applicant_name = trim($_POST['applicant_name'] ?? '');
    $applicant_email = trim($_POST['applicant_email'] ?? '');
    $applicant_phone = trim($_POST['applicant_phone'] ?? '');
    $applicant_city = trim($_POST['applicant_city'] ?? '');
    $residence_type = trim($_POST['residence_type'] ?? 'Apartment');
    $has_experience = trim($_POST['has_experience'] ?? 'No');
    $other_pets = trim($_POST['other_pets'] ?? 'None');
    $reason = trim($_POST['reason'] ?? '');

    if (!empty($applicant_name) && !empty($applicant_email) && !empty($applicant_phone)) {
        $stmt = $pdo->prepare("INSERT INTO applications 
            (user_id, pet_id, applicant_name, applicant_email, applicant_phone, applicant_city, residence_type, has_experience, other_pets, reason, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Under Review')");
        $stmt->execute([
            $user_id, $pet['id'], $applicant_name, $applicant_email, 
            $applicant_phone, $applicant_city, $residence_type, 
            $has_experience, $other_pets, $reason
        ]);
        $new_app_id = $pdo->lastInsertId();
        header("Location: adopt.php?app_id=" . $new_app_id . "&success=1");
        exit();
    }
}

$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adoption Application - <?php echo htmlspecialchars($pet['name'] ?? 'Pet'); ?> | FurFaimily</title>
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/sign.css">
    <link rel="stylesheet" href="CSS/premium.css">
    <link rel="stylesheet" href="CSS/dark-mode.css">
    <script src="js/dark-mode.js" defer></script>
    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        body.dark { background-color: #0f172a; color: #f8fafc; }
        .adopt-container { max-width: 860px; margin: 30px auto; padding: 0 20px 60px; }
        .adopt-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.08); overflow: hidden; }
        body.dark .adopt-card { background: #1e293b; border-color: #334155; }
        
        .pet-banner { display: flex; gap: 24px; padding: 32px; background: linear-gradient(135deg, rgba(255,107,107,0.08), rgba(255,82,82,0.02)); border-bottom: 1px solid #e2e8f0; align-items: center; }
        body.dark .pet-banner { background: linear-gradient(135deg, rgba(255,107,107,0.15), rgba(15,23,42,0.4)); border-bottom-color: #334155; }
        .pet-avatar { width: 110px; height: 110px; border-radius: 18px; object-fit: cover; box-shadow: 0 8px 20px rgba(0,0,0,0.12); flex-shrink: 0; }
        .pet-meta h1 { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        body.dark .pet-meta h1 { color: #f8fafc; }
        .pet-meta p { font-size: 14px; color: #64748b; margin-bottom: 12px; }
        body.dark .pet-meta p { color: #94a3b8; }
        
        .health-badges { display: flex; flex-wrap: wrap; gap: 8px; }
        .badge { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; }
        .badge-green { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-blue { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .badge-purple { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }
        body.dark .badge-green { background: rgba(34,197,94,0.2); color: #86efac; border-color: rgba(34,197,94,0.3); }
        body.dark .badge-blue { background: rgba(14,165,233,0.2); color: #7dd3fc; border-color: rgba(14,165,233,0.3); }
        body.dark .badge-purple { background: rgba(168,85,247,0.2); color: #d8b4fe; border-color: rgba(168,85,247,0.3); }
        
        /* Application Stepper Tracker */
        .stepper-box { padding: 32px; background: #fafbfc; border-bottom: 1px solid #e2e8f0; }
        body.dark .stepper-box { background: #151f32; border-bottom-color: #334155; }
        .stepper { display: flex; justify-content: space-between; position: relative; margin: 20px 0 10px; }
        .stepper::before { content: ''; position: absolute; top: 18px; left: 40px; right: 40px; height: 3px; background: #e2e8f0; z-index: 1; }
        body.dark .stepper::before { background: #334155; }
        .step-item { position: relative; z-index: 2; display: flex; flex-direction: column; align-items: center; flex: 1; text-align: center; }
        .step-circle { width: 38px; height: 38px; border-radius: 50%; background: #ffffff; border: 2px solid #cbd5e1; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #64748b; margin-bottom: 8px; transition: all 0.3s; }
        body.dark .step-circle { background: #1e293b; border-color: #475569; color: #94a3b8; }
        .step-item.active .step-circle { background: #ff6b6b; border-color: #ff6b6b; color: #ffffff; box-shadow: 0 0 0 4px rgba(255,107,107,0.2); }
        .step-item.completed .step-circle { background: #22c55e; border-color: #22c55e; color: #ffffff; }
        .step-label { font-size: 12.5px; font-weight: 600; color: #64748b; max-width: 110px; }
        body.dark .step-label { color: #94a3b8; }
        .step-item.active .step-label { color: #ff6b6b; font-weight: 700; }
        .step-item.completed .step-label { color: #22c55e; font-weight: 700; }

        /* Form Styles */
        .form-section { padding: 32px; }
        .section-title { font-size: 18px; font-weight: 700; margin-bottom: 6px; color: #0f172a; }
        body.dark .section-title { color: #f8fafc; }
        .section-desc { font-size: 13.5px; color: #64748b; margin-bottom: 24px; }
        body.dark .section-desc { color: #94a3b8; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        @media (max-width: 650px) { .form-grid { grid-template-columns: 1fr; } }
        
        .field-group { margin-bottom: 16px; }
        .field-group label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; }
        body.dark .field-group label { color: #cbd5e1; }
        .field-group input, .field-group select, .field-group textarea {
            width: 100%; padding: 12px 14px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #ffffff; color: #0f172a; font-family: inherit; font-size: 14px; transition: all 0.2s;
        }
        body.dark .field-group input, body.dark .field-group select, body.dark .field-group textarea {
            background: #0f172a; border-color: #334155; color: #f8fafc;
        }
        .field-group input:focus, .field-group select:focus, .field-group textarea:focus {
            outline: none; border-color: #ff6b6b; box-shadow: 0 0 0 3px rgba(255,107,107,0.15);
        }
        
        .btn-submit-app {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; height: 50px; background: linear-gradient(135deg, #ff6b6b, #ff5252); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 14px rgba(255,107,107,0.3); transition: all 0.2s;
        }
        .btn-submit-app:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255,107,107,0.45); }
        
        .nav-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; margin-bottom: 10px; }
        .status-pill { padding: 6px 14px; border-radius: 9999px; font-size: 13px; font-weight: 700; display: inline-block; }
        .status-pill.review { background: #fef3c7; color: #b45309; }
        .status-pill.approved { background: #dcfce7; color: #166534; }
        .status-pill.homecheck { background: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>
    <div class="adopt-container">
        <!-- Top Navigation -->
        <div class="nav-header">
            <a href="select.php" class="nav-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Pet Selection</span>
            </a>
            <div style="display:flex; gap:12px; align-items:center;">
                <a href="my-applications.php" style="font-size:13px; font-weight:600; color:#ff6b6b; text-decoration:none;">My Applications</a>
                <button id="dark-mode-toggle" class="pill-toggle">🌙 Dark Mode</button>
            </div>
        </div>

        <div class="adopt-card">
            <!-- Pet Banner -->
            <div class="pet-banner">
                <img src="<?php echo htmlspecialchars($pet['image'] ?? 'Img/Img/persian.jpeg'); ?>" alt="Pet" class="pet-avatar">
                <div class="pet-meta">
                    <h1>Meet <?php echo htmlspecialchars($pet['name']); ?></h1>
                    <p><b>Breed:</b> <?php echo htmlspecialchars($pet['breed'] ?? $pet['category']); ?> &bull; <b>Age:</b> <?php echo htmlspecialchars($pet['age'] ?? '1 Year'); ?> &bull; <b>City:</b> <?php echo htmlspecialchars($pet['city'] ?? 'Delhi NCR'); ?></p>
                    <div class="health-badges">
                        <span class="badge badge-green">✓ 100% Vaccinated</span>
                        <span class="badge badge-blue">✓ Neutered / Spayed</span>
                        <span class="badge badge-purple">✓ Smart QR Tag ID #<?php echo $pet['id']; ?></span>
                        <span class="badge badge-green">✓ Dewormed & Checked</span>
                    </div>
                </div>
            </div>

            <!-- Application Status Tracker (If already submitted) -->
            <?php if ($application): ?>
                <?php 
                    $currStatus = $application['status'];
                    $s1 = 'completed';
                    $s2 = ($currStatus == 'Under Review') ? 'active' : (($currStatus == 'Home Check Scheduled' || $currStatus == 'Approved') ? 'completed' : '');
                    $s3 = ($currStatus == 'Home Check Scheduled') ? 'active' : (($currStatus == 'Approved') ? 'completed' : '');
                    $s4 = ($currStatus == 'Approved') ? 'completed active' : '';
                ?>
                <div class="stepper-box">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase;">Application #APP-2026-<?php echo str_pad($application['id'], 3, '0', STR_PAD_LEFT); ?></span>
                            <h2 style="font-size:20px; font-weight:700; margin-top:2px;">Adoption Progress Tracker</h2>
                        </div>
                        <span class="status-pill <?php echo strtolower(str_replace(' ', '', $currStatus)); ?>"><?php echo htmlspecialchars($currStatus); ?></span>
                    </div>

                    <div class="stepper">
                        <div class="step-item <?php echo $s1; ?>">
                            <div class="step-circle">✓</div>
                            <span class="step-label">1. Applied</span>
                        </div>
                        <div class="step-item <?php echo $s2; ?>">
                            <div class="step-circle"><?php echo ($s2 == 'completed') ? '✓' : '2'; ?></div>
                            <span class="step-label">2. Shelter Screening</span>
                        </div>
                        <div class="step-item <?php echo $s3; ?>">
                            <div class="step-circle"><?php echo ($s3 == 'completed') ? '✓' : '3'; ?></div>
                            <span class="step-label">3. Home Visit / Call</span>
                        </div>
                        <div class="step-item <?php echo $s4; ?>">
                            <div class="step-circle"><?php echo ($s4 == 'completed') ? '✓' : '4'; ?></div>
                            <span class="step-label">4. Handover</span>
                        </div>
                    </div>
                    
                    <div style="background:#f1f5f9; padding:14px; border-radius:10px; margin-top:20px; font-size:13.5px; color:#475569;">
                        <b>Shelter Note:</b> <?php 
                            if ($currStatus == 'Approved') echo 'Congratulations! Your adoption has been approved. Please visit our shelter with government ID for the welcome handover ceremony.';
                            elseif ($currStatus == 'Home Check Scheduled') echo 'Our shelter officer will contact your phone (' . htmlspecialchars($application['applicant_phone']) . ') to schedule a brief 10-minute video call.';
                            else echo 'Your application has been received and is under priority review by our shelter verification team.';
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Interactive Screening Form -->
                <div class="form-section">
                    <h2 class="section-title">Official Adoption Screening Form</h2>
                    <p class="section-desc">To ensure <?php echo htmlspecialchars($pet['name']); ?> goes to a safe and loving home, please fill out this quick screening application.</p>

                    <form method="POST" action="adopt.php?id=<?php echo $pet['id']; ?>">
                        <div class="form-grid">
                            <div class="field-group">
                                <label for="applicant_name">Full Name *</label>
                                <input type="text" id="applicant_name" name="applicant_name" required value="<?php echo htmlspecialchars($currentUser['name'] ?? 'Vaibhav Keshari'); ?>">
                            </div>
                            <div class="field-group">
                                <label for="applicant_email">Email Address *</label>
                                <input type="email" id="applicant_email" name="applicant_email" required value="<?php echo htmlspecialchars($currentUser['email'] ?? 'vaibhavkeshari495@gmail.com'); ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field-group">
                                <label for="applicant_phone">WhatsApp / Contact Phone *</label>
                                <input type="tel" id="applicant_phone" name="applicant_phone" placeholder="+91 9876543210" required value="+91 9876543210">
                            </div>
                            <div class="field-group">
                                <label for="applicant_city">Current City *</label>
                                <input type="text" id="applicant_city" name="applicant_city" required value="<?php echo htmlspecialchars($pet['city'] ?? 'Delhi NCR'); ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field-group">
                                <label for="residence_type">Housing Type *</label>
                                <select id="residence_type" name="residence_type">
                                    <option value="Apartment (Pet Friendly)">Apartment (Pet Friendly)</option>
                                    <option value="Independent House with Garden">Independent House with Garden</option>
                                    <option value="Gated Villa">Gated Villa</option>
                                    <option value="Rented with Landlord NOC">Rented with Landlord NOC</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label for="has_experience">Prior Experience with Pets? *</label>
                                <select id="has_experience" name="has_experience">
                                    <option value="Yes (Experienced Pet Parent)">Yes (Experienced Pet Parent)</option>
                                    <option value="No (First Time Parent, Ready to Learn)">No (First Time Parent, Ready to Learn)</option>
                                </select>
                            </div>
                        </div>

                        <div class="field-group">
                            <label for="other_pets">Do you currently have any other pets at home?</label>
                            <input type="text" id="other_pets" name="other_pets" placeholder="e.g. 1 Indie Dog, or None" value="None">
                        </div>

                        <div class="field-group">
                            <label for="reason">Why do you want to adopt <?php echo htmlspecialchars($pet['name']); ?>?</label>
                            <textarea id="reason" name="reason" rows="3" placeholder="Tell us a little bit about your family and daily routine...">We love animals and have a spacious, pet-friendly home. Looking forward to providing a forever loving family.</textarea>
                        </div>

                        <button type="submit" name="submit_application" class="btn-submit-app">
                            <span>Submit Adoption Screening Application</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
