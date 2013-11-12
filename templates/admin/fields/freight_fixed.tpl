    <p>The freight is a fixed price, regardless of where the item is being sent.</p>
    <p>Enter a price of 0 for free shipping, if an option is not available then enter "NA" as the price.</p>

    <p class="advanced" style="display: none;">
        <strong>Total Price</strong> = <strong>Base Price</strong> + <strong>Per Item Price</strong> x roundUp(<strong>Quantity</strong> / <strong># to Combine</strong>)
    </p>

    <div class="advanced" id="tabs">
        <ul class="nav nav-tabs">
            <li><a href="#fixedPerItem" data-toggle="tab">Minimum Price</a></li>
            <li><a href="#fixedBase" data-toggle="tab">Base Price</a></li>
            <li class="active"><a href="#fixedMin" data-toggle="tab">Per Item Price</a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane" id="fixedPerItem">
            <table class="table">
                <tr class="advanced" style="display: table-row-group;">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}" ><h3>Per Item Shipping Cost</h3></td>
                </tr>

                <tr class="{cycle values="row1,row2"}">
                    <td></td>
        {foreach from=$methods item=method}
                    <th title="{$method.longname}">{$method.shortname}</th>
        {/foreach}
                </tr>

                <tr class="{cycle values="row1,row2"}">
                    <th style="width:25%">Per item price</th>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_fixed_default[{$methodid}]" value="{$freight_default[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
            </table>
        </div>
        <div class="tab-pane" id="fixedBase">
            <table class="table">
                <tr class="advanced" style="display: table-row-group;">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Base Shipping Cost</h3></td>
                </tr>
                <tr class="{cycle values="row1,row2"}">
                    <td></td>
        {foreach from=$methods item=method}
                    <th title="{$method.longname}">{$method.shortname}</th>
        {/foreach}
                </tr>

                <tr class="{cycle values="row1,row2"}">
                    <th style="width:25%">Base price</th>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_fixed_base[{$methodid}]" value="{$freight_default_base[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
            </table>
        </div>
        <div class="tab-pane active" id="fixedMin">
            <table class="table">
                <tr class="advanced" style="display: table-row-group;">
                    <th colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Minimum Shipping Cost</h3></th>
                </tr>
                <tr class="{cycle values="row1,row2"}">
                    <td></td>
        {foreach from=$methods item=method}
                    <th title="{$method.longname}">{$method.shortname}</th>
        {/foreach}
                </tr>

                <tr class="{cycle values="row1,row2"}">
                    <th style="width:25%">Minimum freight price</th>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_fixed_min[{$methodid}]" value="{$freight_default_min[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
            </table>
        </div>
    </div>
