<?php



$server = 'localhost\\SQLEXPRESS';

$info = [
    'Database'              => 'AthlixDB',
    'UID'                   => 'athlix',      
    'PWD'                   => 'manager123',
    'CharacterSet'          => 'UTF-8',
    'TrustServerCertificate'=> true,
    'ReturnDatesAsStrings'  => true,
];

$conn = sqlsrv_connect($server, $info);

if (!$conn) {
    die('DB connection failed: ' . print_r(sqlsrv_errors(), true));
}

/* for testing connection
else {
    echo "Connected to database successfully.";
}
*/

function q(string $sql, array $params = []) {
    global $conn;
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt === false) {
        die('Query error: ' . print_r(sqlsrv_errors(), true));
    }
    return $stmt;
}

function q_row(string $sql, array $params = []) {
    $stmt = q($sql, $params);
    return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
}

?>
