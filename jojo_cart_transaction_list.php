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

class jojo_plugin_jojo_cart_transaction_list extends JOJO_Plugin
{
    function _getContent()
    {
        global $smarty;

        $content = array();

        $token = Jojo::getFormData('token', false);
        if(!$token) {
                include(_BASEPLUGINDIR . '/jojo_core/404.php');
                exit;
        } else {

          jojo_plugin_Admin::adminMenu();

          $transaction = Jojo::selectRow("SELECT * FROM {cart} WHERE token=? ",$token);
              $cart = unserialize($transaction['data']);
              $smarty->assign('items',        $cart->items);
              $shipping = array();
              $billing = array();
              foreach ($cart->fields as $k => $f) {
               if (strpos($k, 'shipping')!==false) {
                   $shipping[$k] = $f;
               } elseif (strpos($k, 'billing')!==false) {
                   $billing[$k] = $f;
               }
              }
              $smarty->assign('shipping',       $shipping);
              $smarty->assign('billing',       $billing);
              $smarty->assign('fields',       $cart->fields);
              $smarty->assign('order',        $cart->order);
              $smarty->assign('receipt',        $cart->receipt);
              $smarty->assign('id',           $transaction['id']);
              $currency = isset($cart->order['currency']) ? $cart->order['currency'] : call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $transaction['token']);
              $smarty->assign('currency', $currency);
              $smarty->assign('currencysymbol', call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $currency));

          $content['title'] = 'Transaction report';
          $content['content'] = $smarty->fetch('jojo_cart_transaction_list.tpl');

          return $content;
        }
    }

  function getCorrectUrl()
  {
    //Assume the URL is correct
    return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  }

}