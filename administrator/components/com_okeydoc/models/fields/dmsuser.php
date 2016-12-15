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


//Display only the users who have already created a document with the DMS.
//This avoid to display a long list in case many users have registred.

class JFormFieldDmsuser extends JFormFieldList
{
  protected $type = 'dmsuser';

  protected function getOptions()
  {
    $options = array();
      
    //Get the users who have already put documents on line.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('DISTINCT d.created_by, u.username');
    $query->from('#__okeydoc_document AS d');
    $query->join('LEFT', '#__users AS u ON d.created_by = u.id');
    $query->order('username');
    $db->setQuery($query);
    $users = $db->loadObjectList();

    //Build the select options.
    foreach($users as $user) {
      $options[] = JHtml::_('select.option', $user->created_by, $user->username);
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);

    return $options;
  }
}


