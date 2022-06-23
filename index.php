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
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span1"><p></p></div>
        <div class="span10 sredina">
          <div class="jumbotron jumbotron-fluid">
            <h1 class="display-4" id="naslov">Trt Mrt - Oprema za Ritmičku Gimnastiku</h1>
            <img class="rounded_img"src="images/linoya1600.jpg" alt="Linoy Ashram" >
          </div>
          <div id='novi_big_order'><h2 class="text_centered"><a href='big_order/big_order_form.php'>KREIRAJ NOVU VELIKU PORUDŽBINU</a></h2>
          </div>
          <div class="row-fluid">
            <div class="span12 sredina">
              <div class="row-fluid">
                <?php
                $lista_zatvorenih_bo = array();

                echo '<div class="otvoreni_bo row-fluid">';
                echo '<div class="span12">';
                echo '<h3>OTVORENE PORUDZBINE</h3>';
                foreach ($big_orderi as $bo) {
                  $big_order_id = $bo['big_order_id'];
                  $bo_stat = $upit -> get_big_order_stat_data($pdo, $big_order_id);
                  if ($bo_stat === 0) {
                    $bos_ukupna_kolicina = 0;
                    $bos_ukupna_net = 0;
                    $bos_ukupna_prodajna = 0;
                    $bos_ukupna_zarada = 0;
                  } else {
                    $bos_ukupna_kolicina = htmlentities($bo_stat['sum_kolicina']);
                    $bos_ukupna_net      = number_format(htmlentities($bo_stat['sum_ukupna_net']), 1);
                    $bos_ukupna_prodajna = number_format(htmlentities($bo_stat['sum_ukupna_list']), 1);
                    $bos_ukupna_zarada   =  number_format(htmlentities($bo_stat['sum_zarada']), 1);
                  }
                  $bo_status = get_bo_last_status($pdo, $big_order_id);
                  if (strcmp($bo_status, 'zatvoreno') !== 0){
                    echo'<div class="span3 bo_small">';
                    echo '<a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id='.$big_order_id.'"><h3>'.$bo["oznaka"].' - '.$bo_status.'</h3></a>';
                    echo '<table>';
                    echo '<tr><td class="bo_small_data bolder">Ukupno Net</td><td class="bolder">'.$bos_ukupna_net.'</td></tr>';
                    echo '<tr><td class="bo_small_data">Ukupno List</td><td>'.$bos_ukupna_prodajna.'</td></tr>';
                    echo '<tr><td class="bo_small_data">Ukupno Artikala</td><td>'.$bos_ukupna_kolicina.'</td></tr>';
                    echo '<tr><td class="bo_small_data bolder">ZARADA</td><td class="bolder">'.$bos_ukupna_zarada.'</td></tr>';
                    echo '</table>';
                    echo '</div>';
                  } else {
                    array_push($lista_zatvorenih_bo, $bo);
                  }
                }
                echo '</div>';
                echo '</div>';
                echo '<hr>';
                echo '<div class="zatvoreni_bo row-fluid">';
                echo '<div class="span12">';
                echo '<h3>ZATVORENE PORUDZBINE</h3>';
                foreach ($lista_zatvorenih_bo as $zatvoren_bo) {
                  $big_order_id = $zatvoren_bo['big_order_id'];
                  $bo_stat = $upit -> get_big_order_stat_data($pdo, $big_order_id);
                  $bos_ukupna_kolicina = htmlentities($bo_stat['sum_kolicina']);
                  $bos_ukupna_net      = number_format(htmlentities($bo_stat['sum_ukupna_net']), 1);
                  $bos_ukupna_prodajna = number_format(htmlentities($bo_stat['sum_ukupna_list']), 1);
                  $bos_ukupna_zarada   =  number_format(htmlentities($bo_stat['sum_zarada']), 1);
                  echo'<div class="span3 bo_small">';
                  echo '<a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id='.$big_order_id.'"><h3>'.$zatvoren_bo["oznaka"].' - '.$bo_status.'</h3></a>';
                  echo '<table>';
                  echo '<tr><td class="bo_small_data bolder">Ukupno Net</td><td class="bolder">'.$bos_ukupna_net.'</td></tr>';
                  echo '<tr><td class="bo_small_data">Ukupno List</td><td>'.$bos_ukupna_prodajna.'</td></tr>';
                  echo '<tr><td class="bo_small_data">Ukupno Artikala</td><td>'.$bos_ukupna_kolicina.'</td></tr>';
                  echo '<tr><td class="bo_small_data bolder">ZARADA</td><td class="bolder">'.$bos_ukupna_zarada.'</td></tr>';
                  echo '</table>';
                  echo '</div>';

                }
                echo '</div>';
                echo '</div>';

                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div clas="span1"><p></p></div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>
  </html>
