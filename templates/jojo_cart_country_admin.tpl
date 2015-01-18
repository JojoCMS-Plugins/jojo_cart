{include file="admin/header.tpl"}
<p>Regions are used to define freight costs for your shopping cart. Unless you are selling software products, freight costs will likely be very different depending where in the world your products are being sent. Jojo uses regions to define freight costs - each country is assigned to a region. So when the customer selects France as their country, freight costs are based on the 'Europe' region.</p>
<p>Regions need to be set for all countries, however we have prepopulated them for you to make the job easier. Depending on your freight model, you may want to <a href="#add_region">add more regions</a> to be more specific, or <a href="#delete_region">remove some regions</a> to keep the freight calculations simpler. If your business is based in France, you will probably want to create a new region just for France, as sending freight to France is probably going to be cheaper than sending to other European countries.</p>
<p>When a region is deleted, you will be asked to reassign the countries in that region to another region.</p>


<h2>Set regions for countries</h2>
<p>Each country is set to a region for the purpose of calculating freight prices.</p>
<form method="post" action="">
<table class="table table-striped">
<thead>
    <tr>
        <th>Country Name</th>
        <th>Region</th>
        <th title="Popular countries are listed first in the drop down">Popular Country?</th>
        <th title="Whether tax is added / removed from orders delivered to this country">Apply Tax?</th>
        <td>&nbsp;</td>
    </tr>
</thead>
<tbody>
{foreach from=$countries item=c}
  <tr class="{cycle values="row1,row2"}">
    <td>{$c.name}</td>
    <td>
    <select class="form-control" title="The default region for {$c.name} is {$c.defaultregion}" onchange="$.get('json/jojo_cart_set_region.php', {ldelim} c: '{$c.countrycode|strtolower}', r: $(this).val() {rdelim}, function(data){ldelim}$('#status_{$c.countrycode|strtolower}').html(data).fadeIn('fast').animate({ldelim}opacity: 1.0{rdelim}, 1000).fadeOut(3000);{rdelim});">
{section name=r loop=$regions}
    <option value="{$regions[r].regioncode}"{if $c.region==$regions[r].regioncode} selected="selected"{/if}>{$regions[r].name}</option>
{/section}
    </select>
    </td>
    <td style="text-align: center">
        <input type="checkbox"{if $c.special == 'yes'} checked="checked"{/if} onchange="$.get('json/jojo_cart_country_togglespecial.php', {ldelim} c: '{$c.countrycode|strtolower}',{rdelim}, function(data){ldelim}$('#status_{$c.countrycode|strtolower}').html(data).fadeIn('fast').animate({ldelim}opacity: 1.0{rdelim}, 1000).fadeOut(3000);{rdelim});"/>
    </td>
    <td style="text-align: center">
        <input type="checkbox"{if $c.applytax == 'yes'} checked="checked"{/if} onchange="$.get('json/jojo_cart_country_toggleapplytax.php', {ldelim} c: '{$c.countrycode|strtolower}',{rdelim}, function(data){ldelim}$('#status_{$c.countrycode|strtolower}').html(data).fadeIn('fast').animate({ldelim}opacity: 1.0{rdelim}, 1000).fadeOut(3000);{rdelim});"/>
    </td>
    <td><div id="status_{$c.countrycode|strtolower}"></div></td>
  </tr>
{/foreach}
</tbody>
</table>
</form>
<p>To add or remove countries, please edit the database directly (sorry, we don't have a user interface for this yet). If we have made a mistake in setting the default region for a particular country, please <a href="http://www.jojocms.org/contact/" rel="nofollow">let the Jojo team know</a>.</p>

{* delete regions *}
<a name="delete_region"></a>
<h2>Delete a region</h2>
<p>Feel free to delete a region, if it is not needed by your site. After deleting a region, all countries assigned to that region are reassigned to the region you specify.
<form class="horizontal-form" method="post" action="{$pg_url}/">
  <select class="form-control" name="delete_region" id="delete_region">
    <option value="">Select region to delete</option>
    {foreach item=r from=$regions}
    <option value="{$r.regioncode}">{$r.name}</option>
    {/foreach}
  </select>
  <select class="form-control" name="reassign_region" id="reassign_region">
    <option value="">Reassign all countries in this region to...</option>
    {section name=r loop=$regions}
    <option value="{$regions[r].regioncode}">{$regions[r].name}</option>
    {/section}
  </select>
  <input class="btn btn-default" type="submit" name="delete" value="Delete" onclick="{literal}if (($('#delete_region').val()=='') || ($('#reassign_region').val()=='') || ($('#reassign_region').val()==$('#delete_region').val())) {alert('Please select a region to delete, and a different region to reassign the countries to'); return false;}{/literal}" />
</form>

{* add regions *}
<a name="add_region"></a>
<h2>Add a new region</h2>
<form method="post" action="{$pg_url}/">
  <p>Codes need to be lower case, and contain no special characters other than underscores. Use a logical code for your region, eg "eastern_europe" or "pacific_islands".</p>
  <label>Region code: <input class="form-control" type="text" name="add_region_code" id="add_region_code" value="" /></label>
  <label>Region name: <input class="form-control" type="text" name="add_region_name" id="add_region_name" value="" /></label>
  <input class="btn btn-default" type="submit" name="add" value="Add" onclick="{literal}if (($('#add_region_code').val()=='') || ($('#add_region_name').val()=='') || (!$('#add_region_code').val().match(/^[a-z0-9_]+$/))) {alert('Please ensure both the region name and code fields are completed, and the code is lowercase with no spaces or special characters.'); return false;}{/literal}" />
</form>

{include file="admin/footer.tpl"}