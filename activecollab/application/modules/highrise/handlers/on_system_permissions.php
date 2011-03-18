<?php

  /**
   * Incoming Mail on_system_permissions handler
   *
   * @package activeCollab.modules.incoming_mail
   * @subpackage handlers
   */
  
  /**
   * Handle on_system_permissions
   *
   * @param array $permissions
   * @return null
   */
  function highrise_handle_on_system_permissions(&$permissions) {
  	$permissions[] = 'can_import_highrise';
  } // incoming_mail_handle_on_system_permissions

?>