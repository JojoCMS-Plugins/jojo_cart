<table class="adminZebraTable">
    <tr class="{cycle values='row1,row2'}">
        <td></td>
{foreach from=$methods item=method}
        <th title="{$method.longname}">{$method.shortname}</th>
{/foreach}
    </tr>

    <tr class="{cycle values='row1,row2'}">
        <th>Minimum Shipping Cost</th>
{foreach from=$methods key=methodid item=method}
        <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_prices[{$methodid}]" value="{$prices[$methodid]|default:"0"}" /> {$OPTIONS.cart_default_currency}</td>
{/foreach}
    </tr>
</table>
<input type="hidden" name="fm_{$fd_field}" value="" />
