<div class="transactionlist_ajax">
{if $changestatus}{$changestatus}{else}
  <p>The order has been marked as "shipped". You can optionally use the form below to email the client and notify them of this.</p>
  
  <h2>Enter tracking information</h2>
  <p></p>
  <form method="post" action="{$SITEURL}/cart/shipped/{$token}/{$actioncode}/">
  <label>Tracking information (will be saved in system and included in message to client):<br />
  <textarea name="tracking_message" rows="4" cols="50">{$tracking_message}</textarea></label><br />
  <input type="submit" name="save_tracking" value="Save" />
  <a href="{$SITEURL}/{$ADMIN}/options/">Edit this default message</a>
  </form>
  <form method="post" action="{$SITEURL}/cart/shipped/{$token}/{$actioncode}/">
    <h2>Send message to client:</h2>
    <label>To:<br />
    <input type="text" size="30" name="email" value="{$fields.billing_email|default:$fields.shipping_email}" /></label><br />
    <label>Subject:<br />
    <input type="test" size="30" name="subject" value="Your order has shipped" /></label><br />
    <label>Message:<br />
    <textarea name="message" rows="20" cols="50">{include file="jojo_cart_shipped_email.tpl"}</textarea></label><br />
    <input type="submit" name="send" value="Send" />
  </form>
{/if}
</div>