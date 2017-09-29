<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; //No direct access to this file.

jimport('joomla.application.component.modellist');



class OkeydocModelFolders extends JModelList
{
  public function __construct($config = array())
  {
    if(empty($config['filter_fields']))
    {
      $config['filter_fields'] = array(
	      'id', 'f.id',
	      'title', 'f.title',
	      'files', 'f.files',
	      'created', 'f.created',
	      'created_by', 'f.created_by',
	      'user_id',
	      'category_id'
      );
    }

    parent::__construct($config);
  }


  protected function populateState($ordering = null, $direction = null)
  {
    // Initialise variables.
    $app = JFactory::getApplication();
    $session = JFactory::getSession();

    // Adjust the context to support modal layouts.
    if($layout = JFactory::getApplication()->input->get('layout')) {
      $this->context .= '.'.$layout;
    }

    //Get the state values set by the user.
    $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

    $userId = $app->getUserStateFromRequest($this->context.'.filter.user_id', 'filter_user_id');
    $this->setState('filter.user_id', $userId);

    //Filter for the component categories.
    $categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
    $this->setState('filter.category_id', $categoryId);

    // List state information.
    parent::populateState('f.title', 'asc');
  }


  protected function getStoreId($id = '')
  {
    // Compile the store id.
    $id .= ':'.$this->getState('filter.search');
    $id .= ':'.$this->getState('filter.category_id');
    $id .= ':'.$this->getState('filter.author_id');

    return parent::getStoreId($id);
  }


  protected function getListQuery()
  {
    //Create a new JDatabaseQuery object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Select the required fields from the table.
    $query->select($this->getState('list.select','f.*'));

    $query->from('#__okeydoc_folder AS f');

    //Get the number of binded categories (from their id).
    $query->select('COUNT(fm.catid) AS count_cat');
    $query->join('LEFT OUTER', '#__okeydoc_folder_map AS fm ON fm.folder_id=f.id');
    $query->group('f.id');

    //Get the name of the first binded category.
    $query->select('c.title AS cat_title');
    $query->join('LEFT OUTER', '#__categories AS c ON c.id = fm.catid');

    //Get the author name.
    $query->select('u.name AS author');
    $query->join('LEFT', '#__users AS u ON u.id = f.created_by');

    // Join over the users for the checked out user.
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id=f.checked_out');

    //Filter by component category.
    $categoryId = $this->getState('filter.category_id');
    if(is_numeric($categoryId)) 
    {
      $query->join('LEFT', '#__okeydoc_folder_map AS m ON m.catid ='.(int)$categoryId);
      $query->where('f.id = m.folder_id');
    }
    elseif(is_array($categoryId))
    {
      JArrayHelper::toInteger($categoryId);
      $categoryId = implode(',', $categoryId);
      $query->join('LEFT', '#__okeydoc_folder_map AS m ON m.catid IN ('.$categoryId.')');
      $query->where('f.id = m.folder_id');
    }

    //Filter by title search.
    $search = $this->getState('filter.search');
    if(!empty($search)) {
      if(stripos($search, 'id:') === 0) {
	$query->where('f.id = '.(int) substr($search, 3));
      }else {
	$search = $db->Quote('%'.$db->escape($search, true).'%');
	$query->where('(f.title LIKE '.$search.')');
      }
    }

    //Filter by author.
    echo $userId = $this->getState('filter.user_id');
    if(is_numeric($userId)) {
      $type = $this->getState('filter.user_id.include', true) ? '= ' : '<>';
      $query->where('f.created_by'.$type.(int) $userId);
    }

    //Add the list to the sort.
    $orderCol = $this->state->get('list.ordering', 'f.title');
    $orderDirn = $this->state->get('list.direction'); //asc or desc

    $query->order($db->escape($orderCol.' '.$orderDirn));

    return $query;
  }
}


