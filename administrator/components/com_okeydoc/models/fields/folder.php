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

//Display the DMS folders.

class JFormFieldFolder extends JFormFieldList
{
  protected $type = 'folder';

  protected function getOptions()
  {
    $options = array();
      
    //Get the country names.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('id, title');
    $query->from('#__okeydoc_folder');
    $query->order('title');
    $db->setQuery($query);
    $folders = $db->loadObjectList();

    //Build the select options.
    foreach($folders as $folder)
    {
      $options[] = JHtml::_('select.option', $folder->id, $folder->title);
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);

    return $options;
  }
}

