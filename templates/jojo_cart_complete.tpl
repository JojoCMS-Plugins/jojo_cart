<div>
{if $OPTIONS.cart_success_message}<p>{$OPTIONS.cart_success_message}</p>{/if}
{jojoHook hook="jojo_cart_complete_top"}

{if $receipt}
<div class="receipt">
  {$receipt}
</div>
{/if}

{jojoHook hook="jojo_cart_complete_bottom"}

</div>