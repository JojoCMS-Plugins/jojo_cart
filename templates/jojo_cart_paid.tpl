<div class="transactionlist_ajax">
{if $changestatus}{$changestatus}{else}
  <p>The order has been marked as "paid". You can optionally use the form below to email the client and notify them that their payment has been received.</p>

  <form method="post" action="{$SITEURL}/{$languageurlprefix}cart/paid/{$token}/{$actioncode}/">
    <h2>Send message to client:</h2>
    <label>To:<br />
    <input type="text" size="30" name="email" value="{if $fields.billing_email}{$fields.billing_email}{else}{$fields.shipping_email}{/if}" /></label><br />
    <label>Subject:<br />
    <input type="text" size="30" name="subject" value="Your payment has been received" /></label><br />
    <label>Message:<br />
    <textarea name="message" rows="20" cols="50">{include file="jojo_cart_paid_email.tpl"}</textarea></label><br />
    <input type="submit" name="send" value="Send" />
  </form>
{/if}
</div>