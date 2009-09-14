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
 * @author  Mike Cochrane <mikec@mikenz.geek.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class jojo_plugin_Jojo_cart_payment extends JOJO_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS, $_USERID;

        $content = array();

        /* Make sure there's something in the cart */
        $cart = call_user_func(array(Jojo_Cart_Class, 'getCart'));
        if (!count($cart->items)) {
            Jojo::redirect(_SECUREURL . '/cart/');
            exit;
        }

        /* Prepopulate fields to save data entry while testing */
        $testmode = call_user_func(array(Jojo_Cart_Class, 'isTestMode'));
        if ($testmode) {
            $cart->order['cardType']        = 'mastercard';
            $cart->order['cardNumber']      = '4111111111111111';
            $cart->order['cardExpiryMonth'] = '09';
            $cart->order['cardExpiryYear']  = '09';
            $cart->order['cardName']        = 'Test Cardholder';
        }

        /* Calculate totals */
        $cart->order['subtotal'] = call_user_func(array(Jojo_Cart_Class, 'subTotal'));
        $cart->order['freight']  = call_user_func(array(Jojo_Cart_Class, 'getFreight'));
        $cart->order['amount']   = call_user_func(array(Jojo_Cart_Class, 'total'));
        if (isset($cart->fields['shipping_rd'])) {
            $surcharge = Jojo::selectRow("SELECT rural_surcharge FROM {cart_region} WHERE regioncode = ?", array($cart->fields['shippingRegion']));
            $cart->order['surcharge'] = $surcharge['rural_surcharge'];
        }
        call_user_func(array(Jojo_Cart_Class, 'saveCart'));

        /* are we using the discount code functionality? No need to show the UI if the discount table is empty or if discount has already been set */
        $data = Jojo::selectRow("SELECT COUNT(*) AS numdiscounts FROM {discount}");
        $usediscount = ($data['numdiscounts'] > 0) ? true : false;
        $smarty->assign('usediscount', $usediscount);

        /* Assign vars to Smarty */
        $smarty->assign('countries', Jojo::selectQuery("SELECT cc.countrycode AS code, cc.name, 0 AS special FROM {cart_country} AS cc ORDER BY name"));
        $smarty->assign('token',     $cart->token);
        $smarty->assign('items',     $cart->items);
        $smarty->assign('fields',    $cart->fields);
        $smarty->assign('order',     $cart->order);
        $smarty->assign('discount',  $cart->discount);

        /* Add checkout breadcrumb */
        $breadcrumbs                      = $this->_getBreadCrumbs();
        $breadcrumb                       = array();
        $breadcrumb['name']               = 'Checkout';
        $breadcrumb['rollover']           = 'Checkout';
        $breadcrumb['url']                = 'cart/payment/';
        $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
        $content['breadcrumbs']           = $breadcrumbs;

        /* display a checkout form for each payment method */
        $paymentoptions = array();
        foreach (call_user_func(array(Jojo_Cart_Class, 'getPaymentHandlers')) as $ph) {
            $options = call_user_func(array($ph, 'getPaymentOptions'));
            $paymentoptions = array_merge($paymentoptions, $options);
        }
        $smarty->assign('paymentoptions', $paymentoptions);

        /* hook for plugins to make custom actions */
        Jojo::runHook('jojo_cart_checkout', array());

        $content['title']      = 'Checkout';
        $content['seotitle']   = 'Checkout';
        $content['content']    = $smarty->fetch('jojo_cart_payment.tpl');
        $content['javascript'] = $smarty->fetch('jojo_cart_payment_js.tpl');
        $content['head']       = $smarty->fetch('jojo_cart_payment_head.tpl');
        return $content;
    }
}
