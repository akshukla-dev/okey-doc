<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


// No direct access to this file
defined('_JEXEC') or die;
 // import joomla's filesystem classes
jimport('joomla.filesystem.folder');



class com_okeydocInstallerScript
{
  /**
   * method to run before an install/update/uninstall method
   *
   * @return void
   */
  function preflight($type, $parent) 
  {
    $jversion = new JVersion();

    // Installing component manifest file version
    $this->release = $parent->get('manifest')->version;

    // Show the essential information at the install/update back-end
    echo '<p>'.JText::_('COM_OKEYDOC_INSTALLING_COMPONENT_VERSION').$this->release;
    echo '<br />'.JText::_('COM_OKEYDOC_CURRENT_JOOMLA_VERSION').$jversion->getShortVersion().'</p>';

    //Abort if the component being installed is not newer than the
    //currently installed version.
    if($type == 'update') {
      $oldRelease = $this->getParam('version');
      $rel = ' v-'.$oldRelease.' -> v-'.$this->release;

      if(version_compare($this->release, $oldRelease, 'le')) {
	JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_UPDATE_INCORRECT_VERSION').$rel, 'error');
	return false;
      }
    }

    if($type == 'install') {
      //Create a "okeydoc" folder in the root directory of the site.
      if(JFolder::create(JPATH_ROOT.'/okeydoc')) {
	echo '<p style="color:green;">'.JText::_('COM_OKEYDOC_FOLDER_CREATION_SUCCESS').'</p>';
      }
      else { //Stop the installation if the folder cannot be created. 
	JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_FOLDER_CREATION_ERROR'), 'error');
	return false;
      }

      //Create a .htaccess file in the "okeydoc" directory.
      $buffer = 'Options -Indexes';
      if(JFile::write(JPATH_ROOT.'/okeydoc/.htaccess', $buffer)) {
	echo '<p style="color:green;">'.JText::_('COM_OKEYDOC_HTACCESS_CREATION_SUCCESS').'</p>';
      }
      else { //Stop the installation if the .htaccess file cannot be created. 
	JFactory::getApplication()->enqueueMessage(JText::_('COM_OKEYDOC_HTACCESS_CREATION_ERROR'), 'error');
	return false;
      }
    }
  }


  /**
   * method to install the component
   *
   * @return void
   */
  function install($parent) 
  {
  }


  /**
   * method to uninstall the component
   *
   * @return void
   */
  function uninstall($parent) 
  {
    //Check if file root directory must be removed.
    //Note: Uninstall function cannot cause an abort of the Joomla uninstall action, so returning
    //false would be a waste of time.
    if(JComponentHelper::getParams('com_okeydoc')->get('uninstall_remove_all')) {
      JFolder::delete(JPATH_ROOT.'/okeydoc'); //Remove file root directory and all its content.
    }
    else { //Keep the file root directory untouched.
      //Before the component is uninstalled we gather any relevant data about files then
      //put it into a csv file.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->select('d.file, d.file_name, d.file_size, d.file_path, d.created, d.catid, c.level AS cat_level,'.
	             'c.parent_id AS cat_parent_id, c.alias AS category_alias');
      $query->from('#__okeydoc_document AS d');
      $query->join('LEFT', '#__categories AS c ON c.id=d.catid AND c.extension="com_okeydoc"');
      $query->where('d.file_location="server"');
      $db->setQuery($query);
      $documents = $db->loadObjectList();

      $cR = "\r\n"; //Carriage return.
      //Create the csv header.
      $buffer = 'file,file_name,file_size,folder_name,created,catid,cat_level,cat_parent_id,category_alias'.$cR;
      foreach($documents as $document) {
	$buffer .= $document->file.','.$document->file_name.','.$document->file_size.','.
	           //Remove "okeydoc/" from the beginning of the path. 
	           substr($document->file_path, 8).','.$document->created.','.$document->catid.','.
		   $document->cat_level.','.$document->cat_parent_id.','.$document->category_alias.$cR;
      }
      //Create the csv file.
      JFile::write(JPATH_ROOT.'/okeydoc/file_log.csv', $buffer);
    }

    //Remove tagging informations from the Joomla table.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->delete('#__content_types')
	  ->where('type_alias="com_okeydoc.document" OR type_alias="com_okeydoc.category"');
    $db->setQuery($query);
    $db->query();
  }


  /**
   * method to update the component
   *
   * @return void
   */
  function update($parent) 
  {
  }


  /**
   * method to run after an install/update/uninstall method
   *
   * @return void
   */
  function postflight($type, $parent) 
  {
    if($type == 'install') {
      //The component parameters are not inserted into the table until the user open up the Options panel then click on the save button.
      //The workaround is to update manually the extensions table with the parameters just after the component is installed. 

      //Get the component config xml file
      $form = new JForm('okeydoc_config');
      //Note: The third parameter must be set or the xml file won't be loaded.
      $form->loadFile(JPATH_ROOT.'/administrator/components/com_okeydoc/config.xml', true, '/config');
      $JsonValues = '';
      foreach($form->getFieldsets() as $fieldset) {
        foreach($form->getFieldset($fieldset->name) as $field) {
	  //Concatenate every field as Json values.
	  $JsonValues .= '"'.$field->name.'":"'.$field->getAttribute('default', '').'",';
        } 
      } 

      //Remove comma from the end of the string.
      $JsonValues = substr($JsonValues, 0, -1);

      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      $query->update('#__extensions');
      $query->set('params='.$db->Quote('{'.$JsonValues.'}'));
      $query->where('element='.$db->Quote('com_okeydoc').' AND type='.$db->Quote('component'));
      $db->setQuery($query);
      $db->query();

      //In order to use the Joomla's tagging system we have to give to Joomla some
      //informations about the component items we want to tag.
      //Those informations should be inserted into the #__content_types table.

      //Informations about the Okey DOC document items.
      $columns = array('type_title', 'type_alias', $db->quoteName('table'), 'field_mappings', 'router');
      $query->clear();
      $query->insert('#__content_types');
      $query->columns($columns);
      $query->values($db->Quote('Okey DOC').','.$db->Quote('com_okeydoc.document').','.
$db->Quote('{"special"{"dbtable":"#__okeydoc_document","key":"id","type":"Document","prefix":"OkeydocTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}').','.
$db->Quote('{"common"{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"introtext","core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access","core_params":"null","core_featured":"null","core_metadata":"null","core_language":"language","core_images":"null","core_urls":"null","core_version":"null","core_ordering":"ordering","core_metakey":"null","core_metadesc":"null","core_catid":"catid","core_xreference":"null","asset_id":"null"},"special": {}}').','.
$db->Quote('OkeydocHelperRoute::getDocumentRoute'));
      $db->setQuery($query);
      $db->query();

      //Informations about the Okey DOC category items.
      $query->clear();
      $query->insert('#__content_types');
      $query->columns($columns);
      $query->values($db->Quote('Okey DOC Category').','.$db->Quote('com_okeydoc.category').','.
$db->Quote('{"special"{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common"{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}').','.
$db->Quote('{"common"{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"introtext","core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access","core_params":"params","core_featured":"null","core_metadata":"metadata","core_language":"language","core_images":"null","core_urls":"null","core_version":"version","core_ordering":"null","core_metakey":"metakey","core_metadesc":"metadesc","core_catid":"parent_id","core_xreference":"null","asset_id":"asset_id"},"special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}').','.
$db->Quote('OkeydocHelperRoute::getCategoryRoute'));
      $db->setQuery($query);
      $db->query();
    }

    //Delete the download.php file used by the Okey DOC versions prior to 1.6.
    JFile::delete(JPATH_ROOT.'/okeydoc/download.php');
  }


  /*
   * get a variable from the manifest file (actually, from the manifest cache).
   */
  function getParam($name)
  {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('manifest_cache')
	  ->from('#__extensions')
	  ->where('element = "com_okeydoc"');
    $db->setQuery($query);
    $manifest = json_decode($db->loadResult(), true);

    return $manifest[$name];
  }
}

