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

        jojo_plugin_Admin::adminMenu();

        $transactions = Jojo::selectRow("SELECT * FROM {cart} WHERE token=? LIMIT 1",$token);
            $cart = unserialize($transactions['data']);
            $smarty->assign('items',        $cart->items);
            $smarty->assign('fields',       $cart->fields);
            $smarty->assign('order',        $cart->order);
            $smarty->assign('id',           $transactions['id']);

        $content['title'] = 'Transaction report';
        $content['content'] = $smarty->fetch('jojo_cart_transaction_list.tpl');

        return $content;
    }

  function getCorrectUrl()
  {
    //Assume the URL is correct
    return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
  }

}