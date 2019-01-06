(window.webpackJsonp=window.webpackJsonp||[]).push([[13],{205:function(s){s.exports={"related_posts_by_taxonomy_wp_rest_api_args-130":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$args</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="description">Arguments.</span></p></dd></dl></section>',methods:[],related:{uses:[],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/filter_request_args",text:"Related_Posts_By_Taxonomy_Rest_API::filter_request_args()"}]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'<span class="hook-func">apply_filters</span>( \'related_posts_by_taxonomy_wp_rest_api_args\',  <nobr><span class="arg-type">array</span> <span class="arg-name">$args</span></nobr> )',notice:""},"related_posts_by_taxonomy_wp_rest_api_defaults-107":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$defaults</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="description">See <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_related_posts_by_taxonomy_shortcode/">km_rpbt_related_posts_by_taxonomy_shortcode()</a> for for more information about default arguments.</span></p></dd></dl></section>',methods:[],related:{uses:[],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/filter_request_args",text:"Related_Posts_By_Taxonomy_Rest_API::filter_request_args()"}]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'<span class="hook-func">apply_filters</span>( \'related_posts_by_taxonomy_wp_rest_api_defaults\',  <nobr><span class="arg-type">array</span> <span class="arg-name">$defaults</span></nobr> )',notice:""},"related_posts_by_taxonomy_rest_api-15":{html:'<hr /><section class="description"><h2>Description</h2><p>Registered endpoint: /wp-json/related-posts-by-taxonomy/v1/posts/{$post_id}</p></section>',methods:[{url:"/classes/related_posts_by_taxonomy_rest_api/filter_request_args",title:"filter_request_args",excerpt:"Method: Filter WP Rest API request arguments.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/get_filter_args",title:"get_filter_args",excerpt:"Method: Returns arguments used by the related posts query.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/get_item",title:"get_item",excerpt:"Method: Get one item from the collection.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/get_item_permissions_check",title:"get_item_permissions_check",excerpt:"Method: Check if a given request has access to get a specific item",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/get_item_schema",title:"get_item_schema",excerpt:"Method: Retrieves the related post's schema, conforming to JSON Schema.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/get_items_permissions_check",title:"get_items_permissions_check",excerpt:"Method: Check if a given request has access to get items",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/get_related_posts",title:"get_related_posts",excerpt:"Method: Returns related posts from database or cache.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/prepare_item_for_response",title:"prepare_item_for_response",excerpt:"Method: Prepare the item for the REST response",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/register_routes",title:"register_routes",excerpt:"Method: Register the routes for the objects of the controller.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_rest_api/validate_args",title:"validate_args",excerpt:"Method: Validate WP Rest API arguments.",deprecated:!1}],related:{uses:[],used_by:[]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:"Related_Posts_By_Taxonomy_Rest_API",notice:""},"related_posts_by_taxonomy_rest_api::filter_request_args-95":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$args</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">Request arguments. See <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_related_posts_by_taxonomy_shortcode/">km_rpbt_related_posts_by_taxonomy_shortcode()</a> for for more information on accepted arguments.</span></p></dd><dt>$post_id</dt><dd><p class="desc"><span class="type">(<span class="int">int</span>)</span><span class="required">(Required)</span><span class="description">Post ID.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(array)</span> Filtered request arguments.</p></section>',methods:[],related:{uses:[{source:"includes/settings.php",url:"/functions/km_rpbt_get_default_settings",text:"km_rpbt_get_default_settings()"},{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/validate_args",text:"Related_Posts_By_Taxonomy_Rest_API::validate_args()"},{source:"includes/class-rest-api.php",url:"/hooks/related_posts_by_taxonomy_wp_rest_api_defaults",text:"related_posts_by_taxonomy_wp_rest_api_defaults"},{source:"includes/class-rest-api.php",url:"/hooks/related_posts_by_taxonomy_wp_rest_api_args",text:"related_posts_by_taxonomy_wp_rest_api_args"}],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/get_item",text:"Related_Posts_By_Taxonomy_Rest_API::get_item()"}]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::filter_request_args( <span class="arg-type">array</span>&nbsp;<span class="arg-name">$args</span>,  <span class="arg-type">int</span>&nbsp;<span class="arg-name">$post_id</span>, &nbsp;<span class="arg-name">$request</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::get_filter_args-335":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$results</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">Related posts. Array with Post objects or post IDs or post titles or post slugs.</span></p></dd><dt>$post_id</dt><dd><p class="desc"><span class="type">(<span class="int">int</span>)</span><span class="required">(Required)</span><span class="description">Post id used to get the related posts.</span></p></dd><dt>$taxonomies</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">Taxonomies used to get the related posts.</span></p></dd><dt>$args</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">Query arguments used to get the related posts.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(array)</span> Related Posts.</p></section>',methods:[],related:{uses:[],used_by:[]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::get_filter_args( <span class="arg-type">array</span>&nbsp;<span class="arg-name">$results</span>,  <span class="arg-type">int</span>&nbsp;<span class="arg-name">$post_id</span>,  <span class="arg-type">array</span>&nbsp;<span class="arg-name">$taxonomies</span>,  <span class="arg-type">array</span>&nbsp;<span class="arg-name">$args</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::get_item-65":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$request</dt><dd><p class="desc"><span class="type">(<span class="WP_REST_Request">WP_REST_Request</span>)</span><span class="required">(Required)</span><span class="description">Full data about the request.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(WP_Error|WP_REST_Response)</span> </p></section>',methods:[],related:{uses:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/filter_request_args",text:"Related_Posts_By_Taxonomy_Rest_API::filter_request_args()"},{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/prepare_item_for_response",text:"Related_Posts_By_Taxonomy_Rest_API::prepare_item_for_response()"}],used_by:[]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::get_item( <span class="arg-type">WP_REST_Request</span>&nbsp;<span class="arg-name">$request</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::get_item_permissions_check-194":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$request</dt><dd><p class="desc"><span class="type">(<span class="WP_REST_Request">WP_REST_Request</span>)</span><span class="required">(Required)</span><span class="description">Full data about the request.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(WP_Error|bool)</span> </p></section>',methods:[],related:{uses:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/get_items_permissions_check",text:"Related_Posts_By_Taxonomy_Rest_API::get_items_permissions_check()"}],used_by:[]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::get_item_permissions_check( <span class="arg-type">WP_REST_Request</span>&nbsp;<span class="arg-name">$request</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::get_item_schema-261":{html:"<hr /><section class=\"return\"><h3>Return</h3><p><span class='return-type'>(array)</span> Item schema data.</p></section>",methods:[],related:{uses:[],used_by:[]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:"Related_Posts_By_Taxonomy_Rest_API::get_item_schema()",notice:""},"related_posts_by_taxonomy_rest_api::get_items_permissions_check-177":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$request</dt><dd><p class="desc"><span class="type">(<span class="WP_REST_Request">WP_REST_Request</span>)</span><span class="required">(Required)</span><span class="description">Full data about the request.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(WP_Error|bool)</span> </p></section>',methods:[],related:{uses:[{source:"includes/functions.php",url:"/functions/km_rpbt_plugin_supports",text:"km_rpbt_plugin_supports()"}],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/get_item_permissions_check",text:"Related_Posts_By_Taxonomy_Rest_API::get_item_permissions_check()"}]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::get_items_permissions_check( <span class="arg-type">WP_REST_Request</span>&nbsp;<span class="arg-name">$request</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::get_related_posts-351":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$args</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">Query arguments used to get the related posts. See <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_related_posts/">km_rpbt_get_related_posts()</a> for for more information on accepted arguments.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(array)</span> Related Posts.</p></section>',methods:[],related:{uses:[{source:"includes/functions.php",url:"/functions/km_rpbt_get_related_posts",text:"km_rpbt_get_related_posts()"}],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/prepare_item_for_response",text:"Related_Posts_By_Taxonomy_Rest_API::prepare_item_for_response()"}]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::get_related_posts( <span class="arg-type">array</span>&nbsp;<span class="arg-name">$args</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::prepare_item_for_response-210":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$args</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">WP Rest API arguments of the item. See <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_related_posts/">km_rpbt_get_related_posts()</a> for for more information on accepted request arguments.</span></p></dd><dt>$request</dt><dd><p class="desc"><span class="type">(<span class="WP_REST_Request">WP_REST_Request</span>)</span><span class="required">(Required)</span><span class="description">Request object.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(mixed)</span> </p></section>',methods:[],related:{uses:[{source:"includes/functions.php",url:"/functions/km_rpbt_get_related_posts_html",text:"km_rpbt_get_related_posts_html()"},{source:"includes/functions.php",url:"/functions/km_rpbt_get_public_taxonomies",text:"km_rpbt_get_public_taxonomies()"},{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/get_related_posts",text:"Related_Posts_By_Taxonomy_Rest_API::get_related_posts()"}],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/get_item",text:"Related_Posts_By_Taxonomy_Rest_API::get_item()"}]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::prepare_item_for_response( <span class="arg-type">array</span>&nbsp;<span class="arg-name">$args</span>,  <span class="arg-type">WP_REST_Request</span>&nbsp;<span class="arg-name">$request</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_rest_api::register_routes-30":{html:"",methods:[],related:{uses:[],used_by:[]},changelog:[{description:"Introduced.",version:"2.3.0"}],signature:"Related_Posts_By_Taxonomy_Rest_API::register_routes()",notice:""},"related_posts_by_taxonomy_rest_api::validate_args-153":{html:'<hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$atts</dt><dd><p class="desc"><span class="type">(<span class="array">array</span>)</span><span class="required">(Required)</span><span class="description">Array with WP Rest API arguments. See <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_related_posts/">km_rpbt_get_related_posts()</a> for for more information on accepted arguments.</span></p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(array)</span> Array with validated WP Rest API arguments.</p></section>',methods:[],related:{uses:[{source:"includes/functions.php",url:"/functions/km_rpbt_get_taxonomies",text:"km_rpbt_get_taxonomies()"},{source:"includes/functions.php",url:"/functions/km_rpbt_get_post_types",text:"km_rpbt_get_post_types()"}],used_by:[{source:"includes/class-rest-api.php",url:"/classes/related_posts_by_taxonomy_rest_api/filter_request_args",text:"Related_Posts_By_Taxonomy_Rest_API::filter_request_args()"}]},changelog:[{description:"Introduced.",version:"2.6.0"}],signature:'Related_Posts_By_Taxonomy_Rest_API::validate_args(&nbsp;<span class="arg-name">$args</span>&nbsp;)',notice:""}}}}]);
//# sourceMappingURL=13.a619310f.chunk.js.map