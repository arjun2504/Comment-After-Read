var wpcar_waitSeconds = 60;

jQuery(document).ready(function() {
	
	var wpcar_post_comment_button = "form#commentform .form-submit #submit";
	jQuery(wpcar_post_comment_button).attr('disabled','disabled');

	wpcar_disableTimer();

	var wpcar_buttonText = jQuery(wpcar_post_comment_button).val();
	//console.log(wpcar_buttonText);
	/*if(wpcar_buttonText != null) {
		wpcar_waitSeconds = wpcar_buttonText.match(/\d+/)[0];
	}
	*/
	setInterval(function() {
		
		jQuery(wpcar_post_comment_button).val("Please wait for " + wpcar_waitSeconds + " seconds to comment");
		wpcar_waitSeconds--;

		if(wpcar_waitSeconds<0) {
			
			jQuery(wpcar_post_comment_button).val( jQuery('#wpcar_old_name_restore').data('name') );
			jQuery(wpcar_post_comment_button).removeAttr('disabled');
			
			clearInterval();
		}

	}, 1000);
});

function wpcar_disableTimer() {
	if( jQuery('#_wpcar_autotime_limit').prop('checked') ) {
		jQuery('#_wpcar_maxtime_limit').attr('readonly', 'readonly');
	} else {
		jQuery('#_wpcar_maxtime_limit').removeAttr('readonly');
	}
}