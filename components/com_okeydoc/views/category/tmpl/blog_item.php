<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;
JHtml::_('behavior.framework');

$span = 'span8';
if(!$this->item->params->get('show_details')) {
  $span = 'span12';
}
?>

<div class="document-item">
  <div class="document-general <?php echo $span; ?>">
    <?php echo JLayoutHelper::render('document_title', $this->item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>

    <?php if($this->item->params->get('show_edit_button')) : ?>
      <?php echo JLayoutHelper::render('document_edit', $this->item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>
    <?php endif; ?>

    <?php if($this->item->params->get('show_author')) : ?>
      <div class="author">
	<div class="author-label">
	  <?php echo JText::_('COM_OKEYDOC_FIELD_AUTHOR_LABEL'); ?>
	</div>
	<div class="value">
	  <?php echo $this->escape($this->item->author); ?>
	</div>
      </div>
    <?php endif; ?>

      <div class="introtext">
	  <?php echo $this->item->introtext; ?>
      </div>

    <?php if($this->params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	    <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	    <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
    <?php endif; ?>

    <?php if($this->item->params->get('show_document_page_button') || $this->item->params->get('show_download_button')) : ?>
      <?php  //Build the link to the login page for the user to login or register.
	    if(!$this->item->params->get('access-view')) : 
	      $menu = JFactory::getApplication()->getMenu();
	      $active = $menu->getActive();
	      $itemId = $active->id;
	      $link1 = JRoute::_('index.php?option=com_users&view=login&Itemid='.$itemId);
	      $returnURL = JRoute::_(OkeydocHelperRoute::getDocumentRoute($this->item->slug, $this->item->catid));
	      $link = new JUri($link1);
	      $link->setVar('return', base64_encode($returnURL));
	      $target = '';
	    endif; ?>

      <?php if($this->item->params->get('show_document_page_button')) : ?>
	<?php if($this->item->params->get('show_complete_details')) :
		if($this->item->params->get('access-view')) : //Set the link to the document.
		  $link = JRoute::_(OkeydocHelperRoute::getDocumentRoute($this->item->slug, $this->item->catid));
	      endif; ?>

		<p class="complete-details"><a class="btn" href="<?php echo $link; ?>"> <span class="icon-chevron-right"></span>
		  <?php echo JText::_('COM_OKEYDOC_COMPLETE_DETAILS'); ?>
		</a></p>
	<?php endif; ?>
      <?php endif; ?>

      <?php if($this->item->params->get('show_download_button')) : ?>
	<?php if($this->item->params->get('access-view')) : //Set the link to download the document.
		$link = $this->item->uri->base().'components/com_okeydoc/download/script.php?id='.$this->item->id;
		$target = 'target="blank"'; //Open the document in a different tab.
	      endif; ?>

	<p class="download-button"><a href="<?php echo $link; ?>" class="btn btn-success" <?php echo $target; ?>>
	  <span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?>
	</a></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>

  <?php if($this->item->params->get('show_details')) : ?>
    <div class="document-details span4">
      <?php echo JLayoutHelper::render('document_details', $this->item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>
    </div>
  <?php endif; ?>
</div>

