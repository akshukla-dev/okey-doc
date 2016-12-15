<?php
/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */

defined('_JEXEC') or die;

/**
 * HTML View class for the Okey DOC component
 */
class OkeydocViewCategory extends JViewCategory
{
  /**
   * Execute and display a template script.
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  mixed  A string if successful, otherwise a Error object.
   */
  public function display($tpl = null)
  {
    //Call parent method with common display elements (state, items etc...) used in
    //category list displays.
    parent::commonCategoryDisplay();

    //Get the user object and the current url.
    $user = JFactory::getUser();
    $uri = JUri::getInstance();

    // Prepare the data.
    // Compute the document slugs.
    foreach($this->items as $item) {
      $item->slug = $item->alias ? ($item->id.':'.$item->alias) : $item->id;
      $item->catslug = $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;

      //Variables needed in the document edit layout.
      $item->user_id = $user->get('id');
      $item->uri = $uri;
    }

    // Check for layout override only if this is not the active menu item
    // If it is the active menu item, then the view and category id will match
    $app = JFactory::getApplication();
    $active = $app->getMenu()->getActive();

    //The category has no itemId and thus is not linked to any menu item. 
    if((!$active) || ((strpos($active->link, 'view=category') === false) || (strpos($active->link, '&id='.(string)$this->category->id) === false))) {
      // Get the layout from the merged category params
      if($layout = $this->category->params->get('category_layout')) {
	$this->setLayout($layout);
      }
    }
    // At this point, we are in a menu item, so we don't override the layout
    elseif(isset($active->query['layout'])) {
      // We need to set the layout from the query in case this is an alternative menu item (with an alternative layout)
      $this->setLayout($active->query['layout']);
    }
    //Note: In case the layout parameter is not found within the query, the default layout
    //will be set.

    //Set the name of the active layout in params, (needed for the filter ordering layout).
    $this->params->set('active_layout', $this->getLayout());
    //Set the filter_ordering parameter for the layout.
    $this->filter_ordering = $this->state->get('list.filter_ordering');

    $this->setDocument();

    return parent::display($tpl);
  }


  protected function setDocument() 
  {
    //Include css file.
    $doc = JFactory::getDocument();
    $doc->addStyleSheet(JURI::base().'components/com_okeydoc/css/okeydoc.css');
  }
}
