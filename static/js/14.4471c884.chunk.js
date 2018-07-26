webpackJsonp([14],{34:function(t,s){t.exports={"km_rpbt_get_default_args-68":{html:"\n\t<hr />\n\t<section class=\"return\">\n\t\t<h3>Return</h3>\n\t\t<p><span class='return-type'>(array)</span> Array with default arguments.</p>\n\t</section>\n",methods:[],related:{uses:[{source:"includes/settings.php",url:"/functions/km_rpbt_get_query_vars",text:"km_rpbt_get_query_vars()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"2.1"}],signature:"km_rpbt_get_default_args()"},"km_rpbt_get_related_post_title_link-133":{html:'\n\t<hr />\n\t<section class="parameters">\n\t\t<h3>Parameters</h3>\n\t\t<dl>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$post</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="object">object</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Required)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Post object.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$title_attr</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="bool">bool</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Whether to use a title attribute in the link. </span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: false</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t</dl>\n\t</section>\n\t<hr />\n\t<section class="return">\n\t\t<h3>Return</h3>\n\t\t<p><span class=\'return-type\'>(string)</span> Related post link HTML.</p>\n\t</section>\n',methods:[],related:{uses:[{source:"includes/template-tags.php",url:"/functions/km_rpbt_get_post_link",text:"km_rpbt_get_post_link()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"2.4.0"}],signature:'km_rpbt_get_related_post_title_link( <span class="arg-type">object</span>&nbsp;<span class="arg-name">$post</span>,  <span class="arg-type">bool</span>&nbsp;<span class="arg-name">$title_attr</span>&nbsp;=&nbsp;<span class="arg-default">false</span>&nbsp;)'},"km_rpbt_get_shortcode_atts-15":{html:"\n\t<hr />\n\t<section class=\"return\">\n\t\t<h3>Return</h3>\n\t\t<p><span class='return-type'>(array)</span> Array with default shortcode atts</p>\n\t</section>\n",methods:[],related:{uses:[{source:"includes/settings.php",url:"/functions/km_rpbt_get_default_settings",text:"km_rpbt_get_default_settings()"},{source:"includes/functions.php",url:"/functions/km_rpbt_plugin",text:"km_rpbt_plugin()"}],used_by:[]},changelog:[{description:"This function has been deprecated.",version:"2.2.2"},{description:"Introduced.",version:"2.1"}],signature:"km_rpbt_get_shortcode_atts()"},"km_rpbt_post_title_link-117":{html:'\n\t<hr />\n\t<section class="parameters">\n\t\t<h3>Parameters</h3>\n\t\t<dl>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$post</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="object">object</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Required)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Post object.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$title_attr</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="bool">bool</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Whether to use a title attribute in the link. </span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: false</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t</dl>\n\t</section>\n',methods:[],related:{uses:[{source:"includes/template-tags.php",url:"/functions/km_rpbt_post_link",text:"km_rpbt_post_link()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"2.4.0"}],signature:'km_rpbt_post_title_link( <span class="arg-type">object</span>&nbsp;<span class="arg-name">$post</span>,  <span class="arg-type">bool</span>&nbsp;<span class="arg-name">$title_attr</span>&nbsp;=&nbsp;<span class="arg-default">false</span>&nbsp;)'},"km_rpbt_related_posts_by_taxonomy-54":{html:'\n\t<hr />\n\t<section class="parameters">\n\t\t<h3>Parameters</h3>\n\t\t<dl>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$post_id</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="int">int</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Required)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">The post id to get related posts for.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$taxonomies</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="array">array</span>|<span class="string">string</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">The taxonomies to retrieve related posts from.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: \'category\'</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$args</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="array">array</span>|<span class="string">string</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description"> Change what is returned.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: \'\'</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t</dl>\n\t</section>\n\t<hr />\n\t<section class="return">\n\t\t<h3>Return</h3>\n\t\t<p><span class=\'return-type\'>(array)</span> Empty array if no related posts found. Array with post objects.</p>\n\t</section>\n',methods:[],related:{uses:[{source:"includes/query.php",url:"/functions/km_rpbt_query_related_posts",text:"km_rpbt_query_related_posts()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"0.1"}],signature:'km_rpbt_related_posts_by_taxonomy( <span class="arg-type">int</span>&nbsp;<span class="arg-name">$post_id</span>,  <span class="arg-type">array|string</span>&nbsp;<span class="arg-name">$taxonomies</span>&nbsp;=&nbsp;<span class="arg-default">\'category\'</span>,  <span class="arg-type">array|string</span>&nbsp;<span class="arg-name">$args</span>&nbsp;=&nbsp;<span class="arg-default">\'\'</span>&nbsp;)'},"km_rpbt_related_posts_by_taxonomy_template-102":{html:'\n\t<hr />\n\t<section class="parameters">\n\t\t<h3>Parameters</h3>\n\t\t<dl>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$format</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="string">string</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">The format to get the template for.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: false</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$type</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="string">string</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Supplied by widget or shortcode.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: false</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t</dl>\n\t</section>\n\t<hr />\n\t<section class="return">\n\t\t<h3>Return</h3>\n\t\t<p><span class=\'return-type\'>(mixed)</span> False on failure, template file path on success.</p>\n\t</section>\n',methods:[],related:{uses:[{source:"includes/template-loader.php",url:"/functions/km_rpbt_get_template",text:"km_rpbt_get_template()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated Used by widget and shortcode</span>',version:"2.5.0"},{description:"Introduced.",version:"0.1"}],signature:'km_rpbt_related_posts_by_taxonomy_template( <span class="arg-type">string</span>&nbsp;<span class="arg-name">$format</span>&nbsp;=&nbsp;<span class="arg-default">false</span>,  <span class="arg-type">string</span>&nbsp;<span class="arg-name">$type</span>&nbsp;=&nbsp;<span class="arg-default">false</span>&nbsp;)'},"km_rpbt_related_posts_by_taxonomy_validate_ids-84":{html:'\n\t<hr />\n\t<section class="description">\n\t\t<h2>Description</h2>\n\t\t<p>Checks if ids is a comma separated string or an array with ids.</p>\n\t</section>\n\t<hr />\n\t<section class="parameters">\n\t\t<h3>Parameters</h3>\n\t\t<dl>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$ids</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="string">string</span>|<span class="array">array</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Required)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Comma separated list or array with ids.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t</dl>\n\t</section>\n\t<hr />\n\t<section class="return">\n\t\t<h3>Return</h3>\n\t\t<p><span class=\'return-type\'>(array)</span> Array with postive integers</p>\n\t</section>\n',methods:[],related:{uses:[{source:"includes/settings.php",url:"/functions/km_rpbt_validate_ids",text:"km_rpbt_validate_ids()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"0.2"}],signature:'km_rpbt_related_posts_by_taxonomy_validate_ids( <span class="arg-type">string|array</span>&nbsp;<span class="arg-name">$ids</span>&nbsp;)'},"km_rpbt_related_posts_by_taxonomy_widget-145":{html:"\n",methods:[],related:{uses:[],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"0.1"}],signature:"km_rpbt_related_posts_by_taxonomy_widget()"},"km_rpbt_shortcode_get_related_posts-36":{html:'\n\t<hr />\n\t<section class="parameters">\n\t\t<h3>Parameters</h3>\n\t\t<dl>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$rpbt_args</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="array">array</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Required)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">Widget arguments.</span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t\t\t\t\t\t\t\t<dt>$cache_obj</dt>\n\t\t\t\t\t\t\t\t<dd>\n\t\t\t\t\t<p class="desc">\n\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="type">(<span class="object">object</span>)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="required">(Optional)</span>\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t<span class="description">This plugins cache object. </span>\n\t\t\t\t\t\t\t\t\t\t\t</p>\n\t\t\t\t\t\t\t\t\t\t\t<p class="default">Default value: null</p>\n\t\t\t\t\t\t\t\t\t</dd>\n\t\t\t\t\t</dl>\n\t</section>\n\t<hr />\n\t<section class="return">\n\t\t<h3>Return</h3>\n\t\t<p><span class=\'return-type\'>(array)</span> Array with related post objects.</p>\n\t</section>\n',methods:[],related:{uses:[{source:"includes/functions.php",url:"/functions/km_rpbt_get_related_posts",text:"km_rpbt_get_related_posts()"}],used_by:[]},changelog:[{description:'<span class="since-description">Deprecated</span>',version:"2.5.0"},{description:"Introduced.",version:"2.3.2"}],signature:'km_rpbt_shortcode_get_related_posts( <span class="arg-type">array</span>&nbsp;<span class="arg-name">$rpbt_args</span>,  <span class="arg-type">object</span>&nbsp;<span class="arg-name">$cache_obj</span>&nbsp;=&nbsp;<span class="arg-default">null</span>&nbsp;)'}}}});
//# sourceMappingURL=14.4471c884.chunk.js.map