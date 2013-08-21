<p>##There are multiple shipping methods available for your order. Please select your preferred shipping method below.##</p>

<form action="{$languageurlprefix}cart/shipping/" method="POST">

    <ul class="shipping_methods unstyled">
    {foreach from=$shippingMethods key=methodid item=method}
        <li style="list-style:none"><label for="shippingMethod_{$methodid}" class="radio"><input type="radio" id="shippingMethod_{$methodid}" name="shippingmethod" value="{$methodid}" {if $selectedMethod == $methodid} checked="checked"{/if} />##{$method.label}## - {$currency|default:$OPTIONS.cart_default_currency}{$currencysymbol|default:' '}{$method.cost|string_format:"%01.2f"}</label></li>
    {/foreach}
    </ul>
    <input type="submit" class="btn btn-primary" value="##Continue##" />

</form>
