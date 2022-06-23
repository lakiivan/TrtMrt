<?php
require_once "../util/pdo.php";
require_once "../util/db.php";
require_once "../util/html_maker_form.php";
require_once "../util/html_maker_table.php";
  session_start();
  $form_maker   = new HtmlMakerForm();
  $table_maker  = new HtmlMakerTable();
  $upiti        = new Db();
  $kontakti     = $upiti -> get_kontakti($pdo);

  //provera da li se kontakti pozivaju samostalno ili iz big NoRewindIterator
  //upitioliko se pozivaju samostalno td order je sakriveno
  //upitioliko se kotakti pozivaju iz big ordera onda je order link aktivan kako bi se
  //preko njega pozvao formular za porucivanje od strane tog kupca
  if (isset($_GET['big_order_id'])) {
    //echo 'big order id is '.$boid;
    $order_visible = '';
    $boid = $_GET['big_order_id'];
    $dodaj = 1;
  } else {
    //echo 'boid is not set!';
    $order_visible = 'hide';
    $boid = 0;
    $dodaj = 0;
  }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../util/head.php" ?>
</head>
<body>
  <?php require_once "../util/navbar.php" ?>
  <div class="container">
    <div class="row">
      <div class="span12">
        <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='http://localhost/trt_mrt/kontakti/kontakt_form.php'">Nov Kontakt</button>
        <?php
          if (isset($_SESSION['success'])) {
            echo "<h4 style='color:green'>".$_SESSION['success']."</h4>\n";
            unset($_SESSION['success']);
          } else if(isset($_SESSION['error'])) {
            echo "<h4 style='color:red'>".$_SESSION['error']."</h4>\n";
            unset($_SESSION['error']);
          }
        ?>
      </div>
    </div>
    <div class="row">
      <div class="span12">
        <h2>Pregled Kontakata</h2>
        <input type="text" id="search_ime" title="Unesite ime"
        onkeyup="search_table('search_ime', 'tabela_kontakti', 4)" placeholder = "Pronađi kontakt po imenu...">
        <?php
          $table_maker -> create_tabelu_kontakti($dodaj, $boid, $kontakti);
        ?>

      </div>
    </div>
  </div>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="../js/search.js"></script>
</body>
</html>
