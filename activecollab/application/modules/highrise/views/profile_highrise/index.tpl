{title}Highrise Integration{/title}

  <div class="section_container">
    {form action=$highrise_admin_url method=POST}
      <div class="col_wide">
        {wrap field=domain}
          {label for=domain required=yes}Highrise Domain{/label}
          {text_field id='domain' name=highrise[domain] value=$highrise_data.domain}
          <p class="details">The Domain you use for login, eg. "http://example.highrisehq.com/".</p>         
        {/wrap}
      </div>
      
      <div class="clear"></div>
      
      <div class="col_wide">
        {wrap field=auth_token}
          {label for=auth_token required=yes}Highrise Authentication Token{/label}
          {text_field id='auth_token' name=highrise[auth_token] value=$highrise_data.auth_token}
          <p class="details">Authentication token can be found unter "My Info" in your Highrise profile</p>         
        {/wrap}
      </div>
      
	{wrap_buttons}
		{submit}Submit{/submit}
	{/wrap_buttons}
{/form}
