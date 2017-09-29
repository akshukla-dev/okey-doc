<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

require_once (JPATH_SITE.'/components/com_okeydoc/helpers/route.php');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

// Create shortcuts to some parameters.
JHtml::_('behavior.caption');

//Create a JView object to get the escape() function.
$jview = new JViewLegacy;

//Include the css file.
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base().'modules/mod_okeydoc/mod-okeydoc.css');
?>

<table class="category table-striped">
  <thead>
  <tr>
    <th id="categorylist_header_title">
      <?php echo JText::_('JGLOBAL_TITLE'); ?>
    </th>
    <th width="15%">
      <?php echo JText::_('MOD_OKEYDOC_HEADING_AUTHOR'); ?>
    </th>
    <th id="categorylist_header_date">
      <?php echo JText::_('MOD_OKEYDOC_HEADING_CREATED_DATE'); ?>
    </th>
    <th width="1%">
      <?php echo JText::_('MOD_OKEYDOC_HEADING_DOWNLOADS'); ?>
    </th>
    <th>
    </th>
  </tr>
  </thead>

  <tbody>

  <?php foreach($items as $i => $item) : ?>
    <tr class="row<?php echo $i % 2; ?>">
      <td>
      <?php  //Build the link to the login page for the user to login or register.
	    if(!$item->params->get('access-view')) : 
	      $menu = JFactory::getApplication()->getMenu();
	      $active = $menu->getActive();
	      $itemId = $active->id;
	      $link1 = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
	      $returnURL = JRoute::_(OkeydocHelperRoute::getDocumentRoute($item->slug, $item->catid));
	      $link = new JUri($link1);
	      $link->setVar('return', base64_encode($returnURL));
	    endif; ?>

      <?php if($item->params->get('access-view')) : //Set the link to the document page.
	    $link = JRoute::_(OkeydocHelperRoute::getDocumentRoute($item->slug, $item->catid));
	endif; ?>
	<a href="<?php echo $link;?>"><?php echo $jview->escape($item->title); ?></a>
	</td>
	<td>
	  <?php echo $jview->escape($item->author); ?>
	</td>
	<td>
	  <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?>
	</td>
	<td class="center">
	  <?php echo (int)$item->downloads; ?>
        </td><td>	  

      <?php if($item->params->get('access-view')) : //Set the link to download the document.
	      $uri = JUri::getInstance();
	      $link = $uri->root().'components/com_okeydoc/download/script.php?id='.$item->id;
	      $target = 'target="blank"'; //Open the document in a different tab.
	    endif; ?>

	  <a href="<?php echo $link; ?>" <?php echo $target; ?>><?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?></a>
	</td></tr>
  <?php endforeach; ?>
</table>
