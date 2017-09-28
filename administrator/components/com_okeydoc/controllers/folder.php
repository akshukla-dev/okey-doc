<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; //No direct access to this file.
 
jimport('joomla.application.component.controllerform');
 


class OkeydocControllerFolder extends JControllerForm
{
  //Override all the allow functions to check wether only super administrators are 
  //allowed to manage the folders.

  public function allowAdd($data = array())
  {
    //Get the global parameters of the component.
    $params = JComponentHelper::getParams('com_okeydoc');
    $user = JFactory::getUser();

    if($params->get('superadmin_only') && !$user->get('isRoot')) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_NO_FOLDERS_ACCESS'), 'error');
      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item, false));
      return false;
    }

    return parent::allowAdd($data);
  }


  public function allowEdit($data = array(), $key = 'id')
  {
    //Get the global parameters of the component.
    $params = JComponentHelper::getParams('com_okeydoc');
    $user = JFactory::getUser();

    if($params->get('superadmin_only') && !$user->get('isRoot')) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_NO_FOLDERS_ACCESS'), 'error');
      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item, false));
      return false;
    }

    return parent::allowEdit($data, $key);
  }


  public function allowSave($data, $key = 'id')
  {
    //Get the global parameters of the component.
    $params = JComponentHelper::getParams('com_okeydoc');
    $user = JFactory::getUser();

    if($params->get('superadmin_only') && !$user->get('isRoot')) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_NO_FOLDERS_ACCESS'), 'error');
      $this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_item, false));
      return false;
    }

    return parent::allowSave($data, $key);
  }


  public function save($key = null, $urlVar = null)
  {
    $app = JFactory::getApplication();
    //Get the jform data.
    $jinput = $app->input;
    $data = $jinput->post->get('jform', array(), 'array');

    //The item is new and a symbolic link has been set, or we're editing a symbolic link as folder. 
    if(($data['id'] == 0 && isset($data['symlink_option'])) || $data['is_symlink']) {
      $data['symlink_path'] = trim($data['symlink_path']);
      //Check that path is not relative (starts with a slash) and target folder exists.
      if(!preg_match('#^/.+#', $data['symlink_path']) || !is_dir($data['symlink_path'])) {
	$app->enqueueMessage(JText::_('COM_OKEYDOC_ERROR_INVALID_SYMLINK_PATH'), 'error');
	// Save the data in the session.
	//Note: It allows to preserve the data previously set by the user after the redirection.
	$app->setUserState($this->option.'.edit.'.$this->context.'.data', $data);
	$this->setRedirect('index.php?option='.$this->option.'&view='.$this->context.$this->getRedirectToItemAppend($data['id']));
	return false;
      }
    }

    //New item is a regular folder.
    if($data['id'] == 0 && !isset($data['symlink_option'])) {
      //Clear field just in case.
      $data['symlink_path'] = '';
    }

    //Update jform with the modified data.
    $jinput->post->set('jform', $data);

    //Hand over to the parent function.
    return parent::save($key = null, $urlVar = null);
  }
}

