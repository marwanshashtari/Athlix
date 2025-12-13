<?php
// Path: frontend/dashboards/university_dashboard/uni_dashboard.php

// ==========================================
// 1. CONFIG & SESSION
// ==========================================
session_start();
require_once '../../../backend/config.php'; 

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'university') {
    header('Location: ../../landing_page/landing_page.php');
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
        // --- ACTION: Create New Offer ---
        if ($action === 'create_offer') {
            $title      = $_POST['title'];
            $percentage = $_POST['percentage'];
            $criteria   = $_POST['criteria'];
            $desc       = $_POST['description'];
            $deadline   = $_POST['deadline'];
            $sportName  = $_POST['sport_name'];

            $sportRow = q_row(
            "SELECT Sport_ID FROM [Sports] WHERE Name = ?", 
            [$sportName]);
            if (!$sportRow) {
                throw new Exception('Selected sport does not exist.');
            }
            $sportId = $sportRow['Sport_ID'];
            q("INSERT INTO [Scholarship] (Uni_ID, Title, Percentage, Eligibility_Criteria, Description, Deadline, Active) 
               VALUES (?, ?, ?, ?, ?, ?, 1)", 
               [$user_id, $title, $percentage, $criteria, $desc, $deadline]);

            $scholarshipID = q_row("SELECT SCOPE_IDENTITY() AS id")['id'];

            q("INSERT INTO [Scholarship_Sport] (Scholarship_ID, Sport_ID)
               VALUES (?, ?)",
               [$scholarshipID, $sportId]);
            echo json_encode(['success' => true]);
            exit;
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// ==========================================
// 3. FETCH DATA
// ==========================================

// A. Get University Name
$row = q_row("SELECT Name FROM [University_] WHERE User_ID = ?", [$user_id]);
$uniName = $row['Name'] ?? "University Dashboard";

// B. Get Statistics (General counts only)
$activeOffersCount = q_row("SELECT COUNT(*) as c FROM [Scholarship] WHERE Uni_ID = ? AND Active = 1", [$user_id])['c'];
$totalStudents = q_row("SELECT COUNT(*) as c FROM [Student]")['c'];

// C. Get All Student Athletes (Directory)
$sql_students = "SELECT s.Name, s.GPA, s.City, s.Phone_Number,
                        sp.Name AS SportName, 
                        ss.Years_of_Experience,
                        ss.Number_of_Tournaments_Won,
                        ss.Achievements
                 FROM [Student] s
                 JOIN [Sports_Student] ss ON s.User_ID = ss.Std_ID
                 JOIN [Sports] sp ON ss.Sport_ID = sp.Sport_ID
                 WHERE s.Status = 0
                 ORDER BY 
                        ss.Number_of_Tournaments_Won DESC,
                        ss.Years_of_Experience DESC";
                 
$stmt_stds = q($sql_students);

$studentsList = [];
while ($r = sqlsrv_fetch_array($stmt_stds, SQLSRV_FETCH_ASSOC)) {
    $studentsList[] = $r;
}

// D. Get My Active Offers
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
  <style>
      /* Search Bar Styling */
      .search-box {
          width: 100%;
          padding: 12px;
          margin-bottom: 20px;
          border: 1px solid #ccc;
          border-radius: 8px;
          font-size: 16px;
          background-color: #fff;
      }
  </style>
</head>

<body>
  <div class="container">
    <header>
      <h1 id="universityName"><?php echo htmlspecialchars($uniName); ?></h1>
      <p class="subtitle">Scout athletes and manage scholarship offers</p>
    </header>

    <div class="status-cards">
      <div class="status-card">
        <h2>Active Scholarships</h2>
        <p style="color: #8b5cf6;"><?php echo $activeOffersCount; ?></p>
      </div>
      <div class="status-card">
        <h2>Total Athletes on Platform</h2>
        <p style="color: #10b981;"><?php echo $totalStudents; ?></p>
      </div>
    </div>

    <div class="tabs">
      <div class="tab active" data-tab="directory">Student Directory</div>
      <div class="tab" data-tab="offers">My Scholarships</div>
      <div class="tab" data-tab="create">Create New Scholarship</div>
    </div>

    <div id="directory" class="tab-content active">
      <div class="tab-header">
        <h2>Find Athletes</h2>
      </div>
      
      <input type="text" id="studentSearch" class="search-box" placeholder="Search by Sport" onkeyup="filterDirectory()">

      <table id="directoryTable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Sport</th>
            <th>Experience</th>
            <th>GPA</th>
            <th>City</th>
            <th>Bio</th>
          </tr>
        </thead>
        <tbody>
          <?php if(empty($studentsList)): ?>
            <tr><td colspan='6' style="text-align:center;">No students found in the database.</td></tr>
          <?php else: ?>
              <?php foreach($studentsList as $std): ?>
              <tr>
                <td class="name-col"><strong><?php echo htmlspecialchars($std['Name']); ?></strong></td>
                <td class="sport-col"><?php echo htmlspecialchars($std['SportName']); ?></td>
                <td><?php echo htmlspecialchars($std['Years_of_Experience']); ?> Years</td>
                <td><?php echo htmlspecialchars($std['GPA']); ?></td>
                <td class="city-col"><?php echo htmlspecialchars($std['City']); ?></td>
                <td>
                    <button class="action-btn btn-review" 
                        onclick="openViewModal(
                            '<?php echo htmlspecialchars($std['Name']); ?>',
                            '<?php echo htmlspecialchars($std['Phone_Number']); ?>',
                            '<?php echo htmlspecialchars($std['SportName']); ?>',
                            '<?php echo htmlspecialchars($std['Number_of_Tournaments_Won']); ?>',
                            '<?php echo htmlspecialchars($std['Achievements'] ?? 'No details provided'); ?>'
                        )">View Bio</button>
                </td>
              </tr>
              <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div id="offers" class="tab-content">
      <div class="tab-header"><h2>My Active Scholarships</h2></div>
      <div id="offersContainer">
        <?php if(empty($offers)): ?>
            <p style="text-align:center; color:#666;">No scholarships created yet.</p>
        <?php else: ?>
            <?php foreach($offers as $offer): ?>
            <div class="offer-card">
                <div class="offer-header">
                    <div class="offer-title"><?php echo htmlspecialchars($offer['Title'] ?? 'Scholarship Offer'); ?></div>
                    <div class="offer-discount"><?php echo floatval($offer['Percentage']); ?>% OFF</div>
                </div>
                <div class="offer-description"><?php echo htmlspecialchars($offer['Description']); ?></div>
                <div class="offer-details">
                    <div class="offer-detail-item">
                        <div class="detail-label">Expires</div>
                        <div class="detail-value">
                        <?php 
                            if(isset($offer['Deadline']) && is_object($offer['Deadline'])){
                                echo $offer['Deadline']->format('Y-m-d');
                            } else {
                                echo htmlspecialchars($offer['Deadline']);
                            }
                        ?>
                        </div>
                    </div>
                    <div class="offer-detail-item">
                        <div class="detail-label">Criteria</div>
                        <div class="detail-value"><?php echo substr($offer['Eligibility_Criteria'], 0, 40).'...'; ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <div id="create" class="tab-content">
      <h2>Create New Scholarship</h2>
      <form id="offerForm">
        <div class="form-group">
          <label class="form-label">Sport</label>
          <div class="select-wrapper">
            <select id="sport_name" name="sport_name" class="select" required>
              <option value="" disabled >Select a Sport</option>
              <option value="Football">Football</option>
              <option value="Volleyball">Volleyball</option>
              <option value="Tennis">Tennis</option>
              <option value="Basketball">Basketball</option>
              <option value="Handball">Handball</option>
              <option value="Table Tennis">Table Tennis</option>
              <option value="Karate">Karate</option>
              <option value="Jiu Jitsu">Jiu Jitsu</option>
              <option value="Taekwondo">Taekwondo</option>
              <option value="Badminton">Badminton</option>
            </select>
          </div>
        </div>
        <div class="form-group">
            <label class="form-label">Scholarship Title</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Athletic Excellence Grant" required>
        </div>
        <div class="form-group">
            <label class="form-label">Discount Percentage (%)</label>
            <input type="number" name="percentage" class="form-control" placeholder="25" min="1" max="100" required>
        </div>
        <div class="form-group">
            <label class="form-label">Eligibility Criteria</label>
            <textarea name="criteria" class="form-control" rows="3" placeholder="e.g. Must have GPA > 80.0" required></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Details about this offer..." required></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Application Deadline</label>
            <input type="date" name="deadline" class="form-control" required>
        </div>
        <button type="submit" class="btn-primary">Create Scholarship</button>
      </form>
    </div>
  </div>

  <div id="reviewModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Student Profile</h3>
        <span class="close-modal" onclick="closeReviewModal()">&times;</span>
      </div>
      <!-- <div class="modal-body">
        <div class="info-row">
            <div class="info-label">Name:</div>
            <div class="info-value" id="mName" style="font-weight:bold;"></div>
        </div>
        <div class="info-row">
            <div class="info-label">Sport:</div>
            <div class="info-value" id="mSport"></div>
        </div>
        <div class="info-row" style="margin-top:10px;">
            <div class="info-label">Achievements/Bio:</div>
            <div class="info-value" id="mBio" style="background:#f9f9f9; padding:10px; border-radius:5px;"></div>
        </div>
      </div> -->
      <div class="modal-body">
  <div class="student-info">
    <div class="info-row">
      <div class="info-label">Name</div>
      <div class="info-value" id="mName"></div>
    </div>

    <div class="info-row">
      <div class="info-label">Phone Number</div>
      <div class="info-value" id="mPhone"></div>
    </div>

    <div class="info-row">
      <div class="info-label">Sport</div>
      <div class="info-value" id="mSport"></div>
    </div>
    
    <div class="info-row">
      <div class="info-label">Tournaments Won</div>
      <div class="info-value" id="mTournament"></div>
    </div>

    <div class="info-row full">
      <div class="info-label">Achievements / Bio</div>
      <div class="info-value bio-box" id="mBio"></div>
    </div>
  </div>
</div>

      <!-- <div class="modal-actions">
        <button class="action-btn btn-secondary" onclick="closeReviewModal()">Close</button>
      </div> -->
    </div>
  </div>

  <script>
    // --- TABS LOGIC ---
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            tab.classList.add('active');
            const targetId = tab.getAttribute('data-tab');
            document.getElementById(targetId).classList.add('active');
        });
    });

    // --- SEARCH LOGIC ---
    function filterDirectory() {
        const input = document.getElementById("studentSearch");
        const filter = input.value.toUpperCase();
        const table = document.getElementById("directoryTable");
        const tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) { // Start loop at 1 to skip header
            let tdName = tr[i].getElementsByClassName("name-col")[0];
            let tdSport = tr[i].getElementsByClassName("sport-col")[0];
            let tdCity = tr[i].getElementsByClassName("city-col")[0];
            
            if (tdName || tdSport || tdCity) {
                let txtName = tdName ? (tdName.textContent || tdName.innerText) : "";
                let txtSport = tdSport ? (tdSport.textContent || tdSport.innerText) : "";
                let txtCity = tdCity ? (tdCity.textContent || tdCity.innerText) : "";
                
                if (txtName.toUpperCase().indexOf(filter) > -1 || 
                    txtSport.toUpperCase().indexOf(filter) > -1 || 
                    txtCity.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }

    // // --- MODAL LOGIC ---
    
    const modal = document.getElementById('reviewModal');

  function openViewModal(name, pn, sport, tournaments, bio) {
    document.getElementById('mName').textContent = name;
    document.getElementById('mPhone').textContent = pn;
    document.getElementById('mSport').textContent = sport;
    document.getElementById('mTournament').textContent = tournaments;
    document.getElementById('mBio').textContent = bio;
    modal.classList.add('active');
  }

  function closeReviewModal() {
    modal.classList.remove('active');
  }

  /* Close when clicking outside */
  modal.addEventListener('click', function (e) {
    if (e.target === modal) {
      closeReviewModal();
    }
  });


    // --- AJAX: Create Offer ---
    document.getElementById('offerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'create_offer');

        fetch('', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) { 
                alert("Offer Created Successfully!"); 
                location.reload(); 
            } else { 
                alert("Error: " + data.message); 
            }
        });
    });
  </script>
</body>
</html>