<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined( '_JEXEC' ) or die; // No direct access
 

jimport( 'joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/okeydoc.php';
 

class OkeydocViewDocument extends JViewLegacy
{
  protected $item;
  protected $form;
  protected $state;

  //Display the view.
  public function display($tpl = null)
  {
    $this->item = $this->get('Item');
    $this->form = $this->get('Form');
    $this->state = $this->get('State');

    //Check for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    if($this->item->id) {
      //Warn the user if the document category has been trashed, archived or unpublished.
      switch($this->item->cat_state) {
	case -2 :
	  JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_OKEYDOC_WARNING_CATEGORY_TRASHED', $this->item->cat_title), 'error');
	  break;

	case 0 :
	  JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_OKEYDOC_WARNING_CATEGORY_UNPUBLISHED', $this->item->cat_title), 'error');
	  break;

	case 2 :
	  JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_OKEYDOC_WARNING_CATEGORY_ARCHIVED', $this->item->cat_title), 'error');
	  break;
      }
    }

    //Display the toolbar.
    $this->addToolBar();

    //Display the template.
    parent::display($tpl);
  }


  protected function addToolBar() 
  {
    //Make main menu inactive.
    JFactory::getApplication()->input->set('hidemainmenu', true);

    $user = JFactory::getUser();
    $userId = $user->get('id');

    //Get the allowed actions list
    $canDo = OkeydocHelper::getActions($this->state->get('filter.category_id'));
    $isNew = $this->item->id == 0;

    //Display the view title and the icon.
    JToolBarHelper::title($isNew ? JText::_('COM_OKEYDOC_MANAGER_NEW') : JText::_('COM_OKEYDOC_MANAGER_EDIT'), 'pencil-2');

    if($isNew) {
      //Check the "create" permission for the new records.
      if($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.create'))) > 0) {
	JToolBarHelper::apply('document.apply', 'JTOOLBAR_APPLY');
	JToolBarHelper::save('document.save', 'JTOOLBAR_SAVE');
	JToolBarHelper::custom('document.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
      }
    }
    else {
      if($canDo->get('core.edit') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.edit'))) > 0 || $this->item->created_by == $userId) {
	// We can save the new record
	JToolBarHelper::apply('document.apply', 'JTOOLBAR_APPLY');
	JToolBarHelper::save('document.save', 'JTOOLBAR_SAVE');

	// We can save this record, but check the create permission to see if we can return to make a new one.
	if($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.create'))) > 0) {
	  JToolBarHelper::custom('document.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
	}
      }
    }

    JToolBarHelper::cancel('document.cancel', 'JTOOLBAR_CANCEL');
  }
}



