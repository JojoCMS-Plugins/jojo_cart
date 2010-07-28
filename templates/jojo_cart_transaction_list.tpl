<div id="adminTransactionList">
Order ID = {$id}
<table border="1" class="adminTransactionList">
  <thead>
    <tr>
      <th>Item</th>
      <th>Quan</th>
      <th>Price</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$items key=k item=i}
    {if $i.quantity > 0}
    <tr class="{cycle values='row1,row2'} {$transactions[t].status}">
      <td>{$i.name}</td>
      <td>{$i.quantity}</td>
      <td>{if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
      <td align="right">{$i.linetotal|string_format:"%01.2f"}</td>
    </tr>
    {/if}
  {/foreach}
<tr>
  <td>Subtotal</td><td colspan="2">&nbsp;</td><td align="right">{$order.subtotal|string_format:"%01.2f"}</td>
</tr>
<tr>
  <td>Freight</td><td colspan="2">&nbsp;</td><td align="right">{$order.freight|string_format:"%01.2f"}</td>
</tr>
<tr>
  <td>Amount</td><td colspan="2">&nbsp;</td><td align="right">{$currency}{$currencysymbol}{$order.amount|string_format:"%01.2f"}</td>
</tr>

</tbody>
</table>

<table class="adminTransactionList">
  <thead>
    <tr>
      <th>Shipping Address</th>
    </tr>
  </thead>
  <tbody>
    <tr class="row1'}">
      <td colspan="3">
      {foreach from=$fields key=k item=i}
          {if $i}{$i}<br />{/if}
      {/foreach}
      </td>
      </tr>
  </tbody>
</table>
</div>