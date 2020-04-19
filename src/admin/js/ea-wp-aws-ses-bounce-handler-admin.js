(function( $ ) {
	'use strict';

	$(window).load(function() {

		var runSesBounceTestButton = document.getElementById( 'run-ses-bounce-test-button' );

		if(runSesBounceTestButton != null) {

			runSesBounceTestButton.addEventListener('click', function (event) {

				event.preventDefault();

				var data = $('#run-ses-bounce-test-form').serializeArray();

				$.post(ajaxurl, data, function (response) {

					// TODO: Handle 500 error, e.g. maintenance mode


					var noticeType = response.data.notice;
					var html = response.data.html;

					var content = '<div class="notice notice-' + noticeType + '">';

					content = content + html;

					content = content + '</div>';

					$('#run-ses-bounce-test-response').append(content);

					setTimeout(function () {

						console.log('Fetch results.');

					}, 1000);

				});
			});
		}

	});



})( jQuery );
