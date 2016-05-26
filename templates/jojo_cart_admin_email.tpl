An order has been placed on {$sitetitle}.

{if $status=='payment_pending'}{if $pending_template.admin}{include file=$pending_template.admin}  
{else}{include file='jojo_cart_admin_email_pending.tpl'}  
{/if}{else}**Payment Method:** {if $activeplugin=='Manual Payment'}None (Invoice for payment){else}{$activeplugin}{/if}  
{if $errors}**Errors:** {$errors}  
{else}{/if}
{if $rawreceipt}**Receipt:**  
{foreach from=$rawreceipt key=k item=i}{$k}: {$i}  
{/foreach}{/if}

{/if}{if $OPTIONS.cart_confirm_shipped == 'yes'}####Once the order has been shipped
Click the following link to mark the order as being shipped. You can optionally send a message to the customer to notify them.  
{$SITEURL}/cart/shipped/{$token}/{$actioncode}/

{/if}{foreach from=$items key=k item=i}{if $i.plu}{assign var='plu' value=true}{/if}{/foreach}
###Order Details (#{$id})
item          |{if $plu} plu | {/if}   qty   |   price  | line total 
:------------ |{if $plu}:--- | {/if}:------: | -------: | ----------: {foreach from=$items key=k item=i}{if $i.quantity > 0} 
{$i.name}     |{if $i.plu}{$i.plu}    | {/if} {$i.quantity} | {if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if} | {$i.linetotal|string_format:"%01.2f"}
{/if}{/foreach}

{if $order.fixedorder}Discount: -{$order.fixedorder|string_format:"%01.2f"}  
{/if}{if $points.discount}Points: -{$points.discount|string_format:"%01.2f"}  
{/if}Sub-total: {$order.subtotal|string_format:"%01.2f"}  
{if $order.freight}Freight: {$order.freight|string_format:"%01.2f"}  
{/if}**Total: {$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}**  
{if $OPTIONS.cart_tax_amount}{if $order.apply_tax}includes {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}  
{else}{$OPTIONS.cart_tax_name|default:'Tax'} not applicable  {/if}

{/if}{if $discount && $discount.code != ''}Customer used discount code: {$discount.code}

{/if}{if $order.giftwrap}This is a gift  
{if $order.giftmessage}**Message:**  
{$order.giftmessage}
{/if}{/if}
{jojoHook hook="jojo_cart_admin_email_bottom"}
{include file="jojo_cart_checkout_admin_email.tpl"}
View the full transaction report at {$SITEURL}/admin/cart/transactions/

{if $message}###Message emailed to customer
{$message}{/if}