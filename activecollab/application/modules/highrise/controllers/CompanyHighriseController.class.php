<?php

// Extend company profile
use_controller('companies', SYSTEM_MODULE);


class CompanyHighriseController extends CompaniesController
{
    
    /**
     * Controller name
     *
     * @var string
     */
    var $controller_name = 'company_highrise';
    
    /**
     * Selected company
     *
     * @var Company
     */
    var $active_company;
    
    
    /**
     * Import clients (companies) from Highrise
     *
     * @param void
     * @return null
     */
    function import_clients()
    {
		if(!Company::canAdd($this->logged_user)) {
			$this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
		}
		
		$arrCompanies = Companies::findAll();
		
		foreach( $arrCompanies as $i => $company )
		{
			$arrCompanies[$i] = $company->getName();
		}
		
		$arrAdded = array();
		$arrClients = array();
		$objClients = $this->http_request_xml('companies.xml');
		$arrImport = $this->request->post('clients');
		
		foreach( $objClients->company as $company )
		{
			if (in_array(strval($company->name), $arrCompanies))
				continue;
				
			if ($this->request->isSubmitted() && is_array($arrImport) && in_array(strval($company->id), $arrImport))
			{
				$arrData = array
				(
					'name'				=> strval($company->name),
					'highrise_id'		=> strval($company->id),
					'office_address'	=> '', 
					'office_phone'		=> '', 
					'office_fax'		=> '', 
					'office_homepage'	=> trim(strval($company->{'contact-data'}->{'web-addresses'}->{'web-address'}->url)),
				);
				
				if (($address = $company->{'contact-data'}->addresses->address))
				{
					$street = strval($address->street);
					$city = strval($address->zip) . ' ' . strval($address->city);
					$country = strval($address->country);
					
					$arrData['office_address'] = (strlen($street) ? $street."\n" : '') . (strlen($city) ? $city."\n" : '') . $country;
				}
				
				foreach( $company->{'contact-data'}->{'phone-numbers'}->{'phone-number'} as $number )
				{
					if (!strlen($arrData['office_phone']) && $number->location == 'Work')
					{
						$arrData['office_phone'] = $number->number;
					}
					elseif (!strlen($arrData['office_fax']) && $number->location == 'Fax')
					{
						$arrData['office_fax'] = $number->number;
					}
				}
				
				$arrAdded[] = $this->addCompany($arrData);
			}
				
			$arrClients[] = array
			(
				'id'		=> strval($company->id),
				'name'		=> strval($company->name),
			);
		}
		
		if ($this->request->isSubmitted())
		{
			if (count($arrAdded) == 0)
			{
				flash_error('No clients imported.');
				$this->redirectToReferer(null);
			}
			elseif (count($arrAdded) == 1)
			{
				flash_success("Selected client has been imported from Highrise.");
				$this->redirectTo('people_company', array('company_id'=>$arrAdded[0]));
			}
			else
			{
				flash_success(count($arrAdded) . " clients have been imported from Highrise.");
				$this->redirectTo('people');
			}
		}
		
		$this->smarty->assign(array(
			'clients' => $arrClients,
		));
    } 
    
    
    /**
     * Import contacts (people) from Highrise
     *
     * @param void
     * @return null
     */
    function import_contacts()
    {
		if(!Company::canAdd($this->logged_user)) {
			$this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
		}
		
		$company_id = $this->request->getId('company_id');
		if($company_id)
		{
			$this->active_company = Companies::findById($company_id);
		}
		
		if(!instance_of($this->active_company, 'Company'))
		{
			$this->httpError(HTTP_ERR_BAD_REQUEST, null, true, false);
		}
		
		$highrise_id = CompanyConfigOptions::getValue('highrise_id', $this->active_company);
		if (!$highrise_id)
		{
			flash_error('This company was not importet from Highrise');
			$this->redirectToUrl($this->active_company->getViewUrl());
		}
		
		$arrUsers = Users::findByCompany($this->active_company);
		if (is_array($arrUsers))
		{
			foreach( $arrUsers as $i => $user )
			{
				$arrUsers[$i] = $user->getName();
			}
		}
		else
		{
			$arrUsers = array();
		}
		
		$arrAdded = array();
		$arrContacts = array();
		$objContacts = $this->http_request_xml('companies/' . $highrise_id . '/people.xml');
		$arrImport = $this->request->post('contacts');
		
		foreach( $objContacts->person as $contact )
		{
			$name = strval($contact->{'first-name'} . ' ' . $contact->{'last-name'});
			
			if (in_array($name, $arrUsers))
				continue;
				
				
			if ($this->request->isSubmitted() && is_array($arrImport) && in_array(strval($contact->id), $arrImport))
			{
				$arrData = array
				(
					'highrise_id'		=> strval($contact->id),
					'first_name'		=> strval($contact->{'first-name'}),
					'last_name'			=> strval($contact->{'last-name'}),
					'email'				=> strval($contact->{'contact-data'}->{'email-addresses'}->{'email-address'}->address),
				);
				
				$intId = $this->addUser($arrData);
				
				if ($intId !== false)
				{
					$arrAdded[] = $intId;
				}
			}
			
				
			$arrContacts[] = array
			(
				'id'		=> strval($contact->id),
				'name'		=> strval($name),
			);
		}
		
		$this->smarty->assign(array
		(
			'contacts'	=> $arrContacts,
			'company'	=> $this->active_company->getName(),
			'action'	=> assemble_url('people_company_import_highrise', array('company_id'=>$this->active_company->getId())),
		));
		
		if ($this->request->isSubmitted())
		{
			if (count($arrAdded) == 0)
			{
				flash_error('No contacts imported.');
				$this->redirectToReferer(null);
			}
			if (count($arrAdded) == 1)
			{
				flash_success("Selected contact has been imported from Highrise.");
				$this->redirectTo('people_company_user', array('company_id'=>$this->active_company->getId(), 'user_id'=>$arrAdded[0]));
			}
			else
			{
				flash_success(count($arrAdded) . " contacts have been imported from Highrise.");
				$this->redirectTo('people_company', array('company_id'=>$this->active_company->getId()));
			}
		}
    }
    
    
    function http_request_xml($file)
    {
    	$domain = UserConfigOptions::getValue('highrise_domain', $this->logged_user);
    	$token = UserConfigOptions::getValue('highrise_auth_token', $this->logged_user);
    	
    	$ch = curl_init($domain . $file);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $token.':X');
		
		$data = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		switch( $code )
		{
			case '200':
				$obj = simplexml_load_string($data);
				
				if (!is_object($obj))
				{
				}
				
				return $obj;
				break;
				
			case '401':
				flash_error('Highrise authentication failed. Please check your access credentials.');
				break;
				
			default:
				flash_error('Connecting to Highrise failed. Please check your access credentials.');
				break;
		}
		
		$this->redirectTo('admin_highrise');
		
		return false;
    }
    
    
	function addCompany($company_data) 
	{
		if(!Company::canAdd($this->logged_user)) {
			$this->httpError(HTTP_ERR_FORBIDDEN, null, true, $this->request->isApiCall());
		}
		
		$company = new Company();
		$options = array('office_address', 'office_phone', 'office_fax', 'office_homepage', 'highrise_id');
		
		db_begin_work();
		
		$company = new Company();
		$company->setAttributes($company_data);
		$company->setIsOwner(false);
		
		$save = $company->save();
		
		if($save && !is_error($save))
		{
			foreach($options as $option)
			{
				$value = trim(array_var($company_data, $option));
				
				if($option == 'office_homepage' && $value && strpos($value, '://') === false)
				{
					$value = 'http://' . $value;
				}
				
				if($value != '') 
				{
					CompanyConfigOptions::setValue($option, $value, $company);
				}
			}
			
			db_commit();
		}
		else
		{
			db_rollback();
//				$this->smarty->assign('errors', $save);
		}
		
		return $company->getId();
	}
	
	
	function addUser($user_data)
	{
		db_begin_work();
		
		$user = new User();
		$user->setAttributes($user_data);
		$user->setPassword(make_password(11));
		$user->setCompanyId($this->active_company->getId());
		$user->setRoleId(ConfigOptions::getValue('default_role'));
		
		$save = $user->save();
		
		if($save && !is_error($save))
		{
//			UserConfigOptions::setValue('highrise_id', $user_data['highrise_id'], $user);
		
			db_commit();
			
			return $user->getId();
		}
		else
		{
			db_rollback();
			return false;
		}
	}
	
}

