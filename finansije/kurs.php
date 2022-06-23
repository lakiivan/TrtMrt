<?php
require_once "../util/pdo.php";
session_start();

//************ POST METOD ******************************
if (isset($_POST['kurs'])) {
  echo"POST STARTED";
  $sql = "INSERT INTO kurs (kurs, komentar)
  VALUES (:kurs, :komentar)";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':kurs' => $_POST['kurs'],
    ':komentar' => $_POST['komentar']
  ));
  $_SESSION['success'] = 'Kurs '.$_POST['kurs'].' je uspešno dodat u bazu';
  header('Location: kurs.php');
  return;
}

function get_kurs($pdo) {
  $stmt = $pdo -> prepare ("SELECT kurs
    FROM kurs
    ORDER BY kurs_id DESC");
    $stmt -> execute(array(
    ));
    $row = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return floatval($row[0]['kurs']);
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
            <h2>Trenutni Kurs je - <?=get_kurs($pdo)?></h2>
            <form class="form-horizontal" method="post">
            <fieldset>
              <legend>Unesite nov kurs</legend>
              <div class="control-group">
                <label class="control-label" for="kurs">Kurs</label>
                <div class="controls">
                  <input type="number" class="input-xlarge" name="kurs" id="kurs" step="0.1">
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
