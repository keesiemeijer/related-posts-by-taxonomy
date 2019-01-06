(window.webpackJsonp=window.webpackJsonp||[]).push([[10],{202:function(e){e.exports={"related_posts_by_taxonomy_defaults-25":{html:'<hr /><section class="description"><h2>Description</h2><p>Data needed by this plugin:</p><ul><li>registered taxonomies</li><li>registered post types</li><li>default and registered image sizes</li><li>allowed formats</li><li>cache instance (if feature is activated)</li></ul></section>',methods:[{url:"/classes/related_posts_by_taxonomy_defaults/_setup",title:"_setup",excerpt:"Method: Sets up class properties.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_default_settings",title:"get_default_settings",excerpt:"Method: Returns default settings for the shortcode and widget.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_formats",title:"get_formats",excerpt:"Method: Returns all supported formats.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_image_sizes",title:"get_image_sizes",excerpt:"Method: Returns default and added image sizes.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_instance",title:"get_instance",excerpt:"Method: Acces this plugin's working instance.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_plugin_supports",title:"get_plugin_supports",excerpt:"Method: Get the features this plugin supports",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_post_types",title:"get_post_types",excerpt:"Method: Returns all public post types.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/get_taxonomies",title:"get_taxonomies",excerpt:"Method: Returns all public taxonomies Sets the id for 'All Taxonomies' Sets the default taxonomy",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/has_taxonomies",title:"has_taxonomies",excerpt:"Method:",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/init",title:"init",excerpt:"Method: Sets up class properties on action hook wp_loaded.",deprecated:!1},{url:"/classes/related_posts_by_taxonomy_defaults/plugin_supports",title:"plugin_supports",excerpt:"Method: Adds opt in support with a filter for cache, WP REST API and debug.",deprecated:!1}],related:{uses:[],used_by:[]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults",notice:""},"related_posts_by_taxonomy_defaults::_setup-123":{html:"",methods:[],related:{uses:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/get_taxonomies",text:"Related_Posts_By_Taxonomy_Defaults::get_taxonomies()"},{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/get_post_types",text:"Related_Posts_By_Taxonomy_Defaults::get_post_types()"},{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/get_image_sizes",text:"Related_Posts_By_Taxonomy_Defaults::get_image_sizes()"},{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/get_formats",text:"Related_Posts_By_Taxonomy_Defaults::get_formats()"}],used_by:[]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::_setup()",notice:""},"related_posts_by_taxonomy_defaults::get_default_settings-288":{html:'<hr /><section class="description"><h2>Description</h2><h3>See also</h3><ul><li><a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_default_settings">km_rpbt_get_default_settings()</a></li></ul></section><hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$type</dt><dd><p class="desc"><span class="type">(<span class="string">string</span>)</span><span class="required">(Optional)</span><span class="description">Type of settings. Choose from \'widget\', \'shortcode\', \'wp_rest_api\'.</span></p><p class="default">Default value: \'\'</p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(array)</span> Default feature type settings.</p></section>',methods:[],related:{uses:[{source:"includes/settings.php",url:"/functions/km_rpbt_get_default_settings",text:"km_rpbt_get_default_settings()"}],used_by:[]},changelog:[{description:'<span class="since-description">Moved logic to <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_default_settings/">km_rpbt_get_default_settings()</a>.</span>',version:"2.5.0"},{description:"Introduced.",version:"2.2.2"}],signature:'Related_Posts_By_Taxonomy_Defaults::get_default_settings( <span class="arg-type">string</span>&nbsp;<span class="arg-name">$type</span>&nbsp;=&nbsp;<span class="arg-default">\'\'</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_defaults::get_formats-267":{html:"<hr /><section class=\"return\"><h3>Return</h3><p><span class='return-type'>(array)</span> Formats.</p></section>",methods:[],related:{uses:[],used_by:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/_setup",text:"Related_Posts_By_Taxonomy_Defaults::_setup()"}]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::get_formats()",notice:""},"related_posts_by_taxonomy_defaults::get_image_sizes-229":{html:'<hr /><section class="description"><h2>Description</h2><p>Default image sizes</p><ul><li>thumbnail</li><li>medium</li><li>large</li><li>post-thumbnail</li></ul></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(array)</span> Array with all image sizes.</p></section>',methods:[],related:{uses:[],used_by:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/_setup",text:"Related_Posts_By_Taxonomy_Defaults::_setup()"}]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::get_image_sizes()",notice:""},"related_posts_by_taxonomy_defaults::get_instance-102":{html:"<hr /><section class=\"return\"><h3>Return</h3><p><span class='return-type'>(object)</span> </p></section>",methods:[],related:{uses:[],used_by:[{source:"includes/functions.php",url:"/functions/km_rpbt_plugin",text:"km_rpbt_plugin()"},{source:"includes/class-plugin.php",url:"/classes/related_posts_by_taxonomy_plugin/cache_init",text:"Related_Posts_By_Taxonomy_Plugin::cache_init()"},{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/init",text:"Related_Posts_By_Taxonomy_Defaults::init()"}]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::get_instance()",notice:""},"related_posts_by_taxonomy_defaults::get_plugin_supports-302":{html:'<hr /><section class="description"><h2>Description</h2><h3>See also</h3><ul><li><a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_plugin_supports">km_rpbt_get_plugin_supports()</a></li></ul></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(Array)</span> Array with plugin support types</p></section>',methods:[],related:{uses:[{source:"includes/settings.php",url:"/functions/km_rpbt_get_plugin_supports",text:"km_rpbt_get_plugin_supports()"}],used_by:[]},changelog:[{description:'<span class="since-description">Moved logic to a <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_get_plugin_supports/">km_rpbt_get_plugin_supports()</a>.</span>',version:"2.5.0"},{description:"Introduced.",version:"2.3.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::get_plugin_supports()",notice:""},"related_posts_by_taxonomy_defaults::get_post_types-139":{html:"<hr /><section class=\"return\"><h3>Return</h3><p><span class='return-type'>(array)</span> Array with post type objects.</p></section>",methods:[],related:{uses:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/has_taxonomies",text:"Related_Posts_By_Taxonomy_Defaults::has_taxonomies()"}],used_by:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/_setup",text:"Related_Posts_By_Taxonomy_Defaults::_setup()"}]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::get_post_types()",notice:""},"related_posts_by_taxonomy_defaults::get_taxonomies-180":{html:"<hr /><section class=\"return\"><h3>Return</h3><p><span class='return-type'>(array)</span> Array with taxonomy names and labels.</p></section>",methods:[],related:{uses:[],used_by:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/_setup",text:"Related_Posts_By_Taxonomy_Defaults::_setup()"}]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::get_taxonomies()",notice:""},"related_posts_by_taxonomy_defaults::has_taxonomies-164":{html:"",methods:[],related:{uses:[],used_by:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/get_post_types",text:"Related_Posts_By_Taxonomy_Defaults::get_post_types()"}]},changelog:[],signature:'Related_Posts_By_Taxonomy_Defaults::has_taxonomies(&nbsp;<span class="arg-name">$post_type</span>&nbsp;)',notice:""},"related_posts_by_taxonomy_defaults::init-114":{html:'<hr /><section class="description"><h2>Description</h2><p>wp_loaded is fired after custom post types and taxonomies are registered by themes and plugins.</p></section>',methods:[],related:{uses:[{source:"includes/class-defaults.php",url:"/classes/related_posts_by_taxonomy_defaults/get_instance",text:"Related_Posts_By_Taxonomy_Defaults::get_instance()"}],used_by:[{source:"includes/class-plugin.php",url:"/classes/related_posts_by_taxonomy_plugin/init",text:"Related_Posts_By_Taxonomy_Plugin::init()"}]},changelog:[{description:"Introduced.",version:"0.2.1"}],signature:"Related_Posts_By_Taxonomy_Defaults::init()",notice:""},"related_posts_by_taxonomy_defaults::plugin_supports-317":{html:'<hr /><section class="description"><h2>Description</h2><h3>See also</h3><ul><li><a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_plugin_supports">km_rpbt_plugin_supports()</a></li></ul></section><hr /><section class="parameters"><h3>Parameters</h3><dl><dt>$type</dt><dd><p class="desc"><span class="type">(<span class="string">string</span>)</span><span class="required">(Optional)</span><span class="description">Type of support (\'cache\', \'wp_rest_api\', etc.).</span></p><p class="default">Default value: \'\'</p></dd></dl></section><hr /><section class="return"><h3>Return</h3><p><span class=\'return-type\'>(bool)</span> True if set to true with a filter. Default false.</p></section>',methods:[],related:{uses:[{source:"includes/functions.php",url:"/functions/km_rpbt_plugin_supports",text:"km_rpbt_plugin_supports()"}],used_by:[]},changelog:[{description:'<span class="since-description">Moved logic to <a href="https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_plugin_supports/">km_rpbt_plugin_supports()</a>.</span>',version:"2.5.0"},{description:"Introduced.",version:"2.3.0"}],signature:'Related_Posts_By_Taxonomy_Defaults::plugin_supports( <span class="arg-type">string</span>&nbsp;<span class="arg-name">$type</span>&nbsp;=&nbsp;<span class="arg-default">\'\'</span>&nbsp;)',notice:""}}}}]);
//# sourceMappingURL=10.bfdd033b.chunk.js.map