<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

JHtml::_('behavior.framework');

// Create a shortcut for params.
$params = $displayData->params;
?>

<?php if($params->get('show_title') || $displayData->published == 0 || ($params->get('show_author') && !empty($displayData->author))) : ?>
  <div class="page-header">

    <?php if($params->get('show_extension_icon')) : ?>
      <img src="media/com_okeydoc/extensions/<?php echo $displayData->file_icon; ?>" class="file-icon" 
	   alt="<?php echo $displayData->file_icon; ?>" width="16" height="16" />
    <?php endif; ?>

    <?php if($params->get('show_title')) : ?>
	    <h2>
	      <?php if($params->get('link_title') && $params->get('access-view')) : ?>
		<a href="<?php echo JRoute::_(OkeydocHelperRoute::getDocumentRoute($displayData->slug, $displayData->catid)); ?>">
		      <?php echo $this->escape($displayData->title); ?></a>
	      <?php else : ?>
		<?php echo $this->escape($displayData->title); ?>
	      <?php endif; ?>
	    </h2>
    <?php endif; ?>

    <?php if ($displayData->published == 0) : ?>
	    <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
    <?php endif; ?>
    <?php if (strtotime($displayData->publish_up) > strtotime(JFactory::getDate())) : ?>
	    <span class="label label-warning"><?php echo JText::_('JNOTPUBLISHEDYET'); ?></span>
    <?php endif; ?>
    <?php if ((strtotime($displayData->publish_down) < strtotime(JFactory::getDate())) && $displayData->publish_down != '0000-00-00 00:00:00') : ?>
	    <span class="label label-warning"><?php echo JText::_('JEXPIRED'); ?></span>
    <?php endif; ?>
  </div>
<?php endif; ?>
