/* Add AJAX behaviour to save quantities automatically */
$(document).ready(function(){

    $('input.cart-quantity').bind('keyup input mousewheel', function(){
        var rowid = $(this).closest('div.row').attr('id');
        var id = $(this).attr('id');
        var code = id.replace(/quantity\[(.*?)\]/ig, "$1");
        var discountcode = $('#discountCode').val();
        $.getJSON('json/jojo_cart_change_quantity.php', {qty: $(this).val(), code: code, rowid: rowid, discount: discountcode}, change_quantity_callback);
    });

    $('select.cart-quantity').change(function(){
        var rowid = $(this).closest('div.row').attr('id');
        var id = $(this).attr('id');
        var code = id.replace(/quantity\[(.*?)\]/ig, "$1");
        var discountcode = $('#discountCode').val();
        $.getJSON('json/jojo_cart_change_quantity.php', {qty: $(this).val(), code: code, rowid: rowid, discount: discountcode}, change_quantity_callback);
    });

    $('.cart-remove a').bind('click', function(){
        event.preventDefault();
        var rowid = $(this).closest('div.row').attr('id');
        var discountcode = $('#discountCode').val();
        var code = $(this).attr('data-id');
        $.getJSON('json/jojo_cart_change_quantity.php', {qty: 0, code: code, rowid: rowid, discount: discountcode}, change_quantity_callback);
        return false;
    });

    $('#applyDiscount').bind('click', function(){
        var discountcode = $('#discountCode').val();
        $.getJSON('json/jojo_cart_change_quantity.php', {discount: discountcode}, change_quantity_callback);
        return false;
    });

    $('input.cart-quantity').bind('focus', function(){
        if ($(this).val() == '0') {
          $(this).val('');
        }
    });

    $('#giftwrap').bind('click', function(){
        $('#giftmessagefield').toggle();
        if ($(this).is(':checked')) {
            $(this).after('<input type="hidden" name="nogiftwrap" id="nogiftwrap" value="1"/>');
            var message = $('#giftmessage').val();
            $.getJSON('json/jojo_cart_change_quantity.php', {giftwrap: true, giftmessage: message}, change_quantity_callback);
        } else {
            $('#nogiftwrap').remove();
            $.getJSON('json/jojo_cart_change_quantity.php', {nogiftwrap: true}, change_quantity_callback);
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
    if ($('#discountCode').length>0 && data.discount) {
      $('#discountCode').html(data.discount.code);
    }
    if (!data.quantity) {
        $('#'+data.rowid).hide();
        $('#'+data.rowid+' .cart-linetotal span').html('');
    } else {
        $('#'+data.rowid+' .cart-quantity').val(data.quantity);
        $('#'+data.rowid+' .cart-linetotal span').html(data.linetotal.toFixed(2));
    }
    if (data.items) {
            $.each(data.items, function(itemid, item) {
                if (item.baseprice!=item.netprice) {
                    $('#row_'+itemid+' .cart-price strike').show();
                    $('#row_'+itemid+' .cart-price strike').html(item.baseprice);
                    $('#row_'+itemid+' .cart-price span').html(item.netprice);
                } else {
                    $('#row_'+itemid+' .cart-price strike').hide();
                    $('#row_'+itemid+' .cart-price span').html(item.netprice);
                }
                $('#row_'+itemid+' .cart-linetotal span').html(item.linetotal.toFixed(2));
            });
    }
    $('#cart-subtotal span').html(data.subtotal.toFixed(2));
    if (data.freight && $.isNumeric(data.freight)) {
    	$('#cart-freight span').html(parseFloat(data.freight).toFixed(2));
    } else if (data.freight!==false){
    	$('#cart-freight span').html(data.freight);
    }
    if (!data.surcharge && $('#cart-surcharge span').length>0) {
      $('#cart-surcharge').hide();
    }
    if (!data.order.fixedorder && $('#cart-fixedorder span').length>0) {
      $('#cart-fixedorder').hide();
    } else {
      $('#cart-fixedorder span').html(data.order.fixedorder.toFixed(2))
      $('#cart-fixedorder').show();
    }
   $('.cartItemTotal').html(data.itemtotal);
    $('.cart-total span').html(data.total.toFixed(2));
    console.log(data.errors);
    if (!data.errors) {
      $('.shoppingcart .errors').hide();
    } else {
        var errors = '';
        $.each(data.errors, function(key, value) {
            errors = errors + '<li>' + value + '</li>';
        });
        $('.shoppingcart .errors').html(errors);
        $('.shoppingcart .errors').show();

    }
}