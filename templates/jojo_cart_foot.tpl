<script type="text/javascript">
/* <![CDATA[ */
pageTracker._addTrans(
    "{$token}",                                  {* Order ID *}
    "",                                          {* Affiliation *}
    "{$order.amount|string_format:"%01.2f"}",    {* Total *}
    "0",                                         {* Tax *}
    "{$order.freight|string_format:"%01.2f"}",   {* Shipping *}
    "{$fields.City}",                            {* City *}
    "{$fields.State}",                           {* State *}
    "{$fields.Country}"                          {* Country *}
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