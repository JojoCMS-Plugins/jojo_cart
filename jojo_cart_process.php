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
 * @author  Tom Dale <tom@zero.co.nz>
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
        $errors = array();
        $languageurlprefix = $this->page['pageid'] ? Jojo::getPageUrlPrefix($this->page['pageid']) : $_SESSION['languageurlprefix'];

        /* Make sure there's something in the cart */
        $cart = call_user_func(array(Jojo_Cart_Class, 'getCart'), $token);
        if (!count($cart->items)) {
            Jojo::redirect(_SECUREURL . '/' .$languageurlprefix. 'cart/');
        }

        /* Which payment plugin are we using? */
        $activeplugin = self::activePaymentPlugin();
        if (!$activeplugin) {
            echo 'Error: unable to find active payment plugin';
            $log             = new Jojo_Eventlog();
            $log->code       = 'cart';
            $log->importance = 'critical';
            $log->shortdesc  = 'Error: unable to find active payment plugin';
            $log->desc       = 'Active payment plugin error on ' . _SITEURI . ' - either this is a hack or the payment provider response is not in the expected format';
            $log->savetodb();
            unset($log);
            exit;
        }
        $cart->handler = $activeplugin;

        /* Ensure the transaction is not already being processed */
        if (Jojo::selectRow("SELECT * FROM {cart} WHERE token=? AND (status='complete' OR status='processing')", $token)) {
            $errors[] = 'This transaction already processed.';
            $log             = new Jojo_Eventlog();
            $log->code       = 'cart';
            $log->importance = 'high';
            $log->shortdesc  = 'Error: payment already processed';
            $log->desc       = '';
            $log->savetodb();
            unset($log);
            $smarty->assign('errors',  $errors);
            /* display error page */
            $content['title']    = '##Payment error##';
            $content['content']  = $smarty->fetch('jojo_cart_process.tpl');
            return $content;
        }

        /* Attempt to process the payment */
        $result = call_user_func(array($activeplugin, 'process'));
        $errors = $result['errors'];

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

        if (!$errors && $result['success']) {
            /* Get or assign order id */
            if (!(isset($cart->id) && $cart->id)) {
                $lastinsert = Jojo::insertQuery("INSERT INTO {cart_ordernumbers} set value=1");
                $cart->id = $lastinsert;
            } 
            $cart->cartstatus = $result['paid'] ? 'complete' : 'payment_pending';
            call_user_func(array(Jojo_Cart_Class, 'saveCart'), $cart);

            if (isset($cart->fields['shipping_rd'])) {
                $surcharge = Jojo::selectRow("SELECT rural_surcharge FROM {cart_region} WHERE regioncode = ?", array($cart->fields['shippingRegion']));
                $cart->order['surcharge'] = $surcharge['rural_surcharge'];
            }

            /* update the status of single-use discount codes */
            if (isset($cart->discount['singleuse']) && $cart->discount['singleuse']) {
                Jojo::updateQuery("UPDATE {discount} SET usedby=? WHERE discountcode=?", array($cart->token, $cart->discount['code']));
            }

            /* update loyalty point balance */
            if ($cart->userid && Jojo::getOption('cart_loyalty_cost', '') && JOJO_Plugin_Jojo_cart::getCartCurrency($token)==Jojo::getOption('cart_default_currency', 'USD')) {
                $pointsused = isset($cart->points['used']) ? $cart->points['used'] : 0;
                $cost = Jojo::getOption('cart_loyalty_cost', '');
                $value = $cart->order['apply_tax'] ? JOJO_Plugin_Jojo_cart::removeTax($cart->order['subtotal']) : $cart->order['subtotal'];
                $pointsadded = $cost && $value ? floor($value/$cost) : 0;
                $currentpoints = $cart->points['balance'] ? Jojo::selectRow("SELECT points FROM {cart_points} WHERE userid=?", array($cart->userid)) : 0;
                if ($currentpoints) {
                    $balance = $currentpoints['points'] + $pointsadded - $pointsused;
                    if ($balance<0) {
                        //need to check for negative point balance (from having 2 carts open at once say) and alert
                    }
                    Jojo::updateQuery("UPDATE {cart_points} SET points=? WHERE userid=? LIMIT 1", array($balance, $cart->userid));
                } else {
                    $balance = $pointsadded;
                    if (Jojo::selectRow("SELECT userid FROM {cart_points} WHERE userid=?", array($cart->userid))) {
                        Jojo::updateQuery("UPDATE {cart_points} SET points=? WHERE userid=? LIMIT 1", array($balance, $cart->userid));
                    } else {
                        Jojo::insertQuery("INSERT INTO {cart_points} SET userid=?, points=?", array($cart->userid, $balance));
                    }
                }
                $cart->points['added'] = $pointsadded;
                $cart->points['finalbalance'] = $balance;
           }

            /* Send emails */
            Jojo_Plugin_Jojo_cart::sendEmails($token, 'all');
            Jojo_Plugin_Jojo_cart::saveCart($cart, $noupdatetime=false, $lock=true);

           /* log transaction */
            $log             = new Jojo_Eventlog();
            $log->code       = 'transaction';
            $log->importance = 'high';
            $log->shortdesc  = 'Transaction completed on cart ' . $cart->id;
            $log->desc       = 'Successful transaction: '. $result['receipt'];
            $log->savetodb();
            unset($log);

            /* Hook for plugins to make custom actions */
            Jojo::runHook('jojo_cart_success', array('cart' => $cart));
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
        }

        $cart->cartstatus = 'failed';
        call_user_func(array(Jojo_Cart_Class, 'saveCart'), $cart);

        /* log payment failure */
        $log             = new Jojo_Eventlog();
        $log->code       = 'cart';
        $log->importance = 'high';
        $log->shortdesc  = 'Cart payment failed';
        $log->desc       = 'Transaction failed. ' . ($errors ? 'Errors: ' . implode("\n",$errors) : 'Payment was declined or there was a payment gateway confirmation glitch.');
        $log->savetodb();
        unset($log);

        /* display error page */
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