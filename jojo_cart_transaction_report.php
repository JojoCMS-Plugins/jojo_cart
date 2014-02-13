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
		
		if (isset($_POST['submit'])) {
            $sent = $this->exportLogs();
        }
		
        jojo_plugin_Admin::adminMenu();
        $ago = time() - (60*60*24*30);
        $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0  OR (updated > ?) ORDER BY id DESC, updated DESC LIMIT " . Jojo::getOption('cart_transactions_report_number', 150), array($ago));
        $totals = array();
        foreach($transactions as $k=>&$transaction) {
           /* Remove any old carts with no details entered */
            if (!$transaction['id'] && !$transaction['handler']) {
                unset($transactions[$k]);
                continue;
            }
            $cart = unserialize($transaction['data']);
            $transaction['completed'] = strftime('%F,  %l:%M%P', $transaction['updated']);
            $transaction['datetime'] = $transaction['updated'];
            $transaction['status'] = $transaction['status'];
            $transaction['handler'] = str_replace('jojo_plugin_jojo_cart_','', $transaction['handler']);
             if (is_array($cart)) {
                $transaction['fields'] = $cart['fields'];
                $transaction['FirstName'] = $cart['fields']['FirstName'];
                $transaction['LastName']  = $cart['fields']['LastName'];
                $transaction['amount']    = $cart['order']['amount'];
                $transaction['currency'] = isset($cart['order']['currency']) ? $cart['order']['currency'] : '';
            } elseif (is_object($cart) ) {
                $transaction['fields'] = isset($cart->fields) ? $cart->fields : '';
                $transaction['FirstName'] = !empty($cart->fields['billing_firstname']) ? $cart->fields['billing_firstname'] : $cart->fields['shipping_firstname'];
                $transaction['LastName']  = !empty($cart->fields['billing_lastname']) ? $cart->fields['billing_lastname'] : $cart->fields['shipping_lastname'];
                $transaction['amount']    = $cart->order['amount'];
                $transaction['currency'] = isset($cart->order['currency']) ? $cart->order['currency'] : '';
                $transaction['apply_tax'] = isset($cart->order['apply_tax']) ? $cart->order['apply_tax'] : 'unknown';
            }
            $transaction['data']=$cart;
            $transaction['currency'] = !empty($transaction['currency']) ? $transaction['currency'] : call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $transaction['token']);
            $transaction['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $transaction['currency']);
            /* Calculate monthly sales totals */
            if (Jojo::getOption('cart_force_default_currency', 'yes') == 'yes' && $transaction['status']=='complete') {
                $month = substr($transaction['completed'], 0, 7);
                if (isset($totals[$month])) {
                    $totals[$month] = $totals[$month] + round($transaction['amount'], 2);
                } else {
                    $totals[$month] = round($transaction['amount'], 2);
                }
            }
        }
        $smarty->assign('transactions', $transactions);
        $smarty->assign('transactiontotals', $totals);
        

        $content['title'] = 'Transaction report';
        $content['content'] = $smarty->fetch('jojo_cart_transaction_report.tpl');

        return $content;
    }

	function exportLogs()
    {
        /* Check for form injection attempts */
        Jojo::noFormInjection();
        if (isset($_POST['filedownload'])) {
        	
			$transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 ORDER BY id DESC ");
			if (!count($transactions)) { return true; }

			foreach ($transactions as $k => $s) {
                //add stuff here to total quantities and products etc 
                //$transactions[$k]['submitted'] = strftime('%F %T', $s['submitted']);
                $transactions[$k]['date'] = strftime('%F %T', $s['updated']);
                $cart = unserialize($transactions[$k]['data']);
				$transactions[$k]['data'] = $cart;
				
				$items = $cart->items;
				$transactions[$k]['items'] = '';
				foreach($items as $itemkey => $itemvalue){
					$transactions[$k]['items'] .= $itemvalue['quantity'] . " " . $itemvalue['name'] . " : ";
				}
				
				foreach ($cart->fields as $ck => $cv) {
					if (strpos($ck, 'shipping')!==false) {
	                   $shipping[$ck] = $cv;
					   foreach($shipping as $sk => $sv){
					   		$transactions[$k][$sk] = trim(str_replace(array("\n", "\r"), ' ', $sv));
					   }
	                } elseif (strpos($ck, 'billing')!==false) {
	                   $billing[$ck] = $cv;
					   foreach($billing as $sk => $sv){
					   		$transactions[$k][$sk] = trim(str_replace(array("\n", "\r"), ' ', $sv));
					   }
	                }
				}
				
				unset($transactions[$k]['updated']);
				unset($transactions[$k]['data']);
				unset($transactions[$k]['data_blob']);
				unset($transactions[$k]['submitted']);
				unset($transactions[$k]['ip']);
				unset($transactions[$k]['userid']);
				unset($transactions[$k]['token']);
				unset($transactions[$k]['actioncode']);
				unset($transactions[$k]['testmode']);
            }
			
			$output = '';
            $c=0;
            foreach($transactions AS $array) {
                $val_array = array();
                $key_array = array();
                foreach($array AS $key => $val) {
                    $key_array[] = $key;
                    $val = str_replace('"', '""', $val);
                    $val_array[] = "\"$val\"";
                }
                if($c == 0) {
                    $output .= implode(",", $key_array)."\n";
                }
                $output .= implode(",", $val_array)."\n";
                $c++;
            }
			
            header('Content-type: text/csv'."\r\n");
            header('Content-disposition: attachment; filename="Transactions.csv"'."\r\n");
            header("Pragma: no-cache"."\r\n");
            header("Expires: 0"."\r\n");
            header("Content-length: ".strlen($output)."\r\n");
            echo $output;
            exit;
        }
    }

}
