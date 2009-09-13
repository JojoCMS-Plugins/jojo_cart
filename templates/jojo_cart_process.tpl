{if !$success}
<h2>Payment error</h2>
{if $errors}
<p>The following errors were found...<br />
<ul>
{section name=e loop=$errors}
<li>{$errors[e]}</li>
{/section}
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
