<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined('_JEXEC') or die; // No direct access

JHtml::_('behavior.tooltip');
JHtml::_('behavior.tabstate');
JHtml::_('behavior.formvalidation');

//Get the name of the operating system.
$OS = strtoupper(PHP_OS);

$isSymlink = false;
//Check if we're dealing with a symbolic link item.
if($this->form->getValue('id') != 0 && $this->form->getValue('symlink_path') != '') {
  $this->form->setValue('is_symlink', null, 1);
  $isSymlink = true;
}
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
  if(task == 'folder.cancel' || document.formvalidator.isValid(document.id('folder-form'))) {
    Joomla.submitform(task, document.getElementById('folder-form'));
  }
  else {
    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
  }
}
</script>


<form action="<?php echo JRoute::_('index.php?option=com_okeydoc&view=folder&layout=edit&id='.(int) $this->item->id); ?>" 
      method="post" name="adminForm" id="folder-form" class="form-validate">

 <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

  <div class="form-horizontal">

      <?php echo JHtml::_('bootstrap.startTabSet', 'folder', array('active' => 'details')); ?>

      <?php echo JHtml::_('bootstrap.addTab', 'folder', 'details', JText::_('COM_OKEYDOC_TAB_DETAILS', true)); ?>
	<div class="row-fluid">
	  <div class="span4">
	    <div class="form-vertical">
	      <?php
	            //Check the OS we're on.
	            if($OS == 'LINUX') {
		      //In case of a new item we display the symbolic link fields.
		      if($this->form->getValue('id') == 0) {
			echo $this->form->getControlGroup('symlink_option');
			echo $this->form->getControlGroup('symlink_path');
		      }

		      //The item exists and use a symbolic link as folder.
		      if($isSymlink) {
			echo $this->form->getControlGroup('symlink_path');
		      }
		    }

		    echo $this->form->getControlGroup('files');
		    echo $this->form->getInput('is_symlink');
	      ?>
	      <?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
	    </div>
	  </div>
	</div>
      <?php echo JHtml::_('bootstrap.endTab'); ?>

      <?php echo JHtml::_('bootstrap.addTab', 'folder', 'categories', JText::_('COM_OKEYDOC_FIELD_DMS_CATEGORIES', true)); ?>
      <div class="row-fluid form-horizontal-desktop">
	  <?php echo JLayoutHelper::render('category_folder_map_table', $this->item, JPATH_SITE.'/components/com_okeydoc/layouts/'); ?>
      </div>
      <?php echo JHtml::_('bootstrap.endTab'); ?>
  </div>


  <input type="hidden" name="os" id="operating_system" value="<?php echo $OS; ?>" />
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>

<?php
$doc = JFactory::getDocument();
//Load the jQuery script(s).
$doc->addScript(JURI::base().'components/com_okeydoc/js/folder.js');

