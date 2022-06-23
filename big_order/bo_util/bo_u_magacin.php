<?php
require_once "../../util/pdo.php";
session_start();

//*********************POST METHOD UPIS U BAZU ARTIKL_ORDERI**********************************
if (isset($_POST['kolicina']) && isset($_POST['artikl_id'])) {
    $date = date("Y-m-d H:i:s");
    //ako artikl id ne postoji u magacinu prvo ga upisati kao da je porucen od strane trt mrta ali uz komentar i onfa ga upisati i u magacin
    $sql = "INSERT INTO artikl_orderi (big_order_id, kontakt_id,
      artikl_id, kolicina, iz_magacina, ukupna_net, prodajna_cena, komentar,
      pregledano, pregledana_kolicina, datum_pregleda)
    VALUES (:big_order_id, :kontakt_id, :artikl_id, :kolicina, :iz_magacina,
      :ukupna_net, :prodajna_cena, :komentar,
      :pregledano, :pregledana_kolicina, :datum_pregleda
    )";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':big_order_id'       => $_POST['oa_big_order_id'],
      ':kontakt_id'         => $_POST['oa_kontakt_id'],
      ':artikl_id'          => $_POST['artikl_id'],
      ':kolicina'           => $_POST['kolicina'],
      ':pregledana_kolicina'=> $_POST['kolicina'],
      ':datum_pregleda'     => $date,
      ':iz_magacina'        => 0,
      ':pregledano'         => 1,
      ':ukupna_net'         => 0,
      ':prodajna_cena'      => 0,
      ':komentar'           => 'Artikal je greskom poslat'
    ));
    $artikl_order_id = $pdo -> lastInsertId();

    $sql = "INSERT INTO magacin (artikl_id, artikl_order_id, kolicina)
    VALUES (:artikl_id, :artikl_order_id, :kolicina)";
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(array(
      ':artikl_id' => $_POST['artikl_id'],
      //artikl_order_id = 13, jer je 13 oznaka fiktivnog oartikl_order_id, koji sluzi samo za upis artikala koje jos niko nije narucio u bazu
      ':artikl_order_id' => $artikl_order_id,
      ':kolicina' => $_POST['kolicina']));

      $sql = "INSERT INTO bo_isporuke (artikl_order_id, kolicina)
      VALUES (:artikl_order_id, :kolicina)";
      $stmt = $pdo -> prepare($sql);
      $stmt -> execute(array(
        ':artikl_order_id' => $artikl_order_id,
        ':kolicina' => $_POST['kolicina']
      ));

      //*********************SESSION PORUKA U ZAVISNOSTI OD USPEHA POST METODE**********************************
      $_SESSION['success'] = 'Porudbina '.$_POST['oa_big_order_id']."-".$_POST['oa_kontakt_id']."-"
      .$_POST['artikl_id'].' u kolicini od '.$_POST['kolicina'].' je uspešno dodat u bazu';
      header('Location: https://localhost/trt_mrt/big_order/pregled/bo_pregled_interface.php?big_order_id='.$_POST['oa_big_order_id']);
      return;
    }
    ?>

    <!DOCTYPE html>
    <html lang="en" dir="ltr">

    <head>
      <?php require_once "../../util/head.php" ?>
      <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">

    </head>
    <body>
      <?php require_once "../../util/navbar.php" ?>
      <div class="container-fluid">
        <div class="row-fluid">
          <div class="span6">
            <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
            <form class="form-horizontal" method="post">
              <fieldset>
                <legend>Dovanje greškom pristiglog artikla u magacin</legend>

                <label class="control-label" for="odabir_artikla">Izaberite Artikal</label>
                <div class="controls">
                  <input type="text" class="input-xlarge" name="odabir_artikla" id="odabir_artikla" placeholder="Unesite part number" onfocusout="get_artikl_data()">
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


                  <label class="control-label" for="opis">Opis</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" id="opis" name="opis" readonly>
                  </div>

                  <label class="control-label" for="link"><a href=# target="_blank">Link</a></label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" id="link" name="link" readonly>
                  </div>

                  <label class="control-label" for="cena_net">cena_net</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" id="cena_net" name="cena_net" readonly>
                  </div>

                  <label class="control-label" for="cena_list">cena_list</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" id="cena_list" name="cena_list" readonly>
                  </div>

                  <label class="control-label" for="kolicina">Kolicina</label>
                  <div class="controls">
                    <input type="number" class="input-xlarge" name="kolicina" id="kolicina" value=1 step="1">
                  </div>

                  <label class="control-label" for="komentar">Komentar</label>
                  <div class="controls">
                    <textarea class="input-xlarge" name="komentar" id="komentar">Greškom stiglo</textarea>
                  </div>




                  <label class="control-label" for="oa_big_order_id">Big order id</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" name="oa_big_order_id" id="oa_big_order_id" readonly value="<?=$_GET['big_order_id']?>">
                  </div>
                  <label class="control-label" for="oa_kontakt_id">Kontakt id</label>
                  <div class="controls">
                    <input type="text" class="input-xlarge" name="oa_kontakt_id" id="oa_kontakt_id" readonly value="<?=$_GET['kontakt_id']?>">
                  </div>

                  <div class="controls">
                    <button type="reset" class="btn btn_cancel">Reset</button>
                    <button type="submit" class="btn btn-large btn_save">DODAJ U MAGACIN</button>
                  </div>
                </div>
              </fieldset>
            </form>
          </div>
          <div class="span9">
            <!--<iframe src=<?=$a_link?> height="900" width="900" title="Pastorelli"></iframe> -->
          </div>
        </div>
      </div>
      <script src="../../js/jquery-3.6.0.min.js"></script>
      <script src="../../bootstrap/js/bootstrap.min.js"></script>
      <script src="//code.jquery.com/jquery-1.12.4.js"></script>
      <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script src="../../js/order_auto.js"></script>
    </body>
    </html>
