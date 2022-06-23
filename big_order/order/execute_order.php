<?php
require_once "../../util/pdo.php";
require_once "../bo_util/upit.php";
require_once "../bo_util/html_maker.php";

$upit         = new Upit();
$html_maker   = new HtmlMaker();
$big_order_id = $_GET['big_order_id'];

//*******************GET METOD *************************************************
//kreiraj niz od svih porucenih Artikala iz tabela artikl_orderi
$bo_svi_validni_artikli = $upit -> get_bo_svi_validni_artikli($pdo, $big_order_id);
$bo_svi_jos_neporuceni  = $upit -> get_bo_svi_jos_neporuceni_artikli($pdo, $big_order_id);
$bo_svi_vec_poruceni    = $upit -> get_bo_svi_vec_poruceni_artikli($pdo, $big_order_id);

//setovanje promenljive $nema novih, ukoliko nema n oivh je true u suprotnom je false
if(count($bo_svi_jos_neporuceni) == 0) {
  $nema_novih = True;
} else {
  $nema_novih = False;
}

    //*************************UTIL FUNCTIONS****************************


    function update_table_bo_pregledi($bo_svi_validni_artikli, $aoids, $pdo) {
      //funkcija uzima niz svih nepotvrdjenih porucenih artikala, niz vec potvrdjenih porudzbina
      //uporedjuje ih i samo one koji nisu vec upoisani upisuje u tabelu bo_pregledi
      $nema_novih = True;
      $count = 1;
      eo_create_table_div();
      foreach ($bo_svi_validni_artikli as $bopa){
        //provera da li ima nesto novo
        //echo '<p>'.$bopa['aoid'].' - '.$bopa['part_number'].' - '.$bopa['opis'].' - '.$bopa['o_kolicina'].'</p>';
        if(!in_array($bopa['artikl_order_id'], $aoids)){
          //promena nema novih radi ispisivanja poruke da nije bilo novih artikala
          $nema_novih = False;
          //krerianje tabele za slanje Davidu
          eo_fill_table_row($bopa, $count);
          $count += 1;
          //insert data in table pregledi
          $sql = "INSERT INTO bo_pregledi (artikl_order_id, kolicina, datum_pregleda)
          VALUES (:artikl_order_id, :kolicina, :datum_pregleda)";
          $stmt = $pdo -> prepare($sql);
          $stmt -> execute(array(
            ':artikl_order_id' => $bopa['artikl_order_id'],
            ':kolicina' => $bopa['o_kolicina'],
            ':datum_pregleda' => null
          ));
        }
      }
      update_bo_status($nema_novih, $pdo);
      eo_end_table_div();
      return $nema_novih;
    }



    ?>


    <!DOCTYPE html>
    <html lang="en" dir="ltr">
    <head>
      <?php require_once "../../util/head.php" ?>
    </head>
    <body>
      <?php require_once "../../util/navbar.php" ?>
      <div class="container-fluid order">
        <div class="row-fluid">
          <div class="span12">
            <p><a href="#" onclick="history.go(-1)"><h3>ODUSTANI</h3></a></p>
            <p><h2>NOVI ARTIKLI ZA PORUCITI OD DAVIDA</h2></p>
            <?php
            //upisivanje novih porudzbina u tabelu bo_pregledi
            $html_maker -> eo_create_table_div();
            $count = 0;
            if(count($bo_svi_jos_neporuceni) > 0){
              foreach ($bo_svi_jos_neporuceni as $neporucen) {
                $html_maker -> eo_fill_table_row($neporucen, $count);
                $count += 1;
                $upit -> update_ao_porucen($pdo, $neporucen['artikl_order_id']);
              }
              $upit -> update_bo_status($pdo, $big_order_id, "poruceno");
            }
            $html_maker -> eo_create_table_div();
            //ukoliko nije bilo novih upisivanja ispisuje se poruka nema novih artikala
            $html_maker -> eo_nema_novih_message($nema_novih);
            $html_maker -> eo_create_body_end();
            ?>

          </div>
        </div>
        <div class="row-fluid">
          <div class="span6 std">
            <button type="submit" class="btn btn-xxl" onclick="location.href='http://localhost/trt_mrt/big_order/order/istorija_ordera.php?big_order_id=<?=$_GET['big_order_id']?>'">ISTORIJA ORDERA</button>
          </div>
        </div>
        <script src="../../js/jquery-3.6.0.min.js"></script>
        <script src="../../bootstrap/js/bootstrap.min.js"></script>
        <script src="../../js/order.js"></script>
      </body>
      </html>
