(jQuery)(document).ready(function(){

	(jQuery)( ".tags_toggle_triger" ).change(function() {
		if ( (jQuery)(this).prop( "checked" ) ) {
			(jQuery)( '.tags_toggle' ).show();
		} else {
			(jQuery)( '.tags_toggle' ).hide();
		}
	}).change();

});
