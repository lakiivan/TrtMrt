<?php
require_once "../../util/pdo.php";
require_once "../../util/db.php";
require_once "../bo_util/upit.php";
session_start();

$big_order_id = $_GET['big_order_id'];
$upit         = new Upit();
$db           = new Db();
$nepregledani = $upit -> get_pregled_podaci($pdo, $big_order_id, 0);
$pregledani   = $upit -> get_pregled_podaci($pdo, $big_order_id, 1);

$pregledani_do_sada = count($pregledani);
$ukupno_za_pregledati = $pregledani_do_sada + count($nepregledani);
if ($ukupno_za_pregledati == 0){
  $procenat_pregledanih = 0;
} else {
  $procenat_pregledanih = ($pregledani_do_sada/$ukupno_za_pregledati) * 100;
}

$bar_style = 'style="width: '.$procenat_pregledanih.'%;"';
$bo_status = 'pregledano';
//************************* POST METOD ****************************************
if(isset($_POST['pregledana_kolicina']) && ($_POST['pregledana_kolicina'] != '')
&& isset($_POST['artikl_order_id'])) {
  $boid = $_GET['big_order_id'];
  //provera da li se post poziva iz nepregeldanih ili pregeldanih
  // iz nepregledanih post updateuje tabelu bo_pregled i insertuje u tabelu bo_isporuka
  // iz pregledanih post samo updateuje u obe tabele
  if (strcmp($_POST['action'], 'insert') == 0) {
    //provera da li je pregeldana kolicina jednaka ili manja od porucene kolocine
    if($upit -> is_pk_valid(intval($_POST['narucena_kolicina']), intval($_POST['pregledana_kolicina']))) {
      //update pregelda u tabeli bo_pregledi
      $upit -> dodaj_pregledani_artikl_u_magacin($pdo, $_POST['artikl_id'], $_POST['artikl_order_id'], $_POST['pregledana_kolicina']);
      $upit -> update_ao_pregled($pdo,$_POST[''], $_POST['pregledana_kolicina']);
    } else {
      // $_SESSION['error'] = 'GRESKA - Pregled id - '.$_POST['pregled_id'].' u kolicini od '.$_POST['pregledana_kolicina'].' nije updateovan';
      header('Location: bo_pregled_interface.php?big_order_id='.$boid);
      return;
    }
  }

  if (strcmp($_POST['action'], 'update') == 0) {
    $upit -> update_ao_pregled($pdo,$_POST['artikl_order_id'], $_POST['pregledana_kolicina']);
    $upit -> update_magacin($pdo, $_POST['artikl_order_id'], $_POST['pregledana_kolicina']);
  }

  // $_SESSION['success'] = 'Pregled id - '.$_POST['pregled_id'].' u kolicini od '.$_POST['pregledana_kolicina'].' je uspešno updateovan u tabeli bo_pregledi';
  header('Location: http://localhost/trt_mrt/big_order/pregled/bo_pregled_interface.php?big_order_id='.$boid);
  return;
}


//********************** UTIL HTML FUNCTIONS *************************************

function create_table_header($pp, $style, $grad) {
  //kreiranje prvog diva i tabele u njemu i naslova kolona
  //$pp['stigli_svi'] = false;
  if (strcmp($pp['pregledano'], '0') == 0) {
    $status = '';
  } else {
    $status = pregled_status($pp['stigli_svi']);
  }

  echo'<div class="span4 '.$style.'">';
  echo '<h2 class="'.$status.'">'.$status.' - <a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].' - '.$grad.'</a></h2>';
  echo '<table class="p_table">';
  echo '<tr>';
  echo '<th class="hide">artikl_order_id</th>';
  echo '<th class="hide">artikl_id</th>';
  echo '<th>AO ID</th>';
  echo '<th>PN</th>';
  echo '<th>Opis <span class="glyphicon glyphicon-ok-circle"></span></th>';
  echo '<th> O </th>';
  echo '<th> P </th>';
  echo '<th>OK</th>';
  echo '</tr>';
}

function fill_table_row($pp, $action) {
  //ova funkcija popunjava jedan red tabele sa podacima dobijenim iz bo_pregledi
  //upisivanje n-tog reda podataka istog imena kupca
  echo '<form class="form-horizontal" method="post">';
  echo '<input type"number" name="big_order_id" class="pregled_input pi_aoid hide" value="'.$pp['big_order_id'].'"readonly/>';
  echo '<input type"number" name="action" class="pregled_input pi_aoid hide" value="'.$action.'"readonly/>';
  echo '<tr><td class="hide"><input type"number" name="artikl_id" class="pregled_input pi_pid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<tr><td class="hide"><input type"number" name="artikl_id" class="pregled_input pi_pid" value="'.$pp['artikl_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_aoid"><input type"number" name="artikl_order_id" class="pregled_input pi_aoid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_pn">'.$pp['part_number'].'</td>';
  echo '<td class="pregled border_vis td_opis"><a href="'.$pp['link'].'" target="_blank">'.$pp['opis'].'</a></td>';
  echo '<td class="pregled td_kol"><input type"number" name="narucena_kolicina" class="pregled_input pi_ok" value="'.$pp['ao_kolicina'].'" step="1" size="1" readonly/></td>';
  echo '<td class="pregled td_kol"><input type"number" name="pregledana_kolicina" class="pregled_input pi_pk" value="'.$pp['ao_kolicina'].'" step="1" size="1"/></td>';
  echo '<td class="pregled border_vis"><button type="submit" class="btn"><span class="glyphicon glyphicon-ok-circle"></span>OK</button></td>';
  echo '</tr>';
  echo '</form>';
}

function fill_table_row_pregledani($pp, $action) {
  //ova funkcija popunjava jedan red tabele sa podacima dobijenim iz bo_pregledi
  //upisivanje n-tog reda podataka istog imena kupca
  echo '<form class="form-horizontal" method="post">';
  echo '<input type"number" name="big_order_id" class="pregled_input pi_aoid hide" value="'.$pp['big_order_id'].'"readonly/>';
  echo '<input type"number" name="action" class="pregled_input pi_aoid hide" value="'.$action.'"readonly/>';
  echo '<tr><td class="hide"><input type"number" name="artikl_id" class="pregled_input pi_pid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<tr><td class="hide"><input type"number" name="artikl_id" class="pregled_input pi_pid" value="'.$pp['artikl_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_aoid"><input type"number" name="artikl_order_id" class="pregled_input pi_aoid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_pn">'.$pp['part_number'].'</td>';
  echo '<td class="pregled border_vis td_opis"><a href="'.$pp['link'].'" target="_blank">'.$pp['opis'].'</a></td>';
  echo '<td class="pregled td_kol"><input type"number" name="narucena_kolicina" class="pregled_input pi_ok" value="'.$pp['ao_kolicina'].'" step="1" size="1" readonly/></td>';
  echo '<td class="pregled td_kol"><input type"number" name="pregledana_kolicina" class="pregled_input pi_pk" value="'.$pp['pregledana_kolicina'].'" step="1" size="1"/></td>';
  echo '<td class="pregled border_vis"><button type="submit" class="btn"><span class="glyphicon glyphicon-ok-circle"></span>OK</button></td>';
  echo '</tr>';
  echo '</form>';
}

function zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda($pp, $style, $action, $grad) {
  //stampanje naslova diva ime i big order id
  //echo '<p>'.count($pp).'</p>';
  //$pp['stigli_svi'] = true;
  if (strcmp($pp['pregledano'], '0') == 0) {
    $status = '';
  } else {
    $status = pregled_status($pp['stigli_svi']);
  }
  //echo '<p>'.count($pp).'</p>';
  echo '</table>';
  echo '</div>';
  echo'<div class="span4 '.$style.'">';
  echo '<form class="form-horizontal" method="post">';
  echo '<h2 class="'.$status.'">'.$status.' - <a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].' - '.$grad.'</a></h2>';
  echo '<table class="p_table">';
  echo '<tr>';
  echo '<th class="hide">artikl_order_id</th>';
  echo '<th>AO ID</th>';
  echo '<th>PN</th>';
  echo '<th>opis</th>';
  echo '<th> O </th>';
  echo '<th> P </th>';
  echo '</tr>';
  //upisivanje prvog reda
  echo '<form class="form-horizontal" method="post">';
  echo '<input type"number" name="action" class="pregled_input pi_aoid hide" value="'.$action.'"readonly/>';
  echo '<input type"number" name="big_order_id" class="pregled_input pi_aoid hide" value="'.$pp['big_order_id'].'"readonly/>';
  echo '<tr><td class="hide"><input type"number" name="artikl_order_id" class="pregled_input pi_pid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<tr><td class="hide"><input type"number" name="artikl_id" class="pregled_input pi_pid" value="'.$pp['artikl_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_aoid"><input type"number" name="artikl_order_id" class="pregled_input pi_aoid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_pn ">'.$pp['part_number'].'</td>';
  echo '<td class="pregled border_vis td_opis"><a href="'.$pp['link'].'" target="_blank">'.$pp['opis'].'</a></td>';
  echo '<td class="pregled td_kol"><input type"number" name="narucena_kolicina" class="pregled_input pi_ok" value="'.$pp['ao_kolicina'].'" step="1" size="1" readonly/></td>';
  echo '<td class="pregled td_kol"><input type"number" name="pregledana_kolicina" class="pregled_input pi_pk" value="'.$pp['ao_kolicina'].'" step="1" size="1"/></td>';
  echo '<td class="pregled border_vis"><button type="submit" class="btn">OK</button></td>';
  echo '</tr>';
  echo '</form>';
}

function zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda_pregledani($pp, $style, $action, $grad) {
  //stampanje naslova diva ime i big order id
  //echo '<p>'.count($pp).'</p>';
  //$pp['stigli_svi'] = true;
  $status = pregled_status($pp['stigli_svi']);
  //echo '<p>'.count($pp).'</p>';
  echo '</table>';
  echo '</div>';
  echo'<div class="span4 '.$style.'">';
  echo '<form class="form-horizontal" method="post">';
  echo '<h2 class="'.$status.'">'.$status.' - <a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].' - '.$grad.'</a></h2>';
  echo '<table class="p_table">';
  echo '<tr>';
  echo '<th class="hide">artikl_order_id</th>';
  echo '<th>AO ID</th>';
  echo '<th>PN</th>';
  echo '<th>opis</th>';
  echo '<th> O </th>';
  echo '<th> P </th>';
  echo '</tr>';
  //upisivanje prvog reda
  echo '<form class="form-horizontal" method="post">';
  echo '<input type"number" name="action" class="pregled_input pi_aoid hide" value="'.$action.'"readonly/>';
  echo '<input type"number" name="big_order_id" class="pregled_input pi_aoid hide" value="'.$pp['big_order_id'].'"readonly/>';
  echo '<tr><td class="hide"><input type"number" name="artikl_order_id" class="pregled_input pi_pid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<tr><td class="hide"><input type"number" name="artikl_id" class="pregled_input pi_pid" value="'.$pp['artikl_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_aoid"><input type"number" name="artikl_order_id" class="pregled_input pi_aoid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
  echo '<td class="pregled border_vis td_pn ">'.$pp['part_number'].'</td>';
  echo '<td class="pregled border_vis td_opis"><a href="'.$pp['link'].'" target="_blank">'.$pp['opis'].'</a></td>';
  echo '<td class="pregled td_kol"><input type"number" name="narucena_kolicina" class="pregled_input pi_ok" value="'.$pp['ao_kolicina'].'" step="1" size="1" readonly/></td>';
  echo '<td class="pregled td_kol"><input type"number" name="pregledana_kolicina" class="pregled_input pi_pk" value="'.$pp['pregledana_kolicina'].'" step="1" size="1"/></td>';
  echo '<td class="pregled border_vis"><button type="submit" class="btn">OK</button></td>';
  echo '</tr>';
  echo '</form>';
}

function da_li_tabela_za_kontakt_postoji($ime, $bo_curr_ime) {
  //funckija proverava da li je vec kreiran form za dato ime kupca
  if (strcmp($ime, $bo_curr_ime) == 0) {
    return True;
  } else {
    return False;
  }
}

function zatvaranje_poslednje_tabele() {
  echo '</table>';
  echo '</form>';
  echo '</div>';
}

function pregled_status($stigli_svi) {
  if ($stigli_svi) {
    return 'OK';
  } else {
    return 'NE';
  }
}

  function session_message() {
    if (isset($_SESSION['success'])) {
      echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
      unset($_SESSION['success']);
    } else if(isset($_SESSION['error'])) {
      echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
      unset($_SESSION['error']);
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
            <div class="span4"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/order/order.php?big_order_id=<?=$_GET['big_order_id']?>">ORDER</h1></a></div>
            <div class="span4">
              <h1 class="display-4" id="naslov"><a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>">Big order - <?=$_GET['big_order_id']?>, PREGLED - <?=number_format($procenat_pregledanih,0)?>%</a></h1>
            </div>
            <div class="span3 text_right"><h1 class="display-4"><a href="http://localhost/trt_mrt/big_order/isporuka/bo_isporuka_interface.php?big_order_id=<?=$_GET['big_order_id']?>">ISPORUKA</a></h1></div>

            <div class="row-fluid">
              <div class="span12">
                <div class="progress">
                  <div class="bar" <?=$bar_style?>></div>
                </div>
              </div>
            </div>

            <!-- <?=session_message();?> -->
          </div>
        </div>
        <div class="row-fluid">
          <div class="span6">
            <button class="btn btn-large btn-xxl btn_orange" onclick="location.href='http://localhost/trt_mrt/big_order/bo_util/bo_u_magacin.php?big_order_id=<?=$_GET['big_order_id']?>&kontakt_id=42'">UNESI ARTIKL U MAGACIN</button>
          </div>
          <div class="span6">
            <button class="btn btn-large btn-xxl btn_blue" onclick="location.href='http://localhost/trt_mrt/big_order/pregled/bo_update.php?big_order_id=<?=$_GET['big_order_id']?>&status=pregledano'">POTVRDI KRAJ PREGLEDA</button>
          </div>
        </div>

        <button class="btn btn-large btn_srednji" id="btn_nepregledano" style="color:#fea90d;">NEPREGLEDANO</button>
        <div class="row-fluid">
          <div class="span12" id="div_nepregledano">
            <h2 class="text_centered"  style="color:#fea90d;">NEPREGLEDANO</h2>
            <div class="row-fluid">
              <div></div>
              <?php
              if (count($nepregledani) > 0){
                $action = "insert";
                $style = "nepregled";
                $bo_oznaka = $nepregledani[0]['bo_oznaka'];
                $bo_curr_ime = $nepregledani[0]['ime'];
                $grad =  $db -> pronadji_iz_kog_grada_je_kontakt($pdo, $nepregledani[0]['kontakt_id']);
                $stigli_svi = $upit -> da_li_su_stigli_svi_artikli($pdo, $nepregledani[0]['big_order_id'],$nepregledani[0]['kontakt_id']);
                $nepregledani[0]['stigli_svi'] = $stigli_svi;
                //kreiranje prvog diva i tabele u njemu i naslova kolona
                create_table_header($nepregledani[0], $style, $grad);

                foreach ($nepregledani as $pp) {
                  $stigli_svi = $upit -> da_li_su_stigli_svi_artikli($pdo, $pp['big_order_id'],$pp['kontakt_id']);
                  $pp['stigli_svi'] = $stigli_svi;
                  if (strcmp($bo_curr_ime, $pp['ime']) == 0){
                    //upisivanje n-tog reda podataka istog imena kupca
                    fill_table_row($pp, $action);
                  } else {
                    //zatvarnje aktuelne tabele i otvranja nove uz upis prvog reda podataka
                    $grad =  $db-> pronadji_iz_kog_grada_je_kontakt($pdo, $pp['kontakt_id']);
                    zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda($pp, $style, $action, $grad);
                    $bo_curr_ime = $pp['ime'];
                  }
                }
                zatvaranje_poslednje_tabele();
              }
              ?>

            </div>
          </div>
        </div>

        <hr>

        <button class="btn btn-large btn_srednji" id="btn_pregledano" style="color:#0070ff;">PREGLEDANO</button>
        <div class="row-fluid" id="div_pregledano">
          <div class="span12">
            <h2 class="text_centered" style="color:#0070ff;">PREGLEDANO</h2>
            <div class="row-fluid">
              <div></div>
              <?php
              if (count($pregledani) > 0) {
                $action = "update";
                $style = 'pregled';
                $p_curr_ime = $pregledani[0]['ime'];
                $grad =  $db -> pronadji_iz_kog_grada_je_kontakt($pdo, $pregledani[0]['kontakt_id']);
                $stigli_svi = $upit -> da_li_su_stigli_svi_artikli($pdo, $pregledani[0]['big_order_id'], $pregledani[0]['kontakt_id']);
                $pregledani[0]['stigli_svi'] = $stigli_svi;
                create_table_header($pregledani[0], $style, $grad);
                foreach ($pregledani as $p) {

                  if (da_li_tabela_za_kontakt_postoji($p['ime'], $p_curr_ime)) {
                    fill_table_row_pregledani($p, $action);
                  } else {
                    $stigli_svi = $upit -> da_li_su_stigli_svi_artikli($pdo, $p['big_order_id'],$p['kontakt_id']);
                    $p['stigli_svi'] = $stigli_svi;
                    $grad =  $db -> pronadji_iz_kog_grada_je_kontakt($pdo, $p['kontakt_id']);
                    zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda_pregledani($p, $style, $action, $grad);
                    $p_curr_ime = $p['ime'];
                  }
                }
                zatvaranje_poslednje_tabele();
              }
              ?>
            </div>
          </div>
        </div>

        <script src="../../js/jquery-3.6.0.min.js"></script>
        <script src="../../bootstrap/js/bootstrap.min.js"></script>
        <script src="../../js/pregled.js"></script>
      </body>
      </html>
