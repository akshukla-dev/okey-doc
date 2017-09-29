<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; // No direct access
 
jimport('joomla.application.component.view');
require_once JPATH_ADMINISTRATOR.'/components/com_okeydoc/helpers/okeydoc.php';
 

class OkeydocViewDocuments extends JViewLegacy
{
  protected $items;
  protected $state;
  protected $pagination;

  //Display the view.
  public function display($tpl = null)
  {
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    //Check for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    //Check if the Okey DOC plugin is installed (or if it is enabled). If it doesn't we display an
    //information note.
    if(!JPluginHelper::isEnabled('content', 'okeydoc')) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_PLUGIN_NOT_INSTALLED'), 'warning');
    }

    // We don't need toolbar in the modal window.
    if ($this->getLayout() !== 'modal') {
      $this->addToolbar();
      $this->sidebar = JHtmlSidebar::render();
    }

    //Display the template.
    parent::display($tpl);
  }


  //Build the toolbar.
  protected function addToolBar() 
  {
    //Display the view title and the icon.
    JToolBarHelper::title(JText::_('COM_OKEYDOC_MANAGER_DOCUMENTS'), 'stack article');

    //Get the allowed actions list
    $canDo = OkeydocHelper::getActions();
    $user = JFactory::getUser();

    //The user is allowed to create or is able to create in one of the component
    //categories.
    if($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.create'))) > 0) {
      JToolBarHelper::addNew('document.add', 'JTOOLBAR_NEW');
    }

    //Notes: The Edit icon might not be displayed since it's not (yet ?) possible 
    //to edit several items at a time.
    if($canDo->get('core.edit') || $canDo->get('core.edit.own') || 
       (count($user->getAuthorisedCategories('com_okeydoc', 'core.edit'))) > 0 || 
       (count($user->getAuthorisedCategories('com_okeydoc', 'core.edit.own'))) > 0) {
      JToolBarHelper::editList('document.edit', 'JTOOLBAR_EDIT');
    }

    //Check for state permission.
    if($canDo->get('core.edit.state') || (count($user->getAuthorisedCategories('com_okeydoc', 'core.edit.state'))) > 0) {
      JToolBarHelper::divider();
      JToolBarHelper::custom('documents.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
      JToolBarHelper::custom('documents.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::divider();
      JToolBarHelper::archiveList('documents.archive','JTOOLBAR_ARCHIVE');

      if($canDo->get('core.edit.state')) { 
	JToolBarHelper::custom('documents.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
	JToolBarHelper::trash('documents.trash','JTOOLBAR_TRASH');
      }
    }

    //Check for delete permission.
    if($canDo->get('core.delete') || count($user->getAuthorisedCategories('com_okeydoc', 'core.delete'))) {
      JToolBarHelper::divider();
      JToolBarHelper::deleteList('', 'documents.delete', 'JTOOLBAR_DELETE');
    }

    if($canDo->get('core.admin')) {
      JToolBarHelper::divider();
      JToolBarHelper::preferences('com_okeydoc', 550);
    }
  }
}


