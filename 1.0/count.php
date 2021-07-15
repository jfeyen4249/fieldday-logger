<?php

include('config.php');

 
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
  foreach ($allowedOrigins as $allowedOrigin) {
    if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
      header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
      header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
      header('Access-Control-Max-Age: 1000');
      header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
      break;
    }
  }
}


if ($db->connect_errno) {
    printf("Failed to connect to database");
    exit();
}
$result = $db->query("SELECT \"logs\" AS table_name, COUNT(*) AS exact_row_count FROM joefeyen_grarc_fd.logs UNION\n" . "SELECT band, count(*) as occurrences FROM logs GROUP by band union\n" . "SELECT mode, count(*) as occurrences FROM logs GROUP by mode");
$dbdata = array();
while ( $row = $result->fetch_assoc())  {
    $dbdata[]=$row;
}
echo json_encode($dbdata);

?>