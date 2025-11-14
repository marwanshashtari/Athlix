<?php
session_start();
require_once __DIR__ . '/config.php';

// Only universities can see the list of students
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'University') {
    http_response_code(403);
    exit('Access denied');
}


$stmt = q(
    'SELECT S.[User_ID],
            S.[Name],
            S.[City],
            S.[GPA],
            S.[Status],
            S.[Primary_Sport_ID],
            Sp.[Name] AS [PrimarySportName]
     FROM [dbo].[Student] S
     LEFT JOIN [dbo].[Sports] Sp
       ON S.[Primary_Sport_ID] = Sp.[Sport_ID]
     WHERE S.[Status] = 0' // 0 = Available, 1 = Unavailable
);

$students = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $students[] = $row;
}

header('Content-Type: application/json; charset=utf-8');  // Tell the browser this response is JSON
echo json_encode($students); // Convert the PHP array to JSON and output it
exit;
