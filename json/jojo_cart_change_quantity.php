<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 JojoCMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Mike Cochrane <mikec@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

$id  = Jojo::getFormData('rowid', false);
$quantity  = Jojo::getFormData('qty', false);
$code = Jojo::getFormData('code', false);
$discount = Jojo::getFormData('discount', false);

if ($code) {
    /* save the new quantities to the cart */
    call_user_func(array(Jojo_Cart_Class, 'setQuantity'), $code, $quantity);
}

call_user_func(array(Jojo_Cart_Class, 'applyDiscountCode'), $discount);

/* Apply GiftWrap */
if (Jojo::getFormData('nogiftwrap')) {
    call_user_func(array(Jojo_Cart_Class, 'setGiftWrap'), false);
}
if (Jojo::getFormData('giftwrap')) {
    $giftmessage = Jojo::getFormData('giftmessage', '');
    call_user_func(array(Jojo_Cart_Class, 'setGiftWrap'), true, $giftmessage);
}
/* send back the data that has changed in a JSON object for the javascript callback to work with */
$response = array();
$response['freight'] = call_user_func(array(Jojo_Cart_Class, 'getFreight'));
$response['surcharge'] = call_user_func(array(Jojo_Cart_Class, 'getSurcharge'));
$response['subtotal'] = call_user_func(array(Jojo_Cart_Class, 'subTotal'));
$response['total'] = call_user_func(array(Jojo_Cart_Class, 'total'));

$cart = call_user_func(array(Jojo_Cart_Class, 'getCart'));
$response['currency'] = call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $cart->token);
$response['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $response['currency']);
$response['rowid'] = $id ? $id : 'row_' . $code;
$response['code'] = $code;
if ($code) {
    $response['quantity'] = isset($cart->items[$code]) ? $cart->items[$code]['quantity'] : 0;
    $response['linetotal'] = isset($cart->items[$code]) ? $cart->items[$code]['linetotal'] : 0;
}
$response['discount'] = $cart->discount;
$response['errors'] = $cart->errors;
$response['items'] = $cart->items;
$response['itemtotal'] = call_user_func(array(Jojo_Cart_Class, 'getNumItems'), $cart->items);
$response['order'] = $cart->order;

echo json_encode($response);
exit;