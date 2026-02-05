(function($) {
	'use strict';

	$(document).ready(function() {
		// Target both settings page button and admin bar button
		$(document).on('click', '#v7-settings-clear, #wp-admin-bar-v7-clear-cache > a', function(e) {
			e.preventDefault();

			var $btn = $(this);
			var originalText = $btn.text();
			var originalHtml = $btn.html();

			$btn.addClass('v7-clearing').text(v7CacheAdmin.clearing);

			$.ajax({
				url: v7CacheAdmin.ajaxUrl,
				type: 'POST',
				data: {
					action: 'v7_clear_cache',
					nonce: v7CacheAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						$btn.text(v7CacheAdmin.cleared);
						setTimeout(function() {
							$btn.removeClass('v7-clearing');
							// Restore admin bar with updated size
							if ($btn.closest('#wp-admin-bar-v7-clear-cache').length && response.data && response.data.size) {
								$btn.html('<span class="ab-icon dashicons dashicons-trash"></span>Clear Cache (' + response.data.size + ')');
							} else {
								$btn.html(originalHtml);
							}
						}, 2000);
					}
				},
				error: function() {
					$btn.removeClass('v7-clearing').html(originalHtml);
				}
			});
		});
	});
})(jQuery);
