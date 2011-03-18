<?php

/**
* on_company_options event handler
*
* @package activeCollab.modules.invoicing
* @subpackage handlers
*/


function highrise_handle_on_company_options(&$company, &$options, &$logged_user) {

	if (Company::canAdd($logged_user))
	{
		$wf = Wireframe::instance();
		$wf->addPageAction(lang('Highrise Import'), assemble_url('people_company_import_highrise', array('company_id' => $company->getId())));
	}
}

