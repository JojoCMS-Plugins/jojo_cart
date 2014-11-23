<div id="adminTransactionList">
Order ID = {$id}
{jojoHook hook="jojo_cart_transaction_list_above_order_details"}
<table class="table">
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
    <tr class="{if $i.status} {$i.status}{/if}">
      <td>{if $i.plu}{$i.plu} {/if}{$i.name}</td>
      <td>{$i.quantity}</td>
      <td>{if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
      <td align="right">{$i.linetotal|string_format:"%01.2f"}</td>
    </tr>
    {/if}
  {/foreach}
{if $order.fixedorder}<tr>
  <td colspan="3">Discount</td><td align="right">{$order.fixedorder|string_format:"%01.2f"}</td>
</tr>
{/if}{if $discount && $discount.code != ''}<tr>
  <td colspan="3">Customer used discount code</td><td align="right">{$discount.code}</td>
</tr>{/if}{if $points.discount}<tr>
  <td colspan="3">Points Discount</td><td align="right">-{$points.discount|string_format:"%01.2f"}</td>
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

<h3>Delivery Details</h3>
<p>{foreach from=$shipping key=k item=i}{if $i}{$i}<br />{/if}
{/foreach}</p>

<h3>Billing Details</h3>
<p>{foreach from=$billing key=k item=i}{if $i}{$i}<br />{/if}
{/foreach}</p>

{jojoHook hook="jojo_cart_transaction_list_bottom"}
{if $receipt}{$receipt}{/if}
</div>
