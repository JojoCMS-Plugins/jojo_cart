Hi{if $fields.billing_firstname} {$fields.billing_firstname} {$fields.billing_lastname}{elseif $fields.shipping_firstname} {$fields.shipping_firstname} {$fields.shipping_lastname}{/if},

This email is an order confirmation for an order placed recently on {$sitetitle}.

{if $message && $status != 'payment_pending'}
{$message}
{/if}
{if $status=='payment_pending'}

PAYMENT PENDING
===============
This order will not be completed/delivered until payment has been made.

{$OPTIONS.cart_manual_payment_instructions}

Please quote the order number "{$id}" when making any payments, so we can track your payment.
{/if}

{include file="jojo_cart_checkout_customer_email.tpl"}



Order Details
=============
Order no: {$id}
{foreach from=$items key=k item=i}
{if $i.quantity > 0}
  Name: {$i.name}
  Quantity: {$i.quantity}
  Price (each): {if $i.netprice != $i.price}{$i.netprice|string_format:"%01.2f"} (Discounted from {$i.price|string_format:"%01.2f"}){else}{$i.netprice|string_format:"%01.2f"}{/if}
  Line Total: {$i.linetotal|string_format:"%01.2f"}

{/if}
{/foreach}
Sub-total: {$order.subtotal|string_format:"%01.2f"}
{if $order.freight}
Freight: {$order.freight|string_format:"%01.2f"}
{/if}
Total: {$order.currency|default:$OPTIONS.cart_default_currency}{$order.currency_symbol|default:' '}{$order.amount|string_format:"%01.2f"}

{if $OPTIONS.cart_show_gst != 'no' && (($order.currency=='NZD') || ($order.currency=='' && $OPTIONS.cart_default_currency=='NZD'))}includes GST of {$order.currency_symbol|default:' '}{$order.amount/9|string_format:"%01.2f"}
{/if}
{if $discount && $discount.code != ''}
This order used discount code: {$discount.code}
{/if}

{jojoHook hook="jojo_cart_customer_email_bottom"}
If you have any queries regarding this order, please contact us on {$OPTIONS.contactaddress|default:$OPTIONS.webmasteraddress}

Regards,
{$OPTIONS.fromname}
{if $sitetitle!=$OPTIONS.fromname}{$sitetitle}{/if}