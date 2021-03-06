<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2011 Harvey Kane <code@ragepank.com>
 * Copyright 2011 Michael Holt <code@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class JOJO_Plugin_jojo_cart_free extends JOJO_Plugin
{
    static function getPaymentOptions()
    {
        global $smarty;
        $options = array();
        $options[] = array('id' => 'free', 'label' => 'Free', 'html' => $smarty->fetch('jojo_cart_free_checkout.tpl'));
        return $options;
    }

    /*
    * Determines whether this payment plugin is active for the current payment.
    */
    static function isActive()
    {
        /* Look for a post variable specifying the test processor */
        return (Jojo::getFormData('handler', false) == 'free') ? true : false;
    }

    static function process()
    {
        $receipt = (Jojo::getOption('cart_free_message')) == '' ? array() : array('Info' => Jojo::getOption('cart_free_message'));
        $errors  = array();

        return array(
                    'success' => true,
                    'receipt' => $receipt,
                    'errors'  => $errors,
                    'message' => ''
                    );
    }
}