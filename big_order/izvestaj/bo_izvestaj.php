<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";
require_once "../bo_util/html_maker.php";

$big_order_id = $_GET['big_order_id'];
$upit = new Upit();
$html_maker = new HtmlMaker();

$bo_ukupna_zarada         = $upit -> big_order_zarada($pdo, $big_order_id);
$bo_ukupni_troskovi       = $upit -> big_order_troskovi($pdo,$big_order_id);
$bo_profit_bez_rekvizita  = $bo_ukupna_zarada - $bo_ukupni_troskovi;

$masa_rekviziti_trosak    = $upit -> masa_lena_rekviziti_trosak($pdo, $big_order_id, 25);
$lena_rekviziti_trosak    = $upit -> masa_lena_rekviziti_trosak($pdo, $big_order_id, 30);
$trt_mrt_trosak           = $upit -> masa_lena_rekviziti_trosak($pdo, $big_order_id, 42);

$masa_zarada = $bo_profit_bez_rekvizita/2 - $masa_rekviziti_trosak - $trt_mrt_trosak/2;
$lena_zarada = $bo_profit_bez_rekvizita/2 - $lena_rekviziti_trosak - $trt_mrt_trosak/2;

$broj_kupaca              = $upit -> koliko_ima_kupaca($pdo, $big_order_id);
if($broj_kupaca == 0){
  $prosecna_zarada_po_kupcu = 0;
} else {
  $prosecna_zarada_po_kupcu = $bo_ukupna_zarada / $broj_kupaca;
}

$ukupno_porucenih_artikala          = $upit -> koliko_je_ukupno_artikala_poruceno($pdo, $big_order_id);
$ukupno_artikala_koji_nisu_stigli   = $upit -> koliko_je_ukupno_artikala_stiglo($pdo, $big_order_id, 0);
$ukupno_pregledanih_artikala        = $upit -> koliko_je_ukupno_artikala_stiglo($pdo, $big_order_id, 1);
$ukupno_neisporucenih_artikala      = $upit -> koliko_je_ukupno_artikala_isporuceno($pdo, $big_order_id, 0);
$ukupno_isporucenih_artikala        = $upit -> koliko_je_ukupno_artikala_isporuceno($pdo, $big_order_id, 1);

$bo_datum_porucivanja   = $upit -> kada_je_bio_udpate_statusa_big_ordera($pdo,$big_order_id, 'poruceno');
$bo_datum_pregleda      = $upit -> kada_je_bio_udpate_statusa_big_ordera($pdo,$big_order_id, 'pregledano');
$bo_datum_isporuke      = $upit -> kada_je_bio_udpate_statusa_big_ordera($pdo,$big_order_id, 'isporuceno');
$bo_datum_naplate       = $upit -> kada_je_bio_udpate_statusa_big_ordera($pdo,$big_order_id, 'naplaceno');
$bo_datum_zatvaranja    = $upit -> kada_je_bio_udpate_statusa_big_ordera($pdo,$big_order_id, 'zatvoreno');

$svi_poruceni_artikli         = $upit -> svi_poruceni_artikli($pdo, $big_order_id);
$svi_pregledani_artikli       = $upit -> svi_artikli_po_statusu_pregleda($pdo, $big_order_id, 1);
$svi_artikli_koji_nisu_stigli = $upit -> svi_artikli_po_statusu_pregleda($pdo, $big_order_id, 0);
//$svi_artikli_stigli_u_visku   = $upit -> svi_artikli_stigli_u_visku($pdo, $big_order_id);
$svi_isporuceni_artikli       = $upit -> svi_artikli_po_statusu_isporuke($pdo, $big_order_id, 1);
$svi_neisporuceni_artikli     = $upit -> svi_artikli_po_statusu_isporuke($pdo, $big_order_id, 0);
$svi_naplaceni_artikli        = $upit -> svi_artikli_po_statusu_naplate($pdo, $big_order_id, 1);
$svi_nenaplaceni_artikli      = $upit -> svi_artikli_po_statusu_naplate($pdo, $big_order_id, 0);

$bo_ukupna_zarada         = number_format($bo_ukupna_zarada, 1);
$bo_ukupni_troskovi       = number_format($bo_ukupni_troskovi, 1);
$bo_profit_bez_rekvizita  = number_format($bo_profit_bez_rekvizita, 1);
$masa_zarada              = number_format($masa_zarada, 1);
$lena_zarada              = number_format($lena_zarada, 1);
$prosecna_zarada_po_kupcu = number_format($prosecna_zarada_po_kupcu
, 1);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../../util/head.php" ?>
</head>
<body>
  <?php require_once "../../util/navbar.php" ?>
  <div class="container-fluid white bo_finansije">

    <div class="row-fluid">
      <div class="span12">
        <div class="row-fluid">
          <div class="span3"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/naplata/bo_naplata_interface.php?big_order_id=<?=$_GET['big_order_id']?>">NAPLATA</a></h1></div>
          <div class="span4">
            <h1 class="display-4" id="naslov"><a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>">Big order - <?=$_GET['big_order_id']?></a></h1>
          </div>

          <div class="row-fluid">
            <div class="span6">

              <div class="row-fluid">
                <div class="span6">
                  <h2 class="text_centered">STATISTIČKI PRIKAZ</h2>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>UKUPNA ZARADA LENA</p>
                </div>
                <div class="span3">
                  <p class="bolder orange"><?=$lena_zarada?></p>
                </div>
                <div class="span3">
                  <p>UKUPNA ZARADA MAŠA</p>
                </div>
                <div class="span3">
                  <p class="bolder orange"><?=$masa_zarada?></p>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>REKVIZITI LENA</p>
                </div>
                <div class="span3">
                  <p><?=$lena_rekviziti_trosak?></p>
                </div>
                <div class="span3">
                  <p>REKVIZITI MAŠA</p>
                </div>
                <div class="span3">
                  <p><?=$masa_rekviziti_trosak?></p>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>Ukupno Naplaćeno</p>
                </div>
                <div class="span3">
                  <p class="bolder"><?=$bo_ukupna_zarada?></p>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>Pastorelli Cena</p>
                </div>
                <div class="span3">
                  <p class="bolder"><?=$bo_ukupni_troskovi?></p>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>Ukupan Profit bez rekvizita</p>
                </div>
                <div class="span3">
                  <p class="bolder orange"><?=$bo_profit_bez_rekvizita?></p>
                </div>
              </div>

              <div class="row-fluid">
                <div class="span3">
                  <p>Ukupno kupaca</p>
                </div>
                <div class="span3">
                  <p class="bolder"><?=$broj_kupaca?></p>
                </div>
                <div class="span3">
                  <p>Prosečan trošak kupca</p>
                </div>
                <div class="span3">
                  <p><?=$prosecna_zarada_po_kupcu?></p>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>Ukupno poručeno artikala</p>
                </div>
                <div class="span3">
                  <p><?=$ukupno_porucenih_artikala?></p>
                </div>
                <div class="span3">
                  <p>Datum poručivanja artikala</p>
                </div>
                <div class="span3">
                  <p><?=$bo_datum_porucivanja?></p>
                </div>
              </div>

              <div class="row-fluid">
                <div class="span3">
                  <p>Ukupno stiglo artikala</p>
                </div>
                <div class="span3">
                  <p><?=$ukupno_pregledanih_artikala?></p>
                </div>
                <div class="span3">
                  <p>Datum pregleda artikala</p>
                </div>
                <div class="span3">
                  <p><?=$bo_datum_pregleda?></p>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span3">
                  <p>Ukupno isporučeno artikala</p>
                </div>
                <div class="span3">
                  <p><?=$ukupno_isporucenih_artikala?></p>
                </div>
                <div class="span3">
                  <p>Datum isporuke artikala</p>
                </div>
                <div class="span3">
                  <p><?=$bo_datum_isporuke?></p>
                </div>
              </div>

              <hr>
              <div class="row-fluid">
                <div class="span6">
                  <button class="btn btn-large btn_srednji" id="btn_poruceni" style="color:#fea90d;">Poručeni Artikli</button>
                  <div class="row-fluid hide" id="div_poruceni">
                    <p>TEST vidljivosti</p>
                  </div>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span6">
                  <button class="btn btn-large btn_srednji" id="btn_nisu_stigli" style="color:#fea90d;">NISU STIGLI</button>
                  <div class="row-fluid" id="div_nisu_stigli">
                    <p>ovaj, onaj nije stigao, bla, bla...</p>
                  </div>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span6">
                  <button class="btn btn-large btn_srednji" id="btn_stigli_u_visku" style="color:#fea90d;">STIGLI U VISKU</button>
                  <div class="row-fluid" id="div_stigli_u_visku">
                    <p>David, slucajno poslao, bla, bla...</p>
                  </div>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span6">
                  <button class="btn btn-large btn_srednji" id="btn_neisporuceni" style="color:#fea90d;">NISU ISPORUCENI</button>
                  <div class="row-fluid" id="div_neisporuceni">
                    <p>Zagubio kurir, ostetilo se u cuvanju, bla, bla...</p>
                  </div>
                </div>
              </div>
              <div class="row-fluid">
                <div class="span6">
                  <button class="btn btn-large btn_srednji" id="btn_nenaplaceni" style="color:#fea90d;">NISU NAPLACENI</button>
                  <div class="row-fluid" id="div_nenaplaceni">
                    <p>Cekamo da se dete vrati na trening, bla, bla... Mama se ne javlja i tako to</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="span6">
              <div class="row-fluid">
                <div class="span12">
                  <h2 class="text_centered">DETALJAN PRIKAZ</h2>
                  <?php
                    $artikl_order_izvestaji = $upit ->  izvestaj_detaljan_prikaz($pdo, $big_order_id);
                    $html_maker -> napravi_div_detaljan_prikaz($artikl_order_izvestaji);
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span12">
          <button class="btn btn-large btn-xxl btn_blue" onclick="location.href='http://localhost/trt_mrt/big_order/bo_util/bo_update.php?big_order_id=<?=$_GET['big_order_id']?>&status=zatvoreno'">ZATVORI BIG ORDER</button>
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
    </div>
    <script src="../../js/jquery-3.6.0.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <script src="../../js/search.js"></script>
    <script src="../../js/izvestaj.js"></script>
  </body>
  </html>
