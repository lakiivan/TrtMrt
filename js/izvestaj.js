$(function() {
  console.log('izvestaj js started');

  $( "#btn_poruceni" ).click(function() {
  $( "#div_poruceni" ).children().toggleClass("hide");
});

  $( "#btn_nisu_stigli" ).click(function() {
  $( "#div_nisu_stigli" ).children().toggleClass("hide");;
});

  $( "#btn_stigli_u_visku" ).click(function() {
  $( "#div_stigli_u_visku" ).children().toggleClass("hide");;
});

  $( "#btn_neisporuceni" ).click(function() {
  $( "#div_neisporuceni" ).children().toggleClass("hide");;
});

  $( "#btn_nenaplaceni" ).click(function() {
  $( "#div_nenaplaceni" ).children().toggleClass("hide");;
});



$( ".btn_din_eur" ).click(function() {
$( "#div_nepregledano" ).children().css( "background-color", "red" );
$(this).parent().find('.naplata').toggleClass("hide");
});
});
