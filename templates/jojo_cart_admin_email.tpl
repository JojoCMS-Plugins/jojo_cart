An order has been placed on {$sitetitle}.

{if $status=='payment_pending'}
{if $pending_template.admin}
{include file=$pending_template.admin}
{else}
{include file='jojo_cart_admin_email_pending.tpl'}
{/if}
{else}
Payment Method: {if $activeplugin=='jojo_plugin_jojo_cart_emailorder'}Email Order (Invoice for payment){else}Online Transaction using {$activeplugin}{/if}
Errors: {if $errors}{$errors}{else}None{/if}

{if $rawreceipt}Receipt:
{foreach from=$rawreceipt key=k item=i}
{$k}: {$i}
{/foreach}
{/if}

Payment Type
============
{$handler}

{/if}
{if $OPTIONS.cart_confirm_shipped == 'yes'}
Once the order has been shipped
================================
Click the following link to mark the order as being shipped. You can optionally send a message to the customer to notify them.

{$SITEURL}/cart/shipped/{$token}/{$actioncode}/
{/if}

Order Details
=============
Order no: {$id}
{foreach from=$items key=k item=i}
{if $i.quantity > 0}
    Name: {$i.name|escape:'html':$charset}
    Quantity: {$i.quantity}
    Price (each): {if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}
    Line total: {$i.linetotal|string_format:"%01.2f"}

{/if}{/foreach}
{if $order.fixedorder}Discount: {$order.fixedorder|string_format:"%01.2f"}
{/if}
Sub-total: {$order.subtotal|string_format:"%01.2f"}
{if $order.freight}
Freight: {$order.freight|string_format:"%01.2f"}
{/if}Total: {$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}
{if $OPTIONS.cart_tax_amount}
{if $order.apply_tax}includes {$OPTIONS.cart_tax_amount}% {$OPTIONS.cart_tax_name|default:'Tax'}
{else}
{$OPTIONS.cart_tax_name|default:'Tax'} not applicable
{/if}{/if}
{if $discount && $discount.code != ''}

Customer used discount code: {$discount.code}
{/if}{if $order.giftwrap}
Please giftwrap the items on this order
{/if}
{jojoHook hook="jojo_cart_admin_email_bottom"}
{include file="jojo_cart_checkout_admin_email.tpl"}

{if $message}

Message emailed to customer
===========================
{$message}{/if}