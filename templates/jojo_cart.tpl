{if $pg_body}{$pg_body}{/if}

<div class="jojo_cart">
{include file="jojo_cart_test_mode.tpl"}
{if $errors}
    <div class="errors">
        <p>##Please correct the following errors before continuing##</p>
        <ul>
        {foreach from=$errors item=e}
              <li>{$e}</li>
        {/foreach}
        </ul>
    </div>
{/if}

{jojoHook hook="jojo_cart_top"}
<form method="post" action="{$languageurlprefix}cart/update/">
{if $cartisempty}
    <p><strong>##Your shopping cart is empty##.</strong></p>
    {jojoHook hook="jojo_cart_empty"}
{else}
    <div class="box">
        <h2>##Items in cart##</h2>
        <table id="shoppingcart" class="table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="cart-item">##Item##</th>
                    <th class="cart-quantity">##Qty##</th>
                    <th class="cart-price">##Price##</th>
                    <th class="cart-linetotal">##Total##</th>
                </tr>
            </thead>
            <tbody>
    {foreach from=$items key=k item=i}
                <tr id="row_{$i.id}" class="cart-items">
                    <td class="cart-image">{if $i.image}<img class="boxed" src="{$i.image}" alt="{$i.name}" />{else}&nbsp;{/if}</td>
                    <td class="cart-item">{$i.name}{if $i.description}<br /><span class="cart-itemdescription">{$i.description}</span>{/if}</td>
                    <td class="cart-quantity">
        {if $i.quantity_fixed}
                        <input type="hidden" name="quantity[{$i.id}]" id="quantity[{$i.id}]" value="{$i.quantity}" />{$i.quantity}
        {else}
                        <input type="text" class="cart-quantity" name="quantity[{$i.id}]" id="quantity[{$i.id}]" size="3" value="{$i.quantity}" />
        {/if}
                        <a class="cart-remove" title="remove this item" href="{$languageurlprefix}cart/remove/{$i.id|escape:'url'}/" rel="nofollow">x</a>
                    </td>
                    <td class="cart-price">{if $i.netprice != $i.price}<strike>{$i.price|string_format:"%01.2f"}</strike> {$i.netprice|string_format:"%01.2f"}{else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
                    <td class="cart-linetotal"><span>{$i.linetotal|string_format:"%01.2f"}</span></td>
                </tr>
    {/foreach}
{if $order.fixedorder}
        <tr>
            <td colspan="5" id="cart-fixedorder">##Discount##: <span>{$order.currency_symbol|default:' '}{$order.fixedorder|string_format:"%01.2f"}<br /></span></td>
        </tr>
{/if}{if $pointsused}
        <tr>
            <td colspan="5" id="cart-fixedorder">##Points Discount##: <span>{$order.currency_symbol|default:' '}{$pointsdiscount|string_format:"%01.2f"}<br /></span></td>
        </tr>
{/if}
        <tr>
            <td colspan="5"  id="cart-subtotal">##Sub-total##: <span>{$order.currency_symbol|default:' '}{$order.subtotal|string_format:"%01.2f"}</span></td>
        </tr>
       <tr>
            <td colspan="5"  id="cart-freight">##Freight##: {if $order.freight}<span>{$order.freight|string_format:"%01.2f"}</span> (##based on current delivery address##){else}(##to be calculated##){/if}
           {if $order.surcharge}<div id="cart-surcharge">##{$order.surchargedescription}##: {$order.currency_symbol|default:' '}<span>{$order.surcharge|string_format:"%01.2f"}</span></div>
           {/if}
           </td>
       </tr>
    	
        <tr>
            <td colspan="5"  id="cart-total">
             ##Total##: <span>{$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}</span>
            {*{if $OPTIONS.cart_show_gst != 'no' && (($order.currency=='NZD') || ($order.currency=='' && $OPTIONS.cart_default_currency=='NZD'))}<p class="note">##includes GST of## {$order.currency_symbol|default:' '}{$order.amount/7.66666|string_format:"%01.2f"}</p>{/if}*}
            {if $OPTIONS.cart_tax_amount}
            {if $order.apply_tax}<p class="note">##includes## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}</p>
            {else}
            <p class="note">##excluding## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'} (##if applicable##)</p>
            {/if}{/if}
            </td>
        </tr>
       </tbody>
    </table>
    {if $usediscount}
        <div id="cart-discountcode">
            <label for="discountCode">Discount Code:</label>
            <input type="text" size="10" name="discountCode" id="discountCode" value="{if $discount.code}{$discount.code}{/if}" />
            <input type="submit" name="applyDiscount" id="applyDiscount" value="Apply" class="btn btn-small"/>
        </div>
    {/if}
    {if $pointsavailable}
        <div id="cart-points">
            <label for="points">Your Points</label>
            <div class="form-controls"><span class="note">Use </span><input type="text" size="10" name="points" id="points" value="{if $pointsused!==false}{$pointsused}{elseif $pointsavailable}{$pointsavailable}{/if}" /><span class="note"> out of {$pointsavailable}</span><input type="submit" name="applyPoints" id="applyPoints" value="Apply" class="btn btn-small"/></div>
        </div>
    {/if}
    
    {if $OPTIONS.cart_free_gift_wrap == 'yes'}
        <div id="cart-giftwrap">
            <label for="giftwrap">Free gift wrapping:</label>
            <input type="checkbox" name="giftwrap" id="giftwrap" value="1" {if $order.giftwrap==true}checked="checked"{/if} />
        </div>
    {/if}
    </div>
    {jojoHook hook="jojo_cart_before_buttons"}
    <div id="cart-updatebuttons">
        <input type="submit" name="update"   id="update"   value="##Update##"     class="btn btn-small" title="##Updates the totals if you have modified quantities for any items##" />
        <input type="submit" name="empty"    id="empty"    value="##Empty Cart##" class="btn btn-small" title="##Removes all items from your cart##" />
        <input type="submit" name="checkout" id="checkout" value="##Checkout##"   class="btn btn-primary"           title="##Proceed to the checkout page where you can pay for this order##" />
    </div>
{/if}
</form>
{jojoHook hook="jojo_cart_bottom"}
</div>