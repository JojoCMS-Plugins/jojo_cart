<div>
{if $OPTIONS.cart_success_message}<p>##{$OPTIONS.cart_success_message}##</p>{/if}
{jojoHook hook="jojo_cart_complete_top"}

{if $receipt}
<div class="receipt">
  {$receipt}
</div>
{/if}
{if $OPTIONS.cart_tracking_code}{$OPTIONS.cart_tracking_code}{/if}
{if $pointsbalance}
<div class="pointsupdate">
  <p>Your points balance is now <span>{$pointsbalance}</span>.</p>
</div>
{/if}
{jojoHook hook="jojo_cart_complete_bottom"}

</div>