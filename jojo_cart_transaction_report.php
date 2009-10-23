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
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class jojo_plugin_jojo_cart_transaction_report extends JOJO_Plugin
{
    function _getContent()
    {
        global $smarty;

        $content = array();

        $token = Jojo::getFormData('token', false);

        if ($token) {
            echo $token;
            exit;
        }

        jojo_plugin_Admin::adminMenu();

        $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 ORDER BY updated DESC");
        $n = count($transactions);
        for ($i=0; $i<$n; $i++) {
            $cart = unserialize($transactions[$i]['data']);
            $transactions[$i]['datetime'] = $transactions[$i]['updated'];
            $transactions[$i]['status'] = $transactions[$i]['status'];

            if (is_array($cart)) {
                $transactions[$i]['FirstName'] = $cart['fields']['FirstName'];
                $transactions[$i]['LastName']  = $cart['fields']['LastName'];
                $transactions[$i]['amount']    = $cart['order']['amount'];
                $transactions[$i]['currency']  = $cart['order']['currency'];
            } elseif (is_object($cart) ) {
                $transactions[$i]['FirstName'] = !empty($cart->fields['billing_firstname']) ? $cart->fields['billing_firstname'] : $cart->fields['shipping_firstname'];
                $transactions[$i]['LastName']  = !empty($cart->fields['billing_lastname']) ? $cart->fields['billing_lastname'] : $cart->fields['shipping_lastname'];
                $transactions[$i]['amount']    = $cart->order['amount'];
                $transactions[$i]['currency']  = $cart->order['currency'];
            }
            $transactions[$i]['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $transactions[$i]['currency']);
        }
        $smarty->assign('transactions', $transactions);

        $content['title'] = 'Transaction report';
        $content['content'] = $smarty->fetch('jojo_cart_transaction_report.tpl');

        return $content;
    }
}
