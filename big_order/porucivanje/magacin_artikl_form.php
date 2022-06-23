<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";
require_once "../../util/db.php";
require_once "../../util/html_maker_form.php";

$form_maker   = new HtmlMakerForm();
$db           = new Db();

$artikl_id    = $_GET['artikl_id'];
$big_order_id = $_GET['big_order_id'];
$kontakt_id   = $_GET['kontakt_id'];

session_start();

$artikl = $db -> get_artikl_data($pdo, $artikl_id);
$popust = $db -> get_kontaktov_popust($pdo, $kontakt_id);
$max_kol= $db -> get_magacin_max_kol($pdo, $artikl_id);

$upit = new Upit();

//*********************POST METHOD UPIS U BAZU ARTIKL_ORDERI**********************************
if (isset($_POST['kolicina']) && isset($_POST['popust']) && isset($_POST['konacna_cena'])) {
  echo"POST STARTED";
  $sql = "INSERT INTO artikl_orderi (big_order_id, kontakt_id,
    artikl_id, kolicina, pregledano, pregledana_kolicina,
    iz_magacina, ukupna_net, prodajna_cena, komentar)
    VALUES (:big_order_id, :kontakt_id, :artikl_id, :kolicina,
      :pregledano, :pregledana_kolicina,
      :iz_magacina, :ukupna_net, :prodajna_cena, :komentar)";
      $stmt = $pdo -> prepare($sql);
      $stmt -> execute(array(
        ':big_order_id' => $_POST['big_order_id'],
        ':kontakt_id' => $_POST['kontakt_id'],
        ':artikl_id' => $_POST['artikl_id'],
        ':kolicina' => $_POST['kolicina'],
        ':iz_magacina' => 1,
        ':pregledano' => 1,
        ':pregledana_kolicina' => $_POST['kolicina'],
        ':ukupna_net' => $_POST['ukupna_net'],
        ':prodajna_cena' => $_POST['konacna_cena'],
        ':komentar' => $_POST['komentar']
      ));

      $artikl_order_id = $pdo -> lastInsertId();

      //*************************BRISANJE SA STANJA MAGACINA****************************************
      $upit -> skini_sa_stanja($pdo, $_POST['artikl_id'], intval($_POST['kolicina']));

      //****************UBACI U MAGACIN SA NOVIM AOID********************************************
      $upit -> insert_aoid_u_magacin($pdo, $artikl_order_id, $_POST['artikl_id'], $_POST['kolicina']);

      //*********************SESSION PORUKA U ZAVISNOSTI OD USPEHA POST METODE**********************************
      $_SESSION['success'] = 'Porudbina '.$_POST['big_order_id']."-".$_POST['kontakt_id']."-"
      .$_POST['artikl_id'].' u kolicini od '.$_POST['kolicina'].' je uspešno dodat u bazu';
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
            <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
            <form class="form-horizontal" method="post">
              <fieldset>
                <legend>Dodajte artikl u porudžbinu</legend>
                <div class="control-group">
                  <label class="control-label" for="artikl_id">ID</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" name="artikl_id"
                    id="artikl_id" value=<?=$artikl_id?> readonly>
                  </div>
                  <div class="row-fluid">
                    <div class="span12">
                      <?php
                      $form_maker -> create_fieldset_artikl_form($artikl, "readonly");
                      ?>

                    </div>
                  </div>
                  <div class="row-fluid">
                    <div class="span12">
                      <?php
                      $form_maker -> create_fieldset_artikl_order_magacin_form($big_order_id, $kontakt_id, $popust, $max_kol);
                      ?>
                    </div>
                  </div>
                </div>
              </fieldset>
            </form>
          </div>
          <div class="span9">
            <iframe src=<?=$artikl['link']?> height="900" width="900" title="Pastorelli"></iframe>
          </div>
        </div>
      </div>
      <script src="../../js/jquery-3.6.0.min.js"></script>
      <script src="../../bootstrap/js/bootstrap.min.js"></script>
      <script src="../../js/order_artikl.js"></script>
    </body>
    </html>
