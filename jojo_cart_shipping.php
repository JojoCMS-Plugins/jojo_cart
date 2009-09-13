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

class jojo_plugin_Jojo_cart_shipping extends JOJO_Plugin
{
    public function _getContent() {
        /* Is there something in the cart? */
        $cart = call_user_func(array(Jojo_Cart_Class, 'getCart'));
        if (!count($cart->items)) {
            Jojo::redirect(_SECUREURL . '/cart/');
            exit;
        }

        /* Get the common freight methods available for each item */
        $commonMethods = false;
        foreach ($cart->items as $item) {
            $freight = new jojo_cart_freight($item['freight']);
            if ($commonMethods === false) {
                $commonMethods = $freight->getFreightMethods($cart->fields['shippingRegion']);
            } else {
                $methods = $freight->getFreightMethods($cart->fields['shippingRegion']);
                $commonMethods = (array_intersect($commonMethods, $methods));
            }
        }

        if ($commonMethods == false || count($commonMethods) == 0) {
            /* No common freight options */
            return array('content' => "There is no common shipping option for the items in your shopping cart");
        } elseif (count($commonMethods) == 1) {
            /* Only one option so auto choose this one */
            $shippingMethod = array_pop(array_keys($commonMethods));
            call_user_func(array(Jojo_Cart_Class, 'setShippingMethod'), array_pop(array_keys($commonMethods)));
            Jojo::redirect(_SECUREURL . '/cart/payment/');
        } else {
            /* Multiple options */
            $method = Jojo::getFormData('shippingmethod');
            if (isset($commonMethods[$method])) {
                /* User has choosen a valid method */
                call_user_func(array(Jojo_Cart_Class, 'setShippingMethod'), $method);
                Jojo::redirect(_SECUREURL . '/cart/payment/');
            }

            /* Display shipping selection page */
            global $smarty;
            $smarty->assign('selectedMethod', call_user_func(array(Jojo_Cart_Class, 'getShippingMethod')));

            /* Work out the shiping cost for each */
            foreach ($commonMethods as $method => $label) {
                call_user_func(array(Jojo_Cart_Class, 'setShippingMethod'), $method);
                $commonMethods[$method] = array(
                                            'label' => $label,
                                            'cost' => call_user_func(array(Jojo_Cart_Class, 'getFreight'))
                                            );
            }

            $currency = call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'));
            $smarty->assign('currency', $currency);
            $smarty->assign('currencysymbol', call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $currency));
            $smarty->assign('shippingMethods', $commonMethods);
            return array('content' => $smarty->fetch('jojo_cart_shipping.tpl'));
        }
        exit;
    }
}

