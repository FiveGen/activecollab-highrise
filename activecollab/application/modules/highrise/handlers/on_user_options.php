<?php

  /**
   * Discussions module on_project_object_options event handler
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */
  
  /**
   * Populate object options array
   *
   * @param NamedList $options
   * @param ProjectObject $object
   * @param User $user
   * @return null
   */
  function highrise_handle_on_user_options(&$user, &$options, &$logged_user) {
  	if($user->getId() == $logged_user->getId() && $logged_user->getSystemPermission('can_import_highrise')) {
      $options->add('highrise', array(
        'text' => lang('Highrise Credentials'),
        'url'  => assemble_url('profile_highrise', array('user_id' => $user->getId(), 'company_id' => $user->getCompanyId())),
      ));
    } // if
  } // milestones_handle_on_project_object_options

