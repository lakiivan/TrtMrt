$(function() {
  //switch_net();
});

function remove_net() {
  $("#net_price").remove();
  $("tr").each(function() {
    $(this).children("td:eq(5)").remove();
});
}
