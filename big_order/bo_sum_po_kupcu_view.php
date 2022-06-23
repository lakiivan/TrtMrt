<?php
require_once "bo_sum_po_kupcu_query.php";
$boid = $_GET['big_order_id'];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
</head>
<body>

  <div class="container-fluid sum_po_kupcu">
    <div class="row-fluid">
      <div class="span12">
        <h2>Zbirno po Kupcu</h2>
        <?php
        if (isset($_SESSION['success'])) {
          echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
          unset($_SESSION['success']);
        } else if(isset($_SESSION['error'])) {
          echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
          unset($_SESSION['error']);
        }
        ?>
        <input type="text" id="search_ime_1" title="Unesite ime"
        onkeyup="search_table('search_ime_1','tabela_sum_po_kupcu',2)" placeholder = "Pronađi kontakt po imenu...">

        <table class="table table-striped" id="tabela_sum_po_kupcu">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col" class="hide">Kontakt id</th>
              <th scope="col">Ime</th>
              <th scope="col">Artikala</th>
              <th scope="col">Net</th>
              <th scope="col">Prodajna Cena</th>
              <th scope="col">ZARADA</th>
              <th scope="col">Edit</th>
            </tr>
          </thead>
          <tbody>
            <?php
            //ucitavanje pojedinacnih podataka i njihovo upisivanje u tabelu
            if(count($bo_sum_po_kupcima) > 0) {
              $count = 0;
              foreach ($bo_sum_po_kupcima as $bo_sum_po_kupcu) {
                $kid = htmlentities($bo_sum_po_kupcu['kid']);
                $ime = htmlentities($bo_sum_po_kupcu['ime']);
                $sum_artikala = htmlentities($bo_sum_po_kupcu['sum_artikala']);
                $sum_ukupna_net = number_format(htmlentities($bo_sum_po_kupcu['sum_ukupna_net']), 1);
                $prodajna_cena = number_format(htmlentities($bo_sum_po_kupcu['sum_prodajna_cena']), 1);
                $sum_zarada = number_format(htmlentities($bo_sum_po_kupcu['sum_zarada']), 1);
                $count++;
                ?>
                <tr>
                  <td scope="row"><?=$count?></td>
                  <td scope="row" class="hide"><?=$kid?></td>
                  <td scope="row"><?=$ime?></td>
                  <td scope="row"><?=$sum_artikala?></td>
                  <td scope="row"><?=$sum_ukupna_net?></td>
                  <td scope="row"><?=$prodajna_cena?></td>
                  <td scope="row"><?=$sum_zarada?></td>
                  <td scope="row"><a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id=<?=$kid?>&big_order_id=<?=$boid?>">Izmeni</a></td>
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
