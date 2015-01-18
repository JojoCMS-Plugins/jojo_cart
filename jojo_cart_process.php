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

class jojo_plugin_Jojo_cart_process extends JOJO_Plugin
{
    /**
     * Returns the classname of the first active payment plugin.
     */
    private static function activePaymentPlugin()
    {
        foreach (call_user_func(array(Jojo_Cart_Class, 'getPaymentHandlers')) as $handler) {
            if (call_user_func(array($handler, 'isActive'))) {
                return $handler;
            }
        }
        return false;
    }

     private static function getToken()
    {
        foreach (call_user_func(array(Jojo_Cart_Class, 'getPaymentHandlers')) as $handler) {
            if (method_exists($handler, 'getToken') && $token = call_user_func($handler . '::getToken')) {
                return $token;
            }
        }
        return false;
    }

   function _getContent()
    {
        global $smarty, $_USERGROUPS, $_USERID;
        /* if there's no token in GET, see if a payment plugin knows what it is (because it's being passed back by some other method) */
        $token = Jojo::getGet('token') ? Jojo::getGet('token') : self::getToken();

        $content = array();
        
        $languageurlprefix = $this->page['pageid'] ? Jojo::getPageUrlPrefix($this->page['pageid']) : $_SESSION['languageurlprefix'];

		
        /* Make sure there's something in the cart */
        $cart = call_user_func(array(Jojo_Cart_Class, 'getCart'), $token);
        if (!count($cart->items)) {
            Jojo::redirect(_SECUREURL . '/' .$languageurlprefix. 'cart/');
        }

        /* Which payment plugin are we using? */
        $activeplugin = self::activePaymentPlugin();
        $smarty->assign('activeplugin', ucwords(str_replace('_', ' ', (str_replace('jojo_plugin_jojo_cart_', '', $activeplugin)))));
        if (!$activeplugin) {
            echo 'Error: unable to find active payment plugin';
            $log             = new Jojo_Eventlog();
            $log->code       = 'cart';
            $log->importance = 'critical';
            $log->shortdesc  = 'Active payment plugin error';
            $log->desc       = 'Active payment plugin error on ' . _SITEURI . ' - this may indicate a bug in the payment handler script';
            $log->savetodb();
            unset($log);
            exit;
        }
        $cart->handler = $activeplugin;

        /* Error checking */
        $errors = array();

        /* Ensure the transaction has not already been processed */
        $data = Jojo::selectQuery("SELECT * FROM {cart} WHERE token=? AND status='pending'", $token);
        if (!count($data)) {
            $errors[] = 'This transaction has already been processed.';
        } else {
            /* pause execution and check again after 0.5 - 1.5 seconds - this aloows other scripts to complete processing*/
            usleep(mt_rand(500000,1500000));
            $data = Jojo::selectQuery("SELECT * FROM {cart} WHERE token=? AND status='pending'", $token);
            if (!count($data)) {
                $errors[] = 'This transaction has already been processed.';
            }
        }
        

        /* Attempt to process the payment */
        if (!count($errors)) {
            $result = call_user_func(array($activeplugin, 'process'));
        } else {
            $result = array('errors' => $errors, 'success' => false, 'paid' => false, 'receipt' => '');
        }
        /* If the paid variable has not been set, use the value of 'success' to set */
        if (!isset($result['paid'])) {
            $result['paid'] = $result['success'];
        }

        /* If the receipt is an array, convert into HTML first */
        if (is_array($result['receipt'])) {
            $smarty->assign('rawreceipt', $result['receipt']);
            $result['receipt'] = $smarty->fetch('jojo_cart_receipt.tpl');
        }
        $cart->receipt = $result['receipt'];

        call_user_func(array(Jojo_Cart_Class, 'saveCart'), $cart);

        $smarty->assign('errors',  $result['errors']);
        $smarty->assign('success', $result['success']);
        $smarty->assign('receipt', $result['receipt']);
        $smarty->assign('message', $result['message']);
        $smarty->assign('handler', $cart->handler);
        $smarty->assign('countries', Jojo::selectQuery("SELECT cc.countrycode AS code, cc.name, 0 AS special FROM {cart_country} AS cc ORDER BY name"));

        if ($result['success']) {
            /* Get visitor details for emailing etc */
            if (!empty($cart->fields['billing_email'])) {
                $email = $cart->fields['billing_email'];
            } elseif (!empty($cart->fields['shipping_email'])) {
                $email = $cart->fields['shipping_email'];
            } else {
                $email = Jojo::either(_CONTACTADDRESS,_FROMADDRESS,_WEBMASTERADDRESS);
            }

            if (!empty($cart->fields['billing_firstname'])) {
                $name = $cart->fields['billing_firstname'] . ' ' . $cart->fields['billing_lastname'];
            } elseif (!empty($cart->fields['shipping_firstname'])) {
                $name = $cart->fields['shipping_firstname'] . ' ' . $cart->fields['shipping_lastname'];
            } else {
                $name = '';
            }

            if (isset($cart->fields['shipping_rd'])) {
                $surcharge = Jojo::selectRow("SELECT rural_surcharge FROM {cart_region} WHERE regioncode = ?", array($cart->fields['shippingRegion']));
                $cart->order['surcharge'] = $surcharge['rural_surcharge'];
            }

          /* are we using the discount code functionality? No need to show if the discount table is empty */
            $data = Jojo::selectRow("SELECT COUNT(*) AS numdiscounts FROM {discount}");
            if ($data['numdiscounts'] > 0) {
                $smarty->assign('discount', $cart->discount);
            }
            
            /* update the status of single-use discount codes */
            if (isset($cart->discount['singleuse']) && $cart->discount['singleuse']) {
                Jojo::updateQuery("UPDATE {discount} SET usedby=? WHERE discountcode=?", array($cart->token, $cart->discount['code']));
            }

            /* update loyalty point balance */
            if ($_USERID && Jojo::getOption('cart_loyalty_cost', '') && JOJO_Plugin_Jojo_cart::getCartCurrency($token)==Jojo::getOption('cart_default_currency', 'USD')) {
                $pointsused = isset($cart->points['used']) ? $cart->points['used'] : 0;
                $cost = Jojo::getOption('cart_loyalty_cost');
                $value = $cart->order['apply_tax'] ? JOJO_Plugin_Jojo_cart::removeTax($cart->order['subtotal']) : $cart->order['subtotal'];
                $pointsadded = floor($value/$cost);
                $currentpoints = Jojo::selectRow("SELECT points FROM {cart_points} WHERE userid=?", array($_USERID));
                if ($currentpoints) {
                    $balance = $currentpoints['points'] + $pointsadded - $pointsused;
                    if ($balance<0) {
                        //need to check for negative point balance (from having 2 carts open at once say) and alert
                    }
                    Jojo::updateQuery("UPDATE {cart_points} SET points=? WHERE userid=? LIMIT 1", array($balance, $_USERID));
                } else {
                    $balance = $pointsadded;
                    Jojo::insertQuery("INSERT INTO {cart_points} SET userid=?, points=?", array($_USERID, $balance));
                }
                $cart->points['added'] = $pointsadded;
                $cart->points['finalbalance'] = $balance;
                $smarty->assign('points',      $cart->points);
           }
            
            /* log transaction */
            $log             = new Jojo_Eventlog();
            $log->code       = 'transaction';
            $log->importance = 'high';
            $log->shortdesc  = 'Transaction completed';
            $log->desc       = 'Successful transaction: '. $result['receipt'];
            $log->savetodb();
            unset($log);

            $smarty->assign('token',      $cart->token);
            $smarty->assign('actioncode', $cart->actioncode);
            $smarty->assign('fields',     $cart->fields);
            $smarty->assign('order',      $cart->order);
            $smarty->assign('items',      $cart->items);

            /* Get order id */

            $lastinsert = Jojo::insertQuery("INSERT INTO {cart_ordernumbers} set value=1");
            $smarty->assign('id', $lastinsert);
            Jojo::updateQuery("UPDATE {cart} SET id=? where token=? LIMIT 1", array($lastinsert, $token));
            $cart->id = $lastinsert;

            if ($result['paid']) {
                $smarty->assign('status', 'complete');
            } else {
                $smarty->assign('status', 'payment_pending');
                /* allow plugins to replace the 'payment pending' text for both admin and customer emails with their own */
                $pending_template = array('admin' => 'jojo_cart_admin_email_pending.tpl', 'customer' => 'jojo_cart_customer_email_pending.tpl');
                $pending_template = Jojo::applyFilter('jojo_cart_process:pending_template', $pending_template, $cart);
                $smarty->assign('pending_template', $pending_template);
            }


            $contact_name   = Jojo::either(_CONTACTNAME, _FROMNAME,_SITETITLE);
            $contact_email  = Jojo::either(_CONTACTADDRESS,_FROMADDRESS,_WEBMASTERADDRESS);

            $subject     = 'Order by ' . $name . ' on ' . Jojo::getOption('sitetitle');
            $message     = $smarty->fetch('jojo_cart_admin_email.tpl') . Jojo::emailFooter();

            include _BASEPLUGINDIR . '/jojo_core/external/parsedown/Parsedown.php';
            $parsedown = new Parsedown();
            $htmlmessage = $parsedown->text($message);
            
            if ($css = Jojo::getOption('css-email', '')) {
                $htmlmessage = Jojo::inlineStyle($htmlmessage, $css, true);
            }
            
            if (defined('_CART_ORDER_EMAIL')) {
                /* Email admin - if defined in the cart options */
                $to_name     = _CART_ORDER_NAME;
                $to_email    = _CART_ORDER_EMAIL;
                Jojo::simpleMail($to_name, $to_email, $subject, $message, $name, $email, $htmlmessage, $contact_name . ' <' . $contact_email . '>');
            } elseif (Jojo::getOption('cart_order_email', false)) {
                /* Email admin - if defined in the cart options */
                $to_name     = Jojo::getOption('cart_order_name', '');
                $to_email    = Jojo::getOption('cart_order_email', false);
                Jojo::simpleMail($to_name, $to_email, $subject, $message, $name, $email, $htmlmessage, $contact_name . ' <' . $contact_email . '>');
            } 
            if (defined('_CONTACTADDRESS') && (_CONTACTADDRESS != _WEBMASTERADDRESS)) {
                /* Email admin */
                $to_name     = Jojo::either(_CONTACTNAME, _FROMNAME,_SITETITLE);
                $to_email    = Jojo::either(_CONTACTADDRESS,_FROMADDRESS,_WEBMASTERADDRESS);
                Jojo::simpleMail($to_name, $to_email, $subject, $message, $name, $email, $htmlmessage, $contact_name . ' <' . $contact_email . '>');
            }

            /* Email webmaster */
            if (Jojo::getOption('cart_webmaster_copy', 'yes') == 'yes' AND $to_email != _WEBMASTERADDRESS) {
                $to_name     = _WEBMASTERNAME;
                $to_email    = _WEBMASTERADDRESS;
                Jojo::simpleMail($to_name, $to_email, $subject, $message, $name, $email, $htmlmessage, $contact_name . ' <' . $contact_email . '>');
            }

            /* Email client */
            $subject     = 'Order confirmation from ' . Jojo::getOption('sitetitle');
            $message     = $smarty->fetch('jojo_cart_customer_email.tpl');
            $htmlmessage = $parsedown->text($message);
            if ($css) {
                $htmlmessage = Jojo::inlineStyle($htmlmessage, $css, true);
            }
            Jojo::simpleMail($name, $email, $subject, $message, $contact_name, $contact_email, $htmlmessage, $contact_name . ' <' . $contact_email . '>');

            /* Hook for plugins to make custom actions */
            Jojo::runHook('jojo_cart_success', array('cart' => $cart));

            /* save cart to database */
            if ($result['paid']) {
                $cart->cartstatus = 'complete';
            } else {
                $cart->cartstatus = 'payment_pending';
            }
            call_user_func(array(Jojo_Cart_Class, 'saveCart'), $cart);
            
            /* Hook for plugins to make custom actions */
            Jojo::runHook('jojo_cart_success_2', array('cart' => $cart));

            /* empty cart and clear token */
            call_user_func(array(Jojo_Cart_Class, 'emptyCart'));
            unset($cart);

            /* redirect to complete page */
            if ($result['paid']) {
                Jojo::redirect(_SECUREURL.'/' .$languageurlprefix. 'cart/complete/'.$token.'/');
            } else {
                Jojo::redirect(_SECUREURL.'/' .$languageurlprefix. 'cart/payment-info/'.$token.'/');
            }
        } else {
            /* email webmaster / admin */

            /* display error */

        }
        $content['title']    = '##Payment error##';
        $content['seotitle'] = '##Payment error##';
        $content['content']  = $smarty->fetch('jojo_cart_process.tpl');
        return $content;
    }

    function getCorrectUrl()
    {
        return _PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}