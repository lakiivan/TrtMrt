$(function() {
  console.log('naplata js started');

  $( "#btn_nenaplaceno" ).click(function() {
  //$( "#div_nepregledano" ).children().css( "background-color", "red" );
  $( "#div_nenaplaceno" ).children().toggleClass("hide");
});

  $( "#btn_naplaceno" ).click(function() {
  //$( "#div_pregledano" ).children().css( "background-color", "blue" );
  $( "#div_naplaceno" ).children().toggleClass("hide");;
});

$( ".btn_din_eur" ).click(function() {
$( "#div_nepregledano" ).children().css( "background-color", "red" );
//console.log($(this).parent().find('.naplata').text());
$(this).parent().find('.naplata').toggleClass("hide");
//$(this).parent().children().toggleClass("hide");
});

//calc_ukupno_din_nakon_promene_kursa();
//calc_ukupno_evra_nakon_promene_din();

});

function calc_ukupno_evra_nakon_promene_din(kid) {
  console.log('ukupno eura nakon promene dinara started');
  var kurs = parseFloat($("#kurs" + kid).val());
  console.log("Kurs je - " + kurs);
  var din = parseFloat($("#nap_dinara" + kid).val());
  console.log("Za naplatu dinara - " + din);
  var zn_evra = din / kurs;
  console.log("Za naplatu evra - " + zn_evra);
  $("#nap_eura" + kid).val(zn_evra.toFixed(2));
}
function calc_ukupno_din_nakon_promene_kursa(kid) {
  var kurs = parseFloat($("#kurs" + kid).val());
  console.log("Kurs je - " + kurs);
  var eur = parseFloat($("#zn_eura" + kid).val());
  console.log("Za naplatu eura - " + eur);
  var zn_din = kurs * eur;
  console.log("Za naplatu dinara - " + zn_din);
  $("#zn_dinara" + kid).val(zn_din);
  $("#nap_dinara" + kid).val(zn_din);
}
