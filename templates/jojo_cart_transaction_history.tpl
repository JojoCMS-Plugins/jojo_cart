<div id="order-history">
{if $transactions}{foreach $transactions k t}
    <div class="order">
        <h3>Order #{$t.id} &nbsp;<button class="show-details btn btn-xs btn-default" data-target="order-{$k}-details"><span>show</span><span class="hider" style="display:none;">hide</span> details</button></h3>
        <p>{$t.completed} - {$t.numitems} item{if $t.numitems>1}s{/if} for {$t.cart->order.currency}{$t.cart->order.currencysymbol}{$t.cart->order.amount|string_format:"%01.2f"} ({$t.status})</p>
        <div id="order-{$k}-details" class="order-details">
            <table class="table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th style="text-align:center">Quantity</th>
                  <th style="text-align:center">Price</th>
                  <th style="text-align:right">Total</th>
                </tr>
              </thead>
              <tbody>
              {foreach from=$t.items key=k item=i}
                {if $i.quantity > 0}
                <tr class="{if $i.status} {$i.status}{/if}">
                  <td>{if $i.details && $i.details['url']}<a href="{$i.details['url']}" title="View product page">{/if}{if $i.plu}{$i.plu} {/if}{$i.name}{if $i.details && $i.details['url']}</a>{/if}</td>
                  <td style="text-align:center">{$i.quantity}</td>
                  <td style="text-align:center">{if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}</td>
                  <td style="text-align:right">{$i.linetotal|string_format:"%01.2f"}</td>
                </tr>
                {/if}
              {/foreach}
            {if $order.fixedorder}<tr>
              <td colspan="3">Discount</td><td align="right">{$order.fixedorder|string_format:"%01.2f"}</td>
            </tr>
            {/if}{if $t.cart->discount && $t.cart->discount.code != ''}<tr>
              <td colspan="3">Customer used discount code</td><td align="right">{$t.cart->discount.code}</td>
            </tr>{/if}{if $t.cart->points.discount}<tr>
              <td colspan="3">Points Discount</td><td align="right">-{$t.cart->points.discount|string_format:"%01.2f"}</td>
            </tr>
            {/if}
            <tr>
              <td colspan="3">Subtotal</td><td align="right">{$t.cart->order.subtotal|string_format:"%01.2f"}</td>
            </tr>
            <tr>
              <td colspan="3">Freight</td><td align="right">{$t.cart->order.freight|string_format:"%01.2f"}</td>
            </tr>
            <tr>
              <td colspan="3">Amount</td><td align="right"><b>{$t.cart->order.currency}{$t.cart->order.currencysymbol}{$t.cart->order.amount|string_format:"%01.2f"}</b><br>({if $t.cart->order.apply_tax}inc {$OPTIONS.cart_tax_name|default:'Tax'}{else}no {$OPTIONS.cart_tax_name|default:'Tax'}{/if})</td>
            </tr>
            </tbody>
            </table>

            <div class="row">
                {if $t.shipping}<div class="col-sm-6">
                    <h4>Delivery Details</h4>
                    <p>{foreach from=$t.shipping key=k item=i}{if $k=='shippingMethodName' && $i}Shipping method: {/if}{if $i && $k!='shippingMethod' && $k!='shippingRegion'}{$i} {if $k!='shipping_firstname'}<br />{/if}{/if}
                    {/foreach}</p>
                </div>
                {/if}
                {if $t.billing}<div class="col-sm-6">
                    <h4>Billing Details</h4>
                    <p>{foreach from=$t.billing key=k item=i}{if $i}{$i} {if $k!='billing_firstname'}<br />{/if}{/if}
                    {/foreach}</p>
                </div>
                {/if}
            </div>
        </div>
    </div>
{/foreach}
{elseif $loggedIn}
    <p>There are no records for previous orders available for this login.</p>
{else}
    <p>You must be <a href="{$SITEURL}/login/cart/transaction_history/">logged in</a> to retrieve past order information</p>
{/if}
</div>
