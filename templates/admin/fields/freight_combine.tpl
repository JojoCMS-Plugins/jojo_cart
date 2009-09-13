    <p>Combine up to <em>x</em> items together at the same cost.</p>

    <table class="adminZebraTable">
        <tr class="{cycle values="row1,row2"}">
            <td></td>
{foreach from=$methods item=method}
            <th title="{$method.longname}">{$method.shortname}</th>
{/foreach}
        </tr>

        <tr class="{cycle values="row1,row2"}">
            <th>Number of items to combine</th>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_combine[{$methodid}]" value="{$freight_combine[$methodid]|default:"1"}" /></td>
{/foreach}
        </tr>
    </table>

