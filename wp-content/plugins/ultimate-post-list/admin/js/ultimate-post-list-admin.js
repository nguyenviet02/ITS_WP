(function( $ ) {
	'use strict';

	$(function () {
		// append Iris color picker to elements of class wp-color-picker
		$('.wp-color-picker').wpColorPicker();
	});

})( jQuery );

function upl_copy() {
  // Get the text field
  var el = document.getElementById('uplshortcode');

  // Select the text field
  el.select();

  // Copy the text inside the text field
  document.execCommand('copy');

  // Alert the copied text
  alert( upl_i18n.success );

}