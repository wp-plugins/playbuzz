(jQuery)(document).ready(function(){

	// Site Settings - tags_toggle_triger
	(jQuery)( ".tags_toggle_triger" ).change(function() {
		if ( (jQuery)(this).prop( "checked" ) ) {
			(jQuery)( '.tags_toggle' ).show();
		} else {
			(jQuery)( '.tags_toggle' ).hide();
		}
	}).change();

	// Feedback - submit form
	(jQuery)("#submit").click(function () {
		(jQuery).ajax({
			type    : "POST",
			url     : "http://www.playbuzz.com/contactus",
			data    : (jQuery)("#playbuzz_feedback_form").serialize(),
			success : function (data) {
				(jQuery)(".playbuzz_feedback_message p").text("Feedback sent, thank you!");
				(jQuery)(".playbuzz_feedback_message").addClass("updated");
			},
			error   : function (data) {
				(jQuery)(".playbuzz_feedback_message p").text("Something went wrong please try again...");
				(jQuery)(".playbuzz_feedback_message").addClass("error");
			}
		});
	});

});
