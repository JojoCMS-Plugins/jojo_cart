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

class jojo_plugin_jojo_cart_transaction_history extends JOJO_Plugin
{
    function _getContent()
    {
        global $smarty, $_USERGROUPS, $_USERID;
        $content = array();

        if ($_USERID) {
            if ($transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE userid = ? AND (status='payment_pending' OR status='complete') ORDER BY updated DESC", array($_USERID))) {
                foreach ($transactions as $k=>&$t) {
                    $transactions[$k]['cart'] = unserialize($t['data']);
                    foreach ($t['cart']->fields as $k => $f) {
                       if (strpos($k, 'shipping')!==false) {
                           $t['shipping'][$k] = $f;
                       } elseif (strpos($k, 'billing')!==false) {
                           $t['billing'][$k] = $f;
                       }
                    }
                    foreach ($t['cart']->items as $i) {
                    }
                    $t['numitems'] = 0;                  
                    foreach ($t['cart']->items as $k => &$f) {
                        $t['numitems'] = $t['numitems'] + $f['quantity'];
                        foreach (Jojo_Plugin_Jojo_Cart::getProductHandlers() as $productHandler) {
                            $item = call_user_func(array($productHandler, 'getProductDetails'), $f['id']);
                            if ($item) {
                                $f['details'] = $item;
                                break;
                            }
                        }
                    }
                    $t['cart']->order['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $t['cart']->order['currency']);
                    $t['completed'] = strftime('%A %e %B %Y at %l:%M%P', $t['updated']);
                    $t['handler'] = str_replace('jojo_plugin_jojo_cart_','', $t['handler']);
               }
                $smarty->assign('transactions', $transactions);
            }
        }
        
        $content['content'] = $smarty->fetch('jojo_cart_transaction_history.tpl');

        return $content;
    }

}
