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
        <table id="shoppingcart">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="cart-item">##Item##</th>
                    <th>##Qty##</th>
                    <th>##Price##</th>
                    <th>##Total##</th>
                </tr>
            </thead>
            <tbody>
    {foreach from=$items key=k item=i}
                <tr id="row_{$i.id}">
                    <td>{if $i.image}<img class="boxed" src="{$i.image}" alt="{$i.name}" />{else}&nbsp;{/if}</td>
                    <td class="cart-item">{$i.name}{if $i.description}<br /><span class="cart-itemdescription">{$i.description}</span>{/if}</td>
                    <td class="cart-quantity">
        {if $i.quantity_fixed}
                        <input type="hidden" name="quantity[{$i.id}]" id="quantity[{$i.id}]" value="{$i.quantity}" />{$i.quantity}
        {else}
                        <input type="text" class="cart-quantity" name="quantity[{$i.id}]" id="quantity[{$i.id}]" size="3" value="{$i.quantity}" />
        {/if}
                        <a class="cart-remove" title="remove this item" href="{$languageurlprefix}cart/remove/{$i.id}/" rel="nofollow">x</a>
                    </td>
                    <td class="cart-price">{if $i.netprice != $i.price}<strike>{$i.price|string_format:"%01.2f"}</strike> {$i.netprice|string_format:"%01.2f"}{else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
                    <td class="cart-linetotal"><span>{$i.linetotal|string_format:"%01.2f"}</span></td>
                </tr>
    {/foreach}
            </tbody>
        </table>
        <div id="cart-subtotal">
            ##Sub-total##: <span>{$order.currency_symbol|default:' '}{$order.subtotal|string_format:"%01.2f"}</span>
        </div>
       <div id="cart-freight">
            ##Freight## (##confirmed on next page##): {if $order.freight}{$order.freight|string_format:"%01.2f"}{else}To be calculated{/if}
       </div>
        <div id="cart-total">
             ##Total##: <span>{$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}</span>
            {if $OPTIONS.cart_show_gst != 'no' && (($order.currency=='NZD') || ($order.currency=='' && $OPTIONS.cart_default_currency=='NZD'))}<p class="note">##includes GST of## {$order.currency_symbol|default:' '}{$order.amount/7.66666|string_format:"%01.2f"}</p>{/if}
        </div>
    {if $usediscount}
        <div id="cart-discountcode">
            <label for="discountCode">Discount Code:</label>
            <input type="text" size="10" name="discountCode" id="discountCode" value="{if $discount.code}{$discount.code}{/if}" />
            <input type="submit" name="applyDiscount" id="applyDiscount" value="Apply" />
        </div>
    {/if}
    </div>
    {jojoHook hook="jojo_cart_before_buttons"}
    <div id="cart-updatebuttons">
        <input type="submit" name="update"   id="update"   value="##Update##"     title="##Updates the totals if you have modified quantities for any items##" />
        <input type="submit" name="empty"    id="empty"    value="##Empty Cart##" title="##Removes all items from your cart##" />
        <input type="submit" name="checkout" id="checkout" value="##Checkout##"   title="##Proceed to the checkout page where you can pay for this order##" />
    </div>
{/if}
</form>
{jojoHook hook="jojo_cart_bottom"}
</div>