An order has been placed on {$sitetitle}.

{if $status=='payment_pending'}
PAYMENT PENDING
===============
This order is awaiting payment from the customer, which should be expected to happen manually (eg cheque / bank deposit).

Once payment has been made, please dispatch the order and click the following link to update the payment status of the order.

{$SITEURL}/cart/paid/{$token}/{$actioncode}/

{else}
Payment Method: {if $activeplugin=='jojo_plugin_jojo_cart_emailorder'}Email Order (Invoice for payment){else}Online Transaction using {$activeplugin}{/if}

Errors: {if $errors}{$errors}{else}None{/if}

{if $rawreceipt}Receipt: 
{foreach from=$rawreceipt key=k item=i}
{$k}: {$i}
{/foreach}
{/if}

============
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
    Name: {$i.name}
    Quantity: {$i.quantity}
    Price (each): {if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}
    Line total: {$i.linetotal|string_format:"%01.2f"}

{/if}
{/foreach}
Sub-total: {$order.subtotal|string_format:"%01.2f"}
{if $order.freight}
Freight: {$order.freight|string_format:"%01.2f"}
{/if}
Total: {$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}
{if $discount && $discount.code != ''}

Customer used discount code: {$discount.code}
{/if}
{jojoHook hook="jojo_cart_admin_email_bottom"}
{include file="jojo_cart_checkout_admin_email.tpl"}

{if $message}


Message emailed to customer
===========================
{$message}{/if}