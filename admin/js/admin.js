(function($) {
	'use strict';

	$(document).ready(function() {
		$('.v7-clear-cache-btn, #v7-settings-clear').on('click', function(e) {
			e.preventDefault();

			var $btn = $(this);
			var originalText = $btn.text();

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
							$btn.removeClass('v7-clearing').text(originalText);
							if (response.data && response.data.size) {
								$('#wp-admin-bar-v7-clear-cache .ab-item').html(
									'<span class="ab-icon dashicons dashicons-trash"></span>Clear Cache (' + response.data.size + ')'
								);
							}
						}, 2000);
					}
				},
				error: function() {
					$btn.removeClass('v7-clearing').text(originalText);
				}
			});
		});
	});
})(jQuery);
