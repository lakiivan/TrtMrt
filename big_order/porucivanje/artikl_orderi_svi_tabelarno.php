<?php
  require_once "../../util/pdo.php";
  require_once "../bo_util/upit.php";
  require_once "../../util/html_maker_table.php";

  $table_maker        = new HtmlMakerTable();
  $upit               = new Upit();
  $svi_artikl_orderi  = $upit -> get_svi_artikl_orderi($pdo);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <?php require_once "../../util/head.php" ?>
</head>
<body>
  <?php require_once "../../util/navbar.php" ?>
  <div class="container">
    <div class="row">
      <div class="span12">
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
        <input type="text" id="search_kontakt" title="Unesite ime"
        onkeyup="search_table('search_kontakt','table_svi_artikl_orderi',3)" placeholder = "Pronađi artikl po kontaktu...">

        <input type="text" id="search_pn" title="Unesite sifru"
        onkeyup="search_table('search_pn','table_svi_artikl_orderi',4)" placeholder = "Pronađi artikl po part numberu...">

        <input type="text" id="search_opis" title="Unesite opis"
        onkeyup="search_table('search_opis','table_svi_artikl_orderi',5)" placeholder = "Pronađi artikl po opisu...">

        <input type="text" id="search_isp_kol" title="Unesite pregledano"
        onkeyup="search_table('search_isp_kol','table_svi_artikl_orderi',2)" placeholder = "Pronađi artikl po isporuci...">

        <?php
          $table_maker -> create_table_svi_artikl_orderi($svi_artikl_orderi);
        ?>
      </div>
    </div>
  </div>

  <script src="../../js/jquery-3.6.0.min.js"></script>
  <script src="../../bootstrap/js/bootstrap.min.js"></script>
  <script src="../../js/search.js"></script>
</body>
</html>
