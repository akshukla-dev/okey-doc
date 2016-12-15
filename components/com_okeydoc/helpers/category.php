<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

/**
 * Okey DOC Component Category Tree
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_weblinks
 * @since       1.6
 */
class OkeydocCategories extends JCategories
{
  public function __construct($options = array())
  {
    $options['table'] = '#__okeydoc_document';
    $options['extension'] = 'com_okeydoc';

    /* IMPORTANT: By default publish parent function invoke a field called "state" to
     *            publish/unpublish (but also archived, trashed etc...) an item.
     *            Since our field is called "published" we must informed the 
     *            JCategories publish function in setting the "statefield" index of the 
     *            options array
    */
    $options['statefield'] = 'published';

    parent::__construct($options);
  }
}
