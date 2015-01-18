<div class="transactionlist_ajax">
{if $changestatus}{$changestatus}{else}
  <p>The order has been marked as "paid". You can optionally use the form below to email the client and notify them that their payment has been received.</p>

  <form method="post" action="{$SITEURL}/{$languageurlprefix}cart/paid/{$token}/{$actioncode}/" class="contact-form no-ajax">
    <h2>Send message to client:</h2>
    <div class="form-fieldset form-group">
        <label for="email">To:</label>
        <input class="form-control" type="email" size="30" name="email" value="{if $fields.billing_email}{$fields.billing_email}{else}{$fields.shipping_email}{/if}" />
    </div>
    <div class="form-fieldset form-group">
        <label for="subject">Subject:</label>
        <input class="form-control" type="text" size="30" name="subject" value="Your payment has been received" />
    </div>
    <div class="form-fieldset form-group">
        <label for="message">Message:</label>
        <textarea class="form-control" name="message" rows="20" cols="50">{include file="jojo_cart_paid_email.tpl"}</textarea>
    </div>
    <input class="btn btn-primary" type="submit" name="send" value="Send" />
  </form>
{/if}
</div>