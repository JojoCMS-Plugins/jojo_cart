<div id="adminTransactionList">
Order ID = {$id}
{jojoHook hook="jojo_cart_transaction_list_above_order_details"}
<table border="1" class="adminTransactionList">
  <thead>
    <tr>
      <th>Item</th>
      <th>Quantity</th>
      <th>Price</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
  {foreach from=$items key=k item=i}
    {if $i.quantity > 0}
    <tr class="{cycle values='row1,row2'}{if $i.status} {$i.status}{/if}">
      <td>{$i.name}</td>
      <td>{$i.quantity}</td>
      <td>{if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
      <td align="right">{$i.linetotal|string_format:"%01.2f"}</td>
    </tr>
    {/if}
  {/foreach}
{if $order.fixedorder}<tr>
  <td colspan="3">Discount</td><td align="right">{$order.fixedorder|string_format:"%01.2f"}</td>
</tr>
{/if}{if $points}<tr>
  <td colspan="3">Points</td><td align="right">-{$points.discount|string_format:"%01.2f"}</td>
</tr>
{/if}
<tr>
  <td colspan="3">Subtotal</td><td align="right">{$order.subtotal|string_format:"%01.2f"}</td>
</tr>
<tr>
  <td colspan="3">Freight</td><td align="right">{$order.freight|string_format:"%01.2f"}</td>
</tr>
<tr>
  <td colspan="3">Amount</td><td align="right">{$currency}{$currencysymbol}{$order.amount|string_format:"%01.2f"} ({if $order.apply_tax}inc {$OPTIONS.cart_tax_name|default:'Tax'}{else}no {$OPTIONS.cart_tax_name|default:'Tax'}{/if})</td>
</tr>

</tbody>
</table>

{jojoHook hook="jojo_cart_transaction_list_below_order_details"}

<table class="adminTransactionList">
  <thead>
    <tr>
      <th>Delivery Details</th>
    </tr>
  </thead>
  <tbody>
    <tr class="row1">
      <td colspan="3">
      {foreach from=$shipping key=k item=i}
          {if $i}{$i}<br />{/if}
      {/foreach}
      </td>
      </tr>
  </tbody>
</table>
<table class="adminTransactionList">
  <thead>
    <tr>
      <th>Billing Details</th>
    </tr>
  </thead>
  <tbody>
    <tr class="row1">
      <td colspan="3">
      {foreach from=$billing key=k item=i}
          {if $i}{$i}<br />{/if}
      {/foreach}
      </td>
      </tr>
  </tbody>
</table>
{jojoHook hook="jojo_cart_transaction_list_bottom"}
{if $receipt}{$receipt}{/if}
</div>
