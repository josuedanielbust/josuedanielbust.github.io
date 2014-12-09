document.writeln('<div id="scf-container"></div>');
jQuery(document).ready(function(){
	jQuery('#scf-container').addClass('scf-loading');
	jQuery.get('<?php echo url_for(); ?>', function(data) {
		jQuery('#scf-container').removeClass('scf-loading');
		jQuery('#scf-container').html(data);
	});
});

// post form
jQuery('#scf-container > form').live( 'submit', function(event) {
	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var thisForm = jQuery( this );
	var thisFormData = thisForm.serialize();

	var targetUrl = thisForm.attr( 'action' );
	var resultDiv = thisForm.closest('#scf-container');

	resultDiv.addClass( 'scf-loading' );
	/* Send the data using post and put the results in a div */
	jQuery.ajax({
		type: "POST",
		url: targetUrl,
		data: thisFormData,
		success: function(msg, txtStatus, xhr){
			resultDiv.removeClass( 'scf-loading' );
			if( xhr.status == 302 ){
				var href = xhr.getResponseHeader("Location");
				resultDiv.load(href);
				}
			else {
				resultDiv.html( msg );
				}
	       }
	    });
	return false;
	});
