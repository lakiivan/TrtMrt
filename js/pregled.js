$(function() {
  console.log('preled js started');

  $( "#btn_nepregledano" ).click(function() {
  //$( "#div_nepregledano" ).children().css( "background-color", "red" );
  $( "#div_nepregledano" ).children().toggleClass("hide");
});

  $( "#btn_pregledano" ).click(function() {
  //$( "#div_pregledano" ).children().css( "background-color", "blue" );
  $( "#div_pregledano" ).children().toggleClass("hide");;
});

});
