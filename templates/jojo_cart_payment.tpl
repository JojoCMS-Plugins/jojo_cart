<div class="jojo_cart">
    {include file="jojo_cart_test_mode.tpl"}
    {jojoHook hook="jojo_cart_checkout_top"}
    {include file="jojo_cart_payment_customerdetails.tpl"}
    <h2>##Order information## <a href="{$languageurlprefix}cart/" class="btn btn-default btn-xs" title="##Edit the quantities, or remove items from the order##">##Change Order?##</a></h2>
    <div class="shoppingcart table">
        <div class="row hidden-xs titles">
            <div class="cart-item col-sm-7">##Item##</div>
            <div class="cart-quantity col-sm-3">##Qty##</div>
            <div class="cart-linetotal col-sm-2">##Total##</div>
        </div>
{foreach from=$items key=k item=i}{if $i.linetotal > 0}
        <div id="row_{$i.id}" class="row cart-items">
            <div class="item-wrap clearfix">
                <div class="cart-item col-sm-7">{$i.name}</div>
                <div class="cart-quantity col-sm-3 col-xs-8">{$i.quantity}@{$order.currency_symbol|default:' '}{if $i.netprice != $i.price}<strike>{$i.price|string_format:"%01.2f"}</strike> {$i.netprice|string_format:"%01.2f"}{else}{$i.netprice|string_format:"%01.2f"}{/if}</div>
                <div class="cart-linetotal col-sm-2 col-xs-4">{$order.currency_symbol|default:' '}{$i.linetotal|string_format:"%01.2f"}</div>
            </div>
        </div>
{/if}{/foreach}
  {* Subtotal *}
        <div class="row">
            <div class="col-sm-12" id="cart-subtotal">
            {if $order.fixedorder}##Discount##: <span>-{$order.fixedorder|string_format:"%01.2f"}<br /></span>{/if}
            {if $pointsused}##Points Used##: <span>-{$pointsdiscount|string_format:"%01.2f"}<br /></span>{/if}
            ##Sub-total##: <span>{$order.currency_symbol|default:' '}{$order.subtotal|string_format:"%01.2f"}</span>
            </div>
        </div>
        <div class="row">
           <div class="col-sm-12" id="cart-freight">##Freight##: {if $order.freight}{$order.currency_symbol|default:' '}{$order.freight|string_format:"%01.2f"}{else}n/a{/if}
                    {if $order.surcharge}<div id="cart-surcharge">##{$order.surchargedescription}##: {$order.currency_symbol|default:' '}{$order.surcharge|string_format:"%01.2f"}</div>{/if}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 cart-total">
                    ##Total##: <span>{$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}</span>
                    {*{if $OPTIONS.cart_show_gst != 'no' && (($order.currency=='NZD') || ($order.currency=='' && $OPTIONS.cart_default_currency=='NZD'))}<p class="note">##includes GST of## {$order.currency_symbol|default:' '}{$order.amount/7.66666|string_format:"%01.2f"}</p>{/if}*}
                    {if $OPTIONS.cart_tax_amount}
                    {if $order.apply_tax}<p class="note">##includes## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}</p>
                    {else}
                    <p class="note">{$OPTIONS.cart_tax_name|default:'Tax'} ##not applicable##</p>
                    {/if}{/if}
            </div>
        </div>
    </div>

{if $usediscount}
    <div id="cart-discountcode">
    {if !$discount.code}
       <p>If you have a discount code, enter it on the <a href="{$languageurlprefix}cart/" class="btn btn-default btn-sm" title="Edit the quantities, or remove items from the order">Change Order</a> page</p>
    {else}
      <p>Using Discount Code: {$discount.code}</p>
    {/if}
     </div>
{/if}{if $useloyalty && !$pointsused}
    <div id="cart-loyalty">
       <p>You have points available, use them on the <a href="{$languageurlprefix}cart/" class="btn btn-default btn-sm" title="Edit the quantities, or remove items from the order">Change Order</a> page</p>
     </div>
{/if}

{if count($paymentoptions) > 1}
    <div id="cart-paymentoption">
        <h2>##Payment options##</h2>
        <form>
        <div class="form-field controls">
            {foreach from=$paymentoptions key=k item=option}<label class="radio"><input type="radio" id="payment_option_radio_{$option.id}" name="paymentoption" value="{$option.id}" />{$option.label}</label>
            {/foreach}
        </div>
        </form>
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
