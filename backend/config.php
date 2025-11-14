<?php

session_start();

$server = 'localhost\\SQLEXPRESS';
$db     = 'AthlixDB';
$user   = getenv('DB_USER');  //Use environment variables for security
$pass   = getenv('DB_PASS');

$info = 
[
  'Database'              => $db,
  'UID'                   => $user,
  'PWD'                   => $pass,
  'CharacterSet'          => 'UTF-8',
  'TrustServerCertificate'=> true,
];

$conn = sqlsrv_connect($server, $info);

if (!$conn) {
  die('DB connection failed: ' . print_r(sqlsrv_errors(), true));
}

//tiny helper for queries (prepared automatically when you pass params)
function q(string $sql, array $params = []) {
  global $conn;
  $stmt = sqlsrv_query($conn, $sql, $params);
  if ($stmt === false) {
    die('Query error: ' . print_r(sqlsrv_errors(), true));
  }
  return $stmt;
}


//helper t read one row
function q_row(string $sql, array $params = []) {
  $stmt = q($sql, $params);
  return sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) ?: null;
}



?>
