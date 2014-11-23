{*

Please make a copy of this template in your theme and customize the message. It should generally include a postal address for cheques and a bank account for direct deposits.
Alternatively, use another plugin to use the hook to customize this message.

*}
<div>
{jojoHook hook="jojo_cart_payment_info"}
{if $OPTIONS.cart_manual_payment_instructions}<p>{$OPTIONS.cart_manual_payment_instructions|nl2br}</p>{/if}
<p>Please quote the order number <strong>"{$id}"</strong> when making any payments, so we can track your payment.</p>
</div>