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

class jojo_plugin_Jojo_cart_update extends JOJO_Plugin
{
    function __construct()
    {
        Jojo::runHook('jojo_cart_update:top');
        
        $action = Jojo::getFormData('action');

        /* Add item */
        if ($action == 'add') {
            call_user_func(array(Jojo_Cart_Class, 'addToCart'), Jojo::getFormData('id'));
        }

        /* Remove item */
        if ($action == 'remove') {
            call_user_func(array(Jojo_Cart_Class, 'removeFromCart'), Jojo::getFormData('id'));
        }

        /* Update Quantities */
        if (Jojo::getFormData('update')) {
            foreach (Jojo::getFormData('quantity', array()) as $id => $qty) {
                call_user_func(array(Jojo_Cart_Class, 'setQuantity'), $id, $qty);
            }
        }

        /* Empty cart */
        if ($action == 'empty' || Jojo::getFormData('empty')) {
            call_user_func(array(Jojo_Cart_Class, 'emptyCart'));
        }

        /* Apply Discount */
        if (Jojo::getFormData('applyDiscount')) {
            call_user_func(array(Jojo_Cart_Class, 'applyDiscountCode'), Jojo::getFormData('discountCode'));
        }
        
        /* Apply Discount (backwards compatibility) */
        if (Jojo::getFormData('discount')) {
            call_user_func(array(Jojo_Cart_Class, 'applyDiscountCode'), Jojo::getFormData('discount'));
        }

        /* Redirect to the Checkout */
        if (Jojo::getFormData('checkout')) {
            Jojo::redirect(_SECUREURL . '/cart/checkout/');
        }

        /* Redirect back to the cart */
        if ((_PROTOCOL == 'http://') && (_SITEURL != _SECUREURL)) { //TODO - no need for session id passing when using http://domain.com and https://domain.com
            
            /* Pass the session id when going from non secure to secure if the secure session has not already been started */
            if (isset($_SESSION['secure_session_started']) && ($_SESSION['secure_session_started'] == true)) {
                $url = _SECUREURL . '/cart/';
            } else {
                $url = _SECUREURL . '/cart/?sid='.session_id();
            }

            /* analytics tracking requires a javascript redirect, not a server redirect */
            if (Jojo::getOption('analyticscode', false) && (Jojo::getOption('crossdomainanalytics', 'no') == 'yes')) {
                global $smarty;
                $smarty->assign('redirect', $url);
                $smarty->display('analytics_cross_domain_redirect.tpl');
                exit;
            } else {
                Jojo::redirect($url);
            }
        }
        Jojo::redirect(_SECUREURL . '/cart/');
    }
}
