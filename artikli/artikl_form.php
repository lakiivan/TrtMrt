<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";
session_start();
$form_maker = new HtmlMakerForm();

//************ POST METOD ******************************
if (isset($_POST['part_number'])) {
  echo"POST STARTED";
  $sql = "INSERT INTO artikli (part_number, opis, link, cena_net, cena_list, komentar)
  VALUES (:part_number, :opis, :link, :cena_net, :cena_list, :komentar)";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':part_number'  => $_POST['part_number'],
    ':opis'         => $_POST['opis'],
    ':link'         => $_POST['link'],
    ':cena_net'     => $_POST['cena_net'],
    ':cena_list'    => $_POST['cena_list'],
    ':komentar'     => $_POST['komentar']
  ));
  $artikl_id = $pdo -> lastInsertId();
  if(isset($_GET['big_order_id']) && isset($_GET['kontakt_id'])) {
    $_SESSION['success'] = 'Artikl '.$_POST['part_number'].' je uspešno dodat u bazu artikala';
    header('Location: https://localhost/trt_mrt/big_order/porucivanje/artikl_order_form.php?big_order_id='.$_GET['big_order_id'].'&artikl_id='.$artikl_id.'&kontakt_id='.$_GET['kontakt_id']);
    return;
  }
  $_SESSION['success'] = 'Artikl '.$_POST['part_number'].' je uspešno dodat u bazu artikala';
  header('Location: artikli.php');
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
  <?php
    $form_maker -> create_artikl_blank_form("Unesite Nov Artikal");
  ?>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
