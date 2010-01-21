<p>There are multiple shipping methods available for your order. Please select your prefered shipping method below.</p>

<form action="{if $OPTIONS.multilanguage=='yes'}{$lclanguage}/{/if}cart/shipping/" method="POST">

    <ul>
    {foreach from=$shippingMethods key=methodid item=method}
        <li><input type="radio" name="shippingmethod" value="{$methodid}" id="shippingMethod_{$methodid}" {if $selectedMethod == $methodid} checked="checked"{/if} /><label for="shippingMethod_{$methodid}">{$method.label} - {$currency|default:$OPTIONS.cart_default_currency}{$currencysymbol|default:' '}{$method.cost|string_format:"%01.2f"}</label></li>
    {/foreach}
    </ul>

    <input type="submit" value="Continue" />

</form>
