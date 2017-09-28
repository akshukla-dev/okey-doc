<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; //No direct access to this file.

jimport('joomla.application.component.modellist');


class OkeydocModelDocuments extends JModelList
{
  /**
   * Constructor.
   *
   * @param	array	An optional associative array of configuration settings.
   * @see		JController
   * @since	1.6
   */
  public function __construct($config = array())
  {
    if(empty($config['filter_fields']))
    {
      $config['filter_fields'] = array(
	      'id', 'd.id',
	      'catid', 'd.catid',
	      'title', 'd.title',
	      'folder_id', 'd.folder_id',
	      'published', 'd.published',
	      'access', 'd.access', 'access_level',
	      'ordering', 'd.ordering',
	      'created', 'd.created',
	      'created_by', 'd.created_by',
	      'author', 'd.author',
	      'downloads', 'd.downloads',
	      'publish_up', 'd.publish_up',
	      'publish_down', 'd.publish_down',
	      'language', 'd.language',
	      'category_id', 'tag',
	      'content_cat_id', 'article_id'
      );
    }

    parent::__construct($config);
  }

 /**
 * Method to auto-populate the model state.
 *
 * This method should only be called once per instantiation and is designed
 * to be called on the first call to the getState() method unless the model
 * configuration flag to ignore the request is set.
 *
 * Note. Calling getState in this method will result in recursion.
 *
 * @return      void
 * @since       1.6
 */
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

    $access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access');
    $this->setState('filter.access', $access);

    $userId = $app->getUserStateFromRequest($this->context.'.filter.user_id', 'filter_user_id');
    $this->setState('filter.user_id', $userId);

    $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
    $this->setState('filter.published', $published);
    //Filter for the component categories.
    $categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
    $this->setState('filter.category_id', $categoryId);
    //Filter for the DMS folders.
    $folderId = $this->getUserStateFromRequest($this->context.'.filter.folder_id', 'filter_folder_id');
    $this->setState('filter.folder_id', $folderId);

    $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
    $this->setState('filter.language', $language);

    $tag = $this->getUserStateFromRequest($this->context . '.filter.tag', 'filter_tag', '');
    $this->setState('filter.tag', $tag);

    $contentCatId = $this->getUserStateFromRequest($this->context . '.filter.content_cat_id', 'filter_content_cat_id', '');
    $this->setState('filter.content_cat_id', $contentCatId);

    $articleId = $this->getUserStateFromRequest($this->context . '.filter.article_id', 'filter_article_id', '');
    $this->setState('filter.article_id', $articleId);

    // List state information.
    parent::populateState('d.title', 'asc');
  }


  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param	string		$id	A prefix for the store id.
   *
   * @return	string		A store id.
   * @since	1.6
   */
  protected function getStoreId($id = '')
  {
    // Compile the store id.
    $id .= ':'.$this->getState('filter.search');
    $id .= ':'.$this->getState('filter.access');
    $id .= ':'.$this->getState('filter.published');
    $id .= ':'.$this->getState('filter.category_id');
    $id .= ':'.$this->getState('filter.user_id');
    $id .= ':'.$this->getState('filter.language');

    return parent::getStoreId($id);
  }


  //Build the MySQL query and load the data list. These datas are retrieved in
  //the view with the getItems function.
  //@return:  JDatabaseQuery
  //@since: 1.6
  protected function getListQuery()
  {
    //Create a new JDatabaseQuery object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Select the required fields from the table.
    $query->select($this->getState('list.select', 'd.id, d.catid, d.title, d.alias, d.checked_out_time,'.
	                           'd.created, d.downloads, d.author, d.language, d.checked_out, '.
				   'd.folder_id, d.published, d.access, d.ordering, d.publish_up, d.publish_down, d.created_by'));

    $query->from('#__okeydoc_document AS d');

    // Join over the language
    $query->select('l.title AS language_title');
    $query->join('LEFT', '`#__languages` AS l ON l.lang_code = d.language');

    // Join over the asset groups.
    $query->select('ag.title AS access_level');
    $query->join('LEFT', '#__viewlevels AS ag ON ag.id = d.access');

    // Join over the users for the checked out user.
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id=d.checked_out');

    //Get the category name.
    $query->select('c.title AS category_title');
    $query->join('LEFT OUTER', '#__categories AS c ON c.id = d.catid');

    $query->select('v.title AS access_level');
    $query->join('LEFT', '#__viewlevels AS v ON v.id = d.access');

    //Filter by component category.
    $categoryId = $this->getState('filter.category_id');
    if(is_numeric($categoryId)) {
      $query->where('d.catid = '.(int)$categoryId);
    }
    elseif(is_array($categoryId)) {
      JArrayHelper::toInteger($categoryId);
      $categoryId = implode(',', $categoryId);
      $query->where('d.catid IN ('.$categoryId.')');
    }

    //Filter by folder.
    $folderId = $this->getState('filter.folder_id');
    if(is_numeric($folderId)) {
      $type = $this->getState('filter.folder_id.include', true) ? '= ' : '<>';
      $query->where('d.folder_id'.$type.(int) $folderId);
    }

    //Filter by title search.
    $search = $this->getState('filter.search');
    if(!empty($search)) {
      if(stripos($search, 'id:') === 0) {
	$query->where('d.id = '.(int) substr($search, 3));
      }
      else {
	$search = $db->Quote('%'.$db->escape($search, true).'%');
	$query->where('(d.title LIKE '.$search.')');
      }
    }

    //Filter by publication state.
    $published = $this->getState('filter.published');
    if(is_numeric($published)) {
      $query->where('d.published = '.(int)$published);
    }
    elseif($published === '') {
      $query->where('(d.published IN (0, 1))');
    }

    //Filter by user.
    $userId = $this->getState('filter.user_id');
    if(is_numeric($userId)) {
      $type = $this->getState('filter.user_id.include', true) ? '= ' : '<>';
      $query->where('d.created_by'.$type.(int) $userId);
    }

    // Filter by access level.
    if($access = $this->getState('filter.access')) {
      $query->where('d.access='.(int) $access);
    }

    // Filter by a single tag.
    $tagId = $this->getState('filter.tag');

    if(is_numeric($tagId)) {
      $query->where($db->quoteName('tagmap.tag_id').' = '.(int) $tagId)
	    ->join('LEFT', $db->quoteName('#__contentitem_tag_map', 'tagmap').
		   ' ON '.$db->quoteName('tagmap.content_item_id').' = '.$db->quoteName('d.id').
		   ' AND '.$db->quoteName('tagmap.type_alias').' = '.$db->quote('com_okeydoc.document'));
    }

    //Filter by language.
    if($language = $this->getState('filter.language')) {
      $query->where('d.language = '.$db->quote($language));
    }

    //Filter by content category.
    if($contentCatId = $this->getState('filter.content_cat_id')) {
      $query->join('LEFT', '#__okeydoc_doc_map AS dmcat ON d.id = dmcat.doc_id');
      $query->where('dmcat.item_id='.(int)$contentCatId.' AND dmcat.item_type="category"');
    }

    //Filter by article.
    if($articleId = $this->getState('filter.article_id')) {
      $query->join('LEFT', '#__okeydoc_doc_map AS dmart ON d.id = dmart.doc_id');
      $query->where('dmart.item_id='.(int)$articleId.' AND dmart.item_type="article"');
    }

    //Add the list to the sort.
    $orderCol = $this->state->get('list.ordering', 'd.title');
    $orderDirn = $this->state->get('list.direction'); //asc or desc

    if($orderCol == 'd.ordering' || $orderCol == 'category_title') {
      $orderCol = 'c.title '.$orderDirn.', d.ordering';
    }

    //sqlsrv change
    if($orderCol == 'language') {
      $orderCol = 'l.title';
    }

    if($orderCol == 'access_level') {
      $orderCol = 'ag.title';
    }

    $query->order($db->escape($orderCol.' '.$orderDirn)); 

    return $query;
  }
}

