<?php
// Path: frontend/dashboards/university_dashboard/uni_dashboard.php

// ==========================================
// 1. CONFIG & SESSION
// ==========================================
session_start();
// Path Correction: Up 3 levels to backend
require_once '../../../backend/config.php'; 

// Security Check
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../landing_page/landing_page.html');
    exit();
}

$user_id = $_SESSION['user_id'];

// ==========================================
// 2. API HANDLER (AJAX Requests)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];

    try {
        // --- ACTION A: Update Student Status ---
        if ($action === 'update_status') {
            $app_id = $_POST['app_id'];
            $status = $_POST['status'];
            
            // Use q() helper from config
            q("UPDATE [Application] SET Status = ? WHERE App_ID = ? AND Uni_ID = ?", [$status, $app_id, $user_id]);
            
            echo json_encode(['success' => true]);
            exit;
        }

        // --- ACTION B: Create New Offer ---
        if ($action === 'create_offer') {
            $title      = $_POST['title'];
            $percentage = $_POST['percentage'];
            $criteria   = $_POST['criteria'];
            $desc       = $_POST['description'];
            $deadline   = $_POST['deadline'];

            // Use q() helper from config
            q("INSERT INTO [Scholarship] (Uni_ID, Title, Percentage, Eligibility_Criteria, Description, Deadline, Active) 
               VALUES (?, ?, ?, ?, ?, ?, 1)", 
               [$user_id, $title, $percentage, $criteria, $desc, $deadline]);
            
            echo json_encode(['success' => true]);
            exit;
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// ==========================================
// 3. FETCH DATA FOR DASHBOARD
// ==========================================

// A. Get University Name
$row = q_row("SELECT Name FROM [University_] WHERE User_ID = ?", [$user_id]);
$uniName = $row['Name'] ?? "University Dashboard";

// B. Get Stats (Using q_row for counts)
$pendingCount = q_row("SELECT COUNT(*) as c FROM [Application] WHERE Uni_ID = ? AND Status = 'Pending'", [$user_id])['c'];
$approvedCount = q_row("SELECT COUNT(*) as c FROM [Application] WHERE Uni_ID = ? AND Status = 'Approved'", [$user_id])['c'];
$activeOffersCount = q_row("SELECT COUNT(*) as c FROM [Scholarship] WHERE Uni_ID = ? AND Active = 1", [$user_id])['c'];
// Total students in the whole system
$totalStudents = q_row("SELECT COUNT(*) as c FROM [Student]")['c'];

// C. Get Submissions
$sql_subs = "SELECT a.App_ID, a.Status, a.Submission_Date, s.Name, s.Bio, s.GPA, sp.Name as SportName
             FROM [Application] a
             JOIN [Student] s ON a.Student_ID = s.User_ID
             JOIN [Sports] sp ON s.Primary_Sport_ID = sp.Sport_ID
             WHERE a.Uni_ID = ? ORDER BY a.Submission_Date DESC";
$stmt_subs = q($sql_subs, [$user_id]);

$submissions = [];
while ($r = sqlsrv_fetch_array($stmt_subs, SQLSRV_FETCH_ASSOC)) {
    $submissions[] = $r;
}

// D. Get Offers
$stmt_off = q("SELECT * FROM [Scholarship] WHERE Uni_ID = ? ORDER BY Scholarship_ID DESC", [$user_id]);

$offers = [];
while ($r = sqlsrv_fetch_array($stmt_off, SQLSRV_FETCH_ASSOC)) {
    $offers[] = $r;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>University Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="uni_dashboard.css">
</head>

<body>
  <div class="container">
    <header>
      <h1 id="universityName"><?php echo htmlspecialchars($uniName); ?></h1>
      <p class="subtitle">Review student submissions and manage your discount programs</p>
    </header>

    <div class="status-cards">
      <div class="status-card"><h2>Pending Reviews</h2><p><?php echo $pendingCount; ?></p></div>
      <div class="status-card"><h2>Approved</h2><p><?php echo $approvedCount; ?></p></div>
      <div class="status-card"><h2>Active Offers</h2><p><?php echo $activeOffersCount; ?></p></div>
      <div class="status-card"><h2>Total Students</h2><p><?php echo $totalStudents; ?></p></div>
    </div>

    <div class="tabs">
      <div class="tab active" data-tab="submissions">Student Submissions</div>
      <div class="tab" data-tab="offers">Discount Offers</div>
      <div class="tab" data-tab="create">Create New Offer</div>
    </div>

    <div id="submissions" class="tab-content active">
      <div class="tab-header">
        <h2>Student Submissions</h2>
        <?php if($pendingCount > 0): ?>
            <span class="status-badge status-pending"><?php echo $pendingCount; ?> Pending Review</span>
        <?php endif; ?>
      </div>
      <table id="submissionsTable">
        <thead>
          <tr>
            <th>Student</th><th>Sport</th><th>GPA</th><th>Date</th><th>Status</th><th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($submissions as $sub): ?>
          <tr>
            <td><?php echo htmlspecialchars($sub['Name']); ?></td>
            <td><?php echo htmlspecialchars($sub['SportName']); ?></td>
            <td><?php echo htmlspecialchars($sub['GPA']); ?></td>
            <td>
                <?php 
                    // SQL Server returns DateTime object
                    if(isset($sub['Submission_Date']) && is_object($sub['Submission_Date'])){
                        echo $sub['Submission_Date']->format('Y-m-d');
                    } else {
                        echo htmlspecialchars($sub['Submission_Date']);
                    }
                ?>
            </td>
            <td><span class="status-badge status-<?php echo strtolower($sub['Status']); ?>"><?php echo $sub['Status']; ?></span></td>
            <td>
                <?php if($sub['Status'] == 'Pending'): ?>
                    <button class="action-btn btn-review" 
                        onclick="openReviewModal(
                            '<?php echo $sub['App_ID']; ?>',
                            '<?php echo htmlspecialchars($sub['Name']); ?>',
                            '<?php echo htmlspecialchars($sub['SportName']); ?>',
                            '<?php echo htmlspecialchars($sub['Bio'] ?? 'No bio'); ?>'
                        )">Review</button>
                <?php else: ?>
                    <button class="action-btn btn-secondary" disabled>View</button>
                <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if(empty($submissions)) echo "<tr><td colspan='6'>No submissions found.</td></tr>"; ?>
        </tbody>
      </table>
    </div>

    <div id="offers" class="tab-content">
      <div class="tab-header"><h2>Discount Offers</h2></div>
      <div id="offersContainer">
        <?php foreach($offers as $offer): ?>
        <div class="offer-card">
            <div class="offer-header">
                <div class="offer-title"><?php echo htmlspecialchars($offer['Title'] ?? 'Offer'); ?></div>
                <div class="offer-discount"><?php echo floatval($offer['Percentage']); ?>%</div>
            </div>
            <div class="offer-description"><?php echo htmlspecialchars($offer['Description']); ?></div>
            <div class="offer-details">
                <div>Expires: 
                    <?php 
                        if(isset($offer['Deadline']) && is_object($offer['Deadline'])){
                            echo $offer['Deadline']->format('Y-m-d');
                        } else {
                            echo htmlspecialchars($offer['Deadline']);
                        }
                    ?>
                </div>
                <div>Criteria: <?php echo substr($offer['Eligibility_Criteria'], 0, 25).'...'; ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if(empty($offers)) echo "<p>No offers created yet.</p>"; ?>
      </div>
    </div>

    <div id="create" class="tab-content">
      <h2>Create New Scholarship</h2>
      <form id="offerForm">
        <div class="form-group"><label>Offer Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="form-group"><label>Percentage (%)</label><input type="number" name="percentage" class="form-control" required></div>
        <div class="form-group"><label>Criteria</label><textarea name="criteria" class="form-control" rows="3" required></textarea></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="3" required></textarea></div>
        <div class="form-group"><label>Deadline</label><input type="date" name="deadline" class="form-control" required></div>
        <button type="submit" class="btn-primary">Create Scholarship</button>
      </form>
    </div>
  </div>

  <div id="reviewModal" class="modal">
    <div class="modal-content">
      <span class="close-modal" onclick="closeReviewModal()">&times;</span>
      <h3>Review Student</h3>
      <p><strong>Student:</strong> <span id="mName"></span></p>
      <p><strong>Sport:</strong> <span id="mSport"></span></p>
      <p><strong>Bio:</strong> <span id="mBio"></span></p>
      <input type="hidden" id="mAppID">
      <div class="modal-actions">
        <button class="action-btn btn-success" onclick="updateStatus('Approved')">Approve</button>
        <button class="action-btn btn-danger" onclick="updateStatus('Rejected')">Reject</button>
      </div>
    </div>
  </div>

  <script>
    // --- TABS ---
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

    // --- MODAL ---
    const modal = document.getElementById('reviewModal');
    function openReviewModal(id, name, sport, bio) {
        document.getElementById('mAppID').value = id;
        document.getElementById('mName').textContent = name;
        document.getElementById('mSport').textContent = sport;
        document.getElementById('mBio').textContent = bio;
        modal.style.display = 'flex';
    }
    function closeReviewModal() { modal.style.display = 'none'; }

    // --- AJAX: Update Status ---
    function updateStatus(status) {
        const id = document.getElementById('mAppID').value;
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('app_id', id);
        formData.append('status', status);

        // Fetch to SAME PAGE (empty string)
        fetch('', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) { location.reload(); }
            else { alert("Error: " + data.message); }
        });
    }

    // --- AJAX: Create Offer ---
    document.getElementById('offerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'create_offer');

        fetch('', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) { alert("Offer Created!"); location.reload(); }
            else { alert("Error: " + data.message); }
        });
    });
  </script>
</body>
</html>
