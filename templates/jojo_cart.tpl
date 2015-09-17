{if $pg_body}{$pg_body}{/if}

<div class="shoppingcart">
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
    <div{if $OPTIONS.cart_zero_quantities=='yes'} class="orderlist"{/if}>
        <div class="row hidden-xs titles">
            <div class="cart-item col-sm-6">##Item##</div>
            <div class="cart-quantity col-sm-3">##Qty##</div>
            <div class="cart-linetotal col-sm-2">##Total##</div>
            <div class="col-sm-1">&nbsp;</div>
        </div>
{foreach from=$items key=k item=i}
        <div id="row_{$i.id}" class="row cart-items">
            <div class="item-wrap clearfix">
                <div class="cart-item col-sm-6 col-xs-11">{if $i.image}<img class="cart-image hidden-xs" src="{$i.image}" alt="{$i.name}" /> {/if}<span class="cart-itemname">{$i.name}</span>{if $i.description}<span class="cart-itemdescription"><br />{$i.description}</span>{/if}</div>
                <div class="cart-quantity col-sm-3 col-xs-8">
    {if $i.quantity_fixed}
                    <input type="hidden" name="quantity[{$i.id}]" id="quantity[{$i.id}]" value="{$i.quantity}" />{$i.quantity}
    {else}
                    <input type="number" class="cart-quantity form-control" name="quantity[{$i.id}]" id="quantity[{$i.id}]" size="3" value="{$i.quantity}" min="{if $i.min_quantity}{$i.min_quantity}{elseif $OPTIONS.cart_zero_quantities=='yes'}0{else}1{/if}"{if $i.max_quantity}  max="{$i.max_quantity}"{/if} />
    {/if}
                    <span class="cart-price">@{$order.currency_symbol|default:' '}{if $i.netprice != $i.price}<strike>{$i.price|string_format:"%01.2f"}</strike> {$i.netprice|string_format:"%01.2f"}{else}{$i.netprice|string_format:"%01.2f"}{/if}</span>
                </div>
                <div class="cart-linetotal col-sm-2 col-xs-4">{$order.currency_symbol|default:' '}<span>{$i.linetotal|string_format:"%01.2f"}</span></div>
                <div class="cart-remove col-sm-1"><a class="close" title="remove this item" href="{$languageurlprefix}cart/remove/{$i.id|escape:'url'}/" rel="nofollow">x</a></div>
            </div>
        </div>
    {/foreach}
    </div>
    {if $order.fixedorder}
    <div class="row">
        <div class="col-sm-11" id="cart-fixedorder">##Discount##: {$order.currency_symbol|default:' '}<span>{$order.fixedorder|string_format:"%01.2f"}<br /></span></div>
    </div>
    {/if}{if $pointsused}
    <div class="row">
        <div class="col-sm-11" id="cart-fixedorder">##Points Discount##: {$order.currency_symbol|default:' '}<span>{$pointsdiscount|string_format:"%01.2f"}<br /></span></div>
    </div>
    {/if}
    <div class="row">
        <div class="col-sm-11"  id="cart-subtotal">##Sub-total##: {$order.currency_symbol|default:' '}<span>{$order.subtotal|string_format:"%01.2f"}</span></div>
    </div>
    <div class="row">
        <div class="col-sm-11"  id="cart-freight">##Freight## {if $order.freight}(##based on current delivery address##): {$order.currency_symbol|default:' '}<span>{$order.freight|string_format:"%01.2f"}</span>{else}(##to be calculated##){/if}
       {if $order.surcharge}<div id="cart-surcharge">##{$order.surchargedescription}##: {$order.currency_symbol|default:' '}<span>{$order.surcharge|string_format:"%01.2f"}</span></div>
       {/if}
       </div>
   </div>
    
    <div class="row">
        <div class="col-sm-11 cart-total">
         ##Total##: {$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}<span>{$order.amount|string_format:"%01.2f"}</span>
        {if $OPTIONS.cart_tax_amount}
        {if $order.apply_tax}<p class="note">##includes## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}</p>
        {else}<p class="note">##excluding## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'} (##if applicable##)</p>
        {/if}{/if}
        </div>
    </div>
    {if $usediscount}
    <div class="row">
      <div class="col-sm-4">
              <div id="cart-discountcode">
                <label for="discountCode">Discount Code:</label>
                <div class="input-group">
                <input class="form-control" type="text" size="10" name="discountCode" id="discountCode" value="{if $discount.code}{$discount.code}{/if}" />
                <span class="input-group-btn"><input type="submit" name="applyDiscount" id="applyDiscount" value="Apply" class="btn btn-default"/></span>
                </div>
            </div>
        </div>
    </div>
    {/if}
    {if $pointsavailable}
    <div class="row">
      <div class="col-sm-4">
            <div id="cart-points">
                <label for="points">Your Points</label>
                <div class="input-group">
                    <span class="note">Use </span>
                    <input class="form-control" type="text" size="10" name="points" id="points" value="{if $pointsused!==false}{$pointsused}{elseif $pointsavailable}{$pointsavailable}{/if}" /><span class="note"> out of {$pointsavailable}</span>
                    <span class="input-group-btn"><input type="submit" name="applyPoints" id="applyPoints" value="Apply" class="btn btn-default"/></span>
                </div>
            </div>
        </div>
    </div>
    {/if}
    
    {if $OPTIONS.cart_free_gift_wrap == 'yes'}
    <div id="cart-giftwrap" class="checkbox">
        <label for="giftwrap"><input type="checkbox" name="giftwrap" id="giftwrap" value="1" {if $order.giftwrap==true}checked="checked"{/if} /> ##This is a gift##</label>
    </div>
   {if $OPTIONS.cart_free_gift_message == 'yes'}
   <div id="giftmessagefield" class="form-fieldset form-group"{if $order.giftwrap==false} style="display: none;"{/if}>
        <label for="gift_message">Message</label>
        <textarea class="form-control input textarea" rows="4" cols="40" name="giftmessage" id="giftmessage">{if $order.giftmessage}{$order.giftmessage}{/if}</textarea>
    </div>
    {/if}
    {/if}
    {jojoHook hook="jojo_cart_before_buttons"}
    <div id="cart-updatebuttons">
        <input type="submit" name="update"   id="update"   value="##Update##"     class="btn btn-default btn-small" title="##Updates the totals if you have modified quantities for any items##" />
        {if $OPTIONS.cart_show_empty=='yes'}<input type="submit" name="empty"    id="empty"    value="##Empty Cart##" class="btn btn-default btn-small" title="##Removes all items from your cart##" />{/if}
        {if $OPTIONS.cart_show_continue}<a href="{$OPTIONS.cart_show_continue}" class="btn btn-primary">##Continue Shopping##</a>{/if}
        <button type="submit" name="checkout" id="checkout" class="btn btn-primary" value="##Checkout##" title="##Proceed to the checkout page where you can pay for this order##">##Checkout##</button>
    </div>
{/if}
</form>
{jojoHook hook="jojo_cart_bottom"}
</div>