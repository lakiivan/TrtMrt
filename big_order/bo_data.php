<?php

//UCITAVANJE PODATAKA KORISNIKA AKO SE KORISNIK UCITAO KROZ EDIT OPCIJU
if(isset($_GET['big_order_id'])) {
  $boid = $_GET['big_order_id'];
  $stmt = $pdo->prepare("SELECT * FROM big_orderi WHERE big_order_id = :xyz");
  $stmt->execute(array(":xyz" => $_GET['big_order_id']));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row === false){
    $_SESSION['error']  = "Bad value for big_order id = ".$_GET['big_order_id'];
    header('Location: big_orderi_view.php');
    return;
  } else {
    $bo_oznaka = htmlentities($row['oznaka']);
    $bo_datum_otvaranja = htmlentities($row['datum_otvaranja']);
    $bo_status = htmlentities($row['status']);
    $a_komentar = "'".htmlentities($row['komentar'])."'";
  }
}
