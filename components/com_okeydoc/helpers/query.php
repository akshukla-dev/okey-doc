<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

/**
 * Okey DOC Component Query Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
class OkeydocHelperQuery
{
  /**
   * Translate an order code to a field for primary category ordering.
   *
   * @param   string	$orderby	The ordering code.
   *
   * @return  string	The SQL field(s) to order by.
   * @since   1.5
   */
  public static function orderbyPrimary($orderby)
  {
    switch ($orderby)
    {
      case 'alpha' :
	      $orderby = 'c.path, ';
	      break;

      case 'ralpha' :
	      $orderby = 'c.path DESC, ';
	      break;

      case 'order' :
	      $orderby = 'c.lft, ';
	      break;

      default :
	      $orderby = '';
	      break;
    }

    return $orderby;
  }

  /**
   * Translate an order code to a field for secondary category ordering.
   *
   * @param   string	$orderby	The ordering code.
   * @param   string	$orderDate	The ordering code for the date.
   *
   * @return  string	The SQL field(s) to order by.
   * @since   1.5
   */
  public static function orderbySecondary($orderby, $orderDate = 'created')
  {
    $queryDate = self::getQueryDate($orderDate);

    switch ($orderby)
    {
      case 'date' :
	      $orderby = $queryDate;
	      break;

      case 'rdate' :
	      $orderby = $queryDate.' DESC ';
	      break;

      case 'alpha' :
	      $orderby = 'd.title';
	      break;

      case 'ralpha' :
	      $orderby = 'd.title DESC';
	      break;

      case 'downloads' :
	      $orderby = 'd.downloads';
	      break;

      case 'rdownloads' :
	      $orderby = 'd.downloads DESC';
	      break;

      case 'order' :
	      $orderby = 'd.ordering';
	      break;

      case 'rorder' :
	      $orderby = 'd.ordering DESC';
	      break;

      case 'author' :
	      $orderby = 'd.author';
	      break;

      case 'rauthor' :
	      $orderby = 'd.author DESC';
	      break;

      default :
	      $orderby = 'd.ordering';
	      break;
    }

    return $orderby;
  }

  /**
   * Translate an order code to a field for primary category ordering.
   *
   * @param   string	$orderDate	The ordering code.
   *
   * @return  string	The SQL field(s) to order by.
   * @since   1.6
   */
  public static function getQueryDate($orderDate)
  {
    $db = JFactory::getDbo();

    switch($orderDate) {
      case 'modified' :
	      $queryDate = ' CASE WHEN d.modified = '.$db->quote($db->getNullDate()).' THEN d.created ELSE d.modified END';
	      break;

      // use created if publish_up is not set
      case 'published' :
	      $queryDate = ' CASE WHEN d.publish_up = '.$db->quote($db->getNullDate()).' THEN d.created ELSE d.publish_up END ';
	      break;

      case 'created' :
      default :
	      $queryDate = ' d.created ';
	      break;
    }

    return $queryDate;
  }
}

