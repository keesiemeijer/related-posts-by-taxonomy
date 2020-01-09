/**
 * Ajax query for related posts
 *
 * @since 2.6.0
 */

(function($) {
	if (typeof rpbt_lazy_loading === 'undefined') {
		return;
	}

	var error = 'Related Posts by Taxonomy Ajax Error: ';

	$(document).ready(function() {

		/**
		 * Update container with related posts.
		 */
		$('.rpbt-related-posts-lazy-loading').each(function() {
			var elem = $(this);
			var args = $(this).data('rpbt_args') || {};

			var data = {
				action: "rpbt_lazy_loading",
				nonce: rpbt_lazy_loading.nonce,
				args: args
			};

			$.post(rpbt_lazy_loading.ajaxurl, data)
				.done(function(response) {
					var success = response.hasOwnProperty('success');
					var data = response.hasOwnProperty('data');

					if (!(success && data)) {
						console.log(error + 'missing properties');
						return;
					}

					if (true === response.success) {
						if (response.data.length) {
							// console.log('INSERT', args.post_id);
							elem.replaceWith(response.data);
						} else {
							elem.remove();
						}
					} else {
						console.log(error + 'failed request ');
					}
				})
				.fail(function(response) {
					console.log(error + 'failed response');
				});
		});
	});

})(jQuery);