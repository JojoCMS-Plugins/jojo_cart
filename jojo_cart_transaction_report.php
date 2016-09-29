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
 * @author  Tom Dale <tom@zero.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 */

class jojo_plugin_jojo_cart_transaction_report extends JOJO_Plugin
{
    function _getContent()
    {
        global $smarty;

        $content = array();

        $summaries = Jojo::getFormData('summaries', false);
        $smarty->assign('showsummary',(boolean)(Jojo::getOption('cart_force_default_currency', 'yes') == 'yes'));
        $token = Jojo::getFormData('token', false);
        if ($token && $emailtarget = Jojo::getFormData('emailtarget', false)) {
            Jojo_Plugin_Jojo_cart::sendEmails($token, $emailtarget, $empty=true);
        }

        if (isset($_POST['submit'])) {
            $sent = $this->exportLogs();
        }

        jojo_plugin_Admin::adminMenu();
        $report_start = Jojo::getFormData('report_start', '');
        $report_end  = Jojo::getFormData('report_end', '');
        $ignore_id_date  = Jojo::getFormData('ignore_id_date', '');
        $smarty->assign('report_start', $report_start);
        $smarty->assign('report_end', $report_end);
        $smarty->assign('ignore_id_date', $ignore_id_date);
        $search_text = Jojo::getFormData('search_text', false);
        $ignore_id_text  = Jojo::getFormData('ignore_id_text', '');
        $smarty->assign('search_text', $search_text);
        $smarty->assign('ignore_id_text', $ignore_id_text);

        /* customer name search - ignores date range */
        if ($search_text) {
            $keywords = explode(' ', strtolower($search_text));
            $vals = array();
            $where = '0';
            foreach ($keywords as $k) {
                $where .= ' OR data LIKE \'%'. Jojo::clean($k).'%\'';
            }
            if (is_numeric($search_text)) {
                $where .= ' OR amount = ' . $search_text ;
            }
            $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE " . ($ignore_id_text ? "($where) ORDER BY updated DESC" : " id > 0 AND ($where) ORDER BY id DESC" ) . " LIMIT ".Jojo::getOption('cart_transactions_report_number', 150)); //get all transactions that could be a match
        } else {
            $ago = time() - (60*60*24*30);
            if ($report_start && $report_end) {
                $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE " . ($ignore_id_date ? "" : " id > 0 AND " ) . "(updated >= ?) AND (updated <= ?) ORDER BY updated DESC", array(strtotime($report_start), strtotime($report_end)));
            } else {
                $transactions = Jojo::selectQuery("SELECT * FROM {cart} WHERE id > 0 ORDER BY id DESC"  . ( !$summaries ? " LIMIT ".Jojo::getOption('cart_transactions_report_number', 150) : '' ), array());
            }
        }
        $totals = array();
        foreach($transactions as $k=>&$transaction) {
           /* Remove any old carts with no details entered */
            if (!$transaction['id'] && !($ignore_id_date || $ignore_id_text)) {
                unset($transactions[$k]);
                continue;
            }
            $cart = @unserialize($transaction['data']);
            unset($transactions[$k]['data_blob']);
            $transaction['completed'] = strftime('%F,  %l:%M%P', $transaction['updated']);
            $transaction['shipped'] = $transaction['shipped'] && $transaction['shipped']>0 ? strftime('%F,  %l:%M%P', $transaction['shipped']) : $transaction['shipped'];
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
            if ($summaries && Jojo::getOption('cart_force_default_currency', 'yes') == 'yes' && $transaction['status']=='complete') {
                $currencysymbol = $transaction['currencysymbol'];
                $month = substr($transaction['completed'], 0, 7);
                if (!isset($totals[$month])) {
                    $totals[$month]['total'] = 0;
                    $totals[$month]['number'] = 0;
                    $totals[$month]['items'] = 0;
                    $totals[$month]['itemssold'] = array();
                }
                $totals[$month]['total'] = $totals[$month]['total'] + $transaction['amount'];
                $totals[$month]['number']++;
                foreach ($transaction['items'] as $i) {
                    $pid = Jojo::cleanUrl($i['id']);
                    $totals[$month]['items'] = $totals[$month]['items'] + $i['quantity'];
                    if (!isset($totals[$month]['itemssold'][$pid])) {
                        $totals[$month]['itemssold'][$pid] = $i['quantity'];
                        $totals[$month]['itemssoldvalue'][$pid] = $i['linetotal'];
                        $totals[$month]['itemnames'][$pid] = $i['name'];
                    } else {
                        $totals[$month]['itemssold'][$pid] = $totals[$month]['itemssold'][$pid] + $i['quantity'];
                        $totals[$month]['itemssoldvalue'][$pid] = $totals[$month]['itemssoldvalue'][$pid] + $i['linetotal'];
                    }
                }
            }
        }
        if ($summaries && $totals) {
            $gt = array('total'=>0, 'number'=>0, 'average'=>0, 'items'=>0,'avitems'=>0);
            $yt = array();
            foreach ($totals as $m=>&$t) {
                $date = explode('-', $m);
                $gt['total'] = $gt['total'] + $t['total'];
                $gt['items'] = $gt['items'] + $t['items'];
                $gt['number'] = $gt['number'] + $t['number'];
                $t['average'] = number_format($t['total'] / $t['number'], 0);
                $t['avitems'] = number_format($t['items'] / $t['number'], 1);
                $t['avitemvalue'] = $t['number'] && $t['avitems'] ? number_format(($t['total'] / $t['number']) / ($t['items'] / $t['number']), 0) : 0;
                $t['rawtotal'] = $t['total'];
                $t['total'] = number_format($t['total'], 0);
                arsort($t['itemssold']);
                $t['itemssold'] = array_slice($t['itemssold'], 0, 3);
                $t['bestsellers'] = array();
                foreach($t['itemssold'] as $k=>$i) {
                    $t['bestsellers'][$k]['number'] = $i;
                    $t['bestsellers'][$k]['name'] = $t['itemnames'][$k];
                }
                arsort($t['itemssoldvalue']);
                $t['itemssoldvalue'] = array_slice($t['itemssoldvalue'], 0, 3);
                $t['valuesellers'] = array();
                foreach($t['itemssoldvalue'] as $k=>$i) {
                    $t['valuesellers'][$k]['amount'] = round($i, 0);
                    $t['valuesellers'][$k]['name'] = $t['itemnames'][$k];
                }
            }
            foreach ($totals as $m=>&$t) {
                $t['change'] = 0;
                $date = explode('-', $m);
                $year = (int)($date[0]);
                $lastyear = $year -1;
                if (isset($yt[$year])) {
                    $yt[$year]['rawtotal'] = $yt[$year]['rawtotal'] + $t['rawtotal'];
                    $yt[$year]['items'] = $yt[$year]['items'] + $t['items'];
                    $yt[$year]['number'] = $yt[$year]['number'] + $t['number'];
                } else {
                    $yt[$year] = array('rawtotal'=>$t['rawtotal'], 'number'=>$t['number'], 'items'=>$t['items']);
                }
                if (isset($totals[ $lastyear . '-' . $date[1]]) && $totals[ $lastyear . '-' . $date[1]]['rawtotal']) {
                    $lasttotal = $totals[ $lastyear . '-' . $date[1]]['rawtotal'];
                    $thistotal = $t['rawtotal'];
                    $change = $thistotal - $lasttotal;
                    $t['change'] =  round(($change / $lasttotal)*100, 0);
                }
            }
            foreach ($yt as $k=>&$y) {
                $y['average'] = $y['number'] ? number_format($y['rawtotal'] / $y['number'], 0) : 0;
                $y['avitems'] = $y['number'] ? number_format($y['items'] / $y['number'], 1) : '';
                $y['avitemvalue'] = $y['number'] && $y['avitems'] ? number_format(($y['rawtotal'] / $y['number']) / ($y['items'] / $y['number']) , 2) : 0.00;
                if (isset($yt[$k-1]) && $yt[$k-1]['rawtotal']) {
                    $lasttotal = $yt[$k-1]['rawtotal'];
                    $thistotal = $y['rawtotal'];
                    $change = $thistotal - $lasttotal;
                    $y['change'] =  round(($change / $lasttotal)*100, 0);
                }
            }
            $gt['average'] = $gt['number'] ? number_format($gt['total'] / $gt['number'], 0) : 0;
            $gt['avitems'] = $gt['number'] ? number_format($gt['items'] / $gt['number'], 1) : '';
            $gt['avitemvalue'] = $gt['number'] && $gt['avitems'] ? number_format(($gt['total'] / $gt['number']) / ($gt['items'] / $gt['number']) , 2) : 0.00;
            $gt['total'] = number_format($gt['total'], 0);
        
            $smarty->assign('currencysymbol', $currencysymbol);
            $smarty->assign('transactiontotals', $totals);
            $smarty->assign('yeartotals', $yt);
            $smarty->assign('grandtotals', $gt);
        }

        if ($report_start && $report_end) {
            $smarty->assign('transactions', $transactions);
        } else {
            $smarty->assign('transactions', array_slice($transactions, 0, Jojo::getOption('cart_transactions_report_number', 150)));
        }
        
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
                if (is_array($cart)) {
                    $items = $cart['items'];
                    $fields = $cart['fields'];
                } elseif (is_object($cart) ) {
                    $items = $cart->items;
                    $fields = $cart->fields;
                }
                $transactions[$k]['items'] = '';
                foreach($items as $itemkey => $itemvalue){
                    $transactions[$k]['items'] .= $itemvalue['quantity'] . " " . $itemvalue['name'] . " : ";
                }
                
                foreach ($fields as $ck => $cv) {
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
