<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";
require_once "../util/html_maker_table.php";
require_once "../big_order/bo_util/upit.php";

session_start();
$form_maker = new HtmlMakerForm();
$table_maker= new HtmlMakerTable();
$db         = new Db();
$upit       = new Upit();

$gradovi    = $db -> get_gradovi($pdo);
$klubovi    = $db -> get_klubovi($pdo);
$pop_grupe  = $db -> get_pop_grupe($pdo);
$kontakt_id = $_GET['kontakt_id'];
$kontakt    = $db -> get_kontakt_info($pdo, $kontakt_id);
$kontakt_stat = $db -> statistika_kontakta($pdo, $kontakt_id);
$ukupno_naplaceno     = $upit -> kolika_je_ukupna_zarada_po_kontaktu($pdo, $kontakt_stat['kontakt_id']);
$ukupna_zarada        = $ukupno_naplaceno - $kontakt_stat['ukupna_net'];
$grad                 = $upit -> pronadji_iz_kog_grada_je_kontakt($pdo, $kontakt_id);
$datum_last_big_order = $upit -> get_datum_last_big_order($pdo, $kontakt_id);
$kontaktove_porudzbine= $db -> get_sve_kontaktove_porudzbine($pdo, $kontakt_id);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php
  require_once "../util/navbar.php";
  ?>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span5">
        <?php
        $form_maker -> create_header_form("Pregled Kontakta");
        $form_maker -> create_fieldset_kontakt_form($gradovi, $klubovi, $pop_grupe, $kontakt, "readonly", "disabled");
        $form_maker -> create_footer_form();
        ?>
      </div>
      <div class="span7">
        <h2>ISTORIJA</h2>
        <div class="row-fluid">
          <div class="span6">
            <p>Ukupno Porudzbina</p>
          </div>
        <div class="span5 bolder">
          <p><?=$kontakt_stat['ukupno_porudzbina']?></p>
        </div>
      <div class="row-fluid">
        <div class="span6">
          <p>ZARADA</p>
        </div>
        <div class="span5 bolder">
          <p><?=number_format($ukupna_zarada, 1)?></p>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <p>Ukupno List</p>
        </div>
        <div class="span5 bolder">
          <p><?=number_format($kontakt_stat['ukupna_list'], 1)?></p>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <p>Ukupno Net</p>
        </div>
        <div class="span5 bolder">
          <p><?=number_format($kontakt_stat['ukupna_net'], 1)?></p>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <p>Ukupno Artikala</p>
        </div>
        <div class="span5 bolder">
          <p><?=$kontakt_stat['ukupno_artikala']?></p>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <p>Poslednja Porudžbina</p>
        </div>
        <div class="span5 bolder">
          <p><?=$datum_last_big_order?></p>
        </div>
      </div>
    </div>
    <div class="row-fluid">
      <div class="span12">
        <?=$table_maker -> create_table_sve_porudzbine_kontakta($kontaktove_porudzbine)?>
      </div>
    </div>
  </div>
        <script src="../js/jquery-3.6.0.min.js"></script>
        <script src="../bootstrap/js/bootstrap.min.js"></script>
      </body>
      </html>
