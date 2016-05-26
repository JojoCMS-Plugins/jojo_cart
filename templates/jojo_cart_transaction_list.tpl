<div id="adminTransactionList">
{jojoHook hook="jojo_cart_transaction_list_above_order_details"}
<h4>Order {$id} from {if $fields.billing_firstname}{$fields.billing_firstname} {/if}{if $fields.billing_lastname}{$fields.billing_lastname}{/if}</h4>
<table class="table">
  <thead>
    <tr>
      <th>Item</th>
      <th>Qty</th>
      <th>Price</th>
      <th style="text-align:right">Total</th>
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
  <td colspan="3">Discount</td><td style="text-align:right">{$order.fixedorder|string_format:"%01.2f"}</td>
</tr>
{/if}{if $discount && $discount.code != ''}<tr>
  <td colspan="3">Customer used discount code</td><td style="text-align:right">{$discount.code}</td>
</tr>{/if}{if $points.discount}<tr>
  <td colspan="3">Points Discount</td><td style="text-align:right">-{$points.discount|string_format:"%01.2f"}</td>
</tr>
{/if}
<tr>
  <td colspan="3">Subtotal</td><td style="text-align:right">{$order.subtotal|string_format:"%01.2f"}</td>
</tr>
<tr>
  <td colspan="3">Freight</td><td style="text-align:right">{$order.freight|string_format:"%01.2f"}</td>
</tr>
<tr>
  <td colspan="3">Total</td><td style="text-align:right"><span class="note">{$currency}</span>{$currencysymbol}{$order.amount|string_format:"%01.2f"}{if $order.apply_tax}<span class="note"> (inc {$OPTIONS.cart_tax_name|default:'Tax'})</span>{/if}</td>
</tr>
</tbody>
</table>

{jojoHook hook="jojo_cart_transaction_list_below_order_details"}
    <div class="row">
        <div class="col-sm-6">
            <h4>##Shipping##</h4>
            <p><span class="name">{if $fields.shipping_firstname}{$fields.shipping_firstname} {/if}{if $fields.shipping_lastname}{$fields.shipping_lastname}{/if}</span><br>
            {if $fields.shipping_phone}ph: {$fields.shipping_phone}<br>{/if}
            {if $fields.shipping_email}e: <a href="mailto:{$fields.shipping_email}">{$fields.shipping_email}</a><br>{/if}</p>
            <p>{if $fields.shipping_company}<b>{$fields.shipping_company}</b><br>{/if}
            {if $fields.shipping_address1}{$fields.shipping_address1}<br>{/if}
            {if $fields.shipping_address2}{$fields.shipping_address2}<br>{/if}
            {if $fields.shipping_suburb}{$fields.shipping_suburb}<br>{/if}
            {if $fields.shipping_city}{$fields.shipping_city}{/if}{if $fields.shipping_state} {$fields.shipping_state}{/if}{if $fields.shipping_postcode} {$fields.shipping_postcode}{/if}<br>
            {$found=false}{foreach from=$countries item=country}{if $fields.shipping_country && !$found && $country.code|strtoupper==$fields.shipping_country}<span class="country">{$country.name}</span><br/>{$found=true}{/if}{/foreach}
            </p>
            <p>{if $shippingRegion}Zone: {$shippingRegion}<br>{/if}
            {if $shippingMethod}Method: {$shippingMethod}<br>{/if}
            {foreach from=$shipping key=k item=i}{if $i}{$k}: {$i}<br />{/if}
            {/foreach}</p>
            {if $fields.shipping_special}<p>Special instructions: {$fields.shipping_special}</p>{/if}
        </div>
        <div class="col-sm-6">
            <h4>##Billing##</h4>
            <p><span class="name">{if $fields.billing_firstname}{$fields.billing_firstname} {/if}{if $fields.billing_lastname}{$fields.billing_lastname}{/if}</span><br>
            {if $fields.billing_phone}ph: {$fields.billing_phone}<br>{/if}
            {if $fields.billing_email}e: <a href="mailto:{$fields.billing_email}">{$fields.billing_email}</a><br>{/if}</p>
            <p>{if $fields.billing_company}<b>{$fields.billing_company}</b><br>{/if}
            {if $fields.billing_address1}{$fields.billing_address1}<br>{/if}
            {if $fields.billing_address2}{$fields.billing_address2}<br>{/if}
            {if $fields.billing_suburb}{$fields.billing_suburb}<br>{/if}
            {if $fields.billing_city}{$fields.billing_city}{/if}{if $fields.billing_state}<br>{$fields.billing_state}{/if}{if $fields.billing_postcode} {$fields.billing_postcode}{/if}<br>
            {$found=false}{foreach from=$countries item=country}{if $fields.billing_country && !$found && $country.code|strtoupper==$fields.billing_country}<span class="country">{$country.name}</span><br/>{$found=true}{/if}{/foreach}
            </p>
            <p>{foreach from=$billing key=k item=i}{if $i}{$k}: {$i}<br />{/if}
            {/foreach}</p>
        </div>
    </div>
{jojoHook hook="jojo_cart_transaction_list_bottom"}
{if $receipt}{$receipt}{/if}
</div>
