<?php
require_once "../../util/pdo.php";
header('Access-Control-Allow-Origin: https://localhost');
header('Content-Type: application/json; charset=utf-8');

$stmt = $pdo->prepare('SELECT artikl_id, part_number, opis, link, cena_net, cena_list FROM artikli WHERE part_number LIKE :prefix');
$stmt->execute(array( ':prefix' => $_REQUEST['term']."%"));
$retval = array();
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
  $retval[] = $row['part_number'].'-'.$row['artikl_id'].'-'.$row['opis'];
}

echo(json_encode($retval, JSON_PRETTY_PRINT));
