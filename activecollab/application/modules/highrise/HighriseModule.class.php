<?php

  /**
   * Invoicing module defintiion
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class HighriseModule extends Module {
    
    /**
     * Plain module name
     *
     * @var string
     */
    var $name = 'highrise';
    
    /**
     * Is system module flag
     *
     * @var boolean
     */
    var $is_system = false;
    
    /**
     * Module version
     *
     * @var string
     */
    var $version = '1.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     *
     * @param Router $r
     * @return null
     */
    function defineRoutes(&$router)
    {
		$router->map('people_import_highrise', 'people/import/highrise', array('controller' => 'company_highrise', 'action' => 'import_clients'));
		$router->map('people_company_import_highrise', 'people/:company_id/import/highrise', array('controller' => 'company_highrise', 'action' => 'import_contacts'));
		
		$router->map('profile_highrise', 'people/:company_id/users/:user_id/highrise', array('controller' => 'profile_highrise', 'action' => 'index'), array('company_id' => '\d+', 'user_id' => '\d+'));
		$router->map('profile_highrise_test_token', 'people/:company_id/users/:user_id/highrise/test-token', array('controller' => 'profile_highrise', 'action' => 'test_token'), array('company_id' => '\d+', 'user_id' => '\d+'));
    } 
    
    /**
     * Define event handlers
     *
     * @param EventsManager $events
     * @return null
     */
    function defineHandlers(&$events) {
	  $events->listen('on_company_options', 'on_company_options');
	  $events->listen('on_build_menu', 'on_build_menu');
	  $events->listen('on_system_permissions', 'on_system_permissions');
	  $events->listen('on_user_options', 'on_user_options');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Un(Install)
    // ---------------------------------------------------
    
    
    /**
     * Can this module be installed or not
     *
     * @param array $log
     * @return boolean
     */
	function canBeInstalled(&$log)
	{
		if(extension_loaded('SimpleXML') && function_exists('simplexml_load_string'))
		{
			$log[] = lang('OK: SimpleXML extension loaded');
			
			if(extension_loaded('curl') && function_exists('curl_init'))
			{
				$log[] = lang('OK: CURL extension loaded');
			}
			else
			{
				$log[] = lang('This module requires CURL PHP extension to be installed. Read more about CURL extension in PHP documentation: http://www.php.net/manual/en/book.curl.php');
			
				return false;
			}
			
			return true;
		}
		else
		{
			$log[] = lang('This module requires SimpleXML PHP extension to be installed. Read more about SimpleXML extension in PHP documentation: http://www.php.net/manual/en/book.simplexml.php');
			
			return false;
		}
	}
    
    
    /**
     * Install this module
     *
     * @param void
     * @return boolean
     */
    function install() {
    
      // config options
      $this->addConfigOption('highrise_domain', USER_CONFIG_OPTION, null);
      $this->addConfigOption('highrise_auth_token', USER_CONFIG_OPTION, null);
      
      $this->addConfigOption('highrise_id', COMPANY_CONFIG_OPTION, null);
      $this->addConfigOption('highrise_id', USER_CONFIG_OPTION, null);

      return parent::install();
    } // install
    
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Highrise');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @param void
     * @return string
     */
    function getDescription() {
      return lang('Import clients and contacts from Highrise');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @param void
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. This will not affect any imported data.');
    } // getUninstallMessage
    
  }

