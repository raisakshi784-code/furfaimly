<?php
session_start();
require_once 'db.php';

$success = false;
$receipt = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_donation'])) {
    $donor_name = trim($_POST['donor_name'] ?? 'Kind Supporter');
    $donor_email = trim($_POST['donor_email'] ?? 'supporter@gmail.com');
    $amount = floatval($_POST['amount'] ?? 1000);
    $payment_method = trim($_POST['payment_method'] ?? 'UPI (Google Pay)');
    $receipt_no = 'FUR-' . strtoupper(substr(md5(time() . rand()), 0, 8));

    $stmt = $pdo->prepare("INSERT INTO donations (donor_name, donor_email, amount, payment_method, receipt_no) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$donor_name, $donor_email, $amount, $payment_method, $receipt_no]);
    
    $success = true;
    $receipt = [
        'name' => $donor_name,
        'email' => $donor_email,
        'amount' => $amount,
        'receipt_no' => $receipt_no,
        'method' => $payment_method,
        'date' => date('d M Y, h:i A')
    ];
}

// Fetch stats
$totalRaised = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM donations")->fetchColumn();
$donorCount = $pdo->query("SELECT COUNT(*) FROM donations")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sponsor Medical & Rescue Fund - FurFaimily</title>
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
        .donate-container { max-width: 840px; margin: 30px auto; padding: 0 20px 60px; }
        
        .donation-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-radius: 20px; padding: 36px 40px; color: white; margin-bottom: 24px;
            box-shadow: 0 10px 25px rgba(5,150,105,0.25);
        }
        .stats-bar { display: flex; gap: 30px; margin-top: 18px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 16px; }
        .stat-item h3 { font-size: 26px; font-weight: 800; }
        .stat-item p { font-size: 12.5px; opacity: 0.9; }
        
        .donate-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); }
        body.dark .donate-card { background: #1e293b; border-color: #334155; }
        
        .preset-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 20px; }
        .preset-btn {
            border: 2px solid #e2e8f0; background: white; border-radius: 12px; padding: 16px; text-align: center; cursor: pointer; transition: all 0.2s;
        }
        body.dark .preset-btn { background: #0f172a; border-color: #334155; color: white; }
        .preset-btn.selected, .preset-btn:hover { border-color: #059669; background: #ecfdf5; }
        body.dark .preset-btn.selected { background: rgba(5,150,105,0.2); border-color: #34d399; }
        .preset-btn strong { font-size: 20px; display: block; color: #047857; margin-bottom: 4px; }
        body.dark .preset-btn strong { color: #34d399; }
        .preset-btn span { font-size: 12px; color: #64748b; }
        
        .certificate-box {
            background: #ffffff; border: 3px double #059669; border-radius: 16px; padding: 30px; text-align: center; margin-top: 24px;
        }
        body.dark .certificate-box { background: #1e293b; }
    </style>
</head>
<body>
    <div class="donate-container">
        <!-- Nav -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <a href="index.php" class="nav-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Home</span>
            </a>
            <button id="dark-mode-toggle" class="pill-toggle">🌙 Dark Mode</button>
        </div>

        <div class="donation-header">
            <span style="background:rgba(255,255,255,0.2); padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:700; text-transform:uppercase;">100% Tax Deductible 80G</span>
            <h1 style="font-size:30px; font-weight:800; margin: 8px 0 4px;">Shelter Medical & Rescue Fund</h1>
            <p style="opacity:0.95; font-size:14px;">Every rupee goes directly to rescuing abandoned animals, emergency surgeries, and rabies vaccination drives.</p>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <h3>₹<?php echo number_format($totalRaised); ?></h3>
                    <p>Total Raised Across India</p>
                </div>
                <div class="stat-item">
                    <h3><?php echo number_format($donorCount); ?>+</h3>
                    <p>Compassionate Guardians</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Transparent Fund Allocation</p>
                </div>
            </div>
        </div>

        <?php if ($success && $receipt): ?>
            <div class="certificate-box">
                <span style="font-size:42px;">🎖️</span>
                <h2 style="color:#059669; font-size:24px; margin: 8px 0;">Official Guardian Certificate</h2>
                <p style="color:#64748b; font-size:14px; margin-bottom:16px;">This verifies that <b><?php echo htmlspecialchars($receipt['name']); ?></b> contributed <b>₹<?php echo number_format($receipt['amount']); ?></b> towards FurFaimily Shelter Animal Welfare.</p>
                
                <div style="background:#f0fdf4; border-radius:10px; padding:14px; max-width:400px; margin: 0 auto 20px; font-size:13px; text-align:left; color:#166534;">
                    <div><b>Receipt #:</b> <?php echo htmlspecialchars($receipt['receipt_no']); ?></div>
                    <div><b>Payment Mode:</b> <?php echo htmlspecialchars($receipt['method']); ?></div>
                    <div><b>Date & Time:</b> <?php echo htmlspecialchars($receipt['date']); ?></div>
                    <div><b>Status:</b> Completed (Verified)</div>
                </div>
                
                <button onclick="window.print()" class="pill-toggle" style="background:#059669; color:white; border:none; padding:10px 20px; cursor:pointer;">🖨️ Print Receipt Certificate</button>
                <a href="index.php" style="margin-left:12px; font-size:13px; color:#64748b; text-decoration:none;">Return to Homepage</a>
            </div>
        <?php else: ?>
            <div class="donate-card">
                <h2 style="font-size:20px; font-weight:700; margin-bottom:6px;">Choose Your Sponsorship Amount</h2>
                <p style="font-size:13.5px; color:#64748b; margin-bottom:20px;">Select a preset or enter any custom contribution amount.</p>

                <div class="preset-grid">
                    <div class="preset-btn selected" onclick="selectAmount(500, this)">
                        <strong>₹500</strong>
                        <span>Rabies & Deworming Kit</span>
                    </div>
                    <div class="preset-btn" onclick="selectAmount(1500, this)">
                        <strong>₹1,500</strong>
                        <span>1 Month Nutrition & Foster</span>
                    </div>
                    <div class="preset-btn" onclick="selectAmount(3500, this)">
                        <strong>₹3,500</strong>
                        <span>Emergency Rescue Surgery</span>
                    </div>
                </div>

                <form method="POST">
                    <input type="hidden" id="selected-amount" name="amount" value="500">
                    
                    <div class="field-group">
                        <label>Your Full Name *</label>
                        <input type="text" name="donor_name" value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Vaibhav Keshari'); ?>" required>
                    </div>
                    
                    <div class="field-group">
                        <label>Your Email Address (for 80G tax receipt) *</label>
                        <input type="email" name="donor_email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? 'vaibhavkeshari495@gmail.com'); ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="UPI (Google Pay / PhonePe)">UPI (Google Pay / PhonePe / Paytm)</option>
                            <option value="Debit / Credit Card">Debit / Credit Card (Visa, MasterCard, RuPay)</option>
                            <option value="Netbanking">Netbanking</option>
                        </select>
                    </div>

                    <button type="submit" name="process_donation" class="btn-submit-app" style="background: linear-gradient(135deg, #059669, #047857);">
                        <span>Proceed to Instant Contribution</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function selectAmount(val, elem) {
            document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('selected'));
            elem.classList.add('selected');
            document.getElementById('selected-amount').value = val;
        }
    </script>
</body>
</html>
