<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


defined( '_JEXEC' ) or die; // No direct access

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tabstate');
JHtml::_('formbehavior.chosen', 'select');

//Prevent loading fields and fielsets twice through the JLayoutHelper::render('joomla.edit.params', $this) function.
$this->ignore_fieldsets = array('permissions', 'details', 'jmetadata');

$canDo = OkeydocHelper::getActions();
$uri = JUri::getInstance();
?>

<script type="text/javascript">
Joomla.submitbutton = function(task)
{
  if(task == 'document.cancel' || document.formvalidator.isValid(document.id('document-form'))) {
    Joomla.submitform(task, document.getElementById('document-form'));
  }
  else {
    alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
  }
}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_okeydoc&view=document&layout=edit&id='.(int)$this->item->id); ?>" 
 method="post" name="adminForm" id="document-form" enctype="multipart/form-data" class="form-validate">

 <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

  <div class="form-horizontal">

    <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_OKEYDOC_TAB_DETAILS', true)); ?>
      <div class="row-fluid">
	<div class="span9">
	  <div class="form-vertical">
	      <?php if($this->form->getValue('id') != 0) : //Existing item. ?>
		  <div class="control-group">
		    <div class="control-label">
		      <?php echo JText::_('COM_OKEYDOC_FIELD_DOWNLOAD_LABEL'); ?>
		    </div>
		    <div class="controls">
		      <a href="<?php echo $uri->root().'components/com_okeydoc/download/script.php?id='.$this->item->id; ?>" class="btn btn-success" target="_blank">
			<span class="icon-download"></span>&#160;<?php echo JText::_('COM_OKEYDOC_BUTTON_DOWNLOAD'); ?>
		      </a>
		    </div>
		   </div>

		    <?php echo $this->form->getControlGroup('file_name'); ?>
		    <?php //Toggle button which hide/show the link method fields to replace the original file. ?>
		    <a href="#" id="switch_replace" style="margin-bottom:10px;" class="btn">
		      <span id="replace-title"><?php echo JText::_('COM_OKEYDOC_REPLACE'); ?></span>
		      <span id="cancel-title"><?php echo JText::_('JCANCEL'); ?></span></a>
	       <?php endif; ?>

	      <?php
		echo $this->form->getControlGroup('link_method');
		echo $this->form->getControlGroup('uploaded_file');
		echo $this->form->getControlGroup('document_url');
		echo $this->form->getControlGroup('author');
		echo $this->form->getControlGroup('documenttext');
		?>
	  </div>
	</div>

	<div class="span3">
	  <?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
	</div>
      </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'attachment', JText::_('COM_OKEYDOC_TAB_LINK_DOCUMENT', true)); ?>
    <div class="row-fluid form-horizontal-desktop">
      <div class="span6">
	<?php echo $this->form->getControlGroup('contcatids'); ?>
	<?php echo $this->form->getControlGroup('articleids'); ?>
      </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
    <div class="row-fluid form-horizontal-desktop">
      <div class="span6">
	<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
	<?php echo $this->form->getControlGroup('downloads'); ?>
      </div>
      <div class="span6">
	<?php echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
      </div>
    </div>
    <?php echo JHtml::_('bootstrap.endTab'); ?>

    <?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

    <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_OKEYDOC_TAB_RULES', true)); ?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php echo $this->form->getInput('asset_id'); ?>
    <?php echo JHtml::_('bootstrap.endTab'); ?>
  </div>

    <?php if($this->form->getValue('id') != 0) {
	    //Hidden input flag to check if a file replacement is required.
	    echo $this->form->getInput('replace_file');
	  } ?>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<?php

$doc = JFactory::getDocument();
//Load the jQuery script(s).
$doc->addScript(JURI::base().'components/com_okeydoc/js/document.js');


