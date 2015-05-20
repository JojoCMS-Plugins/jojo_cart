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
        $report_start = Jojo::getFormData('report_start', '');
        $report_end  = Jojo::getFormData('report_end', '');
        $smarty->assign('report_start', $report_start);
        $smarty->assign('report_end', $report_end);
        
        /* customer name search - ignores date range */
        $customer_name = Jojo::getFormData('customer_name', false);
        $smarty->assign('customer_name', $customer_name);
        if ($customer_name) {
            $keywords = explode(' ', strtolower($customer_name));
            $vals = array();
            $where = '0';
            foreach ($keywords as $k) {
                $where .= ' OR data LIKE \'%'.Jojo::clean($k).'%\'';
                //$vals[] = $k;
            }
            $data = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 AND ($where) ORDER BY id DESC LIMIT ".Jojo::getOption('cart_transactions_report_number', 150)); //get all transactions that could be a match
            $transactions = array();
            /* remove transactions that don't match the name fields */
            foreach ($data as $transaction) {
                $cart = unserialize($transaction['data']);
                foreach ($keywords as $k) {
                    if ((strpos(strtolower($cart->fields['billing_firstname']), $k) !== false)
                        || (strpos(strtolower($cart->fields['billing_lastname']), $k) !== false) 
                        || (strpos(strtolower($cart->fields['shipping_firstname']), $k) !== false) 
                        || (strpos(strtolower($cart->fields['shipping_lastname']), $k) !== false) ) {
                        $transactions[] = $transaction;
                        break;
                    }
                }
            }
        } else {
            $ago = time() - (60*60*24*30);
            if ($report_start && $report_end) {
                $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 AND (updated >= ?) AND (updated <= ?) ORDER BY id DESC", array(strtotime($report_start), strtotime($report_end)));
            } else {
                $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 OR (updated >= ?) ORDER BY id DESC", array($ago));
            }
        }
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
                $transaction['items'] = $cart['items'];
            } elseif (is_object($cart) ) {
                $transaction['fields'] = isset($cart->fields) ? $cart->fields : '';
                $transaction['FirstName'] = isset($cart->fields['billing_firstname']) && $cart->fields['billing_firstname'] ? $cart->fields['billing_firstname'] : ( isset($cart->fields['shipping_firstname']) && $cart->fields['shipping_firstname'] ? $cart->fields['shipping_firstname'] : '');
                $transaction['LastName']  = isset($cart->fields['billing_lastname']) && $cart->fields['billing_lastname'] ? $cart->fields['billing_lastname'] : ( isset($cart->fields['shipping_lastname']) && $cart->fields['shipping_lastname'] ? $cart->fields['shipping_lastname'] : '');
                $transaction['amount']    = $cart->order['amount'];
                $transaction['currency'] = isset($cart->order['currency']) ? $cart->order['currency'] : '';
                $transaction['apply_tax'] = isset($cart->order['apply_tax']) ? $cart->order['apply_tax'] : 'unknown';
                $transaction['items'] = isset($cart->items) ? $cart->items : array();
            }
            $transaction['data']=$cart;
            $transaction['currency'] = !empty($transaction['currency']) ? $transaction['currency'] : call_user_func(array(Jojo_Cart_Class, 'getCartCurrency'), $transaction['token']);
            $transaction['currencysymbol'] = call_user_func(array(Jojo_Cart_Class, 'getCurrencySymbol'), $transaction['currency']);
            /* Calculate monthly sales totals */
            if (Jojo::getOption('cart_force_default_currency', 'yes') == 'yes' && $transaction['status']=='complete') {
                $month = substr($transaction['completed'], 0, 7);
                if (isset($totals[$month])) {
                    $totals[$month]['total'] = $totals[$month]['total'] + $transaction['amount'];
                    $totals[$month]['number']++;
                    foreach ($transaction['items'] as $i) {
                        $totals[$month]['items'] = $totals[$month]['items'] + $i['quantity'];
                    }
                } else {
                    $totals[$month]['total'] = $transaction['amount'];
                    $totals[$month]['number'] = 1;
                    $totals[$month]['items'] = 0;
                    foreach ($transaction['items'] as $i) {
                        $totals[$month]['items'] = $totals[$month]['items'] + $i['quantity'];
                    }
                }
            }
        }
        $gt = array('total'=>0, 'number'=>0, 'average'=>0, 'items'=>0,'avitems'=>0);
        foreach ($totals as $m=>&$t) {
            $gt['total'] = $gt['total'] + $t['total'];
            $gt['items'] = $gt['items'] + $t['items'];
            $gt['number'] = $gt['number'] + $t['number'];
            $t['average'] = number_format($t['total'] / $t['number'], 2);
            $t['avitems'] = number_format($t['items'] / $t['number'], 1);
            $t['avitemvalue'] = $t['number'] && $t['avitems'] ? number_format(($t['total'] / $t['number']) / ($t['items'] / $t['number']), 2) : 0.00;
            $t['total'] = number_format($t['total'], 2);
        }
        
        $gt['average'] = number_format($gt['total'] / $gt['number'], 2);
        $gt['avitems'] = number_format($gt['items'] / $gt['number'], 1);
        $gt['avitemvalue'] = $gt['number'] && $gt['avitems'] ? number_format(($gt['total'] / $gt['number']) / ($gt['items'] / $gt['number']) , 2) : 0.00;
        $gt['total'] = number_format($gt['total'], 2);
        
        if ($report_start && $report_end) {
            $smarty->assign('transactions', $transactions);
        } else {
            $smarty->assign('transactions', array_slice($transactions, 0, Jojo::getOption('cart_transactions_report_number', 150)));
        }
        $smarty->assign('transactiontotals', $totals);
        $smarty->assign('grandtotals', $gt);
        

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
