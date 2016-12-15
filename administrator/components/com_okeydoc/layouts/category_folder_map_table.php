<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.framework');
$folders = $displayData->folders;
$categories = $displayData->categories;
?>

<?php if(!empty($categories)) : ?>
  <table class="table table-striped">
    <thead>
    <tr>
      <th width="1%" class="nowrap center hidden-phone">
      </th>
      <th>
	<?php echo JText::_('JCATEGORY'); ?>
      </th>
      <th width="1%">
	<?php echo JText::_('COM_OKEYDOC_HEADING_FILES'); ?>
      </th>
      <th>
	<?php echo JText::_('COM_OKEYDOC_HEADING_FOLDER'); ?>
      </th>
    </thead>
    <tbody>

    <?php foreach($categories as $category) : 

      // Setup  the variable attributes.
      $eid = 'categorie_'.$category->id;

      $checked = $disabled = $bindedFolder = $nbDocuments = '';

      //Set the appropriate attributes for each folder. 
      foreach($folders as $folder) { 
	//Get the catids binded to the folder and put them in an array.
	$folderCatids = explode(',', $folder->catids);

	//This category is in the array and is binded to the current folder.
	if(in_array($category->id, $folderCatids) && $folder->title == $displayData->title) {
	  $checked = ' checked="checked"'; // We check the box.
	  break; //Get out of the loop.
	}

	//Category is in the array but is not binded to the current folder.
	if(in_array($category->id, $folderCatids) && $folder->title != $displayData->title) {
	  $checked = ' checked="checked"'; //The box is checked
	  $disabled = ' disabled';         //but disabled.
	  $bindedFolder = $folder->title;
	  break; //Get out of the loop.
	}
      }

      $rel = ($category->parent_id > 0) ? ' rel="categorie_'.$category->parent_id.'"' : ''; 

      //Set up the level symbol.
      $levels = '';
      if($category->level > 1) {
	$levels = str_repeat('<span class="gi">|&mdash;</span>', $category->level - 1).'&nbsp;'; 
      }
    ?>

      <tr><td>
	<input type="checkbox" name="jform[categories][]" value="<?php echo $category->id; ?>"
		id="<?php echo $eid; ?>" <?php echo $checked.$rel.$disabled; ?> />
     </td><td>
       <?php echo $levels.$this->escape($category->title); ?>
     </td><td class="center">
       <?php echo $category->documents; ?>
     </td><td>
       <?php echo $this->escape($bindedFolder); ?>
     </td><td>
     </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

