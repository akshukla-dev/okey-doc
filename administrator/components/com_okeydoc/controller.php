<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; // No direct access.

jimport('joomla.application.component.controller');


class OkeydocController extends JControllerLegacy
{
  public function display($cachable = false, $urlparams = false) 
  {
    require_once JPATH_COMPONENT.'/helpers/okeydoc.php';

    //Display the submenu.
    OkeydocHelper::addSubmenu(JRequest::getCmd('view', 'documents'));

    //Set the default view.
    JRequest::setVar('view', JRequest::getCmd('view', 'documents'));

    //Display the view.
    parent::display();
  }

}


