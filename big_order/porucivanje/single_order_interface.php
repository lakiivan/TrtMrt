<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";
require_once "../bo_util/html_maker.php";
require_once "../bo_data.php";
require_once "../../util/db.php";
require_once "../../util/html_maker_table.php";

$upit         = new Upit();
$db           = new Db();
$table_maker  = new HtmlMakerTable();
$html_maker   = new HtmlMaker();
$kontakt_id   = $_GET['kontakt_id'];
$big_order_id = $_GET['big_order_id'];
$artikl_orderi= $db -> get_bo_artikl_orderi($pdo, $big_order_id, $kontakt_id);
$artikli      = $db -> get_artikli($pdo);
$kontakt_stat = $db -> statistika_kontakta($pdo, $kontakt_id);
$kontakt_data = $db -> get_kontakt_info($pdo, $kontakt_id);
$k_ime        = $kontakt_data['ime'];

$artikli_na_stanju    = $db   -> get_magacin_data($pdo);
$ukupno_naplaceno     = $upit -> kolika_je_ukupna_zarada_po_kontaktu($pdo, $kontakt_stat['kontakt_id']);
$ukupna_zarada        = $kontakt_stat['ukupna_list'] - $kontakt_stat['ukupna_net'];
$grad                 = $upit -> pronadji_iz_kog_grada_je_kontakt($pdo, $kontakt_id);
$datum_last_big_order = $upit -> get_datum_last_big_order($pdo, $kontakt_id);

$broj_artikala_sada   = $upit -> broj_porucenih_artikala_kontakta_u_ovoj_porudzbini($pdo, $big_order_id, $kontakt_id);
$ukupna_net_sada      = $upit -> ukupna_net_kontakta_u_ovoj_porudzbini($pdo, $big_order_id, $kontakt_id);
$ukupna_list_sada     = $upit -> ukupna_prodajna_kontakta_u_ovoj_porudzbini($pdo, $big_order_id, $kontakt_id);

if($broj_artikala_sada == 0) {
  $prosecna_cena_artikla = 0;
} else {
  $prosecna_cena_artikla = $ukupna_list_sada / $broj_artikala_sada;
}

$ukupna_trenutna_zarada = $ukupna_list_sada - $ukupna_net_sada;
$prosecna_cena_artikla  = number_format($prosecna_cena_artikla, 2);

//statusi icona
$status_korpa       = get_icon_korpa_status($artikl_orderi);
$status_order       = get_icon_status($artikl_orderi, "kolicina", "pregledana_kolicina");
$status_pregled     = get_icon_status($artikl_orderi, "pregledana_kolicina", "isporucena_kolicina");
//$status_isporuka    = get_icon_status($artikl_orderi, "isporucena_kolicina", "naplaceno");
//$status_naplaceno   = get_icon_status($artikl_orderi, "naplaceno", "naplaceno");

session_start();

$last_status = get_bo_last_status($pdo, $boid);
function get_bo_last_status($pdo, $big_order_id) {
  //funkcija vraca psolednji status za trzani big order
  $stmt = $pdo -> prepare ("SELECT status
    FROM bo_statusi
    WHERE big_order_id = :boid
    ORDER BY id DESC");
    $stmt -> execute(array(
      ':boid' => $_GET['big_order_id']
    ));
    $bo_statusi = $stmt -> fetchAll(PDO::FETCH_ASSOC);

    return htmlentities($bo_statusi[0]['status']);
  }

  //post


  //*********************  UTIL FUNCTIONS ****************************************
  function session_message() {
    if (isset($_SESSION['success'])) {
      echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
      unset($_SESSION['success']);
    } else if(isset($_SESSION['error'])) {
      echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
      unset($_SESSION['error']);
    }
  }

  function create_ao($artikl_order, $num) {
    $aoid                 = htmlentities($artikl_order["aoid"]);
    $kontakt_id           = htmlentities($artikl_order["kontakt_id"]);
    $big_order_id         = htmlentities($artikl_order["big_order_id"]);
    $part_number          = htmlentities($artikl_order["part_number"]);
    $opis                 = htmlentities($artikl_order["opis"]);
    $validno              = htmlentities($artikl_order["validno"]);
    $pregledano           = htmlentities($artikl_order["pregledano"]);
    $isporuceno           = htmlentities($artikl_order["isporuceno"]);
    $naplaceno            = htmlentities($artikl_order["naplaceno"]);
    $porucena_kolicina    = htmlentities($artikl_order["kolicina"]);
    $cena                 = htmlentities($artikl_order["cena"]);
    $pregledana_kolicina  = htmlentities($artikl_order["pregledana_kolicina"]);
    $isporucena_kolicina  = htmlentities($artikl_order["isporucena_kolicina"]) ;
    $id_por_kol           = "porucenaKolicina_".strval($num);
    $id_pre_kol           = "pregledanaKolicina_".strval($num);
    $id_isp_kol           = "isporucenaKolicina_".strval($num);
    $id_pregledano        = "pregledano_".strval($num);
    $id_isporuceno        = "isporuceno_".strval($num);
    $id_naplaceno         = "naplaceno_".strval($num);
    $id_isp_default       = "isporucenaDefault_".strval($num);
    $id_cena              = "cena_".strval($num);
    $id_ukupna_cena       = "ukupnaCena_".strval($num);
    echo'<div class="container">';
    echo '<form class="form-horizontal" method="post">';
    //echo '<h2 class="'.$status.'">'.$status.' - <a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].' - '.$grad.'</a></h2>';
    echo '<table class="small_table">';
    echo '<tr>';
    echo '<th class="small_th hide">'.$aoid.'</th>';
    echo '<th class="small_th hide"><input type"number" id="'.$id_pregledano.'" name="pregledano" class="pregled_input pi_ok" value="'.$pregledano.'"readonly/></th>';
    echo '<th class="small_th hide"><input type"number" id="'.$id_isporuceno.'" name="isporuceno" class="pregled_input pi_ok" value="'.$isporuceno.'"readonly/></th>';
    echo '<th class="small_th hide"><input type"number" id="'.$id_naplaceno.'" name="naplaceno" class="pregled_input pi_ok" value="'.$naplaceno.'"readonly/></th>';
    echo '<th class="medium_th"><p id="'.$id_cena.'">'.$cena.'</p></td>';
    echo '<th class="medium_th"><a href="http://localhost/trt_mrt/big_order/porucivanje/artikl_order_form_edit.php?artikl_order_id='.$aoid.'&big_order_id='.$big_order_id.'&kontakt_id='.$kontakt_id.'">'.$part_number.'</th>';
    echo '<th class="large_th">'.$opis.'</th>';
    echo '<td class="pregled td_kol"><input type"number" id="'.$id_por_kol.'" name="cena" class="pregled_input pi_ok" value="'.$porucena_kolicina.'" step="1" size="1" readonly/></td>';
    echo '<td class="pregled td_kol"><input type"number" id="'.$id_pre_kol.'" name="pregledana_kolicina" class="pregled_input pi_ok hide" value="'.$pregledana_kolicina.'" step="1" size="1"/></td>';
    echo '<td class="pregled td_kol"><input type"number" id="'.$id_isp_kol.'" name="isporucena_kolicina" class="pregled_input pi_pk hide" onkeyup="calc_cena('.$id_isp_kol.')" value="'.$isporucena_kolicina.'" step="1" size="1"/></td>';
    echo '<th class="small_th"><p id="'.$id_ukupna_cena.'">0.0</p></th>';
    echo '<td class="pregled border_vis hide"><button type="submit" class="btn"><span class="glyphicon glyphicon-ok-circle"></span>OK</button></td>';
    echo '</tr>';
    echo '</table>';
    echo '</form>';
    echo '</div>';
  }

  function get_icon_korpa_status($artikl_orderi) {
    $status = "icon_hide";
    foreach ($artikl_orderi as $artikl_order) {
      if(intval($artikl_order['poruceno']) === 0) {
        $status = "";
      }
    }
    return $status;
  }

  function get_icon_status($artikl_orderi, $kol1, $kol2) {
    $sum_kol1 = 0;
    $sum_kol2 = 0;
    foreach ($artikl_orderi as $artikl_order) {
      $sum_kol1 += intval($artikl_order[$kol1]);
      $sum_kol2 += intval($artikl_order[$kol2]);
    }

    if($sum_kol1 > $sum_kol2) {
      return "";
    } else {
      return "icon_hide";
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
          <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
          <?=session_message();?>
          <div class="row-fluid">
            <div class="span12 so_interface">
              <h2>Br. Porudžbine <a href="http://localhost/trt_mrt/big_order/big_order_interface.php?big_order_id=<?=$_GET['big_order_id']?>"><?=$bo_oznaka?></a> - Status - <?=$last_status?></h2>
              <h3><?=$k_ime?> - <?=$grad?></h3>

              <div class="row-fluid">
                <div class="span4 borderasi">
                  <p>LEVA POLOVINA</p>
                  <div class="row-fluid">
                    <div class="span5">
                      <h3>Istorija</h3>
                      <div class="row-fluid">
                        <div class="span6">
                          <p>Ukupno porudzbina</p>
                        </div>
                        <div class="span5 bolder">
                          <p><?=$kontakt_stat['ukupno_porudzbina']?></p>
                        </div>
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


                    <div class="span6 so_trenutno_stanje">
                      <h3>Trenutno stanje</h3>
                      <div class="row-fluid">
                        <div class="span6">
                          <p>Prosecna cena artikla</p>
                        </div>
                        <div class="span5 bolder">
                          <p><?=$prosecna_cena_artikla?></p>
                        </div>
                      </div>
                      <div class="row-fluid">
                        <div class="span6">
                          <p>ZARADA </p>
                        </div>
                        <div class="span5 bolder">
                          <p><?=number_format($ukupna_trenutna_zarada, 1)?></p>
                        </div>
                      </div>
                      <div class="row-fluid">
                        <div class="span6">
                          <p>Ukupno List</p>
                        </div>
                        <div class="span5 bolder">
                          <p><?=number_format($ukupna_list_sada, 1)?></p>
                        </div>
                      </div>
                      <div class="row-fluid">
                        <div class="span6">
                          <p>Ukupno Net</p>
                        </div>
                        <div class="span5 bolder">
                          <p><?=number_format($ukupna_net_sada, 1)?></p>
                        </div>
                      </div>
                      <div class="row-fluid">
                        <div class="span6">
                          <p>Ukupno Artikala</p>
                        </div>
                        <div class="span5 bolder">
                          <p><?=$broj_artikala_sada?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row-fluid">
                    <div class="span12 so_interface_magacin">
                      <div class="row">
                        <div class="span9">
                          <h2>MAGACIN STANJE Artikala</h2>
                          <input type="text" id="search_mpn" title="Unesite ime"
                          onkeyup="search_table('search_mpn','tabela_magacin_artikli',3)" placeholder = "Pronađi artikl po part numberu...">

                          <input type="text" id="search_mopis" title="Unesite ime"
                          onkeyup="search_table('search_mopis','tabela_magacin_artikli',4)" placeholder = "Pronađi artikl po opisu...">

                          <?php $table_maker -> create_table_stanje_magacina_so($artikli_na_stanju, $big_order_id, $kontakt_id) ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="span7 borderasi">
                  <p>DESNA POLOVINA
                    <img src="https://localhost/trt_mrt/images/grocery-basket-icon-3.png" alt="Korpa" class="icon_s <?=$status_korpa?>">
                    <img src="https://localhost/trt_mrt/images/list.png" alt="Order" class="icon_s <?=$status_order?>">
                    <img src="https://localhost/trt_mrt/images/search3.png" alt="Pregled" class="icon_s <?=$status_pregled?>">
                    <img src="https://localhost/trt_mrt/images/send.png" alt="Isporuka" class="icon_s <?=$status_isporuka?>">
                    <img src="https://localhost/trt_mrt/images/paid.jpg" alt="Naplata" class="icon_s icon_hide" id="naplata">
                  </p>
                  <div class="row-fluid">
                    <div class="controls">
                      <button class="btn btn-large btn_nov" id="btn_nov_artikal" onclick="location.href='https://localhost/trt_mrt/artikli/artikl_form.php?big_order_id=<?=$_GET['big_order_id']?>&kontakt_id=<?=$_GET['kontakt_id']?>'">Nov Artikal</button>
                    </div>
                    <legend>
                      Dodavanje Artikla u Porudžbinu Kupca
                    </legend>

                    <div clas="table_wrapper">
                      <button class="btn btn_cancel" onclick="reset_odabir_artikla()">Reset</button>
                      <input type="text" class="input-xlarge" name="odabir_artikla" id="odabir_artikla" placeholder="Unesite part number" onfocusout="get_artikl_data2()">
                    </div>
                    <button class="btn btn-large btn-xxl btn_orange" id="btn_nov_artikal" onclick="go_to_ao_form()">DODAJ u Porudžbinu</button>
                    <input type="text" class="input-xlarge hide" name="artikl_id" id="artikl_id" readonly>
                    <input type="text" class="input-xlarge hide" name="part_number" id="part_number" readonly>
                    <input type="text" class="input-xlarge hide" name="big_order_id" id="big_order_id" value="<?=$_GET['big_order_id']?>" readonly>
                    <input type="text" class="input-xlarge hide" name="kontakt_id" id="kontakt_id" value="<?=$_GET['kontakt_id']?>" readonly>

                  </div>
                  <hr></hr>
                  <div class="row-fluid">
                    <div class="span6">
                      <button class="btn btn-large btn-xxl btn_orange <?=$status_pregled?>" id="button_p" onclick="pregled()">PREGLED</button>
                    </div>
                    <div class="span5">
                      <button class="btn btn-large btn-xxl btn_blue hide <?=$status_isporuka?>" id="button_in" onclick="isporuka(true)">ISPORUKA i NAPLATA</button>
                    </div>
                    <div class="span5">
                      <button class="btn btn-large btn-xxl btn_blue hide  <?=$status_isporuka?>" id="button_i" onclick="isporuka(false)">ISPORUKA</button>
                    </div>
                    <div class="span6">
                      <button class="btn btn-large btn-xxl btn_blue hide" id="button_n" onclick="naplata()">NAPLATA</button>
                    </div>
                  </div>
                  <!-- Ovde dolazi pravljenje divova sa trenutnim porudzbinama -->
                  <div class="borderasi male_proudzbine">
                    <form class="form-horizontal" method="post">
                      <fieldset>
                        <legend>
                          Trnutno Poruceni Artikli
                        </legend>
                        <?php
                        $num = 0;
                        if($artikl_orderi > 0) {
                          foreach ($artikl_orderi as $artikl_order) {
                            create_ao($artikl_order, $num);
                            $num += 1;
                          }
                        }
                        ?>
                        <div class="row-fluid">
                          <div class="span6">
                        <button type="submit" class="btn btn-large btn-xxl btn_blue">UPDATE</button>
                      </div>
                      </div>
                      </fieldset>
                    </form>
                  </div>
                  <div>
                    <h3 id="cena_sum"></h3>
                  </div>

                </div>
              </div>

            </div>
          </div>

          <div class="row-fluid">
            <div class="span12 so_interface">
              <h3>TRENUTNO PORUČENO</h3>
              <?= $table_maker -> create_table_bo_artikl_orderi($artikl_orderi, $big_order_id, $kontakt_id)?>
            </div>
          </div>
        </div>

        <hr>

        <div class="container-fluid">

        </div>

      </div>
    </div>
    <script src="../../js/jquery-3.6.0.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="../../js/so_order_auto.js"></script>
    <script src="../../js/search.js"></script>
  </body>
  </html>
