<?php
// slanje upita za dobijanje Kontakata iz baze
$stmt = $pdo -> prepare ("SELECT k.ime as ime, ao.kontakt_id as kid, ao.big_order_id as boid,
  ao.artikl_order_id as aoid, ao.artikl_id as aid,
  ao.kolicina as kolicina, a.part_number as part_number, a.opis as opis, ao.prodajna_cena as cena,
  ao.datum_porudzbine as dporudzbine, ao.datum_modifikovanja as dmodifikovanja, ao.komentar as komentar
  FROM artikl_orderi as ao
  INNER JOIN artikli as a ON a.artikl_id = ao.artikl_id
  INNER JOIN kontakti as k ON k.kontakt_id = ao.kontakt_id
  WHERE ao.big_order_id = :boid
  ORDER BY ime ASC, dporudzbine DESC");
  $stmt -> execute(array(
    ':boid' => $_GET['big_order_id']
));
  $artikl_orderi = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  ?>
  <!DOCTYPE html>
  <html lang="en" dir="ltr">
  <head>
  </head>
  <body>
    <div class="container-fluid">
      <div class="row-fluid sum_po_kupcu">
        <div class="span12">
          <h3>Svi poruceni artikli</h3>
          <input type="text" id="search_ime_sum" title="Unesite ime"
          onkeyup="search_table('search_ime_sum','tabela_artikl_orderi_detaljno',3)" placeholder = "Pronađi kontakt po imenu...">
          <table class="table table-striped" id="tabela_artikl_orderi_detaljno">
            <thead>
              <tr>
                <th scope="col"></th>
                <th scope="col" class="hide">id</th>
                <th scope="col">id</th>
                <th scope="col">ime</th>
                <th scope="col">part number</th>
                <th scope="col">opis</th>
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
                  $aoid = $artikl_order['aoid'];
                  $boid = $artikl_order['boid'];
                  $kid = $artikl_order['kid'];
                  $aod_ime = $artikl_order['ime'];
                  $ao_part_ = htmlentities($artikl_order['part_number']);
                  $ao_opis = htmlentities($artikl_order['opis']);
                  $ao_artikl_id = htmlentities($artikl_order['aid']);
                  $ao_kolicina = htmlentities($artikl_order['kolicina']);
                  $ao_prodajna_cena = htmlentities($artikl_order['cena']);
                  $ao_datum_porudzbine = htmlentities($artikl_order['dporudzbine']);
                  $datum_modifikovanja = htmlentities($artikl_order['dmodifikovanja']);
                  $komentar = "'".htmlentities($artikl_order['komentar'])."'";
                  $count++;
                  ?>
                  <tr>
                    <td scope="row"><?=$count?></td>
                    <td class="hide"><?=$aoid?></td>
                    <td><?=$aoid?></td>
                    <td><?=$aod_ime?></td>
                    <td><?=$ao_part_?></td>
                    <td><?=$ao_opis?></td>
                    <td><?=$ao_kolicina?></td>
                    <td><?=$ao_prodajna_cena?></td>
                    <td><?=$ao_datum_porudzbine?></td>
                    <td><?=$datum_modifikovanja?></td>
                    <td><?=$komentar?></td>
                    <td><a href="http://localhost/trt_mrt/big_order/porucivanje/artikl_order_form_edit.php?artikl_order_id=<?=$aoid?>&kontakt_id=<?=$kid?>&big_order_id=<?=$boid?>">Edit</a></td>
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
  </body>
  </html>
