<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";

$upit = new Upit();

//require_once "query_artikli_sumarno.php";
//$bo_oznaka = $svi_poruceni_artikli[0]['bo_oznaka'];
$svi_poruceni_artikli = $upit -> get_bo_svi_validni_artikli_zbirno($pdo, $_GET['big_order_id']);
if (count($svi_poruceni_artikli) > 0) {
  $bo_oznaka = $svi_poruceni_artikli[0]['bo_oznaka'];
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
      <div class="span2">
        <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
      </div>
      <div class="span1">
        <button class="btn btn-large bo_interface" id="order_switch" onclick="remove_net()">Ukloni cene</button>
      </div>
      <div class="span1">
        <button class="btn btn-large bo_interface" id="order_reset" onclick="location.href='https://localhost/trt_mrt/big_order/order/order.php?big_order_id=<?=$_GET['big_order_id']?>'">Vrati cene</button>
      </div>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span4">
      <h1>Order No <a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>"><?=$bo_oznaka?></a></h1>

      <table class="table table-striped" id="tabela_order">
        <thead>
          <tr>
            <th scope="col">No</th>
            <th scope="col">Part Number</th>
            <th scope="col">Description</th>
            <th scope="col">Qty</th>
            <th scope="col" id="net_price">Net Price, Eur</th>
          </tr>
        </thead>
        <?php
        //pravljenje divova na osnovu dobijenog niza iz query_artikli_sumarno
        $ukupno_artikala = 0;
        $ukupno_net = 0;
        if(count($svi_poruceni_artikli) > 0) {
          $count = 0;
          foreach ($svi_poruceni_artikli as $artikl) {
            $id = $artikl['artikl_id'];
            $part_number = htmlentities($artikl['part_number']);
            $opis = htmlentities($artikl['opis']);
            $kolicina = htmlentities($artikl['sum_artikala']);
            $cena_net = number_format(htmlentities($artikl['sum_ukupna_net']), 1);
            $ukupno_artikala += $kolicina;
            $ukupno_net += $cena_net;
            $count++;
            ?>
            <tr>
              <td scope="row"><?=$count?></td>
              <td class="hide"><?=$id?></td>
              <td><?=$part_number?></td>
              <td><?=$opis?></td>
              <td><?=$kolicina?></td>
              <td><?=$cena_net?></td>
            </tr>
            <?php
          }
        }
        ?>
        <tr>
          <hr>
        </tr>
        <tr>
          <td scope="row"></td>
          <td class="hide"><?=$id?></td>
          <td></td>
          <td></td>
          <td class="suma"><?=$ukupno_artikala?></td>
          <td class="suma net_visible"><?=$ukupno_net?></td>
        </tr>
      </table>
    </div>
  </div>
  <div class="row-fluid">
    <div class="span4">
      <button type="submit" class="btn btn-xxl" onclick="location.href='http://localhost/trt_mrt/big_order/order/execute_order.php?big_order_id=<?=$_GET['big_order_id']?>'">NARUČI</button>
    </div>
  </div>
  <script src="../../js/jquery-3.6.0.min.js"></script>
  <script src="../../bootstrap/js/bootstrap.min.js"></script>
  <script src="../../js/order.js"></script>
</body>
</html>
