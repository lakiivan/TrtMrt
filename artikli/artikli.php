<?php
  require_once "../util/pdo.php";
  require_once "../util/db.php";
  require_once "../util/html_maker_table.php";

  $table_maker      = new HtmlMakerTable();
  $upit             = new Db();
  $artikli          = $upit -> get_artikli($pdo);

  if (isset($_GET['big_order_id']) && isset($_GET['kontakt_id'])) {
    //echo 'big order id is '.$boid;
    $order_visible  = '';
    $boid           = $_GET['big_order_id'];
    $kid            = $_GET['kid'];
    $dodaj          = 1;
  } else {
    //echo 'boid is not set!';
    $order_visible  = 'hide';
    $boid           = 0;
    $kid            = 0;
    $dodaj          = 0;
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
        <button class="btn btn-large btn-xxl" id="btn_novkontakt" onclick="location.href='https://localhost/trt_mrt/artikli/artikl_form.php?is_view_a=0'">Nov Artikl</button>
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
        <h2>Pregled Artikala</h2>
        <input type="text" id="search_pn" title="Unesite ime"
        onkeyup="search_table('search_pn','tabela_artikli',3)" placeholder = "Pronađi artikl po part numberu...">

        <input type="text" id="search_opis" title="Unesite ime"
        onkeyup="search_table('search_opis','tabela_artikli',4)" placeholder = "Pronađi artikl po opisu...">
        <?php
          $table_maker -> create_tabelu_artikli($dodaj, $boid, $kid, $artikli);
        ?>
      </div>
    </div>
  </div>

  <script src="../js/jquery-3.6.0.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <script src="../js/search.js"></script>
</body>
</html>
