{include file="admin/header.tpl"}
<div id="transaction-report">
<form action='' method="post" class="form-inline pull-right">
    <div class="form-group" style="margin-bottom: 10px;">
{if $transactiontotals}
        <input type="hidden" value="showlist" name="showlist" id="showlist" /> <input type="submit" name="submit" value="Transaction List" class="btn btn-default" />
{elseif $showsummary}
        <input type="hidden" value="summaries" name="summaries" id="summaries" /> <input type="submit" name="submit" value="Transaction Summaries" class="btn btn-default" />
{/if}
    </div>
</form>
<form action='' method="post" class="form-inline pull-right">
    <div class="form-group">
        <input type="hidden" value="csv" name="filedownload" id="filedownload" /><input type="submit" name="submit" value="Download as CSV" class="btn btn-default" />&nbsp;
    </div>
</form>

{if !$transactiontotals}
<div id="transaction_list_search">
    <h3>Search by date</h3>
    <form method="post" action="" class="form-inline">
        <div class="form-group">
            <label for="report_start">Start date:&nbsp;</label><input id="report_start" class="form-control date anytime" type="text" name="report_start" value="{$report_start|date_format:'%d %b %Y'}" />
         </div>   
        <div class="form-group">
            <label for="report_end">End date:&nbsp;</label><input id="report_end" class="form-control  date anytime" type="text" name="report_end" value="{$report_end|date_format:'%d %b %Y'}" />
         </div>   
        <label><input type="checkbox" name="ignore_id_date"{if $ignore_id_date} checked="checked"{/if} /> All</label>
        <input type="submit" name="search" value="Search"  class="btn btn-default"/>  <button class="btn btn-default button-xs" onclick="$('#clearsearch').submit();">clear</button>
    </form>
    <h3>Search by details</h3>
    <form method="post" action="" class="form-inline">
        <div class="form-group">
            <label for="search_text">Detail:&nbsp;</label><input class="form-control text" type="text" name="search_text" value="{$search_text}" />
        </div>
        <label><input type="checkbox" name="ignore_id_text"{if $ignore_id_text} checked="checked"{/if} /> All</label>
        <input type="submit" name="search" value="Search"  class="btn btn-default"/> <button class="btn btn-default button-xs" onclick="$('#clearsearch').submit();">clear</button>
    </form>
    <form method="post" action="" class="form-inline hidden" id="clearsearch">
        <input type="submit" name="search" value="Clear Search" class="btn btn-default"/>
    </form>
</div>
<br>
<table class="sortabletable table table-striped table-bordered">
  <thead>
    <tr>
      <th>ID</th>
      <th>Date</th>
      <th>Details</th>
      <th>Amount</th>
      <th>Currency</th>
      <th>Status</th>
      <th>Handler</th>
      <th>Shipped</th>
      <th>Send Emails</th>
      {jojoHook hook="jojo_cart_transaction_report_th"}
    </tr>
  </thead>
  <tbody>
    {foreach from=$transactions item=transaction key=k}
    <tr class="{$transaction.status}">
      <td>{if $transaction.id}{$transaction.id}{/if}</td>
      <td>{$transaction.completed}</td>
      <td>{if $transaction.FirstName}{$transaction.FirstName} {/if}{if $transaction.LastName}{$transaction.LastName}{/if} <button class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#showtransaction" data-link="/{$ADMIN}/cart/transaction_list/{$transaction.token}/" >Order Details</button></td>
      <td>{$transaction.currencysymbol}{$transaction.amount|string_format:"%0.2f"}{if $OPTIONS.cart_show_gst|default:'yes'=='yes'} <span class="note">{if $transaction.apply_tax === "unknown"}({$OPTIONS.cart_tax_name|default:'Tax'} unknown){elseif $transaction.apply_tax}(inc {$OPTIONS.cart_tax_name|default:'Tax'}){/if}</span>{/if}</td>
      <td>{if $transaction.currency}{$transaction.currency}{/if}</td>
      <td><span class="status">{if $transaction.status=='failed'}Payment verification {/if}{str_replace('_',' ',$transaction.status)}</span>
      <div class="actions pull-right">
          <button {if $transaction.status=='complete'}style="display:none;" {/if}class="btn btn-default btn-xs complete" data-toggle="modal" data-target="#transactionupdate" data-action="complete" target="_blank" data-link="/cart/paidadmin_complete/{$transaction.token}/{$transaction.actioncode}/" title="Change status to complete"><span class="glyphicon glyphicon-ok"></span></button> 
          <button {if $transaction.status=='abandoned'}style="display:none;" {/if}class="btn btn-default btn-xs abandoned"  data-toggle="modal" data-target="#transactionupdate" data-action="abandoned" target="_blank" data-link="/cart/paidadmin_abandoned/{$transaction.token}/{$transaction.actioncode}/" title="Change Status to Abandoned"><span class="glyphicon glyphicon-remove"></span></button> 
          <button {if $transaction.status=='payment_pending'}style="display:none;" {/if}class="btn btn-default btn-xs payment_pending"  data-toggle="modal" data-target="#transactionupdate" data-action="payment_pending" target="_blank" data-link="/cart/paidadmin_paymentpending/{$transaction.token}/{$transaction.actioncode}/" title="Change Status to Payment Pending"><span class="glyphicon glyphicon-time"></span></button>
      </div>
      </td>
      <td>{str_replace('_',' ',$transaction.handler)}</td>
      <td>{if $transaction.shipped<1}<a class="shipped" target="_blank" href="cart/shippedadmin/{$transaction.token}/{$transaction.actioncode}/" rel="cart/shippedadmin/{$transaction.token}/{$transaction.actioncode}/">click to ship</a>
      {elseif $transaction.shipped}{$transaction.shipped} <a class="shipped" href="cart/shippedadmin_unshipped/{$transaction.token}/{$transaction.actioncode}/" rel="cart/shippedadmin_unshipped/{$transaction.token}/{$transaction.actioncode}/">U</a>{/if}</td>
      <td><form action='' method="post" class="form-inline">
            <input type="hidden" name="token" id="token{$k}" value="{$transaction.token}" />
            <select class="form-control input-sm" name="emailtarget" id="email{$k}">
                    <option value="all">All</option>
                    <option value="admin" selected="selected">Admin</option>
                    <option value="customer">Customer</option>
                    <option value="webmaster">WebM</option>
           </select>
           <button type="submit" name="submit" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-envelope"></span></button></form></td>
    {jojoHook hook="jojo_cart_transaction_report_td"}
    </tr>
    {/foreach}
  </tbody>
</table>

<div id="showtransaction" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Transaction Details</h4>
      </div>
      <div class="modal-body" id="transactiondata">
        <p>One fine body&hellip;</p>
      </div>
      <div class="modal-footer">
        <a type="button" class="btn btn-default" href="" id="printlink">View Printable</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="transactionupdate" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Transaction Updated</h4>
      </div>
      <div class="modal-body" id="transactionupdatedata">
        <p>One fine body&hellip;</p>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
{literal}
$(document).ready(function() {
    $('#report_start').AnyTime_noPicker();
    $('#report_start').AnyTime_picker({format: "%Y-%m-%d"});
    $('#report_end').AnyTime_noPicker();
    $('#report_end').AnyTime_picker({format: "%Y-%m-%d"});
});
{/literal}
</script>

{else}
<h3>Monthly / Annual Summaries</h3>
<table class="sortabletable table table-striped table-bordered">
<tr>
    <th rowspan="2">Month</th>
    <th rowspan="2">$ <span class="note">(y-o-y change)</span></th>
    <th rowspan="2">Sales</th>
    <th rowspan="2">Items</th>
    <th rowspan="2">Top Sellers</th>
    <th colspan="3" style="text-align: center;">Sale Averages</th>
</tr>
<tr>
    <th>Spend</th>
    <th>Items</th>
    <th>Item value</th>
</tr>
<tr>
    <th>All Time</th>
    <th>{$currencysymbol}{$grandtotals.total}</th>
    <th>{$grandtotals.number}</th>
    <th>{$grandtotals.items}</th>
    <th></th>
    <th>{$currencysymbol}{$grandtotals.average}</th>
    <th>{$grandtotals.avitems}</th>
    <th>{$currencysymbol}{$grandtotals.avitemvalue}</th>
</tr>
<tr><td colspan="8"></td></tr>
{foreach from=$transactiontotals key=k item=t}
<tr>
    <td>{$k}</td>
    <td>{$currencysymbol}{$t.total} {if $t.change}<span class="note {if $t.change<0}text-danger{else}text-success{/if}">({if $t.change>0}+{/if}{$t.change}%)</span>{/if}</td>
    <td>{$t.number}</td>
    <td>{$t.items}</td>
    <td>{if $t.bestsellers}#: {foreach $t.bestsellers b}{$b.name} x<b>{$b.number}</b>{if !$.foreach.default.last}, &nbsp;{/if}{/foreach}<br>
    $: {foreach $t.valuesellers b}{$b.name} <b>{$currencysymbol}{$b.amount}</b>{if !$.foreach.default.last}, &nbsp;{/if}{/foreach}<br>
    {/if}</td>
    <td>{$currencysymbol}{$t.average}</td>
    <td>{$t.avitems}</td>
    <td>{$currencysymbol}{$t.avitemvalue}</td>
</tr>
{/foreach}
<tr><td colspan="8"></td></tr>
{foreach from=$yeartotals key=k item=y}
<tr>
    <th>{$k}</th>
    <th>{$currencysymbol}{number_format($y['rawtotal'], 0)}{if $y.change} <span class="note {if $y.change<0}text-danger{else}text-success{/if}">({if $y.change>0}+{/if}{$y.change}%)</span>{/if}</th>
    <th>{$y.number}</th>
    <th>{$y.items}</th>
    <th></th>
    <th>{$currencysymbol}{$y.average}</th>
    <th>{$y.avitems}</th>
    <th>{$currencysymbol}{$y.avitemvalue}</th>
</tr>
{/foreach}
<tr><td colspan="8"></td></tr>
<tr>
    <th>All</th>
    <th>{$currencysymbol}{$grandtotals.total}</th>
    <th>{$grandtotals.number}</th>
    <th>{$grandtotals.items}</th>
    <th></th>
    <th>{$currencysymbol}{$grandtotals.average}</th>
    <th>{$grandtotals.avitems}</th>
    <th>{$currencysymbol}{$grandtotals.avitemvalue}</th>
</tr>
</table>
{/if}
</div>
{include file="admin/footer.tpl"}
