<?php
require_once "../util/pdo.php";
// slanje upita za dobijanje Kontakata iz baze
$stmt = $pdo -> prepare ("SELECT *
  FROM artikl_orderi
  ORDER BY big_order_id ASC");
  $stmt -> execute();
  $artikl_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
  <head>
    <?php require_once "../../util/head.php"?>
  </head>
  <body>
    <?php require_once "../../util/navbar.php"?>
    <div class="container">
      <div class="row">
        <div class="span12">
          <table class="table table-striped" id="tabela_artikl_orderi">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col" class="hide">id</th>
                <th scope="col">big_order_id</th>
                <th scope="col">kontakt id</th>
                <th scope="col">artikl id</th>
                <th scope="col">kolicina</th>
                <th scope="col">prodajna cena</th>
                <th scope="col">datum poručivanja</th>
                <th scope="col">datum poslednje promene</th>
                <th scope="col">Komentar</th>
                <th scope="col">Edit</th>
              </tr>
            </thead>
            <tbody>
              <?php
              //ucitavanje pojedinacnih podataka i njihovo upisivanje u tabelu
              if(count($artikl_orderi) > 0) {
                $count = 0;
                foreach ($artikl_orderi as $artikl_order) {
                  $aoid = $artikl_order['artikl_order_id'];
                  $ao_big_order_id = htmlentities($artikl_order['big_order_id']);
                  $ao_kontakt_id = htmlentities($artikl_order['kontakt_id']);
                  $ao_artikl_id = htmlentities($artikl_order['artikl_id']);
                  $ao_kolicina = htmlentities($artikl_order['kolicina']);
                  $ao_prodajna_cena = htmlentities($artikl_order['prodajna_cena']);
                  $ao_datum_porudzbine = htmlentities($artikl_order['datum_porudzbine']);
                  $datum_modifikovanja = htmlentities($artikl_order['datum_modifikovanja']);
                  $komentar = "'".htmlentities($artikl_order['komentar'])."'";
                  $count++;
                  ?>
                  <tr>
                    <td scope="row"><?=$count?></td>
                    <td class="hide"><?=$aoid?></td>
                    <td><?=$ao_big_order_id?></td>
                    <td><?=$ao_kontakt_id?></td>
                    <td><?=$ao_artikl_id?></td>
                    <td><?=$ao_kolicina?></td>
                    <td><?=$ao_prodajna_cena?></td>
                    <td><?=$ao_datum_porudzbine?></td>
                    <td><?=$datum_modifikovanja?></td>
                    <td><?=$komentar?></td>
                    <td><a href="order_artikl_form_edit.php?artikl_id=<?=$ao_artikl_id?>&kontakt_id=<?=$ao_kontakt_id?>&big_order_id=<?=$ao_big_order_id?>&is_view_a=1">Edit</a></td>
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
    <script src="../../js/jquery-3.6.0.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <script src="../../js/search.js"></script>
  </body>
  </html>
