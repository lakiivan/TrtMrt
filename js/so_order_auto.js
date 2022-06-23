
$(function() {
  $("#odabir_artikla").autocomplete({ source: "http://localhost/trt_mrt/big_order/bo_util/bo_pn_auto.php" });
  console.log('so auto started');
});


function get_artikl_data2(){
  console.log('funkcija get artikl data2 je uspesno startovana');
  var odabrani_artikal = $("#odabir_artikla").val();
  var podaci = odabrani_artikal.split('-');
  console.log('podaci 0 - ' + podaci);
  console.log('podaci 1 - ' + podaci[1]);
  var part_number = podaci[0];
  var artikl_id = podaci[1];
  console.log('part number je - ' + part_number);
  $("#artikl_id").val(artikl_id);
  $("#part_number").val(part_number);
}

function calc_cena(control) {
  var id_name = "#" + control.id;
  //var isp_kol = parseInt($("#isporucenaKolicina_0").val());
  var isp_kol = parseInt($(id_name).val());
  //var isp2    = parseInt(control.val());
  let index = id_name.indexOf('_') + 1;
  var suffix = id_name.substring(index);
  var id_cena = "#cena_" + suffix;
  var id_ukupna_cena = "#ukupnaCena_" + suffix;
  console.log("Control id: " + id_name);
  console.log("Control value preko id_name: " + isp_kol);
  //console.log("Control value preko kontrol val: " + isp2);
  console.log("Index: " + index);
  console.log("Suffix: " + suffix);
  console.log("Cena id: " + id_cena);
  var cena = parseFloat($(id_cena).text());
  console.log("Cena je: " + cena);
  console.log("UKUPNA Cena iz polja je: " + $("ukupnaCena_0").innerHTML);
  var ukupna_cena = isp_kol * cena;

  console.log("UKUPNA Cena je: " + ukupna_cena);
  $(id_ukupna_cena).text(ukupna_cena.toFixed(1));
  console.log("potraga za p: " + $(id_ukupna_cena).text());
  var cena_sum = calc_sum_cena();
  console.log("SUMA ukupnih cena: " + cena_sum);
  $("#cena_sum").text(cena_sum);
}

function calc_sum_cena() {
  var i = 0;
  var cena_sum  = 0;
  var id_ukupna_cena  = "#ukupnaCena_" + i;
  var id_isp_kol  = "#isporucenaKolicina_" + i;
  var id_cena     = "#cena_" + i;
  var isporuka_input  = $(id_isp_kol).val();
  while(isporuka_input != null) {
    var cena      = $(id_cena).text();
    var ukupnaCena = (parseInt(isporuka_input)*parseFloat(cena)).toFixed(1);
    cena_sum += parseFloat(ukupnaCena);
    i++;
    id_isp_kol  = "#isporucenaKolicina_" + i;
    id_cena     = "#cena_" + i;
    isporuka_input = $(id_isp_kol).val();
    cena = $(id_cena).text();
  }
  return cena_sum.toFixed(1);
}

function pregled() {
  console.log("Pregled Started!");
  var i = 0;
  var id_por_kol    = "#porucenaKolicina_" + i;
  var id_pre_kol    = "#pregledanaKolicina_" + i;
  var id_pregledano = "#pregledano_" + i;
  var pregled_input = $(id_por_kol).val();

  while(pregled_input != null) {
    $(id_pre_kol).removeClass("hide");
    $(id_pre_kol).val(pregled_input);
    $(id_pregledano).val(1);
    console.log("porucena kolicina: " + pregled_input);

    i++;
    id_por_kol = "#porucenaKolicina_" + i;
    id_pre_kol = "#pregledanaKolicina_" + i;
    id_pregledano = "#pregledano_" + i;
    pregled_input = $(id_por_kol).val();
  }
  $("#button_p").addClass("hide");
  $("#button_in").removeClass("hide");
  $("#button_i").removeClass("hide");
}

function isporuka(i_naplata) {
  console.log("Isporuka Started!");
  var i = 0;
  var id_pre_kol      = "#pregledanaKolicina_" + i;
  var id_isp_kol      = "#isporucenaKolicina_" + i;
  var id_isporuceno   = "#isporuceno_" + i;
  var id_naplaceno    = "#naplaceno_" + i;
  var id_cena         = "#cena_" + i;
  var id_ukupna_cena  = "#ukupnaCena_" + i;
  var isporuka_input   = $(id_pre_kol).val();
  var cena            = $(id_cena).text();
  var cena_sum = 0;
  while(isporuka_input != null) {
    console.log("i: " + i);
    $(id_isp_kol).removeClass("hide");
    $(id_isp_kol).val(isporuka_input);
    $(id_isporuceno).val(1);
    console.log("Cena: " + cena);
    var ukupnaCena = (parseInt(isporuka_input)*parseFloat(cena)).toFixed(1);
    cena_sum += parseFloat(ukupnaCena);
    console.log("Ukupna cena je: " + ukupnaCena);
    console.log("SUMA svih cena je: " + ukupnaCena);
    $(id_ukupna_cena).text(ukupnaCena);

    if(i_naplata) {
      $(id_naplaceno).val(1);
    } else {
      $("#button_n").removeClass("hide");
    }

    i++;
    id_pre_kol    = "#pregledanaKolicina_" + i;
    id_isp_kol    = "#isporucenaKolicina_" + i;
    id_cena       = "#cena_" + i;
    id_ukupna_cena= "#ukupnaCena_" + i;
    id_isporuceno = "#isporuceno_" + i;
    id_naplaceno = "#naplaceno_" + i;
    console.log("id_ukupna_cena: " + id_ukupna_cena);
    isporuka_input = $(id_pre_kol).val();
    cena = $(id_cena).text();
  }
  $("#button_i").addClass("hide");
  $("#button_in").addClass("hide");
  $("#cena_sum").text(cena_sum.toFixed(1));
}



function naplata() {
  console.log("Naplata Started!");
  var i = 0;
  var id_pre_kol      = "#pregledanaKolicina_" + i;
  var id_naplaceno   = "#naplaceno_" + i;
  var isporuka_input = $(id_pre_kol).val();
  while(isporuka_input != null) {
    console.log("i: " + i);
    $(id_naplaceno).val(1);
    i++;
    id_naplaceno = "#naplaceno_" + i;
    id_pre_kol      = "#pregledanaKolicina_" + i;
    isporuka_input = $(id_pre_kol).val();
  }
  $("#button_n").addClass("hide");
}

function go_to_ao_form() {
  console.log("go to artikl order form Started!");
  var artikl_id = $("#artikl_id").val();
  var big_order_id = $("#big_order_id").val();
  var kontakt_id = $("#kontakt_id").val();
  console.log(artikl_id);
  location.href = "https://localhost/trt_mrt/big_order/porucivanje/artikl_order_form.php?big_order_id="
  + big_order_id + "&kontakt_id=" + kontakt_id + "&artikl_id=" + artikl_id ;
}

function reset_odabir_artikla() {
  console.log("RESET odabir artikla started!");
  var odabir_artikla = $("#odabir_artikla").val();
  console.log(odabir_artikla);
  $("#odabir_artikla").val('');
}
