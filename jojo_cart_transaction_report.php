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

        $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 ORDER BY id DESC");
        foreach($transactions as &$transaction) {
            $cart = unserialize($transaction['data']);
            $transaction['datetime'] = $transaction['updated'];
            $transaction['status'] = $transaction['status'];
            $transaction['handler'] = str_replace('jojo_plugin_jojo_cart_','', $transaction['handler']);
            if (is_array($cart)) {
                $transaction['FirstName'] = $cart['fields']['FirstName'];
                $transaction['LastName']  = $cart['fields']['LastName'];
                $transaction['amount']    = $cart['order']['amount'];
                $transaction['currency'] = isset($cart['order']['currency']) ? $cart['order']['currency'] : '';
            } elseif (is_object($cart) ) {
                $transaction['FirstName'] = !empty($cart->fields['billing_firstname']) ? $cart->fields['billing_firstname'] : $cart->fields['shipping_firstname'];
                $transaction['LastName']  = !empty($cart->fields['billing_lastname']) ? $cart->fields['billing_lastname'] : $cart->fields['shipping_lastname'];
                $transaction['amount']    = $cart->order['amount'];
                $transaction['currency'] = isset($cart->order['currency']) ? $cart->order['currency'] : '';
            }
            $transaction['currency'] = !empty($transaction['currency']) ? $transaction['currency'] : call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $transaction['token']);
            $transaction['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $transaction['currency']);
        }
        $smarty->assign('transactions', $transactions);

        $content['title'] = 'Transaction report';
        $content['content'] = $smarty->fetch('jojo_cart_transaction_report.tpl');

        return $content;
    }
}
