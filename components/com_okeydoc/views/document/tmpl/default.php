<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

// Create shortcuts to some parameters.
$params = $this->item->params;
$item = $this->item;
?>

<div class="document-page <?php echo $this->pageclass_sfx; ?>">
  <?php if ($item->params->get('show_page_heading', 1)) : ?>
    <div class="page-header">
      <h1>
	<?php echo $this->escape($params->get('page_heading')); ?>
      </h1>
    </div>
  <?php endif; ?>

  <div class="document-general span8">
    <?php echo JLayoutHelper::render('document_title', $item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>

    <?php if($item->params->get('show_author')) : ?>
      <div class="author">
	<div class="author-label">
	  <?php echo JText::_('COM_OKEYDOC_FIELD_AUTHOR_LABEL'); ?>
	</div>
	<div class="value">
	  <?php echo $this->escape($item->author); ?>
	</div>
      </div>
    <?php endif; ?>

      <div class="introtext">
	  <?php if($item->params->get('show_introtext')) : ?>
	    <?php echo $item->introtext; ?>
	  <?php endif; ?>

	  <?php if(!empty($item->fulltext)) : ?>
	    <?php echo $item->fulltext; ?>
	  <?php endif; ?>
      </div>

    <?php if($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	    <?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	    <?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
    <?php endif; ?>

    <p class="download-button"><a href="<?php echo $item->uri->root().'components/com_okeydoc/download/script.php?id='.$item->id; ?>" class="btn btn-success" target="_blank">
      <span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?>
    </a></p>
  </div>

  <div class="document-details span4">
    <?php echo JLayoutHelper::render('document_edit', $item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>
    <?php echo JLayoutHelper::render('document_details', $item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>
  </div>
</div>
