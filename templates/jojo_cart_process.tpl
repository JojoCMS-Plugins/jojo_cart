{if !$success}
<h2>Payment error</h2>
{if $errors}
<p>The following errors were found...<br />
<ul>
{foreach name=e from=$errors item=e}
<li>{$e}</li>
{/foreach}
</ul>
</p>
{/if}

<p>This transaction has not been processed and no charges have been made. Please return to the <a href="cart/checkout/" rel="nofollow">checkout page</a> and correct the errors, or contact the <a href="mailto:{$OPTIONS.webmasteraddress}">webmaster</a> if you feel this is a technical error.</p>

{if $receipt}
<div class="receipt">
  {$receipt}
</div>
{/if}
{/if}
