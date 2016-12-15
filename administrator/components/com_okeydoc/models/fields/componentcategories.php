<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');


//Script which build the select html tag containing the name and the id of the
//component categories.
//It only displays categories binded to a folder and which the user
//is allowed to create into (core.create).
//Note: This script is based on: libraries/legacy/form/fields/category.php

class JFormFieldComponentCategories extends JFormFieldList
{
  protected $type = 'componentcategories';

  protected function getOptions()
  {
    $options = array();
    $extension = 'com_okeydoc';

    $options = JHtml::_('category.options', $extension);
      
    //Retrieve all catids from the mapping table.
    $db = JFactory::getDbo();
    $query = 'SELECT catid FROM #__okeydoc_folder_map';
    $db->setQuery($query);
    $catids = $db->loadColumn();
    // Get the current user object.
    $user = JFactory::getUser();

    foreach($options as $i => $option)
    {
      // To take save or create in a category you need to have create rights for that category
      // unless the item is already in that category.
      // Unset the option if the user isn't authorised for it. In this field assets are always categories.

      // Add a OR in the if condition.
      // If the option value is not matched in the catids table it means that 
      // this category is not binded to any folder.
      if($user->authorise('core.create', $extension.'.category.'.$option->value) != true || !in_array($option->value, $catids))
	unset($options[$i]);
    }
	  
    //Add the default option at the beginning of the list.
    array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_SELECT_CATEGORY')));

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);

    return $options;
  }
}

