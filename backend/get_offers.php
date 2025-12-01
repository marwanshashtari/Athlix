<?php
session_start();
require_once __DIR__ . '/config.php';


// check logged in
 if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Login required');
}

// Get all active scholarships with their university info
$stmt = q(
    'SELECT S.[Scholarship_ID],
            S.[Percentage],
            S.[Deadline],
            S.[Eligibility_Criteria],
            S.[Description],
            U.[User_ID]       AS [Uni_ID],
            U.[Name]          AS [UniversityName],
            U.[Location],
            U.[Website_URL]
     FROM [dbo].[Scholarship] S
     JOIN [dbo].[University_] U
       ON S.[Uni_ID] = U.[User_ID]
     WHERE S.[Active] = 1'
);

$offers = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Convert Deadline (DateTime) to string if not null
    if (!empty($row['Deadline']) && $row['Deadline'] instanceof DateTimeInterface) {
        $row['Deadline'] = $row['Deadline']->format('Y-m-d');
    }

    $offers[] = $row;
}

header('Content-Type: application/json; charset=utf-8');  // Tell the browser this response is JSON
echo json_encode($offers); // Convert the PHP array to JSON and output it
exit;
