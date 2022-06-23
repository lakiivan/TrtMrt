<?php
require_once "../../util/pdo.php";
require_once "../../util/db.php";
require_once "../bo_util/upit.php";
require_once "../bo_util/html_maker.php";
session_start();

$upit                 = new Upit();
$db                   = new Db();
$html_maker           = new HtmlMaker();

$neisporuceni         = $upit -> get_isporuka_podaci($pdo, 0);
$isporuceni           = $upit -> get_isporuka_podaci($pdo, 1);
$procenat_isporucenih = $upit -> izracunaj_procenat_bara(count($neisporuceni), count($isporuceni));

$bar_style            = 'style="width: '.$procenat_isporucenih.'%;"';


//************************* POST METOD ****************************************
if(isset($_POST['isporucena_kolicina']) && ($_POST['isporucena_kolicina'] != '') && isset($_POST['artikl_order_id'])) {
  $boid = $_GET['big_order_id'];
  //rpvera da li je pregeldana kolicina jednaka ili manja od porucene kolocine
  if(is_pk_valid(intval($_POST['narucena_kolicina']), intval($_POST['isporucena_kolicina']))) {
    if(intval($_POST['isporucena_kolicina']) > 0){
      //update pregelda u tabeli bo_pregledi
      $upit -> update_ao_isporuka($pdo,$_POST['artikl_order_id'], $_POST['isporucena_kolicina']);
      $upit -> otpremi_artikl_iz_magacina($pdo, $_POST['artikl_order_id']);
      $upit -> insert_u_bo_naplate($pdo, $boid, $_POST['kontakt_id']);
    } else {
      $upit -> update_ao_isporuka($pdo,$_POST['artikl_order_id'], $_POST['isporucena_kolicina']);
      $upit -> prebaci_u_vlasnistvo_trtmrta($pdo, $_POST['artikl_order_id']);
    }

  } else {
    echo '<p style="color:RED;">Morate uneti kolicinu koja je manja ili jednaka proucenoj kolicini!</p>';

    $_SESSION['error'] = 'GRESKA - Pregled id - '.$_POST['artikl_order_id'].' u kolicini od '.$_POST['isporucena_kolicina'].' nije updateovan';
    header('Location: bo_pregled_interface.php?big_order_id='.$boid);
    return;
  }
  $_SESSION['success'] = 'Artikl order id - '.$_POST['artikl_order_id'].' u kolicini od '.$_POST['isporucena_kolicina'].' je uspešno updateovan u tabeli bo_isporuke';
  header('Location: bo_isporuka_interface.php?big_order_id='.$boid);
  return;
}

//*********************** UTIL FUNCTIONS FOR POST METHOD*************************************
function is_pk_valid($narucena_kolicina, $isporucena_kolicina) {
  //ukoliko je upisana pregledana kolicina jednaka ili manja od narucene
  //ova funkcija vraca vrednsot True, ako nije vraca False
  if ($isporucena_kolicina <= $narucena_kolicina) {
    return True;
  } else {
    return False;
  }
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
          <div class="span4"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/pregled/bo_pregled_interface.php?big_order_id=<?=$_GET['big_order_id']?>">PREGLED</h1></a></div>
          <div class="span4">
            <h1 class="display-4" id="naslov"><a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>">Big order - <?=$_GET['big_order_id']?>, ISPORUKA - <?=number_format($procenat_isporucenih,0)?>%</a></h1>
          </div>
          <div class="span3 text_right"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/naplata/bo_naplata_interface.php?big_order_id=<?=$_GET['big_order_id']?>">NAPLATA</a></h1></div>

          <div class="row-fluid">
            <div class="span12">
              <div class="progress">
                <div class="bar" <?=$bar_style?>></div>
              </div>
            </div>
          </div>

          <?=$upit -> session_message();?>
        </div>
      </div>
      <div class="row-fluid">
        <div class="span6">
          <button class="btn btn-large btn-xxl btn_orange" onclick="location.href='http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>'">UNESI ARTIKL U MAGACIN</button>
        </div>
        <div class="span6">
          <button class="btn btn-large btn-xxl btn_blue" onclick="location.href='http://localhost/trt_mrt/big_order/pregled/bo_update.php?big_order_id=<?=$_GET['big_order_id']?>&status=isporuceno'">POTVRDI KRAJ ISPORUKE</button>
        </div>
      </div>

      <button class="btn btn-large btn_srednji" id="btn_neisporuceno" style="color:#fea90d;">NEISPORUCENO</button>
      <div class="row-fluid" id="div_neisporuceno">
        <div class="span12">
          <h2 class="text_centered" style="color:#fea90d;">NEISPORUCENO</h2>
        </div>
        <div class="row-fluid">
          <div></div>
          <?php
          if (count($neisporuceni) > 0){
            $kurs = $upit -> get_kurs($pdo);
            $style = "nepregled";
            $bo_oznaka = $neisporuceni[0]['bo_oznaka'];
            $bo_curr_ime = $neisporuceni[0]['ime'];
            $isporuceni_svi = $upit -> da_li_su_stigli_svi_artikli($pdo, $neisporuceni[0]['big_order_id'],$neisporuceni[0]['kontakt_id']);
            $grad = $upit -> pronadji_iz_kog_grada_je_kontakt($pdo, $neisporuceni[0]['kontakt_id']);
            $dumm_kontakt_id = 1;
            //kreiranje prvog diva i tabele u njemu i naslova kolona
            $html_maker -> create_table_header($neisporuceni[0], $style, $grad, $isporuceni_svi);

            foreach ($neisporuceni as $np) {
              $grad = $upit -> pronadji_iz_kog_grada_je_kontakt($pdo, $np['kontakt_id']);
              $isporuceni_svi = $upit -> da_li_su_stigli_svi_artikli($pdo, $np['big_order_id'], $np['kontakt_id']);
              if (strcmp($bo_curr_ime, $np['ime']) == 0){
                //upisivanje n-tog reda podataka istog imena kupca
                $html_maker -> fill_table_row($np);
              } else {
                //zatvarnje aktuelne tabele i otvranja nove uz upis prvog reda podataka
                $html_maker -> zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda($pdo, $style, $np, $dumm_kontakt_id, $grad, $isporuceni_svi);
                $bo_curr_ime = $np['ime'];
              }
            }
            $html_maker -> zatvaranje_poslednje_tabele($pdo, $style, $np, $dumm_kontakt_id);
          }
          ?>

        </div>
      </div>

      <hr>

      <button class="btn btn-large btn_srednji" id="btn_isporuceno" style="color:#0070ff;">ISPORUCENO</button>
      <div class="row-fluid isporuceno" id="div_isporuceno">
        <div class="span12">
          <h2 class="text_centered" style="color:#0070ff;">ISPORUCENO</h2>
        </div>
        <?php
        if (count($isporuceni) > 0) {
          $style = 'pregled';
          $isp_curr_ime = $isporuceni[0]['ime'];
          $contact_id_za_ukupno_eura = $isporuceni[0]['kontakt_id'];
          $grad = $upit -> pronadji_iz_kog_grada_je_kontakt($pdo, $isporuceni[0]['kontakt_id']);
          $isporuceni_svi = $upit -> da_li_su_isporuceni_svi_artikli($pdo, $isporuceni[0]['big_order_id'],$isporuceni[0]['kontakt_id']);
          $html_maker -> create_table_header($isporuceni[0], $style, $grad, $isporuceni_svi);
          foreach ($isporuceni as $isp) {
            $isporuceni_svi = $upit -> da_li_su_isporuceni_svi_artikli($pdo, $isp['big_order_id'], $isp['kontakt_id']);
            $grad = $upit -> pronadji_iz_kog_grada_je_kontakt($pdo, $isp['kontakt_id']);
            if (strcmp($isp['ime'], $isp_curr_ime) === 0) {
              $html_maker -> fill_table_row($isp);
            } else {
              $html_maker -> zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda($pdo, $style, $isp, $contact_id_za_ukupno_eura, $grad, $isporuceni_svi);
              $isp_curr_ime = $isp['ime'];
              $contact_id_za_ukupno_eura = $isp['kontakt_id'];
            }
          }
          $html_maker -> zatvaranje_poslednje_tabele($pdo, $style, $isp, $contact_id_za_ukupno_eura);
        }
        ?>
      </div>

      <script src="../../js/jquery-3.6.0.min.js"></script>
      <script src="../../bootstrap/js/bootstrap.min.js"></script>
      <script src="../../js/isporuka.js"></script>
    </body>
    </html>
