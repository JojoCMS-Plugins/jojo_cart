<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2008 Harvey Kane <code@ragepank.com>
 * Copyright 2008 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Mike Cochrane <mikec@mikenz.geek.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

if (class_exists(Jojo_Cart_Class)) {
    $currency = call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'));
    $smarty->assign('currency', $currency);
    $smarty->assign('currencysymbol', call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $currency));

    /* Show shopping cart contents in sidebar */
    $cartItems = call_user_func(array(Jojo_Cart_Class, 'getItems'));

    if (!count($cartItems)) {
        $smarty->assign('cartisempty', true);
    } else {
        $smarty->assign('items', $cartItems);
        $smarty->assign('total', call_user_func(array(Jojo_Cart_Class, 'total')));
        $smarty->assign('cartisempty', false);
    }
}
