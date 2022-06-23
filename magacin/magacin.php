<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_table.php";

$db                 = new Db();
$table_maker        = new HtmlMakerTable();
$svi_artikli_na_stanju  = $db -> get_svi_artikli_u_magacinu($pdo);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php require_once "../util/navbar.php" ?>
  <div class="container">
    <div class="row">
      <div class="span12">
        <h2>MAGACIN STANJE Artikala</h2>
        <input type="text" id="search_mpn" title="Unesite ime"
        onkeyup="search_table('search_mpn','tabela_magacin_artikli',3)" placeholder = "Pronađi artikl po part numberu...">
        <input type="text" id="search_mopis" title="Unesite ime"
        onkeyup="search_table('search_mopis','tabela_magacin_artikli',4)" placeholder = "Pronađi artikl po opisu...">

        <table class="table table-striped" id="table_magacin_artikli">
          <thead>
            <?php
            $column_names       = array("Count", "Part_Number", "Kol", "Opis", "Cena net, Eur", "AOID", "Vlasnik", "BOID");
            $column_attributes  = array('scope="col"', 'scope="col"', 'scope="col"', 'scope="col"', 'scope="col"', 'scope="col"', 'scope="col"', 'scope="col"');
            $table_maker        -> create_table_row("th", $column_names, $column_attributes);
            ?>
          </thead>
          <tbody>
            <?php
            if(count($svi_artikli_na_stanju) > 0) {
              $count = 1;
              $ukupna_kolicina    = 0;
              $ukupna_net         = 0;
              foreach ($svi_artikli_na_stanju as $artikl) {
                //izvuci pojedinacne podatke i procisti ih
                $magacin_id       = htmlentities($artikl['magacin_id']);
                $artikl_id        = htmlentities($artikl['artikl_id']);
                $part_number      = htmlentities($artikl['part_number']);
                $kolicina         = htmlentities($artikl['kolicina']);
                $opis             = htmlentities($artikl['opis']);
                $cena_net         = htmlentities($artikl['cena_net']);
                $artikl_order_id  = htmlentities($artikl['artikl_order_id']);
                $kontakt_ime      = htmlentities($artikl['ime']);
                $bo_oznaka        = htmlentities($artikl['oznaka']);

                //kreiraj dva niza neophodna za popunjavanje td polja, row data sadrzi podatke, dok row att sadrzi class odnosno atribute u tagu
                $row_data         = array($count, $part_number, $kolicina, $opis, $cena_net, $artikl_order_id, $kontakt_ime, $bo_oznaka);
                $row_att          = array('class="td_aoid"', 'class="td_pn"', 'class="td_kol"', 'class"td_opis"', 'class="td_pn"', 'class="td_kol"', 'class="td_pn"', 'class="td_pn"');

                $table_maker -> create_table_row("td", $row_data, $row_att);

                $count++;
                $ukupna_kolicina  += $kolicina;
                $ukupna_net       += $cena_net;
              }
            }

              $row_data_sum       = array('', '', $ukupna_kolicina, '', $ukupna_net, '', '', '');
              $row_att_sum        = array('class="td_aoid"', 'class="td_pn', 'class="td_kol bolder"', 'class"td_opis"', 'class="td_pn bolder"', 'class="td_kol"', 'class="td_pn"', 'class="td_pn"');
              $table_maker -> create_table_row("td", $row_data_sum, $row_att_sum);
              echo '</tbody>';
              echo '</table>';
              ?>

            </div>
          </div>
        </div>

        <script src="../js/jquery-3.6.0.min.js"></script>
        <script src="../bootstrap/js/bootstrap.min.js"></script>
        <script src="../js/search.js"></script>
      </body>
      </html>
