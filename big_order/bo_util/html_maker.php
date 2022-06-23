<?php

class HtmlMaker {

  function get_kurs($pdo) {
    $stmt = $pdo -> prepare ("SELECT kurs
      FROM kurs
      ORDER BY kurs_id DESC");
      $stmt -> execute(array(
      ));
      $row = $stmt -> fetchAll(PDO::FETCH_ASSOC);
      return floatval($row[0]['kurs']);
    }

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

  function get_ukupno_eura_za_isporuceno($pdo, $boid, $kid) {
    $stmt = $pdo -> prepare ("SELECT SUM(ao.prodajna_cena * ao.isporucena_kolicina / ao.kolicina) as prodajna_cena
    FROM artikl_orderi as ao
    WHERE big_order_id = :boid AND kontakt_id = :kid");
    $stmt -> execute(array(
      ':boid' => $boid,
      ':kid' => $kid
    ));
    $data = $stmt -> fetch(PDO::FETCH_ASSOC);
    $ukupno_eura = number_format(floatval($data['prodajna_cena']), 1);
    return $ukupno_eura;
  }

  function da_li_je_sve_isporuceno($isporuceni_svi) {
    if($isporuceni_svi) {
      return "OK";
    } else {
      return "NE";
    }
  }

  function create_table_header($pp, $style, $grad, $isporuceni_svi) {
    //kreiranje prvog diva i tabele u njemu i naslova kolona
    $status = $this -> da_li_je_sve_isporuceno($isporuceni_svi);
    echo'<div class="span4 '.$style.'">';
    echo '<h2><span class="'.$status.'">'.$status.' - </span><a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].' - '.$grad.'</a></h2>';
    echo '<table class="p_table">';
    echo '<tr>';
    echo '<th class="hide">pregled_id</th>';
    echo '<th class="hide">kontakt_id</th>';
    echo '<th>AO ID</th>';
    echo '<th>PN</th>';
    echo '<th>Opis</th>';
    echo '<th> P </th>';
    echo '<th> I </th>';
    echo '<th>OK</th>';
    echo '</tr>';
  }

  function fill_table_row($pp) {
    //ova funkcija popunjava jedan red tabele sa podacima dobijenim iz bo_pregledi
    //upisivanje n-tog reda podataka istog imena kupca
    echo '<form class="form-horizontal" method="post">';
    echo '<tr><td class="hide"><input type"number" name="pregled_id" class="pregled_input pi_pid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
    echo '<tr><td class="hide"><input type"number" name="kontakt_id" class="pregled_input pi_pid" value="'.$pp['kontakt_id'].'"readonly/></td>';
    echo '<td class="pregled border_vis td_aoid"><input type"number" name="artikl_order_id" class="pregled_input pi_aoid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
    echo '<td class="pregled border_vis td_pn">'.$pp['part_number'].'</td>';
    echo '<td class="pregled border_vis td_opis"><a href="'.$pp['link'].'" target="_blank">'.$pp['opis'].'</a></td>';
    echo '<td class="pregled td_kol"><input type"number" name="narucena_kolicina" class="pregled_input pi_ok" value="'.$pp['ao_kolicina'].'" step="1" size="1" readonly/></td>';
    echo '<td class="pregled td_kol"><input type"number" name="isporucena_kolicina" class="pregled_input pi_pk" value="'.$pp['pregledana_kolicina'].'" step="1" size="1"/></td>';
    echo '<td class="pregled border_vis"><button type="submit" class="btn">OK</button></td>';
    echo '</tr>';
    echo '</form>';
  }

  function zatvaranje_stare_tabele_formiranje_nove_i_upis_prvog_reda($pdo, $style, $pp, $kontakt_id_za_ukupno_eura, $grad, $isporuceni_svi) {
    //stampanje naslova diva ime i big order id
    echo '</table>';
    if(strcmp($style,'pregled') == 0){
      $ukupno_eura = $this -> get_ukupno_eura_za_isporuceno($pdo, $pp['big_order_id'], $kontakt_id_za_ukupno_eura);
      $kurs = $this -> get_kurs($pdo);
      $this -> za_naplatu($ukupno_eura, $kurs, $kontakt_id_za_ukupno_eura);
    }
    $status = $this -> da_li_je_sve_isporuceno($isporuceni_svi);
    echo '</div>';
    echo'<div class="span4 '.$style.'">';
    echo '<form class="form-horizontal" method="post">';
    echo '<h2><span class="'.$status.'">'.$status.' - </span><a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$pp['kontakt_id'].'&big_order_id='.$pp['big_order_id'].'">'.$pp['ime'].' - '.$grad.'</a></h2>';
    echo '<table class="p_table">';
    echo '<tr>';
    echo '<th class="hide">kontakt_id</th>';
    echo '<th>AO ID</th>';
    echo '<th>PN</th>';
    echo '<th>opis</th>';
    echo '<th> P </th>';
    echo '<th> I </th>';
    echo '</tr>';
    //upisivanje prvog reda
    echo '<form class="form-horizontal" method="post">';
    echo '<tr><td class="hide"><input type"number" name="kontakt_id" class="pregled_input pi_pid" value="'.$pp['kontakt_id'].'"readonly/></td>';
    echo '<td class="pregled border_vis td_aoid"><input type"number" name="artikl_order_id" class="pregled_input pi_aoid" value="'.$pp['artikl_order_id'].'"readonly/></td>';
    echo '<td class="pregled border_vis td_pn ">'.$pp['part_number'].'</td>';
    echo '<td class="pregled border_vis td_opis"><a href="'.$pp['link'].'" target="_blank">'.$pp['opis'].'</a></td>';
    echo '<td class="pregled td_kol"><input type"number" name="narucena_kolicina" class="pregled_input pi_ok" value="'.$pp['ao_kolicina'].'" step="1" size="1" readonly/></td>';
    echo '<td class="pregled td_kol"><input type"number" name="isporucena_kolicina" class="pregled_input pi_pk" value="'.$pp['pregledana_kolicina'].'" step="1" size="1"/></td>';
    echo '<td class="pregled border_vis"><button type="submit" class="btn">OK</button></td>';
    echo '</tr>';
    echo '</form>';
  }

  function zatvaranje_poslednje_tabele($pdo, $style, $pp, $kontakt_id_za_ukupno_eura) {
    echo '</table>';
    if (strcmp($style,"pregled") == 0){
      $ukupno_eura = $this -> get_ukupno_eura_za_isporuceno($pdo, $pp['big_order_id'], $kontakt_id_za_ukupno_eura);
      $kurs = $this -> get_kurs($pdo);
      $this -> za_naplatu($ukupno_eura, $kurs, $kontakt_id_za_ukupno_eura);
    }
    echo '</div>';
  }

  function da_li_tabela_za_kontakt_postoji($ime, $bo_curr_ime) {
    //funckija proverava da li je vec kreiran form za dato ime kupca
    if (strcmp($ime, $bo_curr_ime) === 0) {
      return True;
    } else {
      return False;
    }
  }

  function za_naplatu($ukupno_eura, $kurs, $kid) {
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
    echo '<td class="naplata">Za Naplatu Eura</td>';
    echo '<td class="naplata"><input type"number" id="zn_eura'.$kid.'" name="zn_eura" value='.$ukupno_eura.' step=0.1 readonly/></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="naplata">Naplaceno Eura</td>';
    echo '<td class="naplata"><input type"number" name="nap_eura" value='.$ukupno_eura.' step=0.1/></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="naplata hide">Za Naplatu Dinara</td>';
    echo '<td class="naplata hide"><input type"number" id="zn_dinara'.$kid.'" name="zn_dinara" value='.$ukupno_dinara.' step=10 readonly/></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="naplata hide">Naplaceno Dinara</td>';
    echo '<td class="naplata hide"><input type"number" id="nap_dinara'.$kid.'" name="nap_dinara" value='.$ukupno_dinara.' step=0.1/></td>';
    echo '</tr>';

    echo '</table>';
    //echo '<button type="button" class="btn btn-large btn_save" style="width:100%; margin: 5px;">NAPLATI</button>';
    echo '</div>';
  }

  //*****************IZVESTAJ**************************************
  function napravi_div_detaljan_prikaz($artikl_order_izvetaji){
    $count = 1;
    echo '<div class="detaljan_prikaz">';
    echo '<div class="detaljan_prikaz_naslov row-fluid span12 bolder" id="red_prikaza">';
    echo '<div class="span2">Ime</div>';
    echo '<div class="span1">PN</div>';
    echo '<div class="span5">Opis</div>';
    echo '<div class="span0.5">Q</div>';
    echo '<div class="span1">Net</div>';
    echo '<div class="span1">List</div>';
    echo '<div class="span0.5">P</div>';
    echo '<div class="span0.5">I</div>';
    echo '<div class="span0.5">N</div>';
    echo '</div>';
    //echo '<table>';
      foreach ($artikl_order_izvetaji as $artikl_order_izvetaj) {
        $this -> single_div_detaljnog_prikaza($artikl_order_izvetaj, $count);
        $count += 1;
      }
    //  echo '</table>';
    echo '</div>';
  }

  function single_div_detaljnog_prikaza($artikl_order_izvetaj, $count){
    $ime            = htmlentities($artikl_order_izvetaj['ime']);
    $part_number    = htmlentities($artikl_order_izvetaj['part_number']);
    $opis           = htmlentities($artikl_order_izvetaj['opis']);
    $kolicina       = htmlentities($artikl_order_izvetaj['kolicina']);
    $net_cena       = htmlentities($artikl_order_izvetaj['net_cena']);
    $prodajna_cena  = htmlentities($artikl_order_izvetaj['prodajna_cena']);
    $pregledano     = htmlentities($artikl_order_izvetaj['pregledano']);
    $isporuceno     = htmlentities($artikl_order_izvetaj['isporuceno']);

    echo '<div class="detaljan_prikaz_red row-fluid span12" id="red_prikaza'.$count.'">';
    echo '<div class="span2">'.$ime.'</div>';
    echo '<div class="span1">'.$part_number.'</div>';
    echo '<div class="span5">'.$opis.'</div>';
    echo '<div class="span0.5">'.$kolicina.'</div>';
    echo '<div class="span1">'.$net_cena.'</div>';
    echo '<div class="span1">'.$prodajna_cena.'</div>';
    echo '<div class="span0.5">'.$pregledano.'</div>';
    echo '<div class="span0.5">'.$isporuceno.'</div>';
    echo '</div>';
  }

  //****************ORDER HTML****************************************
  function eo_create_table_div() {
    echo '<div class="row-fluid">';
    echo '<div class="span6">';
    echo '<table class="table table-striped" id="table_novi_artikli_za_order">';
    echo '<tr>';
    echo '<th scope="col">No</th>';
    echo '<th scope="col">Part Number</th>';
    echo '<th scope="col">Description</th>';
    echo '<th scope="col">Qty</th>';
    echo '</tr>';
  }

  function eo_fill_table_row($bopa, $count) {
    echo '<tr>';
    echo '<td scope="row">'.$count.'</td>';
    echo '<td scope="row">'.$bopa['part_number'].'</td>';
    echo '<td scope="row">'.$bopa['opis'].'</td>';
    echo '<td scope="row">'.$bopa['o_kolicina'].'</td>';
    echo '</tr>';
    //echo '<p>'.$bopa['artikl_order_id'].' - '.$bopa['part_number'].' - '.$bopa['opis'].' - '.$bopa['o_kolicina'].'</p>';
  }

  function eo_end_table_div() {
    echo '</table>';
    echo '</div>';
    echo '</div>';

  }

  function eo_create_body_end() {
    echo '</div>';
  }

  function eo_nema_novih_message($nema_novih) {
    if($nema_novih) {
      echo '<h3>NEMA NOVIH ARTIKALA!!!</h3>';
    }
  }

  }
