<?php
require_once "util/pdo.php";
require_once "big_order/bo_util/upit.php";

$upit = new Upit();

session_start();
// dobijanje svih big ordera
$stmt = $pdo -> prepare ("SELECT *
  FROM big_orderi
  ORDER BY datum_otvaranja DESC");
  $stmt -> execute();
  $big_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);

//****************** UTIL FUNCTIONS ***************************
function get_bo_last_status($pdo, $big_order_id) {
//funkcija vraca psolednji status za trzani big order
  $stmt = $pdo -> prepare ("SELECT status
    FROM bo_statusi
    WHERE big_order_id = $big_order_id
    ORDER BY status DESC");
    $stmt -> execute();
    $bo_statusi = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    return $bo_statusi[0]['status'];
}

  ?>

  <!DOCTYPE html>
  <html lang="en" dir="ltr">
  <head>
    <?php require_once "util/head.php" ?>
  </head>
  <body>
    <?php require_once "util/navbar.php" ?>
    <div class="container-fluid span12">
      <div class="row-fluid span12">
        <div class="borderasi span5" ><p>Test</p></div>
        <div class="borderasi span5"><p>Test</p></div>
      </div>
    </div>


    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
  </html>
