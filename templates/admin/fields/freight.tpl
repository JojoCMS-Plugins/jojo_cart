{if !$onlyadvanced}
<p style="float:right"><a href="#" onclick="$('.advanced').toggle(); return false;">Show/Hide Advanced Options</a></p>

<label for="fm_{$fd_field}_type">Pricing Model</label>
<select name="fm_{$fd_field}_type" id="fm_{$fd_field}_type" onchange="$('.fm_{$fd_field}_freight').hide(); $('#fm_{$fd_field}_'+$(this).val()).show();">
    <option value="fixed"{if $freight_type=='fixed' || $freight_type==''} selected="selected"{/if}>Fixed price</option>
    <option value="region"{if $freight_type=='region'} selected="selected"{/if}>Region-based</option>
    <option value="shared"{if $freight_type=='shared'} selected="selected"{/if}>Shared Freight Model</option>
</select><br/><br/>

<div class="advanced" style="display: none;">
{include file="admin/fields/freight_combine.tpl"}
</div>

<div id="fm_{$fd_field}_shared" style="display:none" class="fm_{$fd_field}_freight">
{include file="admin/fields/freight_shared.tpl"}
</div>

<div id="fm_{$fd_field}_fixed" style="display:none" class="fm_{$fd_field}_freight">
{include file="admin/fields/freight_fixed.tpl"}
</div>

<div id="fm_{$fd_field}_region" style="display:none" class="fm_{$fd_field}_freight">
{include file="admin/fields/freight_region.tpl"}
</div>
{else}

<label for="fm_{$fd_field}_type">Pricing Model</label>
<select name="fm_{$fd_field}_type" id="fm_{$fd_field}_type" onchange="$('.fm_{$fd_field}_freight').hide(); $('#fm_{$fd_field}_'+$(this).val()).show();">
    <option value="region"{if $freight_type=='region'} selected="selected"{/if}>Normal</option>
    <option value="packed"{if $freight_type=='packed'} selected="packed"{/if}>Pack Sizes</option>
</select><br/><br/>

<div id="fm_{$fd_field}_packed"{if $freight_type!='packed'} style="display:none"{/if} class="fm_{$fd_field}_freight">
    <p>Enter a pack size and the shared freight model to use for that pack size.</p>

    <table class="table">
        <tr>
            <th>Items in pack</th>
            <th>Pack freight model</th>
        </tr>
{assign var=packid value=0}
{foreach from=$packs item=packmodel key=size}
        <tr>
            <td><input name="fm_{$fd_field}_pack[{$packid}][size]" id="fm_{$fd_field}_pack_{$packid}_size" value="{$size}"/></td>
            <td>
                <select name="fm_{$fd_field}_pack[{$packid}][model]" id="fm_{$fd_field}_pack_{$packid}_model">
                    <option value=""></option>
{foreach from=$sharedmodels_nonpack key=modelid item=model}
                    <option value="{$modelid}"{if $packmodel==$modelid} selected="selected"{/if}>{$model}</option>
{/foreach}
                </select>
            </td>
        </tr>
{assign var=packid value=$packid+1}
{/foreach}
        <tr>
            <td><input name="fm_{$fd_field}_pack[{$packid}][size]" id="fm_{$fd_field}_pack_{$packid}_size" /></td>
            <td>
                <select name="fm_{$fd_field}_pack[{$packid}][model]" id="fm_{$fd_field}_pack_{$packid}_model">
                    <option value=""></option>
{foreach from=$sharedmodels_nonpack key=modelid item=model}
                    <option value="{$modelid}">{$model}</option>
{/foreach}
                </select>
            </td>
        </tr>
{assign var=packid value=$packid+1}
        <tr>
            <td><input name="fm_{$fd_field}_pack[{$packid}][size]" id="fm_{$fd_field}_pack_{$packid}_size"/></td>
            <td>
                <select name="fm_{$fd_field}_pack[{$packid}][model]" id="fm_{$fd_field}_pack_{$packid}_model">
                    <option value=""></option>
{foreach from=$sharedmodels_nonpack key=modelid item=model}
                    <option value="{$modelid}">{$model}</option>
{/foreach}
                </select>
            </td>
        </tr>
{assign var=packid value=$packid+1}
    </table>
    <p>If you have more than {$packid} pack sizes just save and another two empty rows will appear.</p>
</div>

<div id="fm_{$fd_field}_region"{if $freight_type!='region'} style="display:none"{/if}  class="fm_{$fd_field}_freight">
    <div>
    {include file="admin/fields/freight_combine.tpl"}
    </div>

    <p>Freight is different depending which region the product is being sent to. You need to specify the freight price for each region, otherwise leave blank to use the default rate.</p>
    <p>Enter a price of 0 for free shipping, if an option is not available then enter "NA" as the price.</p>

    <p>
        <strong>Total Price</strong> = <strong>Base Price</strong> + <strong>Per Item Price</strong> x roundUp(<strong>Quantity</strong> / <strong># to Combine</strong>)<br/>
        &nbsp;&nbsp;&nbsp;<em>or</em><br/>
        <strong>Total Price</strong> = <strong>Minimum Shipping Price</strong><br/>
        &nbsp;&nbsp;&nbsp;<em>or</em><br/>
        <strong>Total Price</strong> = <strong>Minimum Region Shipping Price</strong><br/>
        &nbsp;&nbsp;&nbsp;<em>which ever is greater</em>
    </p>

    <div class="advanced" id="tabs">
        <ul class="nav nav-tabs">
            <li><a href="#" data-target="#regionMin" data-toggle="tab">Minimum Price</a></li>
            <li><a href="#" data-target="#regionBase" data-toggle="tab">Base Price</a></li>
            <li class="active"><a href="#" data-target="#regionPerItem" data-toggle="tab">Per Item Price</a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane" id="regionMin">
            <table class="table">
                <tr class="advanced">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Minimum Shipping Cost</h3></th>
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
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_default_min[{$methodid}]" value="{$freight_default_min[$methodid]}" />  {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {section name=r loop=$freight_regions}
                <tr class="{cycle values="row1,row2"}">
                    <td>{$freight_regions[r].name}</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_min_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions_min[r][price][$methodid]}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {/section}
            </table>
        </div>
        <div class="tab-pane" id="regionBase">
            <table class="table">
                <tr class="advanced">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Base Shipping Cost</h3></th>
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
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_base_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions_base[r][price][$methodid]}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {/section}
            </table>
        </div>
        <div class="tab-pane active" id="regionPerItem">
            <table class="table">
                <tr class="advanced">
                    <td colspan="{assign var=count value=count($methods)}{$count+1}"><h3>Per Item Shipping Cost</h3></th>
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
        {section name=r loop=$freight_regions}
                <tr>
                    <td>{$freight_regions[r].name}</td>
        {foreach from=$methods key=methodid item=method}
                    <td><input style="text-align: right" type="text" size="6" name="fm_{$fd_field}_region_{$freight_regions[r].code}[{$methodid}]" value="{$freight_regions[r][price][$methodid]}" autocomplete="off" /> {$OPTIONS.cart_default_currency}</td>
        {/foreach}
                </tr>
        {/section}
            </table>
        </div>
    </div>

</div>
{/if}

<input type="hidden" name="fm_{$fd_field}" value="" />