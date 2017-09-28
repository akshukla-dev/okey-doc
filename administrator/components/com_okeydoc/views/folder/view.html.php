<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; // No direct access
 
jimport( 'joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/okeydoc.php';
 

class OkeydocViewFolder extends JViewLegacy
{
  protected $item;
  protected $state;
  protected $form;


  public function display($tpl = null)
  {
    $this->item = $this->get('Item');
    $this->state = $this->get('State');
    $this->form = $this->get('Form');

    //Check for errors.
    if(count($errors = $this->get('Errors'))) {
      JFactory::getApplication()->enqueueMessage($errors, 'error');
      return false;
    }

    //Display the toolbar.
    $this->addToolBar();

    //Display the template.
    parent::display($tpl);
  }


  protected function addToolBar() 
  {
    //Make the main menu inactive.
    JFactory::getApplication()->input->set('hidemainmenu', true);

    //Display the view title and the icon.
    JToolBarHelper::title(JText::_('COM_OKEYDOC_MANAGER_DOCUMENTS'), 'okeydoc');

    //Get the allowed actions list
    $canDo = OkeydocHelper::getActions();

    if($canDo->get('core.create')) {
      JToolBarHelper::apply('folder.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('folder.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::custom('folder.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
    }

    JToolBarHelper::divider();
    JToolBarHelper::cancel('folder.cancel', 'JTOOLBAR_CANCEL');
  }
}


