<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; // No direct access
 
jimport( 'joomla.application.component.view');
 

class OkeydocViewFolders extends JViewLegacy
{
  protected $items;
  protected $state;
  protected $pagination;

  public function display($tpl = null)
  {
    $this->items = $this->get('Items');
    $this->state = $this->get('State');
    $this->pagination = $this->get('Pagination');
    $this->filterForm = $this->get('FilterForm');
    $this->activeFilters = $this->get('ActiveFilters');

    // Check for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    //Check if the Okey DOC plugin is installed (or if it is enabled). If it doesn't we display an
    //information note.
    if(!JPluginHelper::isEnabled('content', 'okeydoc')) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_PLUGIN_NOT_INSTALLED'), 'warning');
    }

    $user = JFactory::getUser();

    //Display the toolbar and the sidebar.
    $this->addToolBar();
    $this->sidebar = JHtmlSidebar::render();

    //Display the template.
    parent::display($tpl);
  }


  protected function addToolBar() 
  {
    JToolBarHelper::title(JText::_('COM_OKEYDOC_MANAGER_FOLDERS'), 'folder-2');

    require_once JPATH_COMPONENT.'/helpers/okeydoc.php';
    $canDo = OkeydocHelper::getActions();

    if($canDo->get('core.create')) {
      JToolBarHelper::addNew('folder.add', 'JTOOLBAR_NEW');
      JToolBarHelper::divider();
    }

    if($canDo->get('core.edit')) {
      JToolBarHelper::editList('folder.edit', 'JTOOLBAR_EDIT');
      JToolBarHelper::divider();
    }

    if($canDo->get('core.edit.state')) { 
      JToolBarHelper::custom('folders.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      JToolBarHelper::divider();
    }

    if($canDo->get('core.delete')) {
      JToolBarHelper::deleteList('', 'folders.delete', 'JTOOLBAR_DELETE');
      JToolBarHelper::divider();
    }

    if($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_okeydoc', 550);
    }
  }
}


