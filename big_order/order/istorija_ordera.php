<?php
require_once "../../util/pdo.php";
//dobijanje niza svih porucenih artikala iz trazene pordzbine
//obrnuto hronoloski grupisano po datumui
$stmt = $pdo -> prepare ("SELECT ao.artikl_order_id as artikl_order_id,
  k.ime as ime, ao.artikl_id aid,
  ao.kolicina as bop_kolicina, ao.datum_ordera as datum_ordera,
  a.part_number as part_number, a.opis as opis, a.cena_net as cena_net
  FROM artikl_orderi as ao
  INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
  INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
  WHERE ao.big_order_id = :boid AND ao.validno = 1
  ORDER BY datum_ordera DESC, ime ASC");
  $stmt -> execute(array(
    ':boid' => $_GET['big_order_id']
  ));
  $bo_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  //******************UTIL FUNCTIONS******************************

  function create_table_order($curr_date){
    echo '<h3 class="istorija_datum">'.$curr_date.'</h3>';
    echo '<table class="table">';
    echo '<tr>';
    echo '<th scope="col">No</th>';
    echo '<th scope="col">Art Ord id</th>';
    echo '<th scope="col">Kontakt</th>';
    echo '<th scope="col">Part Number</th>';
    echo '<th scope="col">Description</th>';
    echo '<th scope="col">Qty</th>';
    echo '<th scope="col">Net Price</th>';
    echo '<th scope="col">Ukupna Net Price</th>';
    echo '</tr>';
  }

  function fill_table_row($bo_order, $count) {
    echo '<tr>';
    echo '<td scope="row">'.$count.'</td>';
    echo '<td scope="row">'.$bo_order['artikl_order_id'].'</td>';
    echo '<td scope="row">'.$bo_order['ime'].'</td>';
    echo '<td scope="row">'.$bo_order['part_number'].'</td>';
    echo '<td scope="row">'.$bo_order['opis'].'</td>';
    echo '<td scope="row">'.$bo_order['bop_kolicina'].'</td>';
    echo '<td scope="row">'.$bo_order['cena_net'].'</td>';
    echo '<td scope="row">'.number_format(intval($bo_order['bop_kolicina']) * floatval($bo_order['cena_net']), 1).'</td>';
    echo '</tr>';
    //echo '<p>'.$bopa['artikl_order_id'].' - '.$bopa['part_number'].' - '.$bopa['opis'].' - '.$bopa['o_kolicina'].'</p>';
  }
  ?>

  <!DOCTYPE html>
  <html lang="en" dir="ltr">
  <head>
    <?php require_once "../../util/head.php" ?>
  </head>
  <body>
    <?php require_once "../../util/navbar.php" ?>
    <div class="container-fluid order">
      <div class="row-fluid">
        <div class="span12">
          <div class="row-fluid">
            <div class="span6">
              <h2 class="naslov">ISTORIJA ORDERA</h2>
            </div>
          </div>
          <div class="row-fluid">
            <div class="span3">
              <a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a>
            </div>
            <div class="span3" id="io_boi">
              <a href="#" onclick="location.href='http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>'"><h3>BIG ORDER INTERFACE</h3>
              </div>
            </div>
          </div>
        </div>
        <div class="row-fluid">
          <div class="span6">
            <?php
            //kreiranje tabela obrnuto hronoloski ordera
            if (count($bo_orderi) > 0) {
              $curr_date = $bo_orderi[0]['datum_ordera'];
              $count = 1;
              create_table_order($curr_date);
              foreach ($bo_orderi as $bo_order) {
                if(strcmp($curr_date, $bo_order['datum_ordera']) === 0) {
                  fill_table_row($bo_order, $count);
                  $count += 1;
                } else {
                  echo '</table>';
                  $curr_date = $bo_order['datum_ordera'];
                  create_table_order($curr_date);
                }
              }
              echo '</table>';
            }
            ?>
          </div>
        </div>
      </div>
      <script src="../../js/jquery-3.6.0.min.js"></script>
      <script src="../../bootstrap/js/bootstrap.min.js"></script>
    </body>
    </html>
