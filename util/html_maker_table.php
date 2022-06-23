<?php
class HtmlMakerTable {

  function create_table_row($table_tag, $col_names, $col_att) {
    //input sadrzi sve sto treba da pise u th polju. Ovo ukljucuje th clasu i title kao promenljive
    echo '<tr>';
    for ($i=0; $i<count($col_names); $i+=1) {
      echo '<'.$table_tag.'>'.$col_names[$i].'</th>';
    }
    echo '</tr>';
  }

  function create_tabelu_kontakti($dodaj, $boid, $kontakti) {
    //$dodaj = false ili true, ukoliko se tablea krerira za potrebe big order interface-a je true dodace se link za dodaj kojim se dodaje single porduzbina u big order
    //$dodaj je false ukoliko se kreira za potrebe pregleda svih kontakta na kontakti.php
    //$kontakti su niz svih aktivnih kontakta iz baze
    if ($dodaj === 1) {
      //echo 'big order id is '.$boid;
      $order_visible = '';
    } else {
      //echo 'boid is not set!';
      $order_visible = 'hide';
    }
    //kreiranje same tabele
    echo '<table class="table table-striped" id="tabela_kontakti">';
    echo  '<thead>';
    echo    '<tr>';
    echo      '<th scope="col"></th>';
    echo      '<th scope="col" class="hide">id</th>';
    echo      '<th scope="col" class='.$order_visible.'>Order</th>';
    echo      '<th scope="col">Ime</th>';
    echo      '<th scope="col">Telefon</th>';
    echo      '<th scope="col">Adresa</th>';
    echo      '<th scope="col">Grad</th>'   ;
    echo      '<th scope="col">Klub</th>';
    echo      '<th scope="col"></th>'          ;
    echo     '</tr>';
    echo    '</thead>';
    echo   '<tbody>';
    //ucitavanje pojedinacnih podataka i njihovo upisivanje u tabelu
    if(count($kontakti) > 0) {
      $count = 0;
      foreach ($kontakti as $kontakt) {
        $id       = $kontakt['kontakt_id'];
        $ime      = htmlentities($kontakt['ime']);
        $telefon  = htmlentities($kontakt['telefon']);
        $adresa   = htmlentities($kontakt['adresa']);
        $grad     = htmlentities($kontakt['grad']);
        $klub     = htmlentities($kontakt['klub']);
        $count++;
        echo '<tr>';
        echo '<td scope="row">'.$count.'</td>';
        echo '<td scope="row" class='.$order_visible.'><a href="http://localhost/trt_mrt/big_order/porucivanje/single_order_interface.php?kontakt_id='.$id.'&big_order_id='.$boid.'">Dodaj</a></td>';
        echo '<td class="hide"><a href="http://localhost/trt_mrt/big_order/porucivanje/order_form.php?kontakt_id='.$id.'">Order</a></td>';
        echo '<td class="hide"><?=$id?></td>';
        echo '<td><a href="http://localhost/trt_mrt/kontakti/kontakt_form_view.php?kontakt_id='.$id.'">'.$ime.'</a></td>';
        echo '<td>'.$telefon.'</td>';
        echo '<td>'.$adresa.'</td>';
        echo '<td>'.$grad.'</td>';
        echo '<td>'.$klub.'</td>';
        echo '<td><a href="http://localhost/trt_mrt/kontakti/kontakt_form_edit.php?kontakt_id='.$id.'">Edit</a></td>';
        echo '</tr>';
      }
    }
    echo '</tbody>';
    echo '</table>';
  }

  function create_tabelu_artikli($dodaj, $boid, $kid, $artikli) {
    if ($dodaj === 1) {
      //echo 'big order id is '.$boid;
      $order_visible = '';
    } else {
      //echo 'boid is not set!';
      $order_visible = 'hide';
    }
    //kreiranje same tabele
    echo '<table class="table table-striped" id="tabela_artikli">';
    echo  '<thead>';
    echo    '<tr>';
    echo      '<th scope="col"></th>';
    echo      '<th scope="col" class="hide">id</th>';
    echo      '<th scope="col" class='.$order_visible.'>Order</th>';
    echo      '<th scope="col">Part_Number</th>';
    echo      '<th scope="col">Opis</th>';
    echo      '<th scope="col">Link</th>';
    echo      '<th scope="col">Net Cena, Eur</th>'   ;
    echo      '<th scope="col">List Cena, Eur</th>';
    echo      '<th scope="col">Edit</th>'          ;
    echo     '</tr>';
    echo    '</thead>';
    echo   '<tbody>';
    //ucitavanje pojedinacnih podataka i njihovo upisivanje u tabelu
    if(count($artikli) > 0) {
      $count = 0;
      foreach ($artikli as $artikl) {
        $id           = $artikl['artikl_id'];
        $part_number  = htmlentities($artikl['part_number']);
        $opis         = htmlentities($artikl['opis']);
        $link         = htmlentities($artikl['link']);
        $cena_net     = htmlentities($artikl['cena_net']);
        $cena_list    = htmlentities($artikl['cena_list']);
        $count++;
        echo '<tr>';
        echo '<td scope="row">'.$count.'</td>';
        echo '<td scope="row" class="hide">'.$id.'</td>';
        echo '<td scope="row" class='.$order_visible.'><a href="http://localhost/trt_mrt/big_order/porucivanje/artikl_order_form.php?kontakt_id='.$kid.'&big_order_id='.$boid.'&artikl_id='.$id.'">Dodaj</a></td>';
        echo '<td><a href="http://localhost/trt_mrt/artikli/artikl_form_view.php?artikl_id='.$id.'">'.$part_number.'</a></td>';
        echo '<td>'.$opis.'</td>';
        echo '<td><a href="'.$link.'" target="_blank">Pastorelli</a></td>';
        echo '<td>'.$cena_net.'</td>';
        echo '<td>'.$cena_list.'</td>';
        echo '<td><a href="http://localhost/trt_mrt/artikli/artikl_form_edit.php?artikl_id='.$id.'">Edit</a></td>';
        echo '</tr>';
      }
    }
    echo '</tbody>';
    echo '</table>';
  }

  function create_table_bo_artikl_orderi($artikl_orderi, $big_order_id, $kontakt_id) {
    echo '<table class="table table-striped" id="tabela_artikl_orderi">';
    echo '            <thead>';
    echo '              <tr>';
    echo '                <th scope="col"></th>';
    echo '                <th scope="col" class="hide">id</th>';
    echo '                <th scope="col">id</th>';
    echo '                <th scope="col">part number</th>';
    echo '                <th scope="col">opis</th>';
    echo '                <th scope="col">kolicina</th>';
    echo '                <th scope="col">prodajna cena</th>';
    echo '                <th scope="col">datum porucivanja</th>';
    echo '                <th scope="col">datum poslednje promene</th>';
    echo '                <th scope="col">Validno</th>';
    echo '                <th scope="col">Komentar</th>';
    echo '                <th scope="col">Edit</th>';
    echo '              </tr>';
    echo '            </thead>';
    echo '            <tbody>';
    if(count($artikl_orderi) > 0) {
      $count = 0;
      foreach ($artikl_orderi as $artikl_order) {
        $aoid                 = $artikl_order['aoid'];
        $ao_part_             = htmlentities($artikl_order['part_number']);
        $ao_opis              = htmlentities($artikl_order['opis']);
        $ao_artikl_id         = htmlentities($artikl_order['aid']);
        $ao_kolicina          = htmlentities($artikl_order['kolicina']);
        $ao_prodajna_cena     = htmlentities($artikl_order['cena']);
        $ao_datum_porudzbine  = htmlentities($artikl_order['dporudzbine']);
        $datum_modifikovanja  = htmlentities($artikl_order['dmodifikovanja']);
        $validno_num          = htmlentities($artikl_order['validno']);
        if (intval($validno_num) === 1) {
          $validno = 'DA';
        } else {
          $validno = 'NE';
        }
        $komentar = "'".htmlentities($artikl_order['komentar'])."'";
        $count++;
        echo '<tr>';
        echo '                    <td scope="row">'.$count.'</td>';
        echo '                    <td class="hide">'.$aoid.'</td>';
        echo '                    <td>'.$aoid.'</td>';
        echo '                    <td>'.$ao_part_.'</td>';
        echo '                    <td>'.$ao_opis.'</td>';
        echo '                    <td>'.$ao_kolicina.'</td>';
        echo '                    <td>'.$ao_prodajna_cena.'</td>';
        echo '                    <td>'.$ao_datum_porudzbine.'</td>';
        echo '                    <td>'.$datum_modifikovanja.'</td>';
        echo '                    <td>'.$validno.'</td>';
        echo '                    <td>'.$komentar.'</td>';
        echo '                    <td><a href="http://localhost/trt_mrt/big_order/porucivanje/artikl_order_form_edit.php?artikl_order_id='.$aoid.'&kontakt_id='.$kontakt_id.'&big_order_id='.$big_order_id.'">Edit</a></td>';
        echo '                  </tr>';
      }
    }
    echo '</tbody>';
    echo '</table>';
  }

  function create_table_stanje_magacina_so($svi_artikli_na_stanju, $big_order_id, $kontakt_id) {
    echo '<table class="table table-striped magacin" id="tabela_magacin_artikli">';
    echo '<thead>';
    echo '            <tr>';
    echo '              <th scope="col"></th>';
    echo '              <th scope="col">Order</th>';
    echo '              <th scope="col">Kol</th>';
    echo '              <th scope="col">Part_Number</th>';
    echo '              <th scope="col">Opis</th>';
    echo '              <th scope="col">Cena net, Eur</th>';
    echo '            </tr>';
    echo '          </thead>';
    echo '          <tbody>';
    if(count($svi_artikli_na_stanju) > 0) {
      $count              = 0;
      $ukupna_kolicina    = 0;
      $ukupna_net         = 0;
      foreach ($svi_artikli_na_stanju as $artikl) {
        $magacin_id       = htmlentities($artikl['magacin_id']);
        $part_number      = htmlentities($artikl['part_number']);
        $artikl_id        = htmlentities($artikl['artikl_id']);
        $kolicina         = htmlentities($artikl['kolicina']);
        $opis             = htmlentities($artikl['opis']);
        $cena_net         = htmlentities($artikl['cena_net']);

        $count++;
        $ukupna_kolicina  += $kolicina;
        $ukupna_net       += $cena_net;
        echo '<tr>';
        echo '';
        echo '                  <td class="td_aoid">'.$count.'</td>';
        echo '                  <td class="td_pn"><a href="http://localhost/trt_mrt/big_order/porucivanje/magacin_artikl_form.php?artikl_id='.$artikl_id.'&kontakt_id='. $kontakt_id.'&big_order_id='. $big_order_id.'">Dodaj</a></td>';
        echo '                  <td class="td_kol">'.$kolicina.'</td>';
        echo '                  <td class="td_pn">'.$part_number.'</td>';
        echo '                  <td class="td_opis">'.$opis.'</td>';
        echo '                  <td class="td_pn">'.$cena_net.'</td>';
        echo '                </tr>';
      }
      echo '                  <td class="td_aoid"></td>';
      echo '                  <td class="td_aoid"></td>';
      echo '                  <td class="td_kol bolder">'.$ukupna_kolicina.'</td>';
      echo '                  <td class="td_pn"></td>';
      echo '                  <td class="td_opis"></td>';
      echo '                  <td class="td_pn bolder">'.$ukupna_net.'</td>';
      echo '                  <td class="td_kol"></td>';
    }
    echo '</tbody>';
    echo '</table>';
  }


  function create_table_so_magacin($svi_artikli_na_stanju, $big_order_id, $kontakt_id) {
    if (strcmp($dodaj, '0') === 0){
      $visible = "hide";
      $bo_style ="";
    } else {
      $visible = '';
      $bo_style = "hide";
    }
    echo '<table class="table table-striped" id="tabela_magacin_artikli">';
    echo '<thead>';
    echo '<thead>';
    echo '            <tr>';
    echo '              <th scope="col"></th>';
    echo '              <th scope="col" class="'.$visible.'">Order</th>';
    echo '              <th scope="col">Kol</th>';
    echo '              <th scope="col">Part_Number</th>';
    echo '              <th scope="col">Opis</th>';
    echo '              <th scope="col">Cena net, Eur</th>';
    echo '              <th scope="col">AOID</th>';
    echo '              <th scope="col" class="'.$bo_style.'">BOID</th>';
    echo '            </tr>';
    echo '          </thead>';
    if(count($svi_artikli_na_stanju) > 0) {
      $count              = 0;
      $ukupna_kolicina    = 0;
      $ukupna_net         = 0;
      foreach ($svi_artikli_na_stanju as $artikl) {
        if(strcmp($dodaj, '0') === 0){
          $big_order_id     = htmlentities($artikl['big_order_id']);
        } else {
          $big_order_id = 0;
        }
        $magacin_id       = htmlentities($artikl['magacin_id']);
        $part_number      = htmlentities($artikl['part_number']);
        $artikl_id        = htmlentities($artikl['artikl_id']);
        $kolicina         = htmlentities($artikl['kolicina']);
        $opis             = htmlentities($artikl['opis']);
        $cena_net         = htmlentities($artikl['cena_net']);
        $artikl_order_id  = htmlentities($artikl['artikl_order_id']);

        $count++;
        $ukupna_kolicina  += $kolicina;
        $ukupna_net       += $cena_net;
        echo '<tr>';
        echo '';
        echo '                  <td class="td_aoid">'.$count.'</td>';
        echo '                  <td class="td_pn '.$visible.'"><a href="http://localhost/trt_mrt/big_order/porucivanje/magacin_artikl_form.php?magacin_id='.$magacin_id.'&artikl_id='.$artikl_id.'&kontakt_id='. $kontakt_id.'&big_order_id='. $big_order_id.'">Dodaj</a></td>';
        echo '                  <td class="td_kol">'.$kolicina.'</td>';
        echo '                  <td class="td_pn">'.$part_number.'</td>';
        echo '                  <td class="td_opis">'.$opis.'</td>';
        echo '                  <td class="td_pn">'.$cena_net.'</td>';
        echo '                  <td class="td_kol">'.$artikl_order_id.'</td>';
        echo '                  <td class="td_kol '.$bo_style.'">'.$big_order_id.'</td>';
        echo '                </tr>';
      }
      echo '                  <td class="td_aoid"></td>';
      echo '                  <td class="td_kol bolder">'.$ukupna_kolicina.'</td>';
      echo '                  <td class="td_pn"></td>';
      echo '                  <td class="td_opis"></td>';
      echo '                  <td class="td_pn bolder">'.$ukupna_net.'</td>';
      echo '                  <td class="td_kol"></td>';
    }
    echo '</tbody>';
    echo '</table>';
  }

  function create_table_sve_porudzbine_kontakta($svi_artikli_na_stanju){
    echo '<table class="table table-striped" id="tabela_magacin_artikli">';
    echo '<thead>';
    echo '<thead>';
    echo '            <tr>';
    echo '              <th scope="col"></th>';
    echo '              <th scope="col">Big Order</th>';
    echo '              <th scope="col">Part Number</th>';
    echo '              <th scope="col">Opis</th>';
    echo '              <th scope="col">Por Kol</th>';
    echo '              <th scope="col">Isp Kol</th>';
    echo '              <th scope="col">Prodajna Cena</th>';
    echo '              <th scope="col">Datum</th>';
    echo '            </tr>';
    echo '          </thead>';
    if(count($svi_artikli_na_stanju) > 0) {
      $count = 0;
      foreach ($svi_artikli_na_stanju as $kon_por) {
        $big_order_id = htmlentities($kon_por['big_order_id']);
        $part_number  = htmlentities($kon_por['part_number']);
        $opis         = htmlentities($kon_por['opis']);
        $prodajna_cena= htmlentities($kon_por['prodajna_cena']);
        $kolicina     = htmlentities($kon_por['kolicina']);
        $isp_kol      = htmlentities($kon_por['isp_kol']);
        $datum        = htmlentities($kon_por['datum_porudzbine']);
        if($isp_kol == 0) {
          $prodajna_cena = 0;
        }
        $count++;
        echo '<tr>';
        echo '';
        echo '                  <td class="td_aoid">'.$count.'</td>';
        echo '                  <td class="td_aoid ">'.$big_order_id.'</td>';
        echo '                  <td class="td_pn">'.$part_number.'</td>';
        echo '                  <td class="td_opis">'.$opis.'</td>';
        echo '                  <td class="td_kol">'.$kolicina.'</td>';
        echo '                  <td class="td_kol">'.$isp_kol.'</td>';
        echo '                  <td class="td_kol">'.$prodajna_cena.'</td>';
        echo '                  <td class="td_pn">'.$datum.'</td>';
        echo '                </tr>';
      }
    }
    echo '</tbody>';
    echo '</table>';
  }

  function create_table_svi_artikl_orderi($svi_artikl_orderi){
    echo '<table class="table table-striped" id="table_svi_artikl_orderi">';
    echo '<thead>';
    echo '<thead>';
    echo '            <tr>';
    echo '              <th scope="col">Count</th>';
    echo '              <th scope="col">AOID</th>';
    echo '              <th scope="col">BOID</th>';
    echo '              <th scope="col">Kontakt</th>';
    echo '              <th scope="col">Part Number</th>';
    echo '              <th scope="col">Opis</th>';
    echo '              <th scope="col">Por Kol</th>';
    echo '              <th scope="col">Pre Kol</th>';
    echo '              <th scope="col">Isp Kol</th>';
    echo '              <th scope="col">Prodajna Cena</th>';
    echo '              <th scope="col">Datum</th>';
    echo '            </tr>';
    echo '          </thead>';
    if(count($svi_artikl_orderi) > 0) {
      $count = 0;
      foreach ($svi_artikl_orderi as $artikl_order) {
        $artikl_order_id  = htmlentities($artikl_order['artikl_order_id']);
        $big_order_id     = htmlentities($artikl_order['big_order_id']);
        $kontakt_id       = htmlentities($artikl_order['kontakt_id']);
        $ime              = htmlentities($artikl_order['ime']);
        $part_number      = htmlentities($artikl_order['part_number']);
        $opis             = htmlentities($artikl_order['opis']);
        $prodajna_cena    = htmlentities($artikl_order['prodajna_cena']);
        $kolicina         = htmlentities($artikl_order['kolicina']);
        $preg_kol         = htmlentities($artikl_order['preg_kol']);
        $isp_kol          = htmlentities($artikl_order['isp_kol']);
        $datum            = htmlentities($artikl_order['datum_porudzbine']);
        if($isp_kol == 0) {
          $prodajna_cena = 0;
        }
        $count++;
        echo '<tr>';
        echo '';
        echo '                  <td class="td_aoid">'.$count.'</td>';
        echo '                  <td class="td_aoid"><a href="http://localhost/trt_mrt/big_order/porucivanje/artikl_order_form_edit.php?artikl_order_id='.$artikl_order_id.'&big_order_id='.$big_order_id.'&kontakt_id='.$kontakt_id.'">'.$artikl_order_id.'</a></td>';
        echo '                  <td class="td_aoid">'.$big_order_id.'</td>';
        echo '                  <td class="td_pn">'.$ime.'</td>';
        echo '                  <td class="td_pn">'.$part_number.'</td>';
        echo '                  <td class="td_opis">'.$opis.'</td>';
        echo '                  <td class="td_kol">'.$kolicina.'</td>';
        echo '                  <td class="td_kol">'.$preg_kol.'</td>';
        echo '                  <td class="td_kol">'.$isp_kol.'</td>';
        echo '                  <td class="td_kol">'.$prodajna_cena.'</td>';
        echo '                  <td class="td_pn">'.$datum.'</td>';
        echo '                </tr>';
      }
    }
    echo '</tbody>';
    echo '</table>';
  }
}
