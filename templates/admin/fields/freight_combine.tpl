    <p>Combine up to <em>x</em> items together at the same cost.</p>

    <table class="table table-bordered">
        <tr>
            <td></td>
{foreach from=$methods item=method}
            <th title="{$method.longname}">{$method.shortname}</th>
{/foreach}
        </tr>
        <tr>
            <th>Number of items to combine</th>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_combine[{$methodid}]" value="{$freight_combine[$methodid]|default:"1"}" /></td>
{/foreach}
        </tr>
    </table>

