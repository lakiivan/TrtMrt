<?php

class NaplataConstructor {

  //*****************UTIL******************************************

  function get_ukupno_eura($pdo, $boid, $kid) {
    $stmt = $pdo -> prepare ("SELECT SUM(prodajna_cena) as prodajna_cena
    FROM artikl_orderi
    WHERE big_order_id = :boid AND kontakt_id = :kid");
    $stmt -> execute(array(
      ':boid' => $boid,
      ':kid' => $kid
    ));
    $data = $stmt -> fetch(PDO::FETCH_ASSOC);
    $ukupno_eura = floatval($data['prodajna_cena']);
    return $ukupno_eura;
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

    function pregled_status($stigli_svi) {
      if ($stigli_svi) {
        return 'OK';
      } else {
        return 'NE';
      }
    }


    //**********************HTML CREATOR********************************************
    function create_naplata_div($pp, $style, $ukupno_eura_za_naplatu, $kid, $action, $pdo) {
      //kreiranje prvog diva i tabele u njemu i naslova kolona
      $status = $this -> pregled_status($pp['svi_isporuceni']);
      $kurs = $this -> get_kurs($pdo);
      echo'<div class="span4 '.$style.'">';
      echo '<h2 class="'.$status.'">'.$status.' - <a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].'</a></h2>';
      echo '<h3>Ukupno eura za naplatu - '.$ukupno_eura_za_naplatu.'</h3>';
      echo '<hr>';
      echo '<button type="button" class="btn btn-large" style="width:100%; margin: 1px;">Neisporuceno Pokazi/Sakrij</button>';
      foreach ($pp['neisporuceni_artikli'] as $neisporucen) {
        $artikl_data = $neisporucen['part_number'].' - '.$neisporucen['opis'].' / '.$neisporucen['kolicina'];
        echo '<p class="hide">'.$artikl_data.'</p>';
      }
      echo '<hr>';
      $this -> za_naplatu($ukupno_eura_za_naplatu, $kurs, $kid, $pp['big_order_id'], $pp['kontakt_id'], $action);
      echo '</div>';
    }

    function za_naplatu($ukupno_eura, $kurs, $kid, $big_order_id, $kontakt_id, $action) {
      $ukupno_dinara = $ukupno_eura * $kurs;
      $ukupno_dinara = number_format($ukupno_dinara, 2, '.', ',');
      echo '<div class="span4">';

      echo '<button type="button" class="btn btn-large btn_din_eur" style="width:100%; margin: 5px;">Din/Eur</button>';
      echo '<table>';
      echo '<tr>';
      echo '<td>Kurs</td>';
      echo '<td><input type"number" id="kurs'.$kid.'" name="kurs" onkeyup="calc_ukupno_din_nakon_promene_kursa('.$kid.')" value='.$kurs.' step=0.1/></td>';
      echo '</tr>';

      echo '<tr>';
      echo '<td class="naplata hide">Za Naplatu Eura</td>';
      echo '<td class="naplata hide"><input type"number" id="zn_eura'.$kid.'" name="zn_eura" value='.$ukupno_eura.' step=0.1 readonly/></td>';
      echo '</tr>';

      echo '<tr>';
      echo '<td class="naplata">Za Naplatu Dinara</td>';
      echo '<td class="naplata"><input type"number" id="zn_dinara'.$kid.'" name="zn_dinara" value='.$ukupno_dinara.' step=10 readonly/></td>';
      echo '</tr>';

      echo '<tr>';
      echo '<form class="form-horizontal" method="post" action="'.$action.'">';
      echo '<td class="naplata hide">Naplaceno Eura</td>';
      echo '<td class="naplata hide"><input type"number" id="nap_eura'.$kid.'" name="nap_eura" value='.$ukupno_eura.' step=0.1/></td>';
      echo '</tr>';

      echo '<tr>';
      echo '<td class="naplata">Naplaceno Dinara</td>';
      echo '<td class="naplata"><input type"number" id="nap_dinara'.$kid.'" name="nap_dinara" onkeyup="calc_ukupno_evra_nakon_promene_din('.$kid.')"   value='.$ukupno_dinara.' step=0.1/></td>';
      echo '</tr>';

      echo '</table>';
      echo '<button type="submit" class="btn btn-large btn_save" style="width:100%; margin: 5px;">NAPLATI</button>';
      echo '<td><input class="hide" type"number" id="big_order_id'.$kid.'" name="big_order_id" value='.$big_order_id.' readonly/></td>';
      echo '<td><input class="hide" type"number" id="kontakt_id'.$kid.'" name="kontakt_id" value='.$kontakt_id.' readonly/></td>';
      echo '</form>';
      echo '</div>';
    }

    function kreiraj_form_izmene_naplacenog($naplacen, $stigli_svi, $ukupno_za_naplatu_eura, $ukupno_naplaceno, $style, $action, $kid){
      $status = $this -> pregled_status($stigli_svi);
      echo'<div class="span4 '.$style.'">';
      echo '<h2 class="'.$status.'">'.$status.' - <a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$naplacen['kontakt_id'].'&big_order_id='.$naplacen['big_order_id'].'">'.$naplacen['ime'].'</a></h2>';
      echo '<h3>Ukupno eura za naplatu/naplaceno - '.$ukupno_za_naplatu_eura.'/'.$ukupno_naplaceno.'</h3>';
      echo '<hr>';
      echo '<form class="form-horizontal" method="post" action="'.$action.'">';
      echo '<table>';
      echo '<td class="naplata">Izmeni naplatu u Eurima</td>';
      echo '<td class="naplata"><input type"number" id="nap_eura'.$kid.'" name="nap_eura" value='.$ukupno_naplaceno.' step=0.1/></td>';
      echo '<button type="submit" class="btn btn-large btn_save" style="width:95%; margin: 5px;">Izmeni naplatu</button>';
      echo '<td><input class="hide" type"number" id="big_order_id'.$kid.'" name="big_order_id" value='.$naplacen['big_order_id'].' readonly/></td>';
      echo '<td><input class="hide" type"number" id="kontakt_id'.$kid.'" name="kontakt_id" value='.$naplacen['kontakt_id'].' readonly/></td>';
      echo '</table>';
      echo '</form>';
      echo '</div>';

    }

  }
