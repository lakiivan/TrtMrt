<?php
require_once "../util/pdo.php";
session_start();

//************ POST METOD ******************************
if (isset($_POST['oznaka'])) {
  $sql = "INSERT INTO big_orderi (oznaka, komentar)
  VALUES (:oznaka, :komentar)";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':oznaka' => $_POST['oznaka'],
    ':komentar' => $_POST['komentar']
  ));

  $big_order_id = $pdo -> lastInsertId();

  $sql2 = "INSERT INTO bo_statusi (big_order_id, status)
  VALUES (:big_order_id, :status)";
  $stmt2 = $pdo -> prepare($sql2);
  $stmt2 -> execute(array(
    ':big_order_id' => $big_order_id,
    ':status' => 'otvoreno'
  ));
  $_SESSION['success'] = 'Big order '.$_POST['oznaka'].' je uspešno dodat u bazu big orderi';
  header('Location: http://localhost/trt_mrt/index.php');
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
        <form class="form-horizontal" method="post">
          <p><a href="#" onclick="history.go(-1)"><h3>Odustani</h3></a></p>
            <legend>Kreiraj novu veliku porudžbinu</legend>
            <fieldset>

            <label class="control-label" for="oznaka">Oznaka</label>
            <div class="controls">
              <input type="text" class="input-xlarge" name="oznaka" id="oznaka">
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
