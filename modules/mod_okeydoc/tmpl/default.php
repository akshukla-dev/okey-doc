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

<?php foreach($items as $i => $item) : ?>
  <div class="document-item">
    <div class="document-general">

      <?php echo JLayoutHelper::render('document_title', $item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>

      <?php if($item->params->get('show_author')) : ?>
	<div class="author">
	  <div class="author-label">
	    <?php echo JText::_('COM_OKEYDOC_FIELD_AUTHOR_LABEL'); ?>
	  </div>
	  <div class="value">
	    <?php echo $jview->escape($item->author); ?>
	  </div>
	</div>
      <?php endif; ?>

	<div class="introtext">
	    <?php echo $item->introtext; ?>
	</div>

	<?php if($item->params->get('show_tags', 1) && !empty($item->tags->itemTags)) : ?>
		<?php $item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
		<?php echo $item->tagLayout->render($item->tags->itemTags); ?>
	<?php endif; ?>

	<?php  //Build the link to the login page for the user to login or register.
	      if(!$item->params->get('access-view')) : 
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
		$returnURL = JRoute::_(OkeydocHelperRoute::getDocumentRoute($item->slug, $item->catid));
		$link = new JUri($link1);
		$link->setVar('return', base64_encode($returnURL));
		$target = '';
	      endif; ?>

	<?php if($item->params->get('show_complete_details')) :
		if($item->params->get('access-view')) : //Set the link to the document.
		  $link = JRoute::_(OkeydocHelperRoute::getDocumentRoute($item->slug, $item->catid));
	      endif; ?>

		<p class="complete-details"><a class="btn" href="<?php echo $link; ?>"> <span class="icon-chevron-right"></span>
		  <?php echo JText::_('COM_OKEYDOC_COMPLETE_DETAILS'); ?>
		</a></p>
	<?php endif; ?>

	<?php if($item->params->get('access-view')) : //Set the link to download the document.
		$uri = JUri::getInstance();
		$link = $uri->root().'components/com_okeydoc/download/script.php?id='.$item->id;
		$target = 'target="blank"'; //Open the document in a different tab.
	      endif; ?>

	<p class="download-button"><a href="<?php echo $link; ?>" class="btn btn-success" <?php echo $target; ?>>
	  <span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?>
	</a></p>

    </div>
    <div class="document-details">
      <?php echo JLayoutHelper::render('document_details', $item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>
    </div>
  </div>
<?php endforeach; ?>
