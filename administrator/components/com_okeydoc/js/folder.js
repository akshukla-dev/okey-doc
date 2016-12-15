/**
 * @package Okey DOC 1.x
 * @copyright Copyright (c)2014 - 2017 Lucas Sanner
 * @license GNU General Public License version 3, or later
 * @contact lucas.sanner@gmail.com
 */


(function($) {

  //Run a function when the page is fully loaded including graphics.
  $(window).load(function() {
    //Get the value of the item id to determine if it is new or not. 
    var itemId = $('#jform_id').val();
    //Get the name of the OS.
    var operatingSystem = $('#operating_system').val();

    //New item and Linux OS.
    if(itemId == 0 && operatingSystem == 'LINUX') {
      var checkbox = $('#jform_symlink_option').click(function() { $.fn.showHide($(this)); });
      $.fn.showHide(checkbox);
    }
  });

  $.fn.showHide = function(checkbox) {
    if(checkbox.is(':checked')) {
      $.fn.showField();
    }
    else {
      $.fn.hideField();
    }
  }

  $.fn.showField = function() {
    $('#jform_symlink_path').parent('div').parent('div').show();
    $('#jform_symlink_path').prop('required', true);
  };

  $.fn.hideField = function() {
    $('#jform_symlink_path').prop('required', false);
    $('#jform_symlink_path').parent('div').parent('div').hide();
  };


})(jQuery);




