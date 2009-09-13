{include file="admin/header.tpl"}
  <script src="external/jQuery.clueTip/jquery.cluetip.js" type="text/javascript"></script>
  <script src="external/jQuery.clueTip/jquery.hoverIntent.js" type="text/javascript"></script>
  <script src="external/jQuery.clueTip/jquery.bgiframe.min.js" type="text/javascript"></script>

  <script type="text/javascript">
{literal}
$(document).ready(function() {
$('a.info').cluetip({activation:"click", showTitle: false});
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
      <td>{$transactions[t].status}</td>
      <td>{$transactions[t].handler}</td>
      <td>{if $transactions[t].shipped==-1}n/a{elseif $transactions[t].shipped}{$transactions[t].shipped|date_format:"%d %b %Y"}{/if}</td>
    </tr>
    {/section}
  </tbody>
</table>

{include file="admin/footer.tpl"}