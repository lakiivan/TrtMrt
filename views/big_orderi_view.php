<?php
  require_once "../util/pdo.php";
  require_once "../big_order/bo_util/upit.php";
  session_start();

  $upit = new Upit();
  $big_orderi = $upit -> svi_big_orderi($pdo);
  // $bo_statusi = $upit -> get_bo_svi_statusi($pdo, $big_order_id);
  // slanje upita za dobijanje Kontakata iz baze
  $stmt = $pdo -> prepare ("SELECT *
    FROM big_orderi
    ORDER BY datum_otvaranja DESC");
  $stmt -> execute();
  $big_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php"?>
</head>
<body>
  <?php require_once "../util/navbar.php"?>
  <div class="container">
    <div class="row">
      <div class="span12">
        <button class="btn btn-large btn-xxl" big_order_id="btn_novkontakt" onclick="location.href='https://localhost/trt_mrt/big_order/big_order.php'">Nova Velika Porudžbina</button>
        <?php
          if (isset($_SESSION['success'])) {
            echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
            unset($_SESSION['success']);
          } else if(isset($_SESSION['error'])) {
            echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
            unset($_SESSION['error']);
          }
        ?>
      </div>
    </div>
    <div class="row">
      <div class="span12">
        <h2>Pregled Velikih porudžbina</h2>

        <input type="text" big_order_id="search_oznaka" title="Unesite ime"
        onkeyup="search_table('search_oznaka','tabela_big_orderi',2)" placeholder = "Pronađi big order po oznaci...">
        <input type="text" big_order_id="search_status" title="Unesite ime"
        onkeyup="search_table('search_status','tabela_big_orderi',3)" placeholder = "Pronađi big order po statusu...">

        <table class="table table-striped" big_order_id="tabela_big_orderi">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col" class="hide">big_order_id</th>
              <th scope="col">Oznaka</th>
              <th scope="col">Status</th>
              <th scope="col">datum otvaranja</th>
              <th scope="col">datum poručivanja</th>
              <th scope="col">datum pregleda</th>
              <th scope="col">datum zatvaranja</th>
              <th scope="col">Komentar</th>
              <th scope="col">Edit</th>
            </tr>
          </thead>

          <tbody>
            <?php
            //ucitavanje pojedinacnih podataka i njihovo upisivanje u tabelu
            if(count($big_orderi) > 0) {
              $count = 0;
              foreach ($big_orderi as $big_order) {
                $big_order_id = $big_order['big_order_id'];
                $bo_statusi = $upit -> get_bo_svi_statusi($pdo, $big_order_id);
                $status = $upit -> get_bo_last_status($pdo, $big_order_id);
                $status = htmlentities($status);

                $oznaka             = htmlentities($big_order['oznaka']);
                $datum_otvaranja    = htmlentities($upit -> find_datum_statusa($bo_statusi, 'otvoreno'));
                $datum_porucivanja  = htmlentities($upit -> find_datum_statusa($bo_statusi, 'poruceno'));
                $datum_pregleda     = htmlentities($upit -> find_datum_statusa($bo_statusi, 'pregledano'));
                $datum_zatvaranja   = htmlentities($upit -> find_datum_statusa($bo_statusi, 'zatvoreno'));
                $komentar           = "'".htmlentities($big_order['komentar'])."'";
                $count++;
                ?>
                <tr>
                  <td scope="row"><?=$count?></td>
									<td class="hide"><?=$big_order_id?></td>
                  <td><?=$oznaka?></td>
                  <td><?=$status?></td>
                  <td><?=$datum_otvaranja?></td>
                  <td><?=$datum_porucivanja?></td>
                  <td><?=$datum_pregleda?></td>
                  <td><?=$datum_zatvaranja?></td>
                  <td><?=$komentar?></td>
                  <td><a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$big_order_id?>">Edit</a></td>
                </tr>
                <?php
              }
            }
            ?>

          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="../js/search.js"></script>
</body>
</html>
