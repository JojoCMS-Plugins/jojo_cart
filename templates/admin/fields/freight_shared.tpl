
        <label for="fm_{$fd_field}_model">Shared model to use</label>
        <select class="form-control" name="fm_{$fd_field}_model" id="fm_{$fd_field}_model">
            <option value=""></option>
            {foreach from=$sharedmodels key=modelid item=model}<option value="{$modelid}"{if $sharedmodel==$modelid} selected="selected"{/if}>{$model}</option>
            {/foreach}
        </select>
        <br>
        <label for="fm_{$fd_field}_freightunits">Number of freight units this product requires</label>
        <input  class="form-control" id="fm_{$fd_field}_freightunits" name="fm_{$fd_field}_freightunits" value="{if $freightunits}{$freightunits}{/if}"/>
