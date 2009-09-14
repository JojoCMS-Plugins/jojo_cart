{include file="admin/header.tpl"}
  <script src="external/jQuery.clueTip/jquery.cluetip.js" type="text/javascript"></script>
  <script src="external/jQuery.clueTip/jquery.hoverIntent.js" type="text/javascript"></script>
  <script src="external/jQuery.clueTip/jquery.bgiframe.min.js" type="text/javascript"></script>

  <script type="text/javascript">
{literal}
$(document).ready(function() {
$('a.info').cluetip({activation:"click", closePosition: 'top',closeText: '<img src="images/cross.png" alt="" />',sticky:true,ajaxCache: true,showTitle:false});
$('a.paid').cluetip({activation:"click", closePosition: 'top',closeText: '<img src="images/cross.png" alt="" />',sticky:true,width:380,showTitle:false});
$('a.shipped').cluetip({activation:"click", closePosition: 'top',closeText: '<img src="images/cross.png" alt="" />',sticky:true,width:380,showTitle:false});
});
{/literal}
</script>

<table class="adminZebraTable">
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
    </tr>
  </thead>
  <tbody>
    {section name=t loop=$transactions}
    <tr class="{cycle values='row1,row2'} {$transactions[t].status}">
      <td>{$transactions[t].id}</td>
      <td>{$transactions[t].datetime|date_format:"%d %b %Y"}</td>
      <td>{$transactions[t].datetime|date_format:"%H:%M"}</td>
      <td><a class="info" href="{$ADMIN}/cart/transactionlist/{$transactions[t].token}/" rel="{$ADMIN}/cart/transaction_list/{$transactions[t].token}/">{$transactions[t].FirstName} {$transactions[t].LastName}</a></td>
      <td>{$transactions[t].currencysymbol}{$transactions[t].amount}</td>
      <td>{$transactions[t].currency}</td>
      <td>
      {if $transactions[t].status=='payment_pending' or $transactions[t].status=='pending' or $transactions[t].status=='abandoned'}<a class="paid" target="_blank" href="cart/paidadmin_complete/{$transactions[t].token}/{$transactions[t].actioncode}/" rel="cart/paidadmin_complete/{$transactions[t].token}/{$transactions[t].actioncode}/">{$transactions[t].status} - click to complete</a>{else}{$transactions[t].status}{/if}
      {if $transactions[t].status<>'abandoned'}<a class="paid" target="_blank" href="cart/paidadmin_abandoned/{$transactions[t].token}/{$transactions[t].actioncode}/" rel="cart/paidadmin_abandoned/{$transactions[t].token}/{$transactions[t].actioncode}/" title="Change Status to Abandoned">A</a>{/if}
      {if $transactions[t].status<>'payment_pending' and $transactions[t].status<>'pending'}<a class="paid" target="_blank" href="cart/paidadmin_paymentpending/{$transactions[t].token}/{$transactions[t].actioncode}/" rel="cart/paidadmin_paymentpending/{$transactions[t].token}/{$transactions[t].actioncode}/" title="Change Status to Payment Pending">P</a>{/if}
      </td>
      <td>{$transactions[t].handler}</td>
      <td>{if $transactions[t].shipped<1}<a class="shipped" target="_blank" href="cart/shippedadmin/{$transactions[t].token}/{$transactions[t].actioncode}/" rel="cart/shippedadmin/{$transactions[t].token}/{$transactions[t].actioncode}/">click to ship</a>
      {elseif $transactions[t].shipped}{$transactions[t].shipped|date_format:"%d %b %Y"} <a class="shipped" href="cart/shippedadmin_unshipped/{$transactions[t].token}/{$transactions[t].actioncode}/" rel="cart/shippedadmin_unshipped/{$transactions[t].token}/{$transactions[t].actioncode}/">U</a>{/if}</td>
    </tr>
    {/section}
  </tbody>
</table>

{include file="admin/footer.tpl"}