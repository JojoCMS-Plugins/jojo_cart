/* Add AJAX behaviour to save quantities automatically */
$(document).ready(function(){
  $('input.cart-quantity').bind('keyup input mousewheel', function(){
    var rowid = $(this).closest('tr').attr('id');
    var id = $(this).attr('id');
    var code = id.replace(/quantity\[(.*?)\]/ig, "$1");
    $.getJSON('json/jojo_cart_change_quantity.php', {qty: $(this).val(), code: code, rowid: rowid}, change_quantity_callback);
  });
  $('select.cart-quantity').change(function(){
    var rowid = $(this).closest('tr').attr('id');
    var id = $(this).attr('id');
    var code = id.replace(/quantity\[(.*?)\]/ig, "$1");
    $.getJSON('json/jojo_cart_change_quantity.php', {qty: $(this).val(), code: code, rowid: rowid}, change_quantity_callback);
  });
  $('input.cart-quantity').bind('focus', function(){
      if ($(this).val() == '0') {
          $(this).val('');
      }
  });
  $('input#giftwrap').click(function(){
      $('#nogiftwrap').remove();
      $('#giftmessagefield').toggle();
      if ($(this).is(':checked')) {
      }else{
          $(this).after('<input type="hidden" name="nogiftwrap" id="nogiftwrap" value="1"/>');
      }
  });
  if ($('#cart-updatebuttons #update').length>0) { $('#cart-updatebuttons #update').hide(); }
  if ($('#order-history').length>0) { 
    $('#order-history .order-details').hide();
    $('#order-history .show-details').bind('click', function(){
        targetid = $(this).attr('data-target');
        $('#' + targetid).toggle();
        $('span', this).toggle();
    });
  }
  
});

function change_quantity_callback(data)
{
    $('#'+data.rowid+' .cart-linetotal span').html(data.linetotal.toFixed(2));
    if (data.quantity==0 && $('.jojo_cart .orderlist').length==0) {
        $('#'+data.rowid).hide();
    } else {
        $('#'+data.rowid+' .cart-quantity').val(data.quantity);
    }
    $('#cart-subtotal span').html(data.subtotal.toFixed(2));
    if (data.freight!==false) {
    	$('#cart-freight span').html(data.freight.toFixed(2));
    }
    if (!data.surcharge && $('#cart-surcharge span').length>0) {
      $('#cart-surcharge').hide();
    }
    $('.cartItemTotal').html(data.itemtotal);
    $('#cart-total span').html(data.total.toFixed(2));
}