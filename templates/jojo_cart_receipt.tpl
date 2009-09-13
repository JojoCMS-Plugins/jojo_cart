{if $receipt}
<table style="border:0">
  <tbody>
  {foreach from=$receipt key=label item=value}
    <tr>
      <td>{$label}:</td>
      <td>{$value}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}