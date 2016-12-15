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

    $('#jform_link_method').change(function() { $.fn.switchMethod(); });
    $.fn.switchMethod();

    //Existing item.
    if(itemId != 0) {
      $.fn.replaceHide();
      $('#switch_replace').toggle(function() { $.fn.replaceShow(); }, function() { $.fn.replaceHide(); });
    }
  });

  $.fn.switchMethod = function() {
   if($('#jform_link_method').val() == 'server') {
     //Note: Get the parent of the parent of the input field: <div> -> <div> -> <input>
     $('#jform_document_url').parent('div').parent('div').hide();
     $('#jform_document_url').prop('required', false);
     $('#jform_uploaded_file').parent('div').parent('div').show();
     $('#jform_uploaded_file').prop('required', true);
   }
   else { //url
     $('#jform_uploaded_file').parent('div').parent('div').hide();
     $('#jform_uploaded_file').prop('required', false);
     $('#jform_document_url').parent('div').parent('div').show();
     $('#jform_document_url').prop('required', true);
   }
  };

  $.fn.replaceShow = function() {
    $('#jform_replace_file').val('1');
    $('#jform_link_method').parent('div').parent('div').show();
    $.fn.switchMethod();
    $('#replace-title').hide();
    $('#cancel-title').show();
  };

  $.fn.replaceHide = function() {
    //
    $('#jform_replace_file').val('0');
    $.fn.hideLinkMethod();
    $('#cancel-title').hide();
    $('#replace-title').show();
  };

  $.fn.hideLinkMethod = function() {
    $('#jform_link_method').parent('div').parent('div').hide();
    $('#jform_document_url').parent('div').parent('div').hide();
    $('#jform_document_url').prop('required', false);
    $('#jform_uploaded_file').parent('div').parent('div').hide();
    $('#jform_uploaded_file').prop('required', false);
  };


})(jQuery);



