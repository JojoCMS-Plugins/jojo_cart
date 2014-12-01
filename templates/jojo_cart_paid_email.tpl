Hi {if $fields.billing_firstname}{$fields.billing_firstname}{else}{$fields.shipping_firstname}{/if},

Your payment for the order placed with {$SITEURL} has recently been received. If you don't receive the order within a reasonable timeframe, feel free to contact us by emailing {$OPTIONS.contactaddress|default:$OPTIONS.webmasteraddress} and we will follow up for you.

Regards,  
{$OPTIONS.fromname}  
{$sitetitle}