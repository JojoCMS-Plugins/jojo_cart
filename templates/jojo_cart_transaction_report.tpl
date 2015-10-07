{include file="admin/header.tpl"}
  <script src="external/jQuery.clueTip/jquery.cluetip.js" type="text/javascript"></script>
  <script src="external/jQuery.clueTip/jquery.hoverIntent.js" type="text/javascript"></script>
  <script src="external/jQuery.clueTip/jquery.bgiframe.min.js" type="text/javascript"></script>

  <script type="text/javascript">
{literal}
$(document).ready(function() {
$('a.info').cluetip({activation:"click", closePosition: 'top',closeText: '<img src="images/cross.png" alt="" />',sticky:true,ajaxCache: true,width:600,showTitle:false});
$('a.paid').cluetip({activation:"click", closePosition: 'top',closeText: '<img src="images/cross.png" alt="" />',sticky:true,width:380,showTitle:false});
$('a.shipped').cluetip({activation:"click", closePosition: 'top',closeText: '<img src="images/cross.png" alt="" />',sticky:true,width:380,showTitle:false,onHide:function(){window.location.reload();}});
    $('#report_start').AnyTime_noPicker();
    $('#report_start').AnyTime_picker({format: "%Y-%m-%d"});
    $('#report_end').AnyTime_noPicker();
    $('#report_end').AnyTime_picker({format: "%Y-%m-%d"});
});
{/literal}
</script>
{if $transactiontotals}<p class="note"><a href="#summaries">Monthly summaries <i class="glyphicon glyphicon-menu-down"></i></a></p>{/if}
<form action='' method="post" class="form-inline pull-right">
    <div class="form-group">
        <input type="hidden" value="csv" name="filedownload" id="filedownload" /><input type="submit" name="submit" value="Download as CSV" class="btn btn-default" />
    </div>
</form>

<div id="transaction_list_search">
    <h3>Search by date</h3>
    <form method="post" action="" class="form-inline">
        <div class="form-group">
            <label for="report_start">Start date:&nbsp;</label><input id="report_start" class="form-control date anytime" type="text" name="report_start" value="{$report_start|date_format:'%d %b %Y'}" />
         </div>   
        <div class="form-group">
            <label for="report_end">End date:&nbsp;</label><input id="report_end" class="form-control  date anytime" type="text" name="report_end" value="{$report_end|date_format:'%d %b %Y'}" />
         </div>   
        <input type="submit" name="search" value="Search"  class="btn btn-default"/>
    </form>
    <h3>Search by customer</h3>
    <form method="post" action="" class="form-inline">
        <div class="form-group">
            <label for="customer_name">Customer name:&nbsp;</label><input class="form-control text" type="text" name="customer_name" value="{$customer_name}" />
        </div>
        <input type="submit" name="search" value="Search"  class="btn btn-default"/>
    </form>
</div>
<br>
<table class="sortabletable table table-striped table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>Date</th>
      <th>Details</th>
      <th>Amount</th>
      <th>Currency</th>
      <th>Status</th>
      <th>Handler</th>
      <th>Shipped</th>
      {jojoHook hook="jojo_cart_transaction_report_th"}
    </tr>
  </thead>
  <tbody>
    {foreach from=$transactions item=transaction}
    <tr class="{$transaction.status}">
      <td>{$transaction.id}</td>
      <td>{$transaction.completed}</td>
      <td><a class="info" href="{$ADMIN}/cart/transaction_list/{$transaction.token}/" rel="{$ADMIN}/cart/transaction_list/{$transaction.token}/">{$transaction.FirstName} {$transaction.LastName}</a></td>
      <td>{$transaction.currencysymbol}{$transaction.amount|string_format:"%0.2f"}{if $OPTIONS.cart_show_gst|default:'yes'=='yes'} <span class="note">{if $transaction.apply_tax === "unknown"}({$OPTIONS.cart_tax_name|default:'Tax'} unknown){elseif $transaction.apply_tax}(inc {$OPTIONS.cart_tax_name|default:'Tax'}){/if}</span>{/if}</td>
      <td>{if $transaction.currency}{$transaction.currency}{/if}</td>
      <td>
      {if $transaction.status=='payment_pending' || $transaction.status=='pending' || $transaction.status=='abandoned'}<a class="paid" target="_blank" href="cart/paidadmin_complete/{$transaction.token}/{$transaction.actioncode}/" rel="cart/paidadmin_complete/{$transaction.token}/{$transaction.actioncode}/">{$transaction.status} - click to complete</a>{else}{$transaction.status}{/if}
      {if $transaction.status!='abandoned'}<a class="paid" target="_blank" href="cart/paidadmin_abandoned/{$transaction.token}/{$transaction.actioncode}/" rel="cart/paidadmin_abandoned/{$transaction.token}/{$transaction.actioncode}/" title="Change Status to Abandoned">A</a>{/if}
      {if $transaction.status!='payment_pending' && $transaction.status!='pending'}<a class="paid" target="_blank" href="cart/paidadmin_paymentpending/{$transaction.token}/{$transaction.actioncode}/" rel="cart/paidadmin_paymentpending/{$transaction.token}/{$transaction.actioncode}/" title="Change Status to Payment Pending">P</a>{/if}
      </td>
      <td>{$transaction.handler}</td>
      <td>{if $transaction.shipped<1}<a class="shipped" target="_blank" href="cart/shippedadmin/{$transaction.token}/{$transaction.actioncode}/" rel="cart/shippedadmin/{$transaction.token}/{$transaction.actioncode}/">click to ship</a>
      {elseif $transaction.shipped}{$transaction.shipped|date_format:"%d %b %Y"} <a class="shipped" href="cart/shippedadmin_unshipped/{$transaction.token}/{$transaction.actioncode}/" rel="cart/shippedadmin_unshipped/{$transaction.token}/{$transaction.actioncode}/">U</a>{/if}</td>
    {jojoHook hook="jojo_cart_transaction_report_td"}
    </tr>
    {/foreach}
  </tbody>
</table>
{if $transactiontotals}
<a name="summaries"></a>
<h3>Summary</h3>
<table class="sortabletable table table-striped table-bordered">
<tr>
    <th rowspan="2">Month</th>
    <th rowspan="2">Total <span class="note">(y-o-y change)</span></th>
    <th rowspan="2"># of transactions</th>
    <th rowspan="2">Total items sold</th>
    <th colspan="3" style="text-align: center;">Averages per transaction</th>
</tr>
<tr>
    <th>Total spend</th>
    <th># of Items</th>
    <th>Item value</th>
</tr>
{foreach from=$transactiontotals key=k item=t}
<tr>
    <td>{$k}</td>
    <td>{$transaction.currencysymbol}{$t.total} {if $t.change}<span class="note {if $t.change<0}text-danger{else}text-success{/if}">({if $t.change>0}+{/if}{$t.change}%)</span>{/if}</td>
    <td>{$t.number}</td>
    <td>{$t.items}</td>
    <td>{$transaction.currencysymbol}{$t.average}</td>
    <td>{$t.avitems}</td>
    <td>{$transaction.currencysymbol}{$t.avitemvalue}</td>
</tr>
{if $t.bestsellers}<tr>
    <td></td>
    <td colspan="6">Top by volume: {foreach $t.bestsellers b}{$b.name} x<b>{$b.number}</b>{if !$.foreach.default.last}, &nbsp;{/if}{/foreach}<br>
    Top by value: {foreach $t.valuesellers b}{$b.name} <b>{$transaction.currencysymbol}{$b.amount}</b>{if !$.foreach.default.last}, &nbsp;{/if}{/foreach}<br>
    </td>
</tr>{/if}
{/foreach}
<tr>
    <th>All</th>
    <th>{$transaction.currencysymbol}{$grandtotals.total}</th>
    <th>{$grandtotals.number}</th>
    <th>{$grandtotals.items}</th>
    <th>{$transaction.currencysymbol}{$grandtotals.average}</th>
    <th>{$grandtotals.avitems}</th>
    <th>{$transaction.currencysymbol}{$grandtotals.avitemvalue}</th>
</tr>
</table>
{/if}
{include file="admin/footer.tpl"}
