<?php

// Extends profile controller
use_controller('users', SYSTEM_MODULE);

class ProfileHighriseController extends UsersController
{

	/**
	* Controller name
	*
	* @var string
	*/
	var $controller_name = 'profile_highrise';
	

	function __construct($request)
	{
		parent::__construct($request);
		$this->wireframe->addBreadCrumb(lang('Highrise Integration'));
	}
	
	
	
	function index() 
	{
		if(!$this->logged_user->getSystemPermission('can_import_highrise') || $this->active_user->getId() != $this->logged_user->getId()) {
			$this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
		}
		
		$highrise_data = $this->request->post('highrise');
		
		if (!is_foreachable($highrise_data))
		{
			$highrise_data = array
			(
				'domain' => UserConfigOptions::getValue('highrise_domain', $this->active_user),
				'auth_token' => UserConfigOptions::getValue('highrise_auth_token', $this->active_user),
			);
		}
		
		if ($this->request->isSubmitted())
		{
			$auth_token = array_var($highrise_data, 'auth_token', null);
			$domain = array_var($highrise_data, 'domain', null);
			
			UserConfigOptions::setValue('highrise_domain', $domain, $this->active_user);
			UserConfigOptions::setValue('highrise_auth_token', $auth_token, $this->active_user);
			
			flash_success("Highrise settings successfully saved");
			$this->redirectTo('people_company_user', array('user_id' => $this->active_user->getId(), 'company_id' => $this->active_user->getCompanyId()));
		} // if
		
		$this->smarty->assign(array(
			'highrise_data' => $highrise_data,
		));
	}
	
}

