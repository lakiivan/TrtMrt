<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";
require_once "../bo_data.php";
require_once "../../util/db.php";
require_once "../../util/html_maker_table.php";

$upit         = new Upit();
$db           = new Db();
$table_maker  = new HtmlMakerTable();
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
  if (isset($_POST['artikl_id']) && strcmp($_POST['artikl_id'], '') !== 0) {
    header('Location: http://localhost/trt_mrt/big_order/porucivanje/artikl_order_form.php?kontakt_id='.$_GET['kontakt_id'].'&big_order_id='.$_GET['big_order_id'].'&artikl_id='.$_POST['artikl_id']);
    return;
  }

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
              <h3>Kupac - <?=$k_ime?> - grad - <?=$grad?></h3>
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
              <hr></hr>
              <div class="row-fluid">
                <div class="span6">
                  <div class="controls">
                    <button class="btn btn-large btn_nov" id="btn_nov_artikal" onclick="location.href='https://localhost/trt_mrt/artikli/artikl_form.php?big_order_id=<?=$_GET['big_order_id']?>&kontakt_id=<?=$_GET['kontakt_id']?>'">Nov Artikal</button>
                  </div>
                  <form class="form-horizontal" method="post">
                    <fieldset>
                      <legend>Dodavanje Artikla u Porudžbinu Kupca</legend>

                      <label class="control-label" for="odabir_artikla">Izaberite Artikal</label>
                      <div class="controls">
                        <input type="text" class="input-xlarge" name="odabir_artikla" id="odabir_artikla" placeholder="Unesite part number" onfocusout="get_artikl_data2()">
                      </div>
                      <div class="control-group">
                        <label class="control-label" for="artikl_id">ID</label>
                        <div class="controls">
                          <input type="text" class="input-xlarge" name="artikl_id" id="artikl_id" readonly>
                        </div>
                        <label class="control-label" for="part_number">Part Number</label>
                        <div class="controls">
                          <input type="text" class="input-xlarge" name="part_number" id="part_number" readonly>
                        </div>
                        <div class="controls">
                          <button type="reset" class="btn btn_cancel">Reset</button>
                          <button type="submit" class="btn btn-large btn_save">DODAJ U Porudžbinu</button>
                        </div>
                    </fieldset>
                  </form>
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
