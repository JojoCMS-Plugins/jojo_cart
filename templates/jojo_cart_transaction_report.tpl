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
});
{/literal}
</script>

<table class="sortabletable">
  <thead>
    <tr>
      <th>ID</th>
      <th>Date</th>
      <th>Time</th>
      <th>Customer</th>
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
      <td>{$transaction.datetime|date_format:"%d %b %Y"}</td>
      <td>{$transaction.datetime|date_format:"%H:%M"}</td>
      <td><a class="info" href="{$ADMIN}/cart/transactionlist/{$transaction.token}/" rel="{$ADMIN}/cart/transaction_list/{$transaction.token}/">{$transaction.FirstName} {$transaction.LastName}</a></td>
      <td>{$transaction.currencysymbol}{$transaction.amount|string_format:"%0.2f"} {if $OPTIONS.cart_show_gst|default:'yes'=='yes'}({if $transaction.apply_tax === "unknown"}{$OPTIONS.cart_tax_name|default:'Tax'} unknown{elseif $transaction.apply_tax}inc {$OPTIONS.cart_tax_name|default:'Tax'}{else}no {$OPTIONS.cart_tax_name|default:'Tax'}{/if}){/if}</td>
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

{include file="admin/footer.tpl"}