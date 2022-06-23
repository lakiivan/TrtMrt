<?php
require_once "util/pdo.php";
session_start();

//************ POST METOD ******************************
if (isset($_POST['boid'])) {
  //pribavljanje poslednjeg artikl_order_ida
  $sql = "SELECT artikl_order_id FROM artikl_orderi WHERE big_order_id = :boid";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':boid' => $_POST['boid']
  ));
  $rows = $stmt -> fetchAll(PDO::FETCH_ASSOC);
  if(count($rows) > 0){
    $start  = $rows[0]['artikl_order_id'];
    $end    = $rows[count($rows) - 1]['artikl_order_id'];
    //selektovanje svih validnih aoid u datom opsegu
    for ($count = $start; $count <= $end; $count += 1){
      $sql = "SELECT artikl_order_id FROM artikl_orderi WHERE artikl_order_id = :count";
      $stmt = $pdo -> prepare($sql);
      $stmt -> execute(array(
        ':count' => $count
      ));
      $row = $stmt -> fetch(PDO::FETCH_ASSOC);
      if ($row === false){
        //nista ne raditi
      } else {
        $artikl_order_id  = $row['artikl_order_id'];
        //konacno upodate ovog artikl_order_ida sa datumima iz formulara
        $sql = "UPDATE `artikl_orderi`
                SET `datum_porudzbine`= :datum_porudzbine,`datum_ordera`='2021-10-06',
                `datum_pregleda`= :datum_pregleda,`datum_isporuke`= :datum_isporuke,
                `datum_modifikovanja`= :datum_isporuke
                WHERE artikl_order_id = :artikl_order_id";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute(array(
          ':artikl_order_id'  => $artikl_order_id,
          ':datum_porudzbine' => $_POST['datum_porudzbine'],
          ':datum_pregleda'   => $_POST['datum_pregleda'],
          ':datum_isporuke'   => $_POST['datum_isporuke']
        ));
      }
    }
  }
  $_SESSION['success'] = 'Svi artikl orderi iz Big order id = '.$_POST['boid'].' su uspešno azuirani u bazi artikal orderi';
  header('Location: ao_update.php');
  return;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "util/head.php" ?>
</head>
<body>
  <?php require_once "util/navbar.php" ?>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <form class="form-horizontal" method="post">
          <p><a href="#" onclick="history.go(-1)"><h3>Odustani</h3></a></p>
          <fieldset>
            <legend>Update tabele artikl_orderi</legend>
            <div class="controls">
              <label class="control-label" for="boid">Big order id</label>
              <div class="controls">
                <input type="number" class="input-xlarge" name="boid" id="boid">
              </div>
              <label class="control-label" for="datum_pregleda">Datum
                porudzbine</label>
              <div class="controls">
                <input type="date" class="input-xlarge" name="datum_porudzbine" id="datum_porudzbine">
              </div>
              <label class="control-label" for="datum_pregleda">Datum pregleda</label>
              <div class="controls">
                <input type="date" class="input-xlarge" name="datum_pregleda" id="datum_pregleda">
              </div>
              <label class="control-label" for="datum_isporuke">Datum isporuke</label>
              <div class="controls">
                <input type="date" class="input-xlarge" name="datum_isporuke" id="datum_isporuke">
              </div>

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
