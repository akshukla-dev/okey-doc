<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

// Create a shortcut for params.
$params = $displayData->params;
JHtml::_('behavior.framework');
//Set the publish_up date.
if($displayData->publish_up == 0) {
  $displayData->publish_up = $displayData->created;
}
?>

  <table class="table table-condensed">
    <?php if($params->get('show_file_name')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_FILE_NAME'); ?></td>
      <td><?php echo $this->escape($displayData->file_name); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_file_size')) :
	    require_once JPATH_ADMINISTRATOR.'/components/com_okeydoc/helpers/okeydoc.php';
	    $conversion = OkeydocHelper::byteConverter($displayData->file_size); ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_FILE_SIZE'); ?></td>
      <td><?php echo JText::sprintf('COM_OKEYDOC_BYTE_CONVERTER_'.$conversion['multiple'], $conversion['result']); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_file_type')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_FILE_TYPE'); ?></td>
      <td><?php echo $this->escape($displayData->file_type); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_create_date')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_CREATE_DATE'); ?></td>
      <td><?php echo JHTML::_('date',$displayData->created, JText::_('DATE_FORMAT_LC3')); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_modify_date') && $displayData->modified !== '0000-00-00 00:00:00') : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_MODIFY_DATE'); ?></td>
      <td><?php echo JHTML::_('date',$displayData->modified, JText::_('DATE_FORMAT_LC3')); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_publish_date')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_PUBLISH_DATE'); ?></td>
      <td><?php echo JHTML::_('date',$displayData->publish_up, JText::_('DATE_FORMAT_LC3')); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_hits')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_HITS'); ?></td>
      <td><?php echo $displayData->hits; ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_downloads')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_DOWNLOADS'); ?></td>
      <td><?php echo $displayData->downloads; ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_put_online_by')) : ?>
      <tr><td class="detail-label"><?php echo JText::_('COM_OKEYDOC_DETAILS_PUT_ONLINE_BY'); ?></td>
      <td><?php echo $this->escape($displayData->put_online_by); ?></td></tr>
    <?php endif; ?>
    <?php if($params->get('show_category')) : ?>
	    <tr><td class="detail-label"><?php echo JText::_('JCATEGORY'); ?></td><td>
	    <?php $title = $this->escape($displayData->category_title);
		  if($params->get('link_category') && $displayData->catslug) { 
		    echo '<a href="'.JRoute::_(OkeydocHelperRoute::getCategoryRoute($displayData->catslug)).'" itemprop="genre">'.$title.'</a>';
		  }
		  else {
		   echo $title; 
		  } ?>
	    </td></tr>
    <?php endif; ?>
  </table>

