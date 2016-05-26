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
            $ignore = array('firstname', 'lastname', 'phone', 'email', 'company', 'address1', 'address2', 'suburb', 'city', 'postcode', 'state', 'country');
               
            if (isset($cart->fields['shippingRegion'])) {
                if ($shippingRegion = $cart->fields['shippingRegion']) {
                    $shippingRegion = Jojo::selectRow("SELECT name FROM {cart_region} WHERE regioncode = ?", array($shippingRegion));
                    unset($cart->fields['shippingRegion']);
                    $smarty->assign('shippingRegion',  $shippingRegion['name']);
                }
            }
            if (isset($cart->fields['shippingMethodName'])) {
                if (!$cart->fields['shippingMethodName']) {
                    $shippingMethod = Jojo::selectRow("SELECT longname FROM {cart_freightmethod} WHERE id = ?", array($shippingMethod));
                    unset($cart->fields['shippingMethod']);
                    $smarty->assign('shippingMethod',  $shippingMethod['longname']);
                } else {
                    $smarty->assign('shippingMethod',  $cart->fields['shippingMethodName']);
                    unset($cart->fields['shippingMethod']);
                    unset($cart->fields['shippingMethodName']);
                }
            }

            foreach ($cart->fields as $k => $f) {
               if (strpos($k, 'shipping')!==false && !in_array(str_replace('shipping_', '', $k), $ignore) ) {
                   $shipping[$k] = $f;
               } elseif (strpos($k, 'billing')!==false && !in_array(str_replace('billing_', '', $k), $ignore) ) {
                   $billing[$k] = $f;
               }
            }

            $smarty->assign('shipping',       $shipping);
            $smarty->assign('billing',       $billing);
            $smarty->assign('fields',       $cart->fields);
            $smarty->assign('order',        $cart->order);
            $smarty->assign('points',        isset($cart->points) ? $cart->points : '');
            $smarty->assign('receipt',        isset($cart->receipt) ? $cart->receipt : '');
            $smarty->assign('id',           $transaction['id']);
            $smarty->assign('discount', isset($cart->discount) ? $cart->discount : '');
            $currency = isset($cart->order['currency']) ? $cart->order['currency'] : call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $transaction['token']);
            $smarty->assign('currency', $currency);
            $smarty->assign('currencysymbol', call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $currency));

            /* Build list of countries for UI */
            $countries = Jojo::selectQuery("SELECT cc.countrycode as code, cc.name, cc.hasstates, 1 as special FROM {cart_country} as cc WHERE special = 'yes' ORDER BY name");
             /* Limit shipping countries to favourited only */
            if (Jojo::getOption('freight_favouritesonly', 'no')=='yes') {
                $smarty->assign('shippingcountries', $countries);
                if (count($countries)==1) {
                    /* Check if State field is needed if only one country */
                    $smarty->assign('shippingnostates', (boolean)(isset($countries[0]['hasstates']) && $countries[0]['hasstates'] == 'no'));
                }
            }
            if ($countries) {
                $countries = array_merge($countries, array(array('code' => '', 'name' => '----------')));
            }
            $countries = array_merge($countries, Jojo::selectQuery("SELECT cc.countrycode as code, cc.name, 0 as special FROM {cart_country} as cc ORDER BY name"));
            $countries = array_merge(array(array('code' => '', 'name' => 'Select country')), $countries); 
            $smarty->assign('countries', $countries);
            
            

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