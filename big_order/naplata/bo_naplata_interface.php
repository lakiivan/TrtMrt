<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";
require_once "bo_naplata_data.php";
require_once "bo_naplata_constructor.php";
session_start();

$naplata_data = New NaplataQueries();
$naplata_constructor = New NaplataConstructor();
$upit = new Upit();

$nenaplaceni = $naplata_data -> bo_naplata_query($pdo, 0);
$naplaceni = $naplata_data -> bo_naplata_query($pdo, 1);

$procenat_naplacenih = $upit -> izracunaj_procenat_bara(count($nenaplaceni), count($naplaceni));

$bar_style = 'style="width: '.$procenat_naplacenih.'%;"';

function session_message() {
  if (isset($_SESSION['success'])) {
    echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
    unset($_SESSION['success']);
  } else if(isset($_SESSION['error'])) {
    echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
    unset($_SESSION['error']);
  }
}

//************************* POST METOD ****************************************
if(isset($_POST['nap_eura']) && isset($_POST['kontakt_id']) && isset($_POST['big_order_id'])) {
  $date = date("Y-m-d H:i:s");
  $sql = "UPDATE bo_naplate
  SET naplaceno =:naplaceno, ukupno_eura =:ukupno_eura,
  datum_naplate =:datum_naplate
  WHERE big_order_id =:big_order_id AND kontakt_id = :kontakt_id";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':big_order_id' => $_POST['big_order_id'],
    ':kontakt_id' => $_POST['kontakt_id'],
    ':ukupno_eura' => $_POST['nap_eura'],
    ':naplaceno' => 1,
    ':datum_naplate' => $date
  ));

  $_SESSION['success'] = 'Big order id - '.$_POST['big_order_id'].' Kontakt id '.$_POST['kontakt_id'].' je naplacen uspesno i unos je updateovan u tabeli bo_naplate';
  header('Location: bo_naplata_interface.php?big_order_id='.$_POST['big_order_id']);
  return;

}
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../../util/head.php" ?>
</head>
<body>
  <?php require_once "../../util/navbar.php" ?>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">

        <div class="row-fluid">
          <div class="span3 text_right"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/isporuka/bo_isporuka_interface.php?big_order_id=<?=$_GET['big_order_id']?>">ISPORUKA</a></h1></div>
          <div class="span4">
            <h1 class="display-4" id="naslov"><a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>">Big order - <?=$_GET['big_order_id']?>, naplata - <?=number_format($procenat_naplacenih,0)?>%</a></h1>
          </div>
          <div class="span3 text_right"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/izvestaj/bo_izvestaj.php?big_order_id=<?=$_GET['big_order_id']?>">IZVEŠTAJ</a></h1></div>

          <div class="row-fluid">
            <div class="span12">
              <div class="progress">
                <div class="bar" <?=$bar_style?>></div>
              </div>
            </div>
          </div>

          <?=session_message();?>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <button class="btn btn-large btn-xxl btn_orange" onclick="location.href='http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>'">UNESI ARTIKL U MAGACIN</button>
        </div>
        <div class="span6">
          <button class="btn btn-large btn-xxl btn_blue" onclick="location.href='http://localhost/trt_mrt/big_order/pregled/bo_update.php?big_order_id=<?=$_GET['big_order_id']?>&status=naplaceno'">POTVRDI KRAJ NAPLATE</button>
        </div>
      </div>

      <button class="btn btn-large btn_srednji" id="btn_nenaplaceno" style="color:#fea90d;">NENAPLACENO</button>
      <div class="row-fluid" id="div_nenaplaceno">
        <div class="span12">
          <h2 class="text_centered" style="color:#fea90d;">NENAPLACENO</h2>
        </div>
        <div class="row-fluid">
          <div></div>
          <?php
          if (count($nenaplaceni) > 0){
            $kurs = $upit -> get_kurs($pdo);
            $style = "nepregled";
            $kid = 1;
            $dumm_kontakt_id = 1;
            $action = "bo_naplata_interface.php";
            foreach ($nenaplaceni as $nenaplacen) {
              $svi_isporuceni = $upit ->
              da_li_su_isporuceni_svi_artikli($pdo, $nenaplacen['big_order_id'], $nenaplacen['kontakt_id']);
              $nenaplacen['svi_isporuceni'] = $svi_isporuceni;
              $ukupno_za_naplatu_eura = $naplata_data ->
              za_naplatu_na_osnovu_isporucenog($pdo, $nenaplacen['big_order_id'], $nenaplacen['kontakt_id']);
              $neisporuceni_artikli = $upit -> sta_sve_od_porucenog_nije_isporuceno($pdo, $nenaplacen['big_order_id'], $nenaplacen['kontakt_id']);
              $nenaplacen['neisporuceni_artikli'] = $neisporuceni_artikli;

              $naplata_constructor -> create_naplata_div($nenaplacen, $style, $ukupno_za_naplatu_eura, $kid, $action, $pdo);
              $kid += 1;
            }
          }
          ?>

        </div>
      </div>

      <hr>

      <button class="btn btn-large btn_srednji" id="btn_naplaceno" style="color:#0070ff;">NAPLACENO</button>
      <div class="row-fluid naplaceno" id="div_naplaceno">
        <div class="span12">
          <h2 class="text_centered" style="color:#0070ff;">NAPLACENO</h2>
        </div>
        <?php
        if (count($naplaceni) > 0) {
          $kid = 1;
          $style = 'pregled';
          $kurs = $upit -> get_kurs($pdo);
          $action = "bo_naplata_update.php";
          foreach ($naplaceni as $naplacen) {
            $svi_isporuceni = $upit ->
            da_li_su_isporuceni_svi_artikli($pdo, $naplacen['big_order_id'], $naplacen['kontakt_id']);
            $naplacen['svi_isporuceni'] = $svi_isporuceni;
            $ukupno_za_naplatu_eura = $naplata_data ->
            za_naplatu_na_osnovu_isporucenog($pdo, $naplacen['big_order_id'], $naplacen['kontakt_id']);
            //$naplata_constructor -> create_naplata_div($naplacen, $style, $ukupno_za_naplatu_eura, $kid);
            $ukupno_naplaceno = $naplata_data -> koliko_je_naplaceno($pdo, $naplacen['big_order_id'], $naplacen['kontakt_id']);
            $stigli_svi = $upit -> da_li_su_isporuceni_svi_artikli($pdo, $naplacen['big_order_id'], $naplacen['kontakt_id']);
            $naplata_constructor -> kreiraj_form_izmene_naplacenog($naplacen, $stigli_svi, $ukupno_za_naplatu_eura, $ukupno_naplaceno, $style, $action, $kid);
            $kid += 1;
          }
        }
        ?>
      </div>

      <script src="../../js/jquery-3.6.0.min.js"></script>
      <script src="../../bootstrap/js/bootstrap.min.js"></script>
      <script src="../../js/naplata.js"></script>
    </body>
    </html>
