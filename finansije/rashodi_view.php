<?php
require_once "../util/pdo.php";
  // slanje upita za dobijanje Kontakata iz baze
  $stmt = $pdo -> prepare ("SELECT *
    FROM rashodi
    ORDER BY rashod_id DESC");
  $stmt -> execute();
  $contacts = $stmt -> fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="span12">
        <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='http://localhost/trt_mrt/kontakti/kontakt_form.php'">Nov Rashod</button>
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
        <h2>Pregled Kontakata
          <span class="glyphicon glyphicon-align-left"></span></h2>
        <input type="text" id="search_ime_bo" title="Unesite ime"
        onkeyup="search_table('search_ime_bo', 'tabela_kontakti_view', 4)" placeholder = "Pronađi kontakt po imenu...">

        <table class="table table-striped" id="tabela_kontakti_view">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col" class="hide">id</th>
              <th scope="col" class=<?=$order_visible?>>Order</th>
              <th scope="col">Ime</th>
              <th scope="col">Telefon</th>
              <th scope="col">Adresa</th>
              <th scope="col">Grad</th>
              <th scope="col">Klub</th>
              <th scope="col"></th>
            </tr>
          </thead>

          <tbody>
            <?php
            //ucitavanje pojedinacnih podataka i njihovo upisivanje u tabelu
            if(count($contacts) > 0) {
              $count = 0;
              foreach ($contacts as $contact) {
                $id = $contact['kontakt_id'];
                $ime = htmlentities($contact['ime']);
                $telefon = htmlentities($contact['telefon']);
                $adresa = htmlentities($contact['adresa']);
                $grad = htmlentities($contact['grad']);
                $klub = htmlentities($contact['klub']);
                $count++;
                ?>
                <tr>
                  <td scope="row"><?=$count?></td>
                  <td scope="row" class=<?=$order_visible?>><a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id=<?=$id?>&big_order_id=<?=$boid?>">Dodaj</a></td>
                  <td class="hide"><a href="http://localhost/trt_mrt/big_order/porucivanje/order_form.php?kontakt_id=<?=$id?>">Order</a></td>
                  <td class="hide"><?=$id?></td>
                  <td><a href="http://localhost/trt_mrt/kontakti/kontakt_form_edit.php?kontakt_id=<?=$id?> & is_view=1"><?=$ime?></a></td>
                  <td><?=$telefon?></td>
                  <td><?=$adresa?></td>
                  <td><?=$grad?></td>
                  <td><?=$klub?></td>
                  <td><a href="http://localhost/trt_mrt/kontakti/kontakt_form_edit.php?kontakt_id=<?=$id?> & is_view=0">Edit</a></td>
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
