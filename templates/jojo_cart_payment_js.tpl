{if count($paymentoptions) > 1}
$(document).ready(function(){ldelim}
  $('div.payment_option_radios').show();
  $('div.payment_option').hide();
  {foreach from=$paymentoptions key=k item=option}
  $('#payment_option_radio_{$option.id}').click(function(){ldelim}
    $('div.payment_option').hide();
    $('#payment_option_{$option.id}').show('fast');
  {rdelim});
  {/foreach}
{rdelim});
{/if}