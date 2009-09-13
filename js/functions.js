/* Add AJAX behaviour to save quantities automatically */
$(document).ready(function(){
  $('input.quantity').change(function(){
    var id = $(this).attr('id');
    var code = id.replace(/quantity\[(.*?)\]/ig, "$1");
    $.getJSON('json/jojo_cart_change_quantity.php', {qty: $(this).val(), code: code}, change_quantity_callback);
  });

  $('input.quantity').bind('focus', function(){
      if ($(this).val() == '0') {
          $(this).val('');
      }
  });
});

function change_quantity_callback(data)
{
    $('#row_'+data.code+' td.cart-linetotal span.linetotal').html(data.linetotal.toFixed(2));
    $('#row_'+data.code+' input.quantity').val(data.quantity);
    $('#subtotal').html(data.subtotal.toFixed(2));
    $('#total').html(data.total.toFixed(2));
}