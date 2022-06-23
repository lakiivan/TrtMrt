<?php
require_once "../../util/pdo.php";
require_once "artikl_orderi_data.php";
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
                  $ao_artikl_id = htmlentities($artikl_order['datum_otvaranja']);
                  $ao_prodajna_cena = htmlentities($artikl_order['prodajna_cena']);
                  $ao_datum_porudzbine = htmlentities($artikl_order['datum_porudzbine']);
                  $datum_modifikovanja = htmlentities($artikl_order['datum_modifikovanja']);
                  $komentar = "'".htmlentities($artikl_order['komentar'])."'";
                  $count++;
                  ?>
                  <tr>
                    <td scope="row"><?=$count?></td>
                    <td class="hide"><?=$id?></td>
                    <td><?=$oznaka?></td>
                    <td><?=$status?></td>
                    <td><?=$datum_otvaranja?></td>
                    <td><?=$datum_porucivanja?></td>
                    <td><?=$datum_zatvaranja?></td>
                    <td><?=$datum_modifikovanja?></td>
                    <td><?=$komentar?></td>
                    <td><a href="artikl_order_interface.php?big_order_id=<?=$id?>">Edit</a></td>
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

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/search.js"></script>
  </body>
  </html>
