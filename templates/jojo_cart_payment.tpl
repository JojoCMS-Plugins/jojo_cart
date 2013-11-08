<div class="jojo_cart">
{include file="jojo_cart_test_mode.tpl"}
{jojoHook hook="jojo_cart_checkout_top"}
    <div>
        {include file="jojo_cart_payment_customerdetails.tpl"}
    </div>
    <div>
        <h2>##Order information##</h2>
        <table id="shoppingcart" class="table">
            <thead>
                <tr>
                    <th class="cart-item">##Item##</th>
                    <th class="cart-quantity">##Qty##</th>
                    <th class="cart-price">##Price##</th>
                    <th class="cart-linetotal">##Total##</th>
                </tr>
            </thead>
            <tbody>
    {foreach from=$items key=k item=i}{if $i.linetotal > 0}
              <tr class="cart-items">
                <td class="cart-item">{$i.name}</td>
                <td class="cart-quantity">{$i.quantity}</td>
                <td class="cart-price">{if $i.netprice != $i.price}<strike>{$i.price|string_format:"%01.2f"}</strike> {$i.netprice|string_format:"%01.2f"}{else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
                <td class="cart-linetotal">{$i.linetotal|string_format:"%01.2f"}</td>
              </tr>
    {/if}{/foreach}
    {if $pointsused}
            <tr>
                <td colspan="5" id="cart-fixedorder">##Points Discount##: <span>{$order.currency_symbol|default:' '}{$pointsdiscount|string_format:"%01.2f"}<br /></span></td>
            </tr>
    {/if}
      {* Subtotal *}
            <tr>
                <td colspan="5" id="cart-subtotal">
            {if $order.fixedorder}##Discount##: {$order.fixedorder|string_format:"%01.2f"}<br />{/if}
            ##Sub-total##: <span>{$order.currency_symbol|default:' '}{$order.subtotal|string_format:"%01.2f"}</span>
                </td>
            </tr>
            <tr>
                <td colspan="5" id="cart-freight">##Freight##: {if $order.freight}{$order.currency_symbol|default:' '}{$order.freight|string_format:"%01.2f"}{else}n/a{/if}
                    {if $order.surcharge}<div id="cart-surcharge">##{$order.surchargedescription}##: {$order.currency_symbol|default:' '}{$order.surcharge|string_format:"%01.2f"}</div>{/if}
                </td>
            </tr>

            <tr>
                <td colspan="5" id="cart-total">
                    ##Total##: <span>{$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}</span>
                    {*{if $OPTIONS.cart_show_gst != 'no' && (($order.currency=='NZD') || ($order.currency=='' && $OPTIONS.cart_default_currency=='NZD'))}<p class="note">##includes GST of## {$order.currency_symbol|default:' '}{$order.amount/7.66666|string_format:"%01.2f"}</p>{/if}*}
                    {if $OPTIONS.cart_tax_amount}
                    {if $order.apply_tax}<p class="note">##includes## {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}</p>
                    {else}
                    <p class="note">{$OPTIONS.cart_tax_name|default:'Tax'} ##not applicable##</p>
                    {/if}{/if}
                </td>
            </tr>
          </tbody>
    </table>

    {if $usediscount}
        <div id="cart-discountcode">
        {if !$discount.code}
           <p>If you have a discount code, enter it on the <a href="{$languageurlprefix}cart/" class="cart-button btn btn-small" title="Edit the quantities, or remove items from the order">Change Order</a> page</p>
        {else}
          <p>Using Discount Code: {$discount.code}</p>
        {/if}
         </div>
    {/if}{if $useloyalty && !$pointsused}
        <div id="cart-loyalty">
           <p>You have points available, use them on the <a href="{$languageurlprefix}cart/" class="cart-button btn btn-small" title="Edit the quantities, or remove items from the order">Change Order</a> page</p>
         </div>
    {/if}

    <p><a href="{$languageurlprefix}cart/" class="cart-button button btn btn-small" title="##Edit the quantities, or remove items from the order##">##Change Order##</a></p>


    </div>

      <div id="cart-paymentoption">
    {if count($paymentoptions) > 1}
        <h3>##Payment options##</h3>
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
      {foreach from=$paymentoptions key=k item=option}
        <div class="payment_option">{$option.html}</div>
      {/foreach}
    {/if}
      </div>

    {jojoHook hook="jojo_cart_checkout_bottom"}
</div>
