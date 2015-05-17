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

/* save the new quantities to the cart */
call_user_func(array(Jojo_Cart_Class, 'setQuantity'), $code, $quantity);

$cart = call_user_func(array(Jojo_Cart_Class, 'getCart'));

/* send back the data that has changed in a JSON object for the javascript callback to work with */
$response = array();
$response['rowid'] = $id ? $id : 'row_' . $code;
$response['code'] = $code;
 
$response['quantity'] = isset($cart->items[$code]) ? $cart->items[$code]['quantity'] : 0;
$response['linetotal'] = isset($cart->items[$code]) ? $cart->items[$code]['linetotal'] : 0;
$response['itemtotal'] = call_user_func(array(Jojo_Cart_Class, 'getNumItems'), $cart->items);
$response['currency'] = call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $cart->token);
$response['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $response['currency']);
$response['freight'] = call_user_func(array(Jojo_Cart_Class, 'getFreight'));
$response['surcharge'] = call_user_func(array(Jojo_Cart_Class, 'getSurcharge'));
$response['subtotal'] = call_user_func(array(Jojo_Cart_Class, 'subTotal'));
$response['total'] = call_user_func(array(Jojo_Cart_Class, 'total'));

echo json_encode($response);
exit;