{if $OPTIONS.analyticscodetype=='async'}

<script type="text/javascript">
/* <![CDATA[ */
_gaq.push(['_addTrans',
    "{$token}",                                  {* Order ID *}
    "",                                          {* Affiliation *}
    "{$order.amount|string_format:"%01.2f"}",    {* Total *}
    "0",                                         {* Tax *}
    "{$order.freight|string_format:"%01.2f"}",   {* Shipping *}
    "{if $fields.City}{$fields.City}{/if}",                            {* City *}
    "{if $fields.State}{$fields.State}{/if}",                           {* State *}
    "{if $fields.Country}{$fields.Country}{/if}"                          {* Country *}
  ]);

{foreach from=$items key=k item=i}
_gaq.push(['_addItem',
    "{$token}",                                 {* Order ID *}
    "{$i.id}",                                  {* SKU *}
    "{$i.name}",                                {* Product Name  *}
    "",                                         {* Category *}
    "{$i.netprice|string_format:"%01.2f"}",     {* Price *}
    "{$i.quantity}"                             {* Quantity *}
  ]);

{/foreach}
{if $testmode}//{/if}_gaq.push(['_trackTrans']); {if $testmode}//this line commented out because test mode is enabled{/if}
/* ]]> */
</script>



{else}

<script type="text/javascript">
/* <![CDATA[ */
pageTracker._addTrans(
    "{$token}",                                  {* Order ID *}
    "",                                          {* Affiliation *}
    "{$order.amount|string_format:"%01.2f"}",    {* Total *}
    "0",                                         {* Tax *}
    "{$order.freight|string_format:"%01.2f"}",   {* Shipping *}
    "{if $fields.City}{$fields.City}{/if}",                            {* City *}
    "{if $fields.State}{$fields.State}{/if}",                           {* State *}
    "{if $fields.Country}{$fields.Country}{/if}"                          {* Country *}
  );

{foreach from=$items key=k item=i}
pageTracker._addItem(
    "{$token}",                                 {* Order ID *}
    "{$i.id}",                                  {* SKU *}
    "{$i.name}",                                {* Product Name  *}
    "",                                         {* Category *}
    "{$i.netprice|string_format:"%01.2f"}",     {* Price *}
    "{$i.quantity}"                             {* Quantity *}
  );

{/foreach}
{if $testmode}//{/if}pageTracker._trackTrans(); {if $testmode}//this line commented out because test mode is enabled{/if}
/* ]]> */
</script>

{/if}