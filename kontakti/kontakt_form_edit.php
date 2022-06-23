<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";

session_start();

$form_maker   = new HtmlMakerForm();
$upiti        = new Db();
$gradovi    = $upiti -> get_gradovi($pdo);
$klubovi    = $upiti -> get_klubovi($pdo);
$pop_grupe  = $upiti -> get_pop_grupe($pdo);
$kontakt_id = $_GET['kontakt_id'];
$kontakt    = $upiti -> get_kontakt_info($pdo, $kontakt_id);

//************ POST METOD UPDATE KONTAKTA U BAZI******************************
if (isset($_POST['kontakt_id']) && isset($_POST['ime'])) {
  $sql = "UPDATE kontakti
  SET ime=:ime, telefon=:telefon, adresa=:adresa, grad_id=:grad_id,
  klub_id=:klub_id, pop_grupa_id = :pop_grupa_id, komentar=:komentar
  WHERE kontakt_id=:id";
  $stmt = $pdo -> prepare($sql);
  $stmt -> execute(array(
    ':id' => $_POST['kontakt_id'],
    ':ime' => $_POST['ime'],
    ':telefon' => $_POST['telefon'],
    ':adresa' => $_POST['adresa'],
    ':grad_id' => $_POST['grad_id'],
    ':klub_id' => $_POST['klub_id'],
    ':pop_grupa_id' => $_POST['pop_grupa_id'],
    ':komentar' => $_POST['komentar']
  ));
  $_SESSION['success'] = 'Kontakt '.$_POST['ime'].' je uspešno izmenjen u bazi';
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
  <?php
  require_once "../util/navbar.php";
  $form_maker -> create_header_form("Ažuriranje Kontakta");
  $form_maker -> create_fieldset_kontakt_form($gradovi, $klubovi, $pop_grupe, $kontakt, "", "");
  $form_maker -> create_footer_form();
  ?>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
