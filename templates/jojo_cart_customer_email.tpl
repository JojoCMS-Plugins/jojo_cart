Hi{if $fields.billing_firstname} {$fields.billing_firstname} {$fields.billing_lastname}{elseif $fields.shipping_firstname} {$fields.shipping_firstname} {$fields.shipping_lastname}{/if},

This is an order confirmation for an order placed recently on {$sitetitle}.

{if $message && $status!='payment_pending'}{$message}
{elseif $status=='payment_pending'}{if $pending_template.customer}
{include file=$pending_template.customer}{else}
{include file='jojo_cart_customer_email_pending.tpl'}
{/if}{/if}

{include file="jojo_cart_checkout_customer_email.tpl"}

###Order Details
Order no: {$id}

item          |   qty   |   price  | line total 
:------------ |:------: | -------: | ----------: {foreach from=$items key=k item=i}{if $i.quantity > 0} 
{$i.name}     |{$i.quantity}|{if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if} | {$i.linetotal|string_format:"%01.2f"}
{/if}{/foreach}

{if $order.fixedorder}Discount: -{$order.fixedorder|string_format:"%01.2f"}  
{/if}{if $points.discount} Points: -{$points.discount|string_format:"%01.2f"}  
{/if}Sub-total: {$order.subtotal|string_format:"%01.2f"}  
{if $order.freight}Freight: {$order.freight|string_format:"%01.2f"}  
{/if}**Total: {$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}**  
{if $OPTIONS.cart_tax_amount}{if $order.apply_tax}includes {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}  
{else}{$OPTIONS.cart_tax_name|default:'Tax'} not applicable  {/if}

{/if}{if $discount && $discount.code != ''}This order used discount code: {$discount.code}

{/if}{if $order.giftwrap}This is a gift  
{if $order.giftmessage}**Message:**  
{$order.giftmessage}
{/if}
{jojoHook hook="jojo_cart_customer_email_bottom"}
If you have any queries regarding this order, please contact us on {$OPTIONS.contactaddress|default:$OPTIONS.webmasteraddress}

Regards,  
{$OPTIONS.fromname}  
{if $sitetitle!=$OPTIONS.fromname}{$sitetitle}{/if}