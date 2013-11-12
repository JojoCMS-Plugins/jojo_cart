    <p>Freight is different depending which region the product is being sent to. You need to specify the freight price for each region, otherwise leave blank to use the default rate.</p>
    <p>Enter a price of 0 for free shipping, if an option is not available then enter "NA" as the price.</p>

    <p class="advanced" style="display: none;">
        <strong>Total Price</strong> = <strong>Base Price</strong> + <strong>Per Item Price</strong> x roundUp(<strong>Quantity</strong> / <strong># to Combine</strong>)
    </p>

    <div class="advanced" id="tabs">
        <ul class="nav nav-tabs">
            <li><a href="#regionMin" data-toggle="tab">Minimum Price</a></li>
            <li><a href="#regionBase" data-toggle="tab">Base Price</a></li>
            <li class="active"><a href="#regionPerItem" data-toggle="tab">Per Item Price</a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane" id="regionPerItem">
            <table class="table">
                <tr class="advanced" style="display: table-row-group;">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Per Item Shipping Cost</h3></td>
                </tr>

                <tr>
                    <th>Region</th>
        {foreach from=$methods item=method}
                    <th title="{$method.longname}">{$method.shortname}</th>
        {/foreach}
                </tr>
                <tr>
                    <td>Default per item rate</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default[{$methodid}]" value="{$freight_default[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {foreach from=$freight_regions item=region}
                <tr>
                    <td>{$region.name}</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_{$region.code}[{$methodid}]" value="{$region[price][$methodid]}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {/foreach}
            </table>
        </div>
        
        <div class="tab-pane" id="regionBase">
            <table class="table">
                <tr class="advanced" style="display: table-row-group;">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Base Shipping Cost</h3></td>
                </tr>
                <tr>
                    <th>Region</th>
        {foreach from=$methods item=method}
                    <th title="{$method.longname}">{$method.shortname}</th>
        {/foreach}
                </tr>
                <tr>
                    <td>Default base rate</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default_base[{$methodid}]" value="{$freight_default_base[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {section name=r loop=$freight_regions}
                <tr>
                    <td>{$freight_regions[r].name}</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_base_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions_base[r].price.$methodid}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {/section}
            </table>
        </div>
        
        <div class="tab-pane active" id="regionMin">
            <table class="table">
                <tr class="advanced" style="display: table-row-group;">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Minimum Shipping Cost</h3></td>
                </tr>
                <tr>
                    <th>Region</th>
        {foreach from=$methods item=method}
                    <th title="{$method.longname}">{$method.shortname}</th>
        {/foreach}
                </tr>
                <tr>
                    <td>Default minimum</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default_min[{$methodid}]" value="{$freight_default_min.$methodid}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {section name=r loop=$freight_regions}
                <tr>
                    <td>{$freight_regions[r].name}</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_min_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions_min[r].price.$methodid}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {/section}
            </table>
