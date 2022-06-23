<?php
require_once "../../util/pdo.php";
require_once "../../util/db.php";
require_once "../bo_util/upit.php";
require_once "../../util/html_maker_form.php";
session_start();

$db                 = new Db();
$upit               = new Upit();
$form_maker         = new HtmlMakerForm();
$artikl_order_id    = $_GET['artikl_order_id'];

$ao_form_data       = $db -> get_ao_form_data($pdo, $artikl_order_id);
$artikl_id          = $ao_form_data['artikl_id'];
$kontakt_id         = $_GET['kontakt_id'];
// UCITAVANJE PODATAKA VEZANIH ZA ARTIKAL
$artikl_data        = $db ->get_artikl_data($pdo, $artikl_id);

$popust             = $db -> get_kontaktov_popust($pdo, $kontakt_id);


//*********************GET METHOD***********************************************
//prikupljanje podataka iz baza artikl_orderi

$artikl_order_id      = htmlentities($ao_form_data['aoid']);
$big_order_id         = htmlentities($ao_form_data['aoboid']);
$kontakt_id           = htmlentities($ao_form_data['aokid']);
$artikl_id            = htmlentities($ao_form_data['artikl_id']);
$kolicina             = htmlentities($ao_form_data['kolicina']);
$isporucena_kolicina  = htmlentities($ao_form_data['isporucena_kolicina']);
$ukupna_net           = htmlentities($ao_form_data['ukupna_net']);
$prodajna_cena        = htmlentities($ao_form_data['prodajna_cena']);
$datum_porudzbine     = htmlentities($ao_form_data['datum_porudzbine']);
$datum_modifikovanja  = htmlentities($ao_form_data['datum_modifikovanja']);
$komentar             = htmlentities($ao_form_data['komentar']);
$validno              = htmlentities($ao_form_data['validno']);
$isporuceno           = htmlentities($ao_form_data['isporuceno']);
$part_number          = htmlentities($ao_form_data['part_number']);
$opis                 = htmlentities($ao_form_data['opis']);
$link                 = htmlentities($ao_form_data['link']);
$cena_net             = htmlentities($ao_form_data['cena_net']);
$cena_list            = htmlentities($ao_form_data['cena_list']);
$part_number          = htmlentities($ao_form_data['part_number']);
$a_komentar           = htmlentities($ao_form_data['a_komentar']);
$ime                  = htmlentities($ao_form_data['ime']);

if(strcmp($isporuceno, "1") == 0 || strcmp($validno, "0") == 0 ) {
  $dugme_class = "hide";
}

//*********************POST METHOD UPDATE U BAZI ARTIKL_ORDERI**********************************
  //ako je post method regularan, obican samo azuriramo novo stanje artikl order id u bazi artikl orderi
  if (isset($_POST['kolicina']) && isset($_POST['popust']) && isset($_POST['konacna_cena'])) {
    echo"POST STARTED";

    if(strcmp($_POST['action'], 'update') == 0) {
      //proveri da li je postavljno da roba nije isporucena. ako je isporuceno = 0 postavi i da
      // je isporucena kolicina jendaka 0
      if(strcmp($_POST['isporuceno'], '0') == 0) {
        $isporucena_kolicina = 0;
      } else {
        $isporucena_kolicina = $_POST['isporucena_kolicina'];
      }

      $sql2 = "UPDATE artikl_orderi
      SET big_order_id =:big_order_id, kontakt_id =:kontakt_id,
      artikl_id =:artikl_id, kolicina =:kolicina, validno = :validno,
      isporuceno =:isporuceno, isporucena_kolicina =:isporucena_kolicina,
      ukupna_net =:ukupna_net, prodajna_cena =:prodajna_cena, komentar = :komentar
      WHERE artikl_order_id =:artikl_order_id";
      $stmt2 = $pdo -> prepare($sql2);
      $stmt2 -> execute(array(
        ':artikl_order_id'      => $_POST['artikl_order_id'],
        ':big_order_id'         => $_POST['big_order_id'],
        ':kontakt_id'           => $_POST['kontakt_id'],
        ':artikl_id'            => $_POST['artikl_id'],
        ':validno'              => $_POST['validno'],
        ':kolicina'             => $_POST['kolicina'],
        ':isporuceno'           => $_POST['isporuceno'],
        ':isporucena_kolicina'  => $isporucena_kolicina,
        ':ukupna_net'           => $_POST['ukupna_net'],
        ':prodajna_cena'        => $_POST['konacna_cena'],
        ':komentar'             => $_POST['komentar']
      ));
  }

  if(strcmp($_POST['action'], 'delete') == 0) {
    require_once "order_artikl_form_delete.php";
  }

  if(strcmp($_POST['action'], 'trtmrt') == 0) {
    //post nije regularan necemo ubaciti nov artikl order id za novu porudzbinu,
    //vec vemo postojecu porudzbinu koja je vec stigla i pregledana staviti u vlasnistvo trt MRTA
    //proglasavanjem starog aoid nevalidnim i kreriranjem novog aoid na trtmrt za ovaj artikal u naznacenoj kolicini
    $upit -> prebaci_u_vlasnistvo_trtmrta($pdo, $_GET['artikl_order_id'], $_POST['big_order_id'], $_POST['artikl_id'], $_POST['kolicina']);
  }

  //*********************SESSION PORUKA U ZAVISNOSTI OD USPEHA POST METODE**********************************
  $_SESSION['success'] = 'Porudbina '.$_POST['big_order_id']."-".$_POST['kontakt_id']."-"
  .$_POST['artikl_id'].' u kolicini od '.$_POST['kolicina'].' je uspešno azuiriran u bazi';
  header('Location: single_order_interface.php?kontakt_id='.$_POST['kontakt_id'].'&big_order_id='.$_POST['big_order_id']);
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
      <div class="span3">
        <div class="span2">
          <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
        </div>
        <div class="span2">
          <!--
          <button class="btn btn-large bo_interface" id="order_reset" onclick="location.href='http://localhost/trt_mrt/big_order/porucivanje/order_artikl_form_edit.php?big_order_id=<?=$big_order_id?>&kontakt_id=<?=$kontakt_id?>&artikl_id=<?=$artikl_id?>'">RESET</button>
        -->
      </div>
      <form class="form-horizontal" method="post">
        <button type="submit" class="btn btn-large btn_save <?=$dugme_class?>" id="btn_trtmrt">Prebaci u Trt Mrt</button>
        <fieldset>
          <legend>Dodajte artikl u porudžbinu</legend>
          <div class="control-group">
            <label class="control-label" for="artikl_order_id">Artikl order ID</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="artikl_order_id"
              id="artikl_order_id" value=<?=$artikl_order_id?> readonly>
            </div>
            <label class="control-label" for="artikl_id">Artikl ID</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="artikl_id"
              id="artikl_id" value=<?=$artikl_id?> readonly>
            </div>

            <label class="control-label" for="part_number">Part Number</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="part_number" id="part_number" readonly value=<?=$part_number?> >
            </div>

            <label class="control-label" for="opis">Opis</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="opis" name="opis"  readonly value=<?=$opis?>>
            </div>

            <label class="control-label" for="link"><a href=<?=$link?> target="_blank">Link</a></label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="link" name="link"  readonly value=<?=$link?>>
            </div>

            <label class="control-label" for="cena_net">cena_net</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="cena_net" name="cena_net" readonly value=<?=$cena_net?> >
            </div>

            <label class="control-label" for="cena_list">cena_list</label>
            <div class="controls">
              <input type="text" class="input-xlarge" id="cena_list" name="cena_list" readonly value=<?=$cena_list?> >
            </div>

            <label class="control-label" for="big_order_id">Big order id</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="big_order_id" id="big_order_id" readonly value="<?=$_GET['big_order_id']?>">
            </div>
            <label class="control-label" for="kontakt_id">Kontakt id</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="kontakt_id" id="kontakt_id" readonly value="<?=$_GET['kontakt_id']?>">
            </div>
            <label class="control-label" for="kolicina">Kolicina</label>
            <div class="controls">
              <input type="number" class="input-xlarge" name="kolicina" id="kolicina" value=<?=$kolicina?> step="1" onkeyup="calc_popust()" onchange="calc_popust()">
            </div>
            <label class="control-label" for="isporuceno">Isporuceno</label>
            <div class="controls">
              <input type="number" class="input-xlarge" name="isporuceno" id="isporuceno" value=<?=$isporuceno?> step="1" onkeyup="calc_popust()" onchange="calc_popust()">
            </div>
            <label class="control-label" for="isporucena_kolicina">Isp Kol</label>
            <div class="controls">
              <input type="number" class="input-xlarge" name="isporucena_kolicina" id="isporucena_kolicina" value=<?=$isporucena_kolicina?> step="1" onkeyup="calc_popust()" onchange="calc_popust()">
            </div>
            <label class="control-label" for="popust">Popust, %</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="popust" id="popust" value=<?=$popust?> onkeyup="calc_popust()">
            </div>
            <label class="control-label" for="ukupna_net">Ukupna net cena</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="ukupna_net" id="ukupna_net" value=<?=$ukupna_net?> readonly>
            </div>
            <label class="control-label" for="konacna_cena">Konačna cena</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="konacna_cena" id="konacna_cena" value=<?=$prodajna_cena?>>
            </div>
            <label class="control-label" for="validno">Validno</label>
            <div class="controls">
              <select id="validno" name="validno" class="dropdownlist">
                <option value="1">DA</option>
                <option value="0">NE</option>
              </select>
            </div>
            <label class="control-label" for="komentar">Komentar</label>
            <div class="controls">
              <textarea class="input-xlarge" name="komentar" id="komentar"><?=$komentar?></textarea>
            </div>

            <label class="control-label " for="action">Akcija</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="action" id="action" value="update" readonly>
            </div>

            <div class="controls">
              <button type="submit" class="btn" id="btn_delete">IZBRIŠI</button>
              <button type="submit" class="btn btn-large btn_save" id="btn_update">AŽURIRAJ</button>
            </div>
          </div>
        </fieldset>
      </form>
    </div>
    <div class="span9">
      <div class="container">
      <iframe src=<?=$link?> height="800" width="800" title="Pastorelli"></iframe>
    </div>
    </div>
  </div>
</div>
<script src="../../js/jquery-3.6.0.min.js"></script>
<script src="../../bootstrap/js/bootstrap.min.js"></script>
<script src="../../js/order_artikl.js"></script>
</body>
</html>
