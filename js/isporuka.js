$(function() {
  console.log('isporuka js started');

  $( "#btn_neisporuceno" ).click(function() {
  //$( "#div_nepregledano" ).children().css( "background-color", "red" );
  $( "#div_neisporuceno" ).children().toggleClass("hide");
});

  $( "#btn_isporuceno" ).click(function() {
  //$( "#div_pregledano" ).children().css( "background-color", "blue" );
  $( "#div_isporuceno" ).children().toggleClass("hide");;
});

$( ".btn_din_eur" ).click(function() {
$( "#div_nepregledano" ).children().css( "background-color", "red" );
//console.log($(this).parent().find('.naplata').text());
$(this).parent().find('.naplata').toggleClass("hide");
//$(this).parent().children().toggleClass("hide");
});

calc_ukupno_din_nakon_promene_kursa();

});

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
