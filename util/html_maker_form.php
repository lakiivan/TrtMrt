<?php
class HtmlMakerForm {


  //GENERAL FORM MAKER
  function create_header_form($legend) {
    echo '<div class="container-fluid">';
    echo '<div class="row-fluid">';
    echo '<div class="span12">';
    echo '<form class="form-horizontal" method="post">';
    echo '<p><a href="#" onclick="history.go(-1)"><h3>Odustani</h3></a></p>';
    echo '<fieldset>';
    echo '<legend>'.$legend.'</legend>';
    echo '<div class="control-group">';
  }

  function create_footer_form() {
    echo '</div>';
    echo '</div>';
    echo '</fieldset>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
  }

  function create_controls_label_input_xlarge($name, $label, $tag, $type, $value, $readonly, $disabled) {
    //funckija kreira jednu label input celinu
    //name je naziv za for, name i id a label za text koji ce biti ispisan izmedju label tagova
    //value je vrednost za value ako je ima, type je tip inputa
    //readonly i disabled su opcije za input
    echo '<label class="control-label" for="'.$name.'">'.$label.'</label>';
    echo '<div class="controls">';
    echo '<'.$tag.' type="'.$type.'" class="input-xlarge" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$readonly.' '.$disabled.'>';
    echo '</div>';
  }

  function create_controls_label_input_xlarge_with_range($name, $label, $tag, $type, $value, $readonly, $disabled, $max_kolicina) {
    //funckija kreira jednu label input celinu
    //name je naziv za for, name i id a label za text koji ce biti ispisan izmedju label tagova
    //value je vrednost za value ako je ima, type je tip inputa
    //readonly i disabled su opcije za input
    echo '<label class="control-label" for="'.$name.'">'.$label.'</label>';
    echo '<div class="controls">';
    echo '<'.$tag.' type="'.$type.'" class="input-xlarge" name="'.$name.'" id="'.$name.'" value="'.$value.'" min="0" max="'.$max_kolicina.'"'.$readonly.' '.$disabled.'>';
    echo '</div>';
  }

  function create_controls_label_2tags_xlarge($name, $label, $tag, $type, $value, $readonly, $disabled) {
    //funckija kreira jednu label dupli tag kao jednu celinu
    //name je naziv za for, name i id a label za text koji ce biti ispisan izmedju label tagova
    //value je vrednost za value ako je ima, type je tip inputa
    //readonly i disabled su opcije za input
    echo '<label class="control-label" for="'.$name.'">'.$label.'</label>';
    echo '<div class="controls">';
    echo '<'.$tag.' type="'.$type.'" class="input-xlarge" name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$readonly.' '.$disabled.'>';
    echo '</'.$tag.'>';
    echo '</div>';
  }

  function create_controls_label_select_option_xlarge($name, $label, $value, $option_text, $all_data, $key0, $key1, $readonly, $disabled) {
    //funckija kreira jednu label select option celinu
    //name je naziv za for, name i id a label za text koji ce biti ispisan izmedju label tagova
    //value je vrednost za value, option_text je text izmedju option tagova
    //readonly i disabled su opcije za input
    //$key0 je kljuc_id a key1 je vrednost koju zelimo da vidimo kao text izmedju option tagova
    echo '<label class="control-label" for="'.$name.'">'.$label.'</label>';
    echo '<div class="controls">';
    echo '<select id="'.$name.'" name="'.$name.'" class="dropdownlist" '.$disabled.'>';
    echo '<option value="'.$value.'">'.$option_text.'</option>';
    for ($i=0; $i<count($all_data); $i++) {
      echo '<option value="'.$all_data[$i][$key0].'">'.$all_data[$i][$key1].'</option>';
    }
    echo '</select>';
    echo '</div>';
  }

  function create_button($type, $class, $inner_text) {
    echo '<button type="'.$type.'" class="'.$class.'">'.$inner_text.'</button>';
  }

  //***********************************KONTAKT FORM*******************************///

  function create_fieldset_kontakt_blank_form($gradovi, $klubovi, $pop_grupe) {

    $this -> create_controls_label_input_xlarge("ime", "Ime", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("telefon", "Telefon", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("adresa", "Adresa", "input", "text", "", "", "");
    $this -> create_controls_label_select_option_xlarge("grad_id", "Grad", "13", "Beograd", $gradovi, "grad_id", "grad", "", "");
    $this -> create_controls_label_select_option_xlarge("klub_id", "Klub", "30", "NEPOZNAT", $klubovi, "klub_id", "naziv", "", "");
    $this -> create_controls_label_select_option_xlarge("pop_grupa_id", "Popust", "1", "Bez Popusta", $pop_grupe, "pop_id", "grupa", "", "");

    echo '<div class="controls">';
    $this -> create_button("reset", "btn btn_cancel", "Reset");
    $this -> create_button("submit", "btn btn-large btn_save", "Sačuvaj");
  }

  function create_fieldset_kontakt_form($gradovi, $klubovi, $pop_grupe, $kontakt, $readonly, $disabled) {
    $kontakt_id   = htmlentities($kontakt['kontakt_id']);
    $ime          = htmlentities($kontakt['ime']);
    $telefon      = htmlentities($kontakt['telefon']);
    $adresa       = htmlentities($kontakt['adresa']);
    $grad_id      = htmlentities($kontakt['grad_id']);
    $grad         = htmlentities($kontakt['grad']);
    $klub_id      = htmlentities($kontakt['klub_id']);
    $klub         = htmlentities($kontakt['klub']);
    $pop_grupa_id = htmlentities($kontakt['pop_grupa_id']);
    $pop_grupa    = htmlentities($kontakt['pop_grupa']);
    $komentar     = "'".htmlentities($kontakt['komentar'])."'";

    $this -> create_controls_label_input_xlarge("kontakt_id", "ID", "input", "text", $kontakt_id, "readonly", "");
    $this -> create_controls_label_input_xlarge("ime", "Ime", "input", "text", $ime, $readonly, "");
    $this -> create_controls_label_input_xlarge("telefon", "Telefon", "input", "text", $telefon, $readonly, "");
    $this -> create_controls_label_input_xlarge("adresa", "Adresa", "input", "text", $adresa, $readonly, "");

    $this -> create_controls_label_select_option_xlarge("grad_id", "Grad", $grad_id, $grad, $gradovi, "grad_id", "grad", $readonly, $disabled);
    $this -> create_controls_label_select_option_xlarge("klub_id", "Klub", $klub_id, $klub, $klubovi, "klub_id", "naziv", $readonly, $disabled);
    $this -> create_controls_label_select_option_xlarge("pop_grupa_id", "Popust", $pop_grupa_id, $pop_grupa, $pop_grupe, "pop_id", "grupa", $readonly, $disabled);

    $this -> create_controls_label_2tags_xlarge("komentar", "Komentar", "textarea", "text", $komentar, $readonly, "");

    echo '<div class="controls">';
    if (strcmp($readonly, "readonly") === 0) {
      echo '<p><a href="kontakt_form_edit.php?kontakt_id='.$kontakt_id.'"><h3>IZMENI KONTAKT</h3></a></p>';
    } else {
      $this -> create_button("reset", "btn btn_cancel", "Reset");
      $this -> create_button("submit", "btn btn-large btn_save", "Sačuvaj");
    }
  }

  function create_kontakt_blank_form($legend, $gradovi, $klubovi, $pop_grupe){
    $this -> create_header_form($legend);
    $this -> create_fieldset_kontakt_blank_form($gradovi, $klubovi, $pop_grupe);
    $this -> create_footer_form();
  }

  function create_kontakt_form($legend, $gradovi, $klubovi, $pop_grupe, $kontakt, $readonly, $disabled){
    $this -> create_header_form($legend);
    $this -> create_fieldset_kontakt_form($gradovi, $klubovi, $pop_grupe, $kontakt, $readonly, $disabled);
    $this -> create_footer_form();
  }

  //***************************ARTIKL FORM**************************************///

  function create_fieldset_artikl_blank_form() {

    $this -> create_controls_label_input_xlarge("part_number", "Part Number", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("opis", "Opis", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("link", "Link", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("cena_net", "Cena net, Eur", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("cena_list", "Cena list, Eur", "input", "text", "", "", "");
    $this -> create_controls_label_2tags_xlarge("komentar", "Komentar", "textarea", "text", "", "", "");

    echo '<div class="controls">';
    $this -> create_button("reset", "btn btn_cancel", "Reset");
    $this -> create_button("submit", "btn btn-large btn_save", "Sačuvaj");
  }

  function create_fieldset_artikl_form($artikl, $readonly) {
    $artikl_id    = htmlentities($artikl['artikl_id']);
    $part_number  = htmlentities($artikl['part_number']);
    $opis         = "'".htmlentities($artikl['opis'])."'";
    $link         = htmlentities($artikl['link']);
    $cena_net     = htmlentities($artikl['cena_net']);
    $cena_list    = htmlentities($artikl['cena_list']);
    $komentar     = "'".htmlentities($artikl['komentar'])."'";

    $this -> create_controls_label_input_xlarge("part_number", "Part Number", "input", "text", $artikl_id, $readonly, "");
    $this -> create_controls_label_input_xlarge("part_number", "Part Number", "input", "text", $part_number, $readonly, "");
    $this -> create_controls_label_input_xlarge("opis", "Opis", "input", "text", $opis, $readonly, "");
    $this -> create_controls_label_input_xlarge("link", "Link", "input", "text", $link, $readonly, "");
    $this -> create_controls_label_input_xlarge("cena_net", "Cena net, Eur", "input", "text", $cena_net, $readonly, "");
    $this -> create_controls_label_input_xlarge("cena_list", "Cena list, Eur", "input", "text", $cena_list, $readonly, "");
    $this -> create_controls_label_2tags_xlarge("komentar", "Komentar", "textarea", "text", $komentar, $readonly, "");

    echo '<div class="controls">';
    if (strcmp($readonly, "readonly") === 0) {
      echo '<p><a href="http://localhost/trt_mrt/artikli/artikl_form_edit.php?artikl_id='.$artikl_id.'"><h3>IZMENI ARTIKL</h3></a></p>';
    } else {
      $this -> create_button("reset", "btn btn_cancel", "Reset");
      $this -> create_button("submit", "btn btn-large btn_save", "Sačuvaj");
    }
  }

  function create_artikl_blank_form($legend){
    $this -> create_header_form($legend);
    $this -> create_fieldset_artikl_blank_form();
    $this -> create_footer_form();
  }

  function create_artikl_form($legend, $artikli, $readonly){
    $this -> create_header_form($legend);
    $this -> create_fieldset_artikl_form($artikli, $readonly);
    $this -> create_footer_form();
  }

  //***************************ARTIKL ORDER FORM**************************************///
  function create_fieldset_artikl_order_blank_form($big_order_id, $kontakt_id, $popust, $mag_kolicina) {
    $popust_i_onkeyup = $popust.' onkeyup="calc_popust()"';
    $this -> create_controls_label_input_xlarge("big_order_id", "Big Order id", "input", "text", $big_order_id, "readonly", "");
    $this -> create_controls_label_input_xlarge("kontakt_id", "Kontakt id", "input", "text", $kontakt_id, "readonly", "");
    //$this -> create_controls_label_input_xlarge("mag_max_kolicina", "U Magacinu", "input", "number", $mag_kolicina,"","readonly");
    //$this -> create_controls_label_input_xlarge_with_range("mag_kolicina", "Magacin Kolicina", "input", "number", 0, 'step="1" onkeyup="calc_popust()" onchange="calc_popust()"', "", $mag_kolicina);
    $this -> create_controls_label_input_xlarge("kolicina", "Kolicina", "input", "number", 1, 'step="1" onkeyup="calc_popust()" onchange="calc_popust()"', "");
    $this -> create_controls_label_input_xlarge("popust", "Popust", "input", "number", $popust, 'onkeyup="calc_popust()"', "");
    $this -> create_controls_label_input_xlarge("ukupna_net", "Ukupna net, Eur", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("konacna_cena", "Konačna Cena, Eur", "input", "text", "", "", "");
    $this -> create_controls_label_2tags_xlarge("komentar", "Komentar", "textarea", "text", "", "", "");

    $this -> create_button("reset", "btn btn_cancel", "Reset");
    $this -> create_button("submit", "btn btn-large btn_save", "DODAJ U PORUDŽBINU");
  }

  function create_fieldset_artikl_order_form($artikl, $readonly) {
    $artikl_id    = htmlentities($artikl['artikl_id']);
    $part_number  = htmlentities($artikl['part_number']);
    $opis         = "'".htmlentities($artikl['opis'])."'";
    $link         = htmlentities($artikl['link']);
    $cena_net     = htmlentities($artikl['cena_net']);
    $cena_list    = htmlentities($artikl['cena_list']);
    $komentar     = "'".htmlentities($artikl['komentar'])."'";

    $this -> create_controls_label_input_xlarge("part_number", "Part Number", "input", "text", $artikl_id, $readonly, "");
    $this -> create_controls_label_input_xlarge("opis", "Opis", "input", "text", $part_number, $readonly, "");
    $this -> create_controls_label_input_xlarge("link", "Link", "input", "text", $opis, $readonly, "");
    $this -> create_controls_label_input_xlarge("cena_net", "Cena net, Eur", "input", "text", $link, $readonly, "");
    $this -> create_controls_label_input_xlarge("cena_list", "Cena list, Eur", "input", "text", $cena_net, $readonly, "");
    $this -> create_controls_label_2tags_xlarge("komentar", "Komentar", "textarea", "text", $komentar, $readonly, "");

    if (strcmp($readonly, "readonly") === 0) {
      echo '<p><a href="artikl_form_edit.php?artikl_id='.$artikl_id.'"><h3>IZMENI ARTIKL</h3></a></p>';
    } else {
      $this -> create_button("reset", "btn btn_cancel", "Reset");
      $this -> create_button("submit", "btn btn-large btn_save", "Sačuvaj");
    }
  }

  function create_artikl_order_blank_form(){
    $this -> create_header_form();
    $this -> create_fieldset_artikl_order_blank_form();
    $this -> create_footer_form();
  }

  function create_artikl_order_form($artikli, $readonly){
    $this -> create_header_form();
    $this -> create_fieldset_artikl_order_form($artikli, $readonly);
    $this -> create_footer_form();
  }

  //***********MAGACIN ARTIKL ORDER FORM************************
  function create_fieldset_artikl_order_magacin_form($big_order_id, $kontakt_id, $popust, $max_kol) {
    $popust_i_onkeyup = $popust.' onkeyup="calc_popust()"';
    $this -> create_controls_label_input_xlarge("big_order_id", "Big Order id", "input", "text", $big_order_id, "readonly", "");
    $this -> create_controls_label_input_xlarge("kontakt_id", "Kontakt id", "input", "text", $kontakt_id, "readonly", "");
    $this -> create_controls_label_input_xlarge("kolicina", "Kolicina", "input", "number", 1, 'min="0" max='.$max_kol.' step="1" onkeyup="calc_popust()" onchange="calc_popust(); check_max_kol();"', "");
    $this -> create_controls_label_input_xlarge("popust", "Popust", "input", "number", $popust, 'onkeyup="calc_popust()"', "");
    $this -> create_controls_label_input_xlarge("ukupna_net", "Ukupna net, Eur", "input", "text", "", "", "");
    $this -> create_controls_label_input_xlarge("konacna_cena", "Konačna Cena, Eur", "input", "text", "", "", "");
    $this -> create_controls_label_2tags_xlarge("komentar", "Komentar", "textarea", "text", "", "", "");

    $this -> create_button("reset", "btn btn_cancel", "Reset");
    $this -> create_button("submit", "btn btn-large btn_save", "DODAJ U PORUDŽBINU");
  }


}
