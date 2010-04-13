Hi {if $fields.billing_firstname}{$fields.billing_firstname}{else}{$fields.shipping_firstname}{/if},

Your recent order placed with {$SITEURL} has recently been shipped. If you don't receive the order within a reasonable timeframe, feel free to contact us by emailing {$OPTIONS.contactaddress|default:$OPTIONS.webmasteraddress} and we will follow up for you.
{* include the tracking information if it has been entered *}
{if $tracking_message!=$OPTIONS.cart_shipped_tracking_message}

{$tracking_message}
{/if}

Regards,
{$OPTIONS.fromname}
{if $sitetitle!=$OPTIONS.fromname}{$sitetitle}{/if}