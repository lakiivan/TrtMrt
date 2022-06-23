<?php
require_once "../util/pdo.php";
session_start();

//************ POST METOD ******************************
if (isset($_POST['kontakt_id']) && isset($_POST['isplata_eura'])) {
  $datum = date("Y-m-d H:i:s");
  $sql = "INSERT INTO isplate (kontakt_id, isplata_eura, datum_isplate, komentar)
  VALUES (:kontakt_id, isplata_eura, :datum_isplate, :komentar)";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':kontakt_id' => $_POST['kontakt_id'],
    ':komentar' => $_POST['komentar'],
    ':isplata_eura' => $_POST['isplata_eura'],
    ':datum_isplate' => $datum
  ));
  $_SESSION['success'] = 'Rashod '.$_POST['opis'].' u iznosu od '.$_POST['isplata_eura'].' je uspešno dodat u bazu';
  header('Location: finansije.php');
  return;
}

//lena ili masa id
$id = $_GET['kontakt_id'];
if(strcmp($id, '25') == 0){
  $second_id = 30;
  $ime = 'MAŠA';
  $second_ime = 'LENA';
} else {
  $second_id = 25;
  $ime = 'LENA';
  $second_ime = 'MAŠA';
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
        <h2>ISPLATA</h2>
        <form class="form-horizontal" method="post">
          <fieldset>
            <legend>Unesite nov isplatu</legend>
            <div class="control-group">

              <label class="control-label" for="kontakt_id">Kontakt Id</label>
              <div class="controls">
                <select id="kontakt_id" name="kontakt_id" class="dropdownlist">
                  <option value="<?=$id?>"><?=$ime?></option>
                  <option value="<?=$second_id?>"><?=$second_ime?></option>
                </select>
              </div>

              <label class="control-label" for="kurs">Isplata u Eurima</label>
              <div class="controls">
                <input type="number" class="input-xlarge" name="isplata_eura" id="isplata_eura" step="0.1">
              </div>

              <label class="control-label" for="komentar">Komentar</label>
              <div class="controls">
                <textarea class="input-xlarge" name="komentar" id="komentar"></textarea>
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
