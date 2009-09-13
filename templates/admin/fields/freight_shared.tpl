    <label for="fm_{$fd_field}_model">Shared model to use</label>
    <select name="fm_{$fd_field}_model" id="fm_{$fd_field}_model">
        <option value=""></option>
{foreach from=$sharedmodels key=modelid item=model}
        <option value="{$modelid}"{if $sharedmodel==$modelid} selected="selected"{/if}>{$model}</option>
{/foreach}
    </select>
