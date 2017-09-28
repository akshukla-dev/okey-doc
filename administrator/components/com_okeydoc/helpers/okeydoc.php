<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; //No direct access to this file.



class OkeydocHelper
{
  //Create the sidebar items ($viewName = name of the active view).
  public static function addSubmenu($viewName)
  {
    JHtmlSidebar::addEntry(JText::_('COM_OKEYDOC_SUBMENU_DOCUMENTS'),
				    'index.php?option=com_okeydoc&view=documents', $viewName == 'documents');

    JHtmlSidebar::addEntry(JText::_('COM_OKEYDOC_SUBMENU_FOLDERS'),
				    'index.php?option=com_okeydoc&view=folders', $viewName == 'folders');

    JHtmlSidebar::addEntry(JText::_('COM_OKEYDOC_SUBMENU_CATEGORIES'),
				    'index.php?option=com_categories&extension=com_okeydoc', $viewName == 'categories');
  }


  //Get the list of the allowed actions for the user.
  public static function getActions($categoryId = 0)
  {
    $user = JFactory::getUser();
    $result = new JObject;

    if(empty($categoryId)) {
      //Check permissions against the component.
      $assetName = 'com_okeydoc'; 
    }
    else {
      //Check permissions against the component category.
      $assetName = 'com_okeydoc.category.'.(int) $categoryId; 
    }

    $actions = array('core.admin', 'core.manage', 'core.create', 'core.edit',
		     'core.edit.own', 'core.edit.state', 'core.delete');

    //Get from the core the user's permission for each action.
    foreach($actions as $action) {
      $result->set($action, $user->authorise($action, $assetName));
    }

    return $result;
  }



  //Return categories in which the user is allowed to do a given action. ("create" by default).
  public static function getUserCategories($action = 'create', $documents = false)
  {
    $subquery = '';
    if($documents) {
      //Get the number of document items linked to each category.
      $subquery = ',(SELECT COUNT(*) FROM #__okeydoc_document WHERE catid=c.id) AS documents';
    }

    //Get the component categories.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('c.id, c.level, c.parent_id, c.title'.$subquery);
    $query->from('#__categories AS c');
    $query->where('extension="com_okeydoc"');
    $query->order('c.lft ASC');
    $db->setQuery($query);
    $categories = $db->loadObjectList();

    $userCategories = array();

    if($categories) {
      foreach($categories as $category) {
	//Get the list of the actions allowed for the user on this category.
	$canDo = OkeydocHelper::getActions($category->id);

	if($canDo->get('core.'.$action)) {
	  $userCategories[] = $category;
	  //$userCategories[] = array('id' => $category->id, 'title' => $category->title);
	}
      }
    }

    return $userCategories;
  }


  //Load file on the server and return an array filled with the data file.
  public static function uploadFile($catid)
  {
    //Array to store the file data. Set an error index for a possible error message.
    $document = array('error' => '');

    //Get the name and the id of the destination folder.
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('f.title, m.folder_id');
    $query->from('#__okeydoc_folder AS f');
    $query->join('LEFT', '#__okeydoc_folder_map AS m ON m.catid='.(int)$catid);
    $query->where('f.id=m.folder_id');
    $db->setQuery($query);
    $destFolder = $db->loadObject();

    $jinput = JFactory::getApplication()->input;
    $files = $jinput->files->get('jform');
    $files = $files['uploaded_file'];

    //Get the component parameters:
    $params = JComponentHelper::getParams('com_okeydoc');
    //- The allowed extensions table
    $allowedExt = explode(';', $params->get('allowed_extensions'));
    // - The available extension icons table
    $iconsExt = explode(';', $params->get('extensions_list'));
    //- Allow or not all types of file. 
    $allFiles = $params->get('all_files');
    //- The authorised file size (in megabyte) for upload. 
    $maxFileSize = $params->get('max_file_size');
    //Convert in byte. 
    $maxFileSize = $maxFileSize * 1048576;

    //Check if the file exists and if no error occurs.
    if($files['error'] == 0) {
      //Get the file extension and convert it to lowercase.
      $ext = strtolower(JFile::getExt($files['name']));

      //Check if the extension is allowed.
      if(!in_array($ext, $allowedExt) && !$allFiles) {
	$document['error'] = 'COM_OKEYDOC_EXTENSION_NOT_ALLOWED';
	return $document;
      }

      //Check the size of the file.
      if($files['size'] > $maxFileSize) {
	$document['error'] = 'COM_OKEYDOC_FILE_SIZE_TOO_LARGE';
	return $document;
      }

      $count = 1;
      while($count > 0) {
	//Create an unique id for this file.
	$file = uniqid();
	$file = $file.'.'.$ext;

	//To ensure it is unique check against the database.
	//If the id is not unique the loop goes on and a new id is generated.
	$query->clear();
	$query->select('COUNT(*)');
	$query->from('#__okeydoc_document');
	$query->where('file='.$db->Quote($file));
	$db->setQuery($query);
	$count = (int)$db->loadResult();
      }

      //Get the file name without its extension.
      preg_match('#(.+)\.[a-zA-Z0-9\#?!$~@()-_]{1,}$#', $files['name'], $matches);
      $fileName = $matches[1];

      //Sanitize the file name which will be used for downloading, (see stringURLSafe function for details).
      $fileName = JFilterOutput::stringURLSafe($fileName);

      //Note: So far the document root directory is unchangeable but who knows in a futur version..
      $docRootDir = 'okeydoc';

      //Create a table containing all data about the file.
      $document['file'] = $file;
      $document['file_name'] = $fileName.'.'.$ext;
      $document['file_type'] = $files['type'];
      $document['file_size'] = $files['size'];
      $document['folder_id'] = $destFolder->folder_id; //id of the folder which will contain the file.
      //Build the file path.
      $document['file_path'] = $docRootDir.'/'.$destFolder->title;

      //To obtain the appropriate icon file name, we get the file extension then we concatenate it with .gif.
      //If the extension doesn't have any appropriate extension icon, we display the generic icon.
      if(!in_array($ext, $iconsExt)) {
	$document['file_icon'] = 'generic.gif';
      }
      else {
	$document['file_icon'] = $ext.'.gif';
      }

      //Move the file on the server.
      if(!JFile::upload($files['tmp_name'], JPATH_ROOT.'/'.$docRootDir.'/'.$destFolder->title.'/'.$document['file'])) {
	$document['error'] = 'COM_OKEYDOC_FILE_TRANSFER_ERROR';
	return $document;
      }

      //File transfert has been successful.
      return $document;
    }
    else { //The upload of the file has failed.
      //Return the error which has occured.
      switch ($files['error']) { 
        case 1:
	  $document['error'] = 'COM_OKEYDOC_FILES_ERROR_1';
	  break;
	case 2:
	  $document['error'] = 'COM_OKEYDOC_FILES_ERROR_2';
	  break;
	case 3:
	  $document['error'] = 'COM_OKEYDOC_FILES_ERROR_3';
	  break;
	case 4:
	  $document['error'] = 'COM_OKEYDOC_FILES_ERROR_4';
	  break;
      }

      return $document;
    }
  }


  //Convert the number of bytes to kilo or mega bytes.
  public static function byteConverter($nbBytes)
  {
    $conversion = array();

    if($nbBytes > 1023 && $nbBytes < 1048576) {  //Convert to kilobyte.
      $result = $nbBytes / 1024;
      $conversion['result'] = round($result, 2);
      $conversion['multiple'] = 'KILOBYTE';
    }
    elseif($nbBytes > 1048575) { //Convert to megabyte.
      $result = $nbBytes / 1048576;
      $conversion['result'] = round($result, 2);
      $conversion['multiple'] = 'MEGABYTE';
    }
    else { //No convertion.
      $conversion['result'] = $nbBytes;
      $conversion['multiple'] = 'BYTE';
    }

    return $conversion;
  }
}


