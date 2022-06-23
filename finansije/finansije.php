<?php
require_once "../util/pdo.php";
require_once "finansije_upiti.php";

// slanje upita za dobijanje Kontakata iz baze
$finansijski_upiti = new FinansijeUpiti();


$ukupni_prihodi = $finansijski_upiti -> get_svi_prihodi_u_eurima($pdo);
$ukupni_rashodi = $finansijski_upiti -> get_svi_rashodi_u_eurima($pdo);

$masa_ukupan_prihod = number_format($ukupni_prihodi / 2, 1);
$lena_ukupan_prihod = number_format($ukupni_prihodi / 2, 1);
$masa_ukupan_rashod = number_format($ukupni_rashodi / 2, 1);
$lena_ukupan_rashod = number_format($ukupni_rashodi / 2, 1);
$masa_ukupan_profit = number_format(($ukupni_prihodi - $ukupni_rashodi)/ 2, 1);
$lena_ukupan_profit = number_format(($ukupni_prihodi - $ukupni_rashodi)/ 2, 1);
$masa_ukupna_isplata = 0;
$lena_ukupna_isplata = 0;
$masa_ukupan_trosak = $finansijski_upiti -> get_masa_lena_ukupan_trosak($pdo, 25);
$masa_ukupan_trosak = number_format($masa_ukupan_trosak, 1);
$lena_ukupan_trosak = $finansijski_upiti -> get_masa_lena_ukupan_trosak($pdo, 30);
$lena_ukupan_trosak = number_format($lena_ukupan_trosak, 1);
$trt_mrt_trosak     = $finansijski_upiti -> get_masa_lena_ukupan_trosak($pdo, 42);
$trt_mrt_trosak     = number_format($trt_mrt_trosak, 1);

$masa_stanje = ($ukupni_prihodi - $ukupni_rashodi) / 2 - $masa_ukupan_trosak - $trt_mrt_trosak/2;
$masa_stanje = number_format($masa_stanje, 1);
$lena_stanje = ($ukupni_prihodi - $ukupni_rashodi) / 2 - $lena_ukupan_trosak - $trt_mrt_trosak/2;
$lena_stanje = number_format($lena_stanje, 1);

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
        <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='http://localhost/trt_mrt/finansije/rashod_form.php'">Nov Rashod</button>
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
        <h2>FINANSIJSKI IZVESTAJ<span class="glyphicon glyphicon-align-left"></span></h2>
        <div class="row">
          <div class="span6 nepregled">
            <div class="row">
              <div class="span3">
                <h2>MASA STANJE</h2>
              </div>
              <div class="span3">
                <h2><?=$masa_stanje?></h2>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>MASA PRIHODI</h3>
              </div>
              <div class="span3">
                <h3><?=$masa_ukupan_prihod?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>MASA RASHODI</h3>
              </div>
              <div class="span3">
                <h3><?=$masa_ukupan_rashod?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>MASA PROFIT</h3>
              </div>
              <div class="span3">
                <h3><?=$masa_ukupan_profit?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>MASA ISPLATA</h3>
              </div>
              <div class="span3">
                <h3><?=$masa_ukupna_isplata?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>MASA REKVIZITI</h3>
              </div>
              <div class="span3">
                <h3><?=$masa_ukupan_trosak?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>TRT MRT</h3>
              </div>
              <div class="span3">
                <h3><?=$trt_mrt_trosak/2?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span6">
                <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='http://localhost/trt_mrt/finansije/isplata_form.php?kontakt_id=25'">ISPLATA MAŠI</button>
              </div>
            </div>
          </div>
          <div class="span6 pregled">
            <div class="row">
              <div class="span3">
                <h2>LENA STANJE</h2>
              </div>
              <div class="span3">
                <h2><?=$lena_stanje?></h2>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>LENA PRIHODI</h3>
              </div>
              <div class="span3">
                <h3><?=$lena_ukupan_prihod?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>LENA RASHODI</h3>
              </div>
              <div class="span3">
                <h3><?=$lena_ukupan_rashod?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>LENA PROFIT</h3>
              </div>
              <div class="span3">
                <h3><?=$lena_ukupan_profit?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>LENA ISPLATA</h3>
              </div>
              <div class="span3">
                <h3><?=$lena_ukupna_isplata?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>LENA REKVIZITI</h3>
              </div>
              <div class="span3">
                <h3><?=$lena_ukupan_trosak?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span3">
                <h3>TRT MRT</h3>
              </div>
              <div class="span3">
                <h3><?=$trt_mrt_trosak/2?></h3>
              </div>
            </div>
            <div class="row">
              <div class="span6">
                <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='http://localhost/trt_mrt/finansije/isplata_form.php?kontakt_id=30'">ISPLATA LENI</button>
              </div>
            </div>

          </div>
        </div>
      </div>
      <script src="../js/jquery-3.6.0.min.js"></script>
      <script src="../bootstrap/js/bootstrap.min.js"></script>
      <script src="../js/search.js"></script>
    </body>
    </html>
