/**
 * Ajax query for related posts
 * 
 * @since 2.5.2
 */

(function($) {
	if (typeof rpbt_ajax_query === 'undefined') {
		return;
	}

	var error = 'Related Posts by Taxonomy Ajax Error: ';

	$(document).ready(function() {

		/**
		 * Updates te related posts ajax div.
		 */
		$('.rpbt_related_posts_ajax').each(function() {
			var elem = $(this);
			var args = $(this).data('rpbt_args');

			var data = {
				action: "rpbt_ajax_query",
				nonce: rpbt_ajax_query.nonce,
				args: args
			};

			$.post(rpbt_ajax_query.ajaxurl, data)
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