<div class="jojo_cart">
{include file="jojo_cart_test_mode.tpl"}

{jojoHook hook="jojo_cart_checkout_top"}

<div class="box">
{include file="jojo_cart_payment_customerdetails.tpl"}
</div>

<div class="box">

<h2>##Order information##</h2>
<table id="shoppingcart">
  <thead>
  <tr>
    <th  style="text-align:left">##Item##</th>
    <th>##Qty##</th>
    <th>##Price##</th>
    <th>##Line Total##</th>
  </tr>
  </thead>
  <tbody>
{foreach from=$items key=k item=i}{*{if $i.linetotal > 0}*}
  <tr>
    <td class="cart-item">{$i.name}</td>
    <td class="quantity">{$i.quantity}</td>
    <td class="cart-price">{if $i.netprice != $i.price}<strike>{$i.price|string_format:"%01.2f"}</strike> {$i.netprice|string_format:"%01.2f"}{else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
    <td class="cart-linetotal">{$i.linetotal|string_format:"%01.2f"}</td>
  </tr>
{*{/if}*}{/foreach}
  </tbody>
</table>
  {* Subtotal *}
   <div id="cart-subtotal">
        {if $order.fixedorder}##Discount##: {$order.fixedorder|string_format:"%01.2f"}<br />{/if}
        ##Sub-total##: <span>{$order.currency_symbol|default:' '}{$order.subtotal|string_format:"%01.2f"}</span>
   </div>

   <div id="cart-freight">
        ##Freight##: {if $order.freight}{$order.currency_symbol|default:' '}{$order.freight|string_format:"%01.2f"}{else}n/a{/if}
   </div>

   {if $order.surcharge}<div id="cart-surcharge">
		##{$order.surchargedescription}##: {$order.currency_symbol|default:' '}{$order.surcharge|string_format:"%01.2f"}
   </div>
	{/if}

    <div id="cart-total">
        ##Total##: <span>{$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}</span>
        {*{if $OPTIONS.cart_show_gst != 'no' && (($order.currency=='NZD') || ($order.currency=='' && $OPTIONS.cart_default_currency=='NZD'))}<p class="note">##includes GST of## {$order.currency_symbol|default:' '}{$order.amount/7.66666|string_format:"%01.2f"}</p>{/if}*}
        {if $OPTIONS.cart_tax_amount}
        {if $order.apply_tax}<p class="note">##includes## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}</p>
        {else}
        <p class="note">{$OPTIONS.cart_tax_name|default:'Tax'} ##not applicable##</p>
        {/if}{/if}
    </div>
{if $usediscount}
    <div id="cart-discountcode">
    {if !$discount.code}
       <p>If you have a discount code, enter it on the <a href="{$languageurlprefix}cart/" class="cart-button" title="Edit the quantities, or remove items from the order">Change Order</a> page</p>
    {else}
      <p>Using Discount Code: {$discount.code}</p>
    {/if}
     </div>
{/if}

<p><a href="{$languageurlprefix}cart/" class="cart-button button btn btn-small" title="##Edit the quantities, or remove items from the order##">##Change Order##</a></p>


</div>

{if count($paymentoptions) > 1}
  <div class="box" id="payment_option_radios">
    <h3>##Payment options##</h3>
    <ul>
    {foreach from=$paymentoptions key=k item=option}
    <li style="list-style:none">
      <label><input type="radio" id="payment_option_radio_{$option.id}" name="paymentoption" value="{$option.id}" />{$option.label}</label>
    </li>
    {/foreach}
    </ul>
  </div>

  {foreach from=$paymentoptions key=k item=option}
  <div id="payment_option_{$option.id}" class="payment_option">{$option.html}</div>
  {/foreach}

{else}
  <div id="cart-paymentoption">
  {foreach from=$paymentoptions key=k item=option}
    <div class="payment_option">{$option.html}</div>
  {/foreach}
  </div>
{/if}

{jojoHook hook="jojo_cart_checkout_bottom"}

</div>
