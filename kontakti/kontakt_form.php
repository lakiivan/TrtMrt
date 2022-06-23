<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";
session_start();
$form_maker   = new HtmlMakerForm();
$upiti        = new Db();
$kontakti   = $upiti -> get_kontakti($pdo);
$gradovi    = $upiti -> get_gradovi($pdo);
$klubovi    = $upiti -> get_klubovi($pdo);
$pop_grupe  = $upiti -> get_pop_grupe($pdo);

//************ POST METOD ******************************
if (isset($_POST['ime'])) {
  echo"POST STARTED";
  $sql = "INSERT INTO kontakti (ime, telefon, adresa, grad_id, klub_id, pop_grupa_id, komentar)
  VALUES (:ime, :telefon, :adresa, :grad_id, :klub_id, :pop_grupa_id, :komentar)";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':ime' => $_POST['ime'],
    ':telefon' => $_POST['telefon'],
    ':adresa' => $_POST['adresa'],
    ':grad_id' => $_POST['grad_id'],
    ':klub_id' => $_POST['klub_id'],
    ':pop_grupa_id' => $_POST['pop_grupa_id'],
    ':komentar' => $_POST['komentar']
  ));
  $kontakt_id = $pdo -> lastInsertId();
  if(isset($_GET['big_order_id'])) {
    $_SESSION['success'] = 'Kontakt '.$_POST['ime'].' je uspešno dodat u bazu';
    header('Location: https://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?big_order_id='.$_GET['big_order_id'].'&kontakt_id='.$kontakt_id);
    return;
  }
  $_SESSION['success'] = 'Kontakt '.$_POST['ime'].' je uspešno dodat u bazu';
  header('Location: kontakti.php');
  return;
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php require_once "../util/navbar.php";
  $form_maker -> create_kontakt_blank_form("Unesite Nov Kontakt", $gradovi, $klubovi, $pop_grupe);
  ?>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
