<?php
require_once "../util/pdo.php";
require_once "bo_data.php";
require_once "bo_util/upit.php";
require_once "../util/html_maker_table.php";
require_once "../util/db.php";

$upit = new Upit();
$db = new Db();
$table_maker  = new HtmlMakerTable();
$big_order_id = $_GET['big_order_id'];

$kontakti           = $db -> get_kontakti($pdo);

$big_order_stat     = $upit -> get_big_order_stat_data($pdo, $big_order_id);
$bos_ukupna_net     = $upit -> kolika_je_ukupna_net($pdo, $big_order_id);
$bos_ukupna_list    = $upit -> kolika_je_ukupna_list($pdo, $big_order_id);
$bos_ukupna_zarada  = $bos_ukupna_list - $bos_ukupna_net;
$bos_ukupna_net     = number_format($bos_ukupna_net, 1);
$bos_ukupna_list    = number_format($bos_ukupna_list, 1);
$bos_ukupna_zarada  = number_format($bos_ukupna_zarada, 1);

$last_status        = $upit -> get_bo_last_status($pdo, $boid);
$bar_style          = $upit -> calc_bar_style($last_status);
$bo_statusi         = $upit -> get_bo_svi_statusi($pdo, $big_order_id);
$datum_otvaranja    = $upit -> find_datum_statusa($bo_statusi, 'otvoreno');
$datum_ordera       = $upit -> find_datum_statusa($bo_statusi, 'poruceno');
$datum_zatvaranja   = $upit -> find_datum_statusa($bo_statusi, 'zatvoreno');

//post
if (isset($_POST['kontakt_id']) && strcmp($_POST['kontakt_id'], '') !== 0) {
  header('Location: http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$_POST['kontakt_id'].'&big_order_id='.$_GET['big_order_id']);
  return;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php require_once "../util/navbar.php" ?>
  <div class="container-fluid">
    <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
    <div class="row-fluid">
      <div class="span12 boi" id="boi_main">
        <div class="container-fluid">
          <div class="row-fluid">
            <div class="span5.8">
              <div class="span6">
                <h2>Br. Porudžbine : <?=" ".$bo_oznaka?></h2>
              </div>
              <div class="span4">
                <h2><?=$bos_ukupna_net?> /
                  <span><?=$bos_ukupna_list?> / <?=$bos_ukupna_zarada?> Evra</span> </h2>
                </div>
              </div>
            </div>
            <div class="row-fluid">
              <div class="span6">
                <h3>Status Porudžbine<?=" - ".$last_status?></h3>
              </div>
              <div class="span6">
                <div class="progress">
                  <div class="bar" <?=$bar_style?>></div>
                </div>
              </div>
            </div>
            <div class="row-fluid">
              <div class=span3>
                <p>Datum otvaranja</p>
              </div>
              <div class=span3>
                <p>Datum porucivanja</p>
              </div>
              <div class=span3>
                <p>Datum zatvaranja</p>
              </div>
            </div>
            <div class="row-fluid">
              <div class=span3>
                <p><?=$datum_otvaranja?></p>
              </div>
              <div class=span3>
                <p><?=$datum_ordera?></p>
              </div>
              <div class=span3>
                <p><?=$datum_zatvaranja?></p>
              </div>
            </div>
            <div class="row-fluid">
              <div clas=span2></div>
              <div class=span2>
                <button class="btn btn-large bo_interface" id="btn_order" onclick="location.href='https://localhost/trt_mrt/big_order/order/order.php?big_order_id=<?=$_GET['big_order_id']?>'">ORDER</button>
              </div>
              <div class=span2>
                <button class="btn btn-large bo_interface" id="btn_pregled" onclick="location.href='https://localhost/trt_mrt/big_order/pregled/bo_pregled_interface.php?big_order_id=<?=$_GET['big_order_id']?>'">PREGLED</button>
              </div>
              <div class=span2>
                <button class="btn btn-large bo_interface" id="btn_isporuka" onclick="location.href='https://localhost/trt_mrt/big_order/isporuka/bo_isporuka_interface.php?big_order_id=<?=$_GET['big_order_id']?>'">ISPORUKA</button>
              </div>
              <div class=span2>
                <button class="btn btn-large bo_interface" id="btn_zatvaranje" onclick="location.href='https://localhost/trt_mrt/big_order/naplata/bo_naplata_interface.php?big_order_id=<?=$_GET['big_order_id']?>'">NAPLATA</button>
              </div>
              <div class=span3>
                <button class="btn btn-large bo_interface" id="btn_zatvaranje" onclick="location.href='https://localhost/trt_mrt/big_order/izvestaj/bo_izvestaj.php?big_order_id=<?=$_GET['big_order_id']?>'">IZVEŠTAJ</button>
              </div>
              <div clas=span1></div>
            </div>
            <hr>
            <div class="row-fluid">

              <div class="controls">
                <button class="btn btn-large btn_nov" id="btn_nov_kontakt" onclick="location.href='https://localhost/trt_mrt/kontakti/kontakt_form.php?big_order_id=<?=$_GET['big_order_id']?>'">Nov Kontakt</button>
              </div>
              <form class="form-horizontal" method="post">
                <fieldset>
              <label class="control-label" for="kontakt">Kontakt</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="kontakt" id="kontakt" placeholder="Unesite ime kontakta" onfocusout="get_kontakt_data()">
              </div>
              <label class="control-label" for="kontakt_id">Kontakt id</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="kontakt_id" id="kontakt_id" readonly>
              </div>
              <label class="control-label" for="ime">Kontakt ime</label>
              <div class="controls">
                <input type="text" class="input-xlarge" name="ime" id="ime" readonly>
                <button class="submit btn" id="btn_kontakt">Otvori Single Order</button>
              </div>
            </div>
          </fieldset>
        </form>
          </div>
        </div>

        <div class="row-fluid">
          <div class="span12">
            <?=require_once "bo_sum_po_kupcu_view.php";?>
          </div>
        </div>

        <div class="row-fluid">
          <div class="span12 boi" id="boi_detaljni_prikaz">
            <?php require_once "bo_detaljno_view.php"; ?>
          </div>
        </div>

      </div>
      <script src="../js/jquery-3.6.0.min.js"></script>
      <script src="../bootstrap/js/bootstrap.min.js"></script>
      <script src="//code.jquery.com/jquery-1.12.4.js"></script>
      <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script src="../js/kontakt.js"></script>
      <script src="../js/bo_kontakt_auto.js"></script>
      <script src="../js/search.js"></script>
    </body>
    </html>
