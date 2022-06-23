<?php
require_once "../util/pdo.php";
header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->prepare('SELECT kontakt_id, ime
  FROM kontakti
  WHERE ime
  LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['ime'].'-'.$row['kontakt_id'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
