<?php
session_start();
require_once __DIR__ . '/config.php';

// ensure the user is logged in , is  student
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'Student') {
    http_response_code(403);
    exit('Access denied');
}

$userId = (int)$_SESSION['user_id'];

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// sanitize and validate inputs
$sportId        = (int)($_POST['sport_id'] ?? 0);
$achievements   = trim($_POST['achievements'] ?? '');
$years          = (int)($_POST['years'] ?? 0);
$tournamentsWon = (int)($_POST['tournaments_won'] ?? 0);
$tournamentDesc = trim($_POST['tournament_desc'] ?? '');

if ($sportId <= 0 || $achievements === '' || $years < 0 || $tournamentsWon < 0) {
    http_response_code(400);
    exit('Invalid input');
}

//prevent duplicate (Std_ID, Sport_ID) row
$existing = q_row(
    'SELECT 1 FROM [dbo].[Sports_Student] WHERE [Std_ID] = ? AND [Sport_ID] = ?',
    [$userId, $sportId]
);
if ($existing) {
    http_response_code(409);
    exit('You already submitted this sport');
}


// Insert the sport submission into the database
q(
    'INSERT INTO [dbo].[Sports_Student]
        ([Std_ID], [Sport_ID], [Number_of_Tournaments_Won], [Tournaments_Description], [Achievements], [Years_of_Experience])
     VALUES (?, ?, ?, ?, ?, ?)',
    [$userId, $sportId, $tournamentsWon, $tournamentDesc, $achievements, $years]
);

http_response_code(200);
echo 'Sport submission successful';
?>
