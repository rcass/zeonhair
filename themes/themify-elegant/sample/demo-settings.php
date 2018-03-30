<?php
$widgets = get_option( "widget_themify-twitter" );
$widgets[1002] = array (
  'title' => 'Latest Tweets',
  'username' => 'themify',
  'show_count' => '3',
  'hide_timestamp' => NULL,
  'show_follow' => 'on',
  'follow_text' => '→ Follow me',
  'include_retweets' => 'on',
  'exclude_replies' => NULL,
);
update_option( "widget_themify-twitter", $widgets );

$widgets = get_option( "widget_themify-feature-posts" );
$widgets[1003] = array (
  'title' => 'Recent Posts',
  'category' => '0',
  'show_count' => '3',
  'show_date' => 'on',
  'show_thumb' => 'on',
  'display' => 'none',
  'hide_title' => NULL,
  'thumb_width' => '50',
  'thumb_height' => '50',
  'excerpt_length' => '55',
  'orderby' => 'date',
  'order' => 'DESC',
);
update_option( "widget_themify-feature-posts", $widgets );

$widgets = get_option( "widget_themify-recent-comments" );
$widgets[1004] = array (
  'title' => 'Recent Comments',
  'show_count' => '2',
  'show_avatar' => 'on',
  'avatar_size' => '55',
  'excerpt_length' => '60',
);
update_option( "widget_themify-recent-comments", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1005] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-large',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );

$widgets = get_option( "widget_themify-social-links" );
$widgets[1006] = array (
  'title' => '',
  'show_link_name' => NULL,
  'open_new_window' => NULL,
  'icon_size' => 'icon-large',
  'orientation' => 'horizontal',
);
update_option( "widget_themify-social-links", $widgets );



$sidebars_widgets = array (
  'sidebar-main' => 
  array (
    0 => 'themify-twitter-1002',
    1 => 'themify-feature-posts-1003',
    2 => 'themify-recent-comments-1004',
  ),
  'social-widget' => 
  array (
    0 => 'themify-social-links-1005',
  ),
  'footer-social-widget' => 
  array (
    0 => 'themify-social-links-1006',
  ),
); 
update_option( "sidebars_widgets", $sidebars_widgets );

$homepage = get_posts( array( 'name' => 'blog', 'post_type' => 'page' ) );
			if( is_array( $homepage ) && ! empty( $homepage ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $homepage[0]->ID );
			}
			
	ob_start(); ?>a:141:{s:15:"setting-favicon";s:0:"";s:23:"setting-custom_feed_url";s:0:"";s:19:"setting-header_html";s:0:"";s:19:"setting-footer_html";s:0:"";s:23:"setting-search_settings";s:0:"";s:31:"setting-search_settings_exclude";s:2:"on";s:21:"setting-feed_settings";s:0:"";s:21:"setting-webfonts_list";s:11:"recommended";s:24:"setting-webfonts_subsets";s:0:"";s:22:"setting-default_layout";s:12:"sidebar-none";s:34:"setting-default_content_text_align";s:7:"default";s:27:"setting-default_post_layout";s:9:"list-post";s:30:"setting-default_layout_display";s:7:"content";s:25:"setting-default_more_text";s:4:"More";s:21:"setting-index_orderby";s:4:"date";s:19:"setting-index_order";s:4:"DESC";s:26:"setting-default_post_title";s:0:"";s:33:"setting-default_unlink_post_title";s:0:"";s:25:"setting-default_post_meta";s:0:"";s:32:"setting-default_post_meta_author";s:0:"";s:34:"setting-default_post_meta_category";s:0:"";s:33:"setting-default_post_meta_comment";s:2:"no";s:29:"setting-default_post_meta_tag";s:0:"";s:25:"setting-default_post_date";s:0:"";s:26:"setting-default_post_image";s:0:"";s:33:"setting-default_unlink_post_image";s:0:"";s:31:"setting-image_post_feature_size";s:5:"blank";s:24:"setting-image_post_width";s:0:"";s:25:"setting-image_post_height";s:0:"";s:32:"setting-default_page_post_layout";s:12:"sidebar-none";s:44:"setting-default_page_post_content_text_align";s:6:"center";s:31:"setting-default_page_post_title";s:0:"";s:38:"setting-default_page_unlink_post_title";s:0:"";s:30:"setting-default_page_post_meta";s:0:"";s:37:"setting-default_page_post_meta_author";s:0:"";s:39:"setting-default_page_post_meta_category";s:0:"";s:38:"setting-default_page_post_meta_comment";s:0:"";s:34:"setting-default_page_post_meta_tag";s:0:"";s:30:"setting-default_page_post_date";s:0:"";s:31:"setting-default_page_post_image";s:0:"";s:38:"setting-default_page_unlink_post_image";s:0:"";s:38:"setting-image_post_single_feature_size";s:5:"blank";s:31:"setting-image_post_single_width";s:0:"";s:32:"setting-image_post_single_height";s:0:"";s:27:"setting-default_page_layout";s:12:"sidebar-none";s:39:"setting-default_page_content_text_align";s:7:"default";s:23:"setting-hide_page_title";s:0:"";s:23:"setting-hide_page_image";s:0:"";s:38:"setting-default_portfolio_index_layout";s:12:"sidebar-none";s:60:"setting-default_portfolio_index_portfolio_content_text_align";s:7:"default";s:43:"setting-default_portfolio_index_post_layout";s:5:"grid4";s:39:"setting-default_portfolio_index_display";s:4:"none";s:37:"setting-default_portfolio_index_title";s:0:"";s:49:"setting-default_portfolio_index_unlink_post_title";s:0:"";s:50:"setting-default_portfolio_index_post_meta_category";s:0:"";s:41:"setting-default_portfolio_index_post_date";s:3:"yes";s:42:"setting-default_portfolio_index_post_image";s:2:"no";s:48:"setting-default_portfolio_index_image_post_width";s:0:"";s:49:"setting-default_portfolio_index_image_post_height";s:0:"";s:61:"setting-default_portfolio_single_portfolio_content_text_align";s:7:"default";s:38:"setting-default_portfolio_single_title";s:0:"";s:50:"setting-default_portfolio_single_unlink_post_title";s:0:"";s:51:"setting-default_portfolio_single_post_meta_category";s:0:"";s:42:"setting-default_portfolio_single_post_date";s:0:"";s:49:"setting-default_portfolio_single_image_post_width";s:0:"";s:50:"setting-default_portfolio_single_image_post_height";s:0:"";s:22:"themify_portfolio_slug";s:7:"project";s:34:"setting-default_team_single_layout";s:12:"sidebar-none";s:51:"setting-default_team_single_team_content_text_align";s:7:"default";s:38:"setting-default_team_single_hide_title";s:0:"";s:40:"setting-default_team_single_unlink_title";s:0:"";s:38:"setting-default_team_single_hide_image";s:0:"";s:40:"setting-default_team_single_unlink_image";s:0:"";s:44:"setting-default_team_single_image_post_width";s:0:"";s:45:"setting-default_team_single_image_post_height";s:0:"";s:17:"themify_team_slug";s:4:"team";s:24:"setting-gallery_lightbox";s:8:"lightbox";s:19:"setting-entries_nav";s:8:"numbered";s:27:"setting-imagefilter_options";s:0:"";s:33:"setting-imagefilter_options_hover";s:0:"";s:27:"setting-imagefilter_applyto";s:12:"featuredonly";s:22:"setting-footer_widgets";s:17:"footerwidget-3col";s:24:"setting-footer_text_left";s:0:"";s:25:"setting-footer_text_right";s:0:"";s:27:"setting-global_feature_size";s:5:"blank";s:22:"setting-link_icon_type";s:9:"font-icon";s:32:"setting-link_type_themify-link-0";s:10:"image-icon";s:33:"setting-link_title_themify-link-0";s:7:"Twitter";s:32:"setting-link_link_themify-link-0";s:0:"";s:31:"setting-link_img_themify-link-0";s:91:"https://themify.me/demo/themes/elegant/wp-content/themes/themify-elegant/images/twitter.png";s:32:"setting-link_type_themify-link-1";s:10:"image-icon";s:33:"setting-link_title_themify-link-1";s:8:"Facebook";s:32:"setting-link_link_themify-link-1";s:0:"";s:31:"setting-link_img_themify-link-1";s:92:"https://themify.me/demo/themes/elegant/wp-content/themes/themify-elegant/images/facebook.png";s:32:"setting-link_type_themify-link-2";s:10:"image-icon";s:33:"setting-link_title_themify-link-2";s:6:"Google";s:32:"setting-link_link_themify-link-2";s:0:"";s:31:"setting-link_img_themify-link-2";s:95:"https://themify.me/demo/themes/elegant/wp-content/themes/themify-elegant/images/google-plus.png";s:32:"setting-link_type_themify-link-3";s:10:"image-icon";s:33:"setting-link_title_themify-link-3";s:7:"YouTube";s:32:"setting-link_link_themify-link-3";s:0:"";s:31:"setting-link_img_themify-link-3";s:91:"https://themify.me/demo/themes/elegant/wp-content/themes/themify-elegant/images/youtube.png";s:32:"setting-link_type_themify-link-4";s:10:"image-icon";s:33:"setting-link_title_themify-link-4";s:9:"Pinterest";s:32:"setting-link_link_themify-link-4";s:0:"";s:31:"setting-link_img_themify-link-4";s:93:"https://themify.me/demo/themes/elegant/wp-content/themes/themify-elegant/images/pinterest.png";s:32:"setting-link_type_themify-link-6";s:9:"font-icon";s:33:"setting-link_title_themify-link-6";s:8:"Facebook";s:32:"setting-link_link_themify-link-6";s:27:"http://facebook.com/themify";s:33:"setting-link_ficon_themify-link-6";s:11:"fa-facebook";s:35:"setting-link_ficolor_themify-link-6";s:0:"";s:37:"setting-link_fibgcolor_themify-link-6";s:0:"";s:32:"setting-link_type_themify-link-5";s:9:"font-icon";s:33:"setting-link_title_themify-link-5";s:7:"Twitter";s:32:"setting-link_link_themify-link-5";s:26:"http://twitter.com/themify";s:33:"setting-link_ficon_themify-link-5";s:10:"fa-twitter";s:35:"setting-link_ficolor_themify-link-5";s:0:"";s:37:"setting-link_fibgcolor_themify-link-5";s:0:"";s:32:"setting-link_type_themify-link-7";s:9:"font-icon";s:33:"setting-link_title_themify-link-7";s:6:"Google";s:32:"setting-link_link_themify-link-7";s:73:"https://plus.google.com/u/2/b/109280316400365629341/109280316400365629341";s:33:"setting-link_ficon_themify-link-7";s:14:"fa-google-plus";s:35:"setting-link_ficolor_themify-link-7";s:0:"";s:37:"setting-link_fibgcolor_themify-link-7";s:0:"";s:32:"setting-link_type_themify-link-8";s:9:"font-icon";s:33:"setting-link_title_themify-link-8";s:7:"YouTube";s:32:"setting-link_link_themify-link-8";s:37:"http://www.youtube.com/user/themifyme";s:33:"setting-link_ficon_themify-link-8";s:10:"fa-youtube";s:35:"setting-link_ficolor_themify-link-8";s:0:"";s:37:"setting-link_fibgcolor_themify-link-8";s:0:"";s:32:"setting-link_type_themify-link-9";s:9:"font-icon";s:33:"setting-link_title_themify-link-9";s:9:"Pinterest";s:32:"setting-link_link_themify-link-9";s:26:"https://www.pinterest.com/";s:33:"setting-link_ficon_themify-link-9";s:12:"fa-pinterest";s:35:"setting-link_ficolor_themify-link-9";s:0:"";s:37:"setting-link_fibgcolor_themify-link-9";s:0:"";s:22:"setting-link_field_ids";s:341:"{"themify-link-0":"themify-link-0","themify-link-1":"themify-link-1","themify-link-2":"themify-link-2","themify-link-3":"themify-link-3","themify-link-4":"themify-link-4","themify-link-6":"themify-link-6","themify-link-5":"themify-link-5","themify-link-7":"themify-link-7","themify-link-8":"themify-link-8","themify-link-9":"themify-link-9"}";s:23:"setting-link_field_hash";s:2:"10";s:30:"setting-page_builder_is_active";s:6:"enable";s:23:"setting-hooks_field_ids";s:2:"[]";s:4:"skin";s:97:"https://themify.me/demo/themes/elegant/wp-content/themes/themify-elegant/themify/img/non-skin.gif";}<?php $themify_data = ob_get_clean();
	themify_set_data( unserialize( $themify_data ) );
	?>