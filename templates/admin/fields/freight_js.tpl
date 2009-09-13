$(function() {ldelim}
  $('.fm_{$fd_field}_freight').hide();
  {if $freight_type}$('#fm_{$fd_field}_{$freight_type}').show();{/if}
{rdelim});