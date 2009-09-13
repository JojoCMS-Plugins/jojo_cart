    <p>Freight is different depending which region the product is being sent to. You need to specify the freight price for each region, otherwise leave blank to use the default rate.</p>
    <p>Enter a price of 0 for free shipping, if an option is not available then enter "NA" as the price.</p>

    <p class="advanced" style="display: none;">
        <strong>Total Price</strong> = <strong>Base Price</strong> + <strong>Per Item Price</strong> x roundUp(<strong>Quantity</strong> / <strong># to Combine</strong>)
    </p>

    <div class="advanced" id="tabs" style="display: none;">
        <ul id="regiontabs" class="tabs">
            <li class="tab basic"><a href="#" onclick="$('#fm_{$fd_field}_region table').hide(); $('#regionMin').show(); $('#regiontabs li').removeClass('selected').addClass('basic'); $(this).parent().removeClass('basic').addClass('selected'); return false;">Minimum Price</a></li>
            <li class="tab basic"><a href="#" onclick="$('#fm_{$fd_field}_region table').hide(); $('#regionBase').show(); $('#regiontabs li').removeClass('selected').addClass('basic'); $(this).parent().removeClass('basic').addClass('selected'); return false;">Base Price</a></li>
            <li class="tab selected"><a href="#" onclick="$('#fm_{$fd_field}_region table').hide(); $('#regionPerItem').show(); $('#regiontabs li').removeClass('selected').addClass('basic'); $(this).parent().removeClass('basic').addClass('selected'); return false;">Per Item Price</a></li>
        </ul>
    </div>

    <br style="clear: both"/>

    <table id="regionPerItem" class="adminZebraTable">
        <tr class="advanced" style="display: table-row-group;">
            <td colspan="{assign var=count value=$methods|@count}{$count+1}"><h3>Per Item Shipping Cost</h3></td>
        </tr>

        <tr>
            <th>Region</th>
{foreach from=$methods item=method}
            <th title="{$method.longname}">{$method.shortname}</th>
{/foreach}
        </tr>
        <tr class="{cycle values="row1,row2"}">
            <td>Default per item rate</td>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default[{$methodid}]" value="{$freight_default[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
{/foreach}
        </tr>
{section name=r loop=$freight_regions}
        <tr class="{cycle values="row1,row2"}">
            <td>{$freight_regions[r].name}</td>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions[r][price][$methodid]}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
{/foreach}
        </tr>
{/section}
    </table>

    <table id="regionBase" class="adminZebraTable" style="display:none">
        <tr class="advanced" style="display: table-row-group;">
            <td colspan="{assign var=count value=$methods|@count}{$count+1}"><h3>Base Shipping Cost</h3></td>
        </tr>
        <tr>
            <th>Region</th>
{foreach from=$methods item=method}
            <th title="{$method.longname}">{$method.shortname}</th>
{/foreach}
        </tr>
        <tr class="{cycle values="row1,row2"}">
            <td>Default base rate</td>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default_base[{$methodid}]" value="{$freight_default_base[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
{/foreach}
        </tr>
{section name=r loop=$freight_regions}
        <tr class="{cycle values="row1,row2"}">
            <td>{$freight_regions[r].name}</td>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_base_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions_base[r].price.$methodid}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
{/foreach}
        </tr>
{/section}
    </table>

    <table id="regionMin" class="adminZebraTable" style="display:none">
        <tr class="advanced" style="display: table-row-group;">
            <td colspan="{assign var=count value=$methods|@count}{$count+1}"><h3>Minimum Shipping Cost</h3></td>
        </tr>
        <tr>
            <th>Region</th>
{foreach from=$methods item=method}
            <th title="{$method.longname}">{$method.shortname}</th>
{/foreach}
        </tr>
        <tr class="{cycle values="row1,row2"}">
            <td>Default minimum</td>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default_min[{$methodid}]" value="{$freight_default_min.$methodid}" />  {$OPTIONS.cart_default_currency}</td>
{/foreach}
        </tr>
{section name=r loop=$freight_regions}
        <tr class="{cycle values="row1,row2"}">
            <td>{$freight_regions[r].name}</td>
{foreach from=$methods key=methodid item=method}
            <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_min_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions_min[r].price.$methodid}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
{/foreach}
        </tr>
{/section}
    </table>
