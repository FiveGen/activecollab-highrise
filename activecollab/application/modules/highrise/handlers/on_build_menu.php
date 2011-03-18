<?php

/**
* on_company_options event handler
*
* @package activeCollab.modules.invoicing
* @subpackage handlers
*/

/**
* Handle on_build_menu event
*
* @param Company $company
* @param NamedList $options
* @param User $logged_user
* @return null
*/
function highrise_handle_on_build_menu(&$menu, &$logged_user) 
{
	if ((ANGIE_PATH_INFO == 'people' || ANGIE_PATH_INFO == 'people/import/highrise') && Company::canAdd($logged_user))
	{
		$wf = Wireframe::instance();
		$wf->addPageAction(lang('Highrise Import'), assemble_url('people_import_highrise'));
	}
} 

