<?php
require_once "../util/pdo.php";
require_once "finansije_upiti.php";
session_start();
$upit = new FinansijeUpiti();

//************ POST METOD ******************************
if (isset($_POST['opis']) && isset($_POST['trosak_eura'])) {
  $datum = date("Y-m-d H:i:s");
  $sql = "INSERT INTO rashodi (big_order_id, opis, trosak_eura, datum_rashoda)
  VALUES (:big_order_id, :opis, :trosak_eura, :datum_rashoda)";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':big_order_id' => $_POST['big_order_id'],
    ':opis' => $_POST['opis'],
    ':trosak_eura' => $_POST['trosak_eura'],
    ':datum_rashoda' => $datum
  ));
  $_SESSION['success'] = 'Rashod '.$_POST['opis'].' u iznosu od '.$_POST['trosak_eura'].' je uspešno dodat u bazu';
  header('Location: finansije.php');
  return;
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php require_once "../util/navbar.php" ?>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <p><a href="#" onclick="history.go(-1)"><h3>Odustani</h3></a></p>
        <h2>RASHOD</h2>
        <form class="form-horizontal" method="post">
          <fieldset>
            <legend>Unesite nov rashod</legend>
            <div class="control-group">

              <label class="control-label" for="big_order_id">Big Order Id</label>
              <div class="controls">
                <select id="big_order_id" name="big_order_id" class="dropdownlist">
                  <option value=""></option>
                  <?php
                  $big_orderi = $upit -> get_svi_big_orderi($pdo);
                  for ($i=0; $i<count($big_orderi); $i++) {
                    ?>
                    <option value="<?=intval($big_orderi[$i]['big_order_id'])?>"><?=$big_orderi[$i]['big_order_id']?> - <?=$big_orderi[$i]['oznaka']?></option>
                    <?php
                  }
                  ?>
                </select>
              </div>

              <label class="control-label" for="opis">Opis</label>
              <div class="controls">
                <textarea class="input-xlarge" name="opis" id="opis"></textarea>
              </div>
              <label class="control-label" for="kurs">Trosak u Eurima</label>
              <div class="controls">
                <input type="number" class="input-xlarge" name="trosak_eura" id="trosak_eura" step="0.1">
              </div>

              <div class="controls">
                <button type="reset" class="btn btn_cancel">Reset</button>
                <button type="submit" class="btn btn-large btn_save">Sačuvaj</button>
              </div>
            </div>



          </fieldset>
        </form>
      </div>
    </div>
  </div>
  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
