{title}Import Highrise Clients{/title}
{add_bread_crumb}Highrise Import{/add_bread_crumb}

<div id="highrise_clients">
{if is_foreachable($clients)}


  {form action='?route=people_import_highrise' method=post autofocus=no uni=no id='highrise_clients_form'}
	<table class="common_table" id="highrise_clients">
	  <thead>
	    <tr>
	      <th class="name">{lang}Highrise Clients{/lang}</th>
	      <th class="checkbox"><input type="checkbox" class="auto master_checkbox input_checkbox" /></th>
	    </tr>
	  </thead>
	  <tbody>
	{foreach from=$clients item=client}
	    <tr class="{cycle values='odd,even'}">
	      <td class="name">
	        {$client.name}
	      </td>
	      <td class="checkbox"><input type="checkbox" name="clients[]" value="{$client.id}" class="auto slave_checkbox input_checkbox" /></td>
	    </tr>
	{/foreach}
	  </tbody>
	</table>
	<script type="text/javascript">
	  $(document).ready(function() {ldelim}
	    $('#highrise_clients').checkboxes();
	  {rdelim});
	</script>

  <div id="import_options">
    <div id="mass_edit">
      <button class="simple" id="highrise_import_submit" type="submit" class="auto">{lang}Import{/lang}</button>
    </div>
  </div>
  {/form}
{else}
  <p class="empty_page">{lang}No (new) clients found{/lang}</p>
{/if}
</div>