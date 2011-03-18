{title}Import Highrise Contacts{/title}
{add_bread_crumb}Highrise Import{/add_bread_crumb}

<div id="highrise_contacts">
{if is_foreachable($contacts)}


  {form action=$action method=post autofocus=no uni=no id='highrise_contacts_form'}
	<table class="common_table" id="highrise_contacts">
	  <thead>
	    <tr>
	      <th class="name">{lang}Highrise contacts for{/lang} {$company}</th>
	      <th class="checkbox"><input type="checkbox" class="auto master_checkbox input_checkbox" /></th>
	    </tr>
	  </thead>
	  <tbody>
	{foreach from=$contacts item=contact}
	    <tr class="{cycle values='odd,even'}">
	      <td class="name">
	        {$contact.name}
	      </td>
	      <td class="checkbox"><input type="checkbox" name="contacts[]" value="{$contact.id}" class="auto slave_checkbox input_checkbox" /></td>
	    </tr>
	{/foreach}
	  </tbody>
	</table>
	<script type="text/javascript">
	  $(document).ready(function() {ldelim}
	    $('#highrise_contacts').checkboxes();
	  {rdelim});
	</script>

  <div id="import_options">
    <div id="mass_edit">
      <button class="simple" id="highrise_import_submit" type="submit" class="auto">{lang}Import{/lang}</button>
    </div>
  </div>
  {/form}
{else}
  <p class="empty_page">{lang}No (new) contacts found{/lang}</p>
{/if}
</div>