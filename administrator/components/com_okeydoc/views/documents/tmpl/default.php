<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined( '_JEXEC' ) or die; // No direct access

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');


$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == -2 ? true : false;
$canOrder = $user->authorise('core.edit.state', 'com_okeydoc.category');
$saveOrder = $listOrder == 'd.ordering';

if($saveOrder) {
  $saveOrderingUrl = 'index.php?option=com_okeydoc&task=documents.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'documentList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

?>

<script type="text/javascript">
Joomla.orderTable = function()
{
  table = document.getElementById("sortTable");
  direction = document.getElementById("directionTable");
  order = table.options[table.selectedIndex].value;

  if(order != '<?php echo $listOrder; ?>') {
    dirn = 'asc';
  }
  else {
    dirn = direction.options[direction.selectedIndex].value;
  }

  Joomla.tableOrdering(order, dirn, '');
}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_okeydoc&view=documents');?>" method="post" name="adminForm" id="adminForm">

<?php if(!empty($this->sidebar)) : ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
<?php else : ?>
  <div id="j-main-container">
<?php endif; //Note: The 2 divs above are closed by the system. ?>

<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); // Search tools bar ?>

  <div class="clr"> </div>
  <?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
	  <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
  <?php else : ?>
  <table class="table table-striped" id="documentList">
    <thead>
    <tr>
      <th width="1%" class="nowrap center hidden-phone">
      <?php echo JHtml::_('searchtools.sort', '', 'd.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
      </th>
      <th width="1%" class="hidden-phone">
      <?php echo JHtml::_('grid.checkall'); ?>
      </th>
      <th width="5%">
      <?php echo JHtml::_('searchtools.sort', 'JPUBLISHED', 'd.published', $listDirn, $listOrder); ?>
      </th>
      <th>
      <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'd.title', $listDirn, $listOrder); ?>
      </th>
      <th width="5%">
      <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'd.access', $listDirn, $listOrder); ?>
      </th>
      <th width="15%">
      <?php echo JHtml::_('searchtools.sort', 'COM_OKEYDOC_HEADING_AUTHOR_LABEL', 'd.author', $listDirn, $listOrder); ?>
      </th>
      <th width="5%">
      <?php echo JHtml::_('searchtools.sort', 'COM_OKEYDOC_HEADING_DOWNLOADS', 'd.downloads', $listDirn, $listOrder); ?>
      </th>
      <th width="5%">
      <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'd.language', $listDirn, $listOrder); ?>
      </th>
      <th width="10%">
      <?php echo JHtml::_('searchtools.sort', 'JDATE', 'd.created', $listDirn, $listOrder); ?>
      </th>
      <th width="1%">
      <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'd.id', $listDirn, $listOrder); ?>
      </th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($this->items as $i => $item) :

    $ordering = ($listOrder == 'd.ordering');
    $canEdit= $user->authorise('core.edit','com_okeydoc.document.'.$item->id);
    $canEditOwn = $user->authorise('core.edit.own', 'com_okeydoc.document.'.$item->id) && $item->created_by == $userId;
    $canCheckin= $user->authorise('core.manage','com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
    $canChange = ($user->authorise('core.edit.state','com_okeydoc.document.'.$item->id) && $canCheckin) || $canEditOwn;
    ?>
      <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
	<td class="order nowrap center hidden-phone">
	  <?php
	  $iconClass = '';
	  if(!$canChange)
	  {
	    $iconClass = ' inactive';
	  }
	  elseif(!$saveOrder)
	  {
	    $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
	  }
	  ?>
	  <span class="sortable-handler<?php echo $iconClass ?>">
		  <i class="icon-menu"></i>
	  </span>
	  <?php if($canChange && $saveOrder) : ?>
	      <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
	  <?php endif; ?>
	  </td>
	  <td class="center hidden-phone">
		  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
	  </td>
	  <td class="center">
	    <div class="btn-group">
	      <?php echo JHtml::_('jgrid.published', $item->published, $i, 'documents.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
	      <?php
	      // Create dropdown items
	      $action = $archived ? 'unarchive' : 'archive';
	      JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'documents');

	      $action = $trashed ? 'untrash' : 'trash';
	      JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'documents');

	      // Render dropdown list
	      echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
	      ?>
	    </div>
	  </td>
	  <td>
	  <?php if ($item->checked_out) : ?>
	    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'documents.', $canCheckin); ?>
	  <?php endif; ?>

	  <?php if($canEdit || $canEditOwn) : ?>
	    <a href="<?php echo JRoute::_('index.php?option=com_okeydoc&task=document.edit&id='.$item->id);?>">
		    <?php echo $this->escape($item->title); ?></a>
	  <?php else : ?>
	    <?php echo $this->escape($item->title); ?>
	  <?php endif; ?>
	      <span class="small">
		<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
	      </span>
	      <div class="small">
		<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
	      </div>
            </td>
	    <td class="nowrap small hidden-phone">
	      <?php echo $this->escape($item->access_level); ?>
	    </td>
	    <td class="nowrap small hidden-phone">
	      <?php echo $this->escape($item->author); ?>
	    </td>
	    <td class="center">
	      <?php echo (int) $item->downloads; ?>
	    </td>
	    <td class="small hidden-phone">
	      <?php if ($item->language == '*'):?>
		      <?php echo JText::alt('JALL', 'language'); ?>
	      <?php else:?>
		      <?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
	      <?php endif;?>
	    </td>
	    <td class="nowrap small hidden-phone">
	      <?php echo JHTML::_('date',$item->created, JText::_('DATE_FORMAT_LC4')); ?>
	    </td>
	    <td class="center">
	      <?php echo (int) $item->id; ?>
	    </td></tr>

    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>

  <?php echo $this->pagination->getListFooter(); ?>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_okeydoc" />
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>

