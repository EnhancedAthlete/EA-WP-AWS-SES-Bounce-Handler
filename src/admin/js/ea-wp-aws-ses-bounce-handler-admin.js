(function( $ ) {
	'use strict';

	$(window).load(function() {

		var runSesBounceTestButton = document.getElementById( 'run-ses-bounce-test-button' );

		if(runSesBounceTestButton != null) {

			runSesBounceTestButton.addEventListener('click', function (event) {

				event.preventDefault();

				$('.bounce-test-running-spinner').css('display','inline');

				var data = $('#run-ses-bounce-test-form').serializeArray();

				$.post(ajaxurl, data, function (response) {

					// TODO: Handle 500 error, e.g. maintenance mode


					var noticeType = response.data.notice;
					var html = response.data.html;
					var bounceTestId = response.data.bounceTestId;


					var content = '<div class="notice notice-' + noticeType + '" id="' + bounceTestId + '">';

					content = content + html;

					content = content + '</div>';

					$('#run-ses-bounce-test-response').append(content);

					setTimeout(function () {

						fetchResults( bounceTestId );

					}, 1000);

				});
			});
		}

	});

	function fetchResults( bounceTestId ) {

		// nonce

		// Test complete?

	}


})( jQuery );
