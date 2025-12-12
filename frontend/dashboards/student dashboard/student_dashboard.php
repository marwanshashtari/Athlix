<?php
//Path: frontend/dashboards/student_dashboard/student_dashboard.php

// ==========================================
// 1. CONFIG & SESSION
// ==========================================
session_start();

// Path Correction: Go up 3 levels to backend
require_once '../../../backend/config.php';

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Student') {
    header('Location: ../../landing_page/landing_page.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================================
// 2. HANDLE "ADD NEW SPORT" FORM SUBMISSION
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_sport') {
    $sportName = trim($_POST['sport']);
    $level = $_POST['level']; 
    $description = $_POST['description'];
    
    // Combine Level and Description
    $fullAchievements = "Level: $level. " . $description;
    
    // A. Check if Sport exists using helper q_row()
    $row = q_row("SELECT Sport_ID FROM Sports WHERE Name = ?", [$sportName]);

    if ($row) {
        $sportID = $row['Sport_ID'];
    } else {
        // Insert Sport using helper q()
        q("INSERT INTO Sports (Name) VALUES (?)", [$sportName]);
        
        // Get the ID (SQL Server specific: SCOPE_IDENTITY)
        $idRes = q_row("SELECT SCOPE_IDENTITY() as id");
        $sportID = $idRes['id'];
    }

    // B. Link to Student
    q("INSERT INTO Sports_Student 
       (Std_ID, Sport_ID, Number_of_Tournaments_Won, Tournaments_Description, Achievements, Years_of_Experience) 
       VALUES (?, ?, 0, '', ?, 0)", 
       [$user_id, $sportID, $fullAchievements]
    );

    // Refresh page
    header("Location: student_dashboard.php");
    exit();
}

// ==========================================
// 3. FETCH DATA FOR DASHBOARD
// ==========================================

// A. Get Student Name
$row = q_row("SELECT Name FROM Student WHERE User_ID = ?", [$user_id]);
$studentName = $row['Name'] ?? 'Student';
$firstName = explode(' ', trim($studentName))[0];

// B. Get Student's Sports Submissions
$sql_subs = "SELECT ss.*, s.Name as SportName 
             FROM Sports_Student ss 
             JOIN Sports s ON ss.Sport_ID = s.Sport_ID 
             WHERE ss.Std_ID = ?";
             
// Execute query using helper
$stmt_subs = q($sql_subs, [$user_id]);

$mySubmissions = [];
// Use sqlsrv_fetch_array instead of PDO fetch
while ($r = sqlsrv_fetch_array($stmt_subs, SQLSRV_FETCH_ASSOC)) {
    $mySubmissions[] = $r;
}

// C. Get Available Discounts (Active Scholarships)
$sql_offers = "SELECT sch.*, u.Name as UniName 
               FROM Scholarship sch 
               JOIN University_ u ON sch.Uni_ID = u.User_ID 
               WHERE sch.Active = 1";
$stmt_offers = q($sql_offers);

$availableDiscounts = [];
while ($r = sqlsrv_fetch_array($stmt_offers, SQLSRV_FETCH_ASSOC)) {
    $availableDiscounts[] = $r;
}

// D. Calculate Stats
$totalSubs = count($mySubmissions);
$activeDiscountCount = count($availableDiscounts);
$approvedCount = 0; // Logic for counting approved status can be added here later
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportMatch - Student Dashboard</title>
    <link rel="stylesheet" href="student_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="min-h-screen bg-gray-50">
        
        <div class="navbar">
            <div class="logo">
                <img src="../../landing_page/landing_images/logo_athlix.jpg" alt="logo" height="40">
                <div class="logo-text">Athlix</div>
            </div>
            <div class="login-buttons">
                <form action="../../edit_profile/edit_profile.php" method="post">
                    <button type="submit" class="btn btn-dark loginButton">Edit profile</button>
                </form>
                <form action="../landing_page/landing_page.php" method="post">
                    <input type="hidden" name="type">
                    <button type="submit" class="btn btn-dark logOutButton">Log Out</button>
                </form>
            </div>
        </div>

        <div class="container py-8">
            
            <div class="mb-8">
                <h1 class="text-4xl mb-2">Welcome back, <?php echo htmlspecialchars($firstName); ?>!</h1>
                <p class="text-gray-600">Manage your sports credentials and discover university discounts</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <div class="card-title text-sm">Total Submissions</div>
                        <i class="fas fa-upload text-gray-500"></i>
                    </div>
                    <div class="card-content">
                        <div class="text-2xl" id="total-submissions"><?php echo $totalSubs; ?></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <div class="card-title text-sm">Approved</div>
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="card-content">
                        <div class="text-2xl text-green-600" id="approved-count"><?php echo $approvedCount; ?></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header flex items-center justify-between">
                        <div class="card-title text-sm">Available Discounts</div>
                        <i class="fas fa-percent text-purple-600"></i>
                    </div>
                    <div class="card-content">
                        <div class="text-2xl text-purple-600" id="discounts-count"><?php echo $activeDiscountCount; ?></div>
                    </div>
                </div>
            </div>

            <div class="tabs">
                <div class="tabs-list">
                    <button class="tabs-trigger active" data-tab="submissions">My Submissions</button>
                    <button class="tabs-trigger" data-tab="discounts">Available Discounts</button>
                </div>

                <div class="tabs-content active" id="submissions-tab">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-2xl">Sport Submissions</h2>
                            <button class="btn btn-primary" id="open-modal-btn">
                                <i class="fas fa-upload"></i>
                                Submit New Sport
                            </button>
                        </div>

                        <div class="grid gap-4" id="submissions-list">
                            <?php if (empty($mySubmissions)): ?>
                                <p class="text-gray-600">You haven't submitted any sports yet.</p>
                            <?php else: ?>
                                <?php foreach ($mySubmissions as $sub): ?>
                                <div class="card">
                                    <div class="card-header">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="card-title"><?php echo htmlspecialchars($sub['SportName']); ?></div>
                                                <div class="card-description">
                                                    <?php echo htmlspecialchars($sub['Years_of_Experience']); ?> Years Experience
                                                </div>
                                            </div>
                                            <div class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Active
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <p class="text-sm text-gray-600 mb-2">
                                            <?php echo htmlspecialchars($sub['Achievements'] ?: 'No details provided.'); ?>
                                        </p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tabs-content" id="discounts-tab">
                    <div class="space-y-4">
                        <h2 class="text-2xl">Available University Discounts</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="discounts-list">
                            <?php if (empty($availableDiscounts)): ?>
                                <p class="text-gray-600">No scholarships available at the moment.</p>
                            <?php else: ?>
                                <?php foreach ($availableDiscounts as $offer): ?>
                                <div class="card hover:shadow-lg transition-shadow">
                                    <div class="card-header">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="badge badge-outline"><?php echo htmlspecialchars($offer['UniName']); ?></div>
                                            <div class="badge badge-success"><?php echo htmlspecialchars((string)$offer['Percentage']); ?>% OFF</div>
                                        </div>
                                        <div class="card-title"><?php echo htmlspecialchars($offer['Title']); ?></div>
                                        <div class="card-description"><?php echo htmlspecialchars($offer['Description']); ?></div>
                                    </div>
                                    <div class="card-content flex justify-between items-center">
                                        <span class="text-sm text-gray-500">
                                            Expires: <?php 
                                                // Your config has "ReturnDatesAsStrings" => true, so we just echo it
                                                echo htmlspecialchars($offer['Deadline']); 
                                            ?>
                                        </span>
                                        <button class="btn btn-primary btn-sm">Claim Discount</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="submission-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Submit Sport Participation</h3>
                <p class="modal-description">
                    Upload proof of your sports achievement or participation.
                </p>
            </div>
            
            <form id="submission-form" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_sport">
                
                <div class="form-group">
                    <label for="sport">Sport</label>
                    <input type="text" id="sport" name="sport" class="input" placeholder="e.g., Basketball" required>
                </div>
                
                <div class="form-group">
                    <label for="level">Level</label>
                    <select id="level" name="level" class="select" required>
                        <option value="">Select level</option>
                        <option value="Recreational">Recreational</option>
                        <option value="Club">Club</option>
                        <option value="Varsity">Varsity</option>
                        <option value="Professional">Professional</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="description">Description / Achievements</label>
                    <textarea id="description" name="description" class="textarea" placeholder="Describe your role and achievements..." rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="proof">Upload Proof (Optional)</label>
                    <input type="file" id="proof" name="proof" class="input" accept="image/*,.pdf">
                    <p class="text-sm text-gray-500 mt-1">
                        Upload certificates or photos.
                    </p>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancel-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit for Verification</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Tab Switching Logic
            const tabsTriggers = document.querySelectorAll('.tabs-trigger');
            const tabsContents = document.querySelectorAll('.tabs-content');
            
            tabsTriggers.forEach(trigger => {
                trigger.addEventListener('click', () => {
                    const tabId = trigger.getAttribute('data-tab');
                    
                    // Reset active states
                    tabsTriggers.forEach(t => t.classList.remove('active'));
                    tabsContents.forEach(c => c.classList.remove('active'));
                    
                    // Set new active state
                    trigger.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });

            // Modal Logic
            const modal = document.getElementById('submission-modal');
            const openModalBtn = document.getElementById('open-modal-btn');
            const cancelBtn = document.getElementById('cancel-btn');

            if(openModalBtn) {
                openModalBtn.addEventListener('click', () => {
                    modal.classList.add('active');
                });
            }

            if(cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    modal.classList.remove('active');
                });
            }

            // Close modal when clicking background
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
