<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; //No direct access to this file.
 
jimport('joomla.application.component.controllerform');
 

class OkeydocControllerDocument extends JControllerForm
{
  //Method override to check if you can edit an existing record.
  protected function allowEdit($data = array(), $key = 'id')
  {
    // Initialise variables.
    $recordId = (int) isset($data[$key]) ? $data[$key] : 0;
    $user = JFactory::getUser();
    $userId = $user->get('id');

    // Check general edit permission first.
    if($user->authorise('core.edit', 'com_okeydoc.document.'.$recordId)) {
      return true;
    }

    // Fallback on edit.own.
    // First test if the permission is available.
    if($user->authorise('core.edit.own', 'com_okeydoc.document.'.$recordId)) {
      // Now test if the owner is the user.
      $ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
      if(empty($ownerId) && $recordId) {
	// Need to do a lookup from the model.
	$record = $this->getModel()->getItem($recordId);

	if(empty($record)) {
	  return false;
	}

	$ownerId = $record->created_by;
      }

      // If the owner matches 'me' then do the test.
      if($ownerId == $userId) {
	return true;
      }
    }

    // Since there is no asset tracking, revert to the component permissions.
    return parent::allowEdit($data, $key);
  }


  public function save($key = null, $urlVar = null)
  {
    $app = JFactory::getApplication();
    //Get the jform data.
    $jinput = $app->input;
    $data = $jinput->post->get('jform', array(), 'array');

    //Set the alias of the document.
    
    //Remove possible spaces.
    $data['alias'] = trim($data['alias']);
    if(empty($data['alias'])) {
      //Created a sanitized alias from the title field, (see stringURLSafe function for details).
      $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
    }

    // Verify that the alias is unique

    //Note: Usually this code goes into the overrided store JTable function but the file
    //would already be uploaded on the server if any duplicate alias is found.
    //To avoid this situation we check the alias unicity here as the file uploading is
    //not still triggered.

    $model = $this->getModel();
    $table = $model->getTable();

    if($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])) && ($table->id != $data['id'] || $data['id'] == 0)) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_DATABASE_ERROR_DOCUMENT_UNIQUE_ALIAS'), 'error');

      // Save the data in the session.
      //Note: It allows to preserve the data previously set by the user after the redirection.
      $app->setUserState($this->option.'.edit.'.$this->context.'.data', $data);

      $this->setRedirect('index.php?option='.$this->option.'&view='.$this->context.$this->getRedirectToItemAppend($data['id']));
      return false;
    }

    //Update jform with the modified data.
    $jinput->post->set('jform', $data);

    //Hand over to the parent function.
    return parent::save($key = null, $urlVar = null);
  }
}


