<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


//No direct access
defined('_JEXEC') or die;
 
//Include helpers.
require_once(dirname(__FILE__).'/helper.php'); 
require_once JPATH_SITE.'/components/com_okeydoc/helpers/query.php';

//Create an object in which we gather the needed parameters.
$parameters = new stdClass;
$jinput = JFactory::getApplication()->input;
$parameters->id = $jinput->get('id', 0, 'int');
$parameters->article_id = 0;
$parameters->view = $jinput->get('view', '', 'string');
//In case of multilingual site.
$parameters->tag_lang = JFactory::getLanguage()->getTag();
//Store the modules parameters.
$parameters->params = $params;
$parameters->frontpage = $frontpage = false;

//When module is loaded by the okeydoc plugin the id of the article is 
//passed in the attribs parameter.
if(isset($attribs['article_id'])) {
  $parameters->article_id = (int)$attribs['article_id'];
}

//Get the linked documents.
$items = ModOkeydoc::getLinkedDocuments($parameters);

$app = JFactory::getApplication();
$menu = $app->getMenu();
//Detect if the frontpage is the current page.
if($menu->getActive() == $menu->getDefault($parameters->tag_lang)) {
  $parameters->frontpage = $frontpage = true;
}

//Display the layout only if a document has been found.
if(!is_null($items)) {
  //Get the selected layout.
  $layout = $params->get('document_layout');

  foreach($items as $i => $item) {
    //Compute the needed slugs.
    $item->slug = $item->alias ? ($item->id.':'.$item->alias) : $item->id;
    $item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;

    //Here we use the module params not the item params as it would be expected.
    $item->params = $params;

    //Documents are displayed even for the unauthorised users.
    if($item->params->get('show_noauth')) {
      //Get the user's access view.
      $user = JFactory::getUser();
      //The layout takes some responsibility for display of limited information.
      $item->params->set('access-view', in_array($item->access, $user->getAuthorisedViewLevels()));
    }
    else {
      //We already have only the documents this user can view.
      $item->params->set('access-view', true);
    }

    // Get the tags
    $item->tags = new JHelperTags;
    $item->tags->getItemTags('com_okeydoc.document', $item->id);

    //Users cannot edit a document from this module.
    $item->params->set('access-edit', false);
  }

  require(JModuleHelper::getLayoutPath('mod_okeydoc', $layout));
}

