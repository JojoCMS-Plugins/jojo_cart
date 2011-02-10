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

class jojo_plugin_Jojo_cart_paid extends JOJO_Plugin
{
    public function _getContent() {
        global $smarty;
        $content = array();

        $send       = Jojo::getFormData('send',       false);
        $token      = Jojo::getFormData('token',      false);
        $actioncode = Jojo::getFormData('actioncode', false);
        $action     = Jojo::getFormData('action', false);

        //paidadmin_complete|paidadmin_paymentpending|paidadmin_abandoned
        if(strpos($action,'paidadmin')!==false) jojo_plugin_Admin::adminMenu();
        /* send the notification to the client */
        if ($send) {
            $from_name   = Jojo::either(_CONTACTNAME, _FROMNAME,_SITETITLE);
            $from_email  = Jojo::either(_CONTACTADDRESS,_FROMADDRESS,_WEBMASTERADDRESS);
            $email       = Jojo::getFormData('email',   false);
            $subject     = Jojo::getFormData('subject', false);
            $message     = Jojo::getFormData('message', false);

            if (defined('_CONTACTADDRESS') && (_CONTACTADDRESS != _WEBMASTERADDRESS)) {
                /* Email admin */
                $to_name     = Jojo::either(_CONTACTNAME, _FROMNAME,_SITETITLE);
                $to_email    = Jojo::either(_CART_SHIPPED_EMAIL,_CONTACTADDRESS,_FROMADDRESS,_WEBMASTERADDRESS);
                Jojo::simpleMail($to_name, $to_email, "copy ".$subject, $message, $from_name, $from_email);
            }

            if (defined('_CART_ORDER_EMAIL')) {
                /* Email admin - if defined in the cart options */
                $to_name     = _CART_ORDER_NAME;
                $to_email    = _CART_ORDER_EMAIL;
                Jojo::simpleMail($to_name, $to_email, "copy ".$subject, $message, $from_name, $from_email);
            }

            /* Email webmaster */
            if (Jojo::getOption('cart_webmaster_copy', 'yes') == 'yes' AND $to_email != _WEBMASTERADDRESS) {
              $to_name     = _WEBMASTERNAME;
              $to_email    = _WEBMASTERADDRESS;
              Jojo::simpleMail($to_name, $to_email, "copy ".$subject, $message, $from_name, $from_email);
            }

            /* Email client */
            Jojo::simpleMail('', $email, $subject, $message, $from_name, $from_email);

            $content['content'] = 'The customer confirmation email has been sent.';

            return $content;
        }

        $cart = call_user_func(array(Jojo_Cart_Class, 'getCart'), $token);
        //if (($cart->shipped == 0) || ($cart->shipped == -1)) $cart->shipped = time();

        /* ensure the actioncode is the same as is stored against the cart */
        if ($actioncode != $cart->actioncode) {
            $content['content'] = 'This link is invalid.';
        } else {
            switch($action){
              case "paid":
              case "paidadmin_complete":
                $cart->cartstatus = 'complete';
                $smarty->assign('changestatus','');
                break;
              case "paidadmin_paymentpending":
                $cart->cartstatus = 'payment_pending';
                $smarty->assign('changestatus','Status has been changed to payment pending');
                break;
              case "paidadmin_abandoned":
                $cart->cartstatus = 'abandoned';
                $smarty->assign('changestatus','Status has been changed to abandoned');
                break;
            }

            call_user_func(array(Jojo_Cart_Class, 'saveCart'));

            /* hook for plugins to make custom actions */
            Jojo::runHook('jojo_cart_paid_hook', $cart->token);

            $smarty->assign('token',      $cart->token);
            $smarty->assign('actioncode', $cart->actioncode);
            $smarty->assign('fields',     $cart->fields);

            $content['content'] = $smarty->fetch('jojo_cart_paid.tpl');
        }

        return $content;
    }

    function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}