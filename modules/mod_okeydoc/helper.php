<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die;
jimport('joomla.application.component.helper');


class ModOkeydoc
{
  public static function getLinkedDocuments($parameters)
  {
    //Safe this function.
    if($parameters->id == 0 || empty($parameters->view)) {
      return null;
    }

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    //Get the document ids linked to the category or article.
    $query->select('doc_id')
	  ->from('#__okeydoc_doc_map');
	  //Module is loaded by the okeydoc plugin.
	  if($parameters->article_id) {
	    $query->where('item_type='.$db->Quote('article').' AND item_id='.(int)$parameters->article_id);
	  }
	  //Normal module loading.
	  else {
	    $query->where('item_type='.$db->Quote($parameters->view).' AND item_id='.(int)$parameters->id);
	  }

    $db->setQuery($query);
    $docIds = $db->loadColumn();

    //Some documents have been found.
    if(!empty($docIds)) {
      //Implode array for the IN SQL clause.
      $ids = implode(',', $docIds);
      //Get the user's access view.
      $user = JFactory::getUser();
      $groups = implode(',', $user->getAuthorisedViewLevels());

      $query->clear();
      //Get the document linked to the category or article.
      $query->select('d.*')
	    ->from($db->quoteName('#__okeydoc_document').' AS d');

      // Join over the users.
      $query->select('u.name AS put_online_by')
	    ->join('LEFT', '#__users AS u ON u.id = d.created_by');

      //Join over the component category.
      $query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias')
	    ->join('LEFT', '#__categories AS c ON c.id = d.catid');

      // Filter by start and end dates.
      $nullDate = $db->quote($db->getNullDate());
      $date = JFactory::getDate();
      $nowDate = $db->quote($date->toSql());

      //Do not show expired documents.
      $query->where('(d.publish_up = '.$nullDate.' OR d.publish_up <= '.$nowDate.')')
	    ->where('(d.publish_down = '.$nullDate.' OR d.publish_down >= '.$nowDate.')');

      // Filter by language
      if(ModOkeydoc::isSiteMultilingual()) {
	$query->where($where.' AND d.language IN ('.$db->Quote($parameters->tag_lang).','.$db->Quote('*').')');
      }

      //Get the linked documents.
      $query->where('d.id IN ('.$ids.')');
      //Only shows the published documents. 
      $query->where('d.published=1');

      if(!$parameters->params->get('show_noauth')) {
        //Display linked documents only for authorised users.
	$query->where('d.access IN ('.$groups.')');
      }

      //Get the field to use in the ORDER BY clause according to the orderby_sec option.
      $orderBy = OkeydocHelperQuery::orderbySecondary($parameters->params->get('order_by'), $parameters->params->get('order_date'));
      $query->order($orderBy);

      $db->setQuery($query);

      return $db->loadObjectList();
    }

    return null;
  }


  //If the system languagefilter plugin is enabled we assume that the site is
  //multilingual.
  public static function isSiteMultilingual()
  {
    if(JPluginHelper::isEnabled('system', 'languagefilter')) {
      return true;
    }

    return false;
  }
}


