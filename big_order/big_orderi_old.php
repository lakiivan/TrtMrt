<?php
  require_once "../util/pdo.php";
  session_start();
  // slanje upita za dobijanje Kontakata iz baze
  $stmt = $pdo -> prepare ("SELECT *
    FROM big_orderi
    ORDER BY datum_otvaranja DESC");
  $stmt -> execute();
  $big_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);

  function get_bo_statusi ($pdo, $big_order_id) {
    //funvkija vraca niz svih statusa vezanih za trazeni big order obrnuto hronoloski
    $stmt2 = $pdo -> prepare ("SELECT *
      FROM bo_statusi
      WHERE big_order_id = $big_order_id
      ORDER BY id DESC
      ");
      $stmt2 -> execute();
      $bo_statusi = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
      return $bo_statusi;
  }

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
        <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='https://localhost/trt_mrt/big_order/big_order.php'">Nova Velika Porudžbina</button>
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

        <input type="text" id="search_oznaka" title="Unesite ime"
        onkeyup="search_table('search_oznaka','tabela_big_orderi',2)" placeholder = "Pronađi big order po oznaci...">
        <input type="text" id="search_status" title="Unesite ime"
        onkeyup="search_table('search_status','tabela_big_orderi',3)" placeholder = "Pronađi big order po statusu...">

        <table class="table table-striped" id="tabela_big_orderi">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col" class="hide">id</th>
              <th scope="col">Oznaka</th>
              <th scope="col">Status</th>
              <th scope="col">datum otvaranja</th>
              <th scope="col">datum poručivanja</th>
              <th scope="col">datum zatvaranja</th>
              <th scope="col">datum poslednje promene</th>
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
                $id = $big_order['big_order_id'];
                $bo_statusi = get_bo_statusi($pdo, $id);
                $bo_last_status = $bo_statusi[0]['status'];
                $bo_datum_ordera = "";
                $bo_datum_zatvaranja = "";
                foreach ($bo_statusi as $bo_status) {
                  if(strcmp($bo_status['status'], 'poruceno') === 0) {
                    $bo_datum_ordera = htmlentities($bo_status['datum_statusa']);
                  }
                  if(strcmp($bo_status['status'], 'zatvoreno') === 0) {
                    $bo_datum_zatvaranja = htmlentities($bo_status['datum_statusa']);
                  }

                }
                $oznaka = htmlentities($big_order['oznaka']);
                $status = htmlentities($big_order['status']);
                $datum_otvaranja = htmlentities($big_order['datum_otvaranja']);
								$datum_modifikovanja = htmlentities($big_order['datum_modifikovanja']);
                $komentar = "'".htmlentities($big_order['komentar'])."'";
                $count++;
                ?>
                <tr>
                  <td scope="row"><?=$count?></td>
									<td class="hide"><?=$id?></td>
                  <td><?=$oznaka?></td>
                  <td><?=$status?></td>
                  <td><?=$datum_otvaranja?></td>
                  <td><?=$bo_datum_ordera?></td>
                  <td><?=$bo_datum_zatvaranja?></td>
                  <td><?=$datum_modifikovanja?></td>
                  <td><?=$komentar?></td>
                  <td><a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$id?>">Edit</a></td>
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
