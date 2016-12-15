<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Folder Table class
 */
class OkeydocTableFolder extends JTable
{
  /**
   * Constructor
   *
   * @param object Database connector object
   */
  function __construct(&$db) 
  {
    parent::__construct('#__okeydoc_folder', 'id', $db);
  }


  /**
   * Overrides JTable::store to set modified data and user id.
   *
   * @param   boolean  $updateNulls  True to update fields even if they are null.
   *
   * @return  boolean  True on success.
   *
   * @since   11.1
   */
  public function store($updateNulls = false)
  {
    $date = JFactory::getDate();
    $user = JFactory::getUser();

    if($this->id) {
      // Existing item
      $this->modified = $date->toSql();
      $this->modified_by = $user->get('id');
    }
    else {
      // New folder. A folder created and created_by field can be set by the user,
      // so we don't touch either of these if they are set.
      if(!(int)$this->created) {
	$this->created = $date->toSql();
      }

      if(empty($this->created_by)) {
	$this->created_by = $user->get('id');
      }
    }

    return parent::store($updateNulls);
  }
}


