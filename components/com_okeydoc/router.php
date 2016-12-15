<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */



defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Build the route for the com_okeydoc component
 *
 * @param	array	An array of URL arguments
 *
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 */
function OkeydocBuildRoute(&$query)
{
  $segments = array();

  if(isset($query['view'])) {
    $segments[] = $query['view'];
    unset($query['view']);
  }

  if(isset($query['id'])) {
    $segments[] = $query['id'];
    unset($query['id']);
  }

  if(isset($query['catid'])) {
    $segments[] = $query['catid'];
    unset($query['catid']);
  }

  if(isset($query['layout'])) {
    unset($query['layout']);
  }

  return $segments;
}


/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 */
function OkeydocParseRoute($segments)
{
  $vars = array();

  switch($segments[0])
  {
    case 'categories':
	   $vars['view'] = 'categories';
	   break;
    case 'category':
	   $vars['view'] = 'category';
	   $id = explode(':', $segments[1]);
	   $vars['id'] = (int)$id[0];
	   break;
    case 'document':
	   $vars['view'] = 'document';
	   $id = explode(':', $segments[1]);
	   $vars['id'] = (int)$id[0];
	   $catid = explode(':', $segments[2]);
	   $vars['catid'] = (int)$catid[0];
	   break;
    case 'form':
	   $vars['view'] = 'form';
	   //Form layout is always set to 'edit'.
	   $vars['layout'] = 'edit';
	   break;
  }

  return $vars;
}

