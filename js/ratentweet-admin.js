jQuery(document).ready(function() {
	jQuery('#ratentweet_short').change(function() {
		jQuery('#ratentweet_bitly').fadeOut('fast');
		if(this.value=='bitly'){
			jQuery('#ratentweet_bitly').fadeIn('fast');
		}
	});

});