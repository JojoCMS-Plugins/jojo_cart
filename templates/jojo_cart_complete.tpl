<div>
{if $OPTIONS.cart_success_message}<p>{$OPTIONS.cart_success_message}</p>{/if}
{jojoHook hook="jojo_cart_complete_top"}

{if $receipt}
<div class="receipt">
  {$receipt}
</div>
{/if}
{if $OPTIONS.cart_tracking_code}{$OPTIONS.cart_tracking_code}{/if}
{jojoHook hook="jojo_cart_complete_bottom"}

</div>