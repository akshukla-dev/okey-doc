<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; //No direct access to this file.

jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT.'/helpers/okeydoc.php';


class OkeydocModelFolder extends JModelAdmin
{
  //Prefix used with the controller messages.
  protected $text_prefix = 'COM_OKEYDOC';

  //Returns a Table object, always creating it.
  //Table can be defined/overrided in the file: tables/mycomponent.php
  public function getTable($type = 'Folder', $prefix = 'OkeydocTable', $config = array()) 
  {
    return JTable::getInstance($type, $prefix, $config);
  }


  public function getForm($data = array(), $loadData = true) 
  {
    $form = $this->loadForm('com_okeydoc.folder', 'folder', array('control' => 'jform', 'load_data' => $loadData));

    if(empty($form)) {
      return false;
    }

    return $form;
  }


  protected function loadFormData() 
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_okeydoc.edit.folder.data', array());

    if(empty($data)) {
      $data = $this->getItem();
    }

    return $data;
  }


  //Check wether only super administrators are allowed to delete the folders.
  public function canDelete($record)
  {
    //Get the global parameters of the component and the current user data.
    $params = JComponentHelper::getParams('com_okeydoc');
    $user = JFactory::getUser();

    if($params->get('superadmin_only') && !$user->get('isRoot')) {
      JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_NO_FOLDERS_ACCESS'), 'error');
      return false;
    }

    return parent::canDelete($record);
  }


  /**
   * Method to get a single record.
   *
   * @param   integer  $pk  The id of the primary key.
   *
   * @return  mixed    Object on success, false on failure.
   *
   * @since   12.2
   */
  public function getItem($pk = null)
  {
    if($item = parent::getItem($pk)) {
      //Get the component categories in which the user is allowed to create.
      $item->categories = OkeydocHelper::getUserCategories('create', true);

      //Get all the DMS folders binded to component categories.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select('f.id, f.title, GROUP_CONCAT(m.catid) AS catids');
      $query->from('#__okeydoc_folder AS f');
      $query->join('LEFT', '#__okeydoc_folder_map AS m ON f.id = m.folder_id');
      $query->group('f.id');
      $db->setQuery($query);
      $item->folders = $db->loadObjectList();
    }

    return $item;
  }
}

