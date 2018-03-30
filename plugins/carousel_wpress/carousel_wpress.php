<?php
/*
Plugin Name: Carousel Evolution for WordPress
Plugin URI: http://yougapi.com/products/wp/jquery-carousel-evolution/
Description: Integrate an Awesome Carousel into your WordPress
Version: 2.5
Author: Yougapi Technology LLC
Author URI: http://yougapi.com
*/

//error_reporting(E_WARNING);
$GLOBALS['ygp_carousel_wpress'] = get_option('ygp_carousel_wpress');
$GLOBALS['ygp_carousel_wpress']['plugin_code'] = 'carousel_wpress';
$GLOBALS['ygp_carousel_wpress']['item_name'] = 'Carousel WPress';
$GLOBALS['ygp_carousel_wpress']['page_slug'] = 'carousel-wpress';

$GLOBALS['ygp_carousel_wpress']['carousel_entry_type'] = array('1'=>'Image', '2'=>'Video');
$GLOBALS['ygp_carousel_wpress']['carousel_type'] = array(''=>'Standard carousel', '2'=>'YouTube carousel');
$GLOBALS['ygp_carousel_wpress']['carousel_description_type'] = array(''=>'Disabled', '1'=>'Under the carousel', '2'=>'Above the carousel');
$GLOBALS['ygp_carousel_wpress']['youtube_feed_type'] = array('1'=>'Channel', '2'=>'Search', '4'=>'Popular Videos', '5'=>'Playlist');

require_once dirname( __FILE__ ).'/activation.php';
require_once dirname( __FILE__ ).'/db.php';
require_once dirname( __FILE__ ).'/display.php';

class Carousel_wpress {
	
	function Carousel_wpress() {
		
		if(is_admin()) {
			//activation
			register_activation_hook(__FILE__, array(__CLASS__, 'on_plugin_activation'));
			//AJAX
			add_action( 'wp_ajax_nopriv_carousel_wpress_listener', array(__CLASS__, 'carousel_wpress_listener') );
			add_action( 'wp_ajax_carousel_wpress_listener', array(__CLASS__, 'carousel_wpress_listener') );
			//Settings link
			add_filter( 'plugin_action_links', array(__CLASS__, 'plugin_action_links'), 10, 2);
		}
		
		//shortcodes
		add_shortcode( 'carousel_wpress', array(__CLASS__, 'display_carousel') );
	}
	
	//display through a shortcode
	function display_carousel($atts, $content = null, $code) {
		$d1 = new Carousel2_wpress_display();
		$carousel = $d1->get_carousel($atts);
		return $carousel;
	}
	
	//AJAX calls
	function carousel_wpress_listener() {
		
		$method = $_POST['method'];
		
		if($method=='save_carousel') {
			$name = $_POST['name'];
			$type_id = $_POST['type_id'];
			$description_display = $_POST['description_display'];
			$db1 = new Carousel_wpress_db();
			$id = $db1->add_carousel(array('name'=>$name, 'type_id'=>$type_id, 'description_display'=>$description_display));
			echo $id;
		}
				
		else if($method=='edit_carousel') {
			$id = $_POST['id'];
			$name = $_POST['name'];
			$type_id = $_POST['type_id'];
			$description_display = $_POST['description_display'];
			
			$db1 = new Carousel_wpress_db();
			$db1->edit_carousel(array('name'=>$name, 'type_id'=>$type_id, 'description_display'=>$description_display), array('id'=>$id));
		}
		
		else if($method=='delete_carousel') {
			$id = $_POST['id'];
			$db1 = new Carousel_wpress_db();
			$db1->delete_carousel($id);
		}
		
		else if($method=='add_entry') {
			$carousel_id = $_POST['carousel_id'];
			$type_id = $_POST['type_id'];
			$title = $_POST['title'];
			$url = $_POST['url'];
			$image = $_POST['image'];
			$description = $_POST['description'];
			
			$db1 = new Carousel_wpress_db();
			$id = $db1->add_carousel_entry(array('carousel_id'=>$carousel_id, 'type_id'=>$type_id, 'title'=>$title, 'url'=>$url, 'image'=>$image, 'description'=>$description));
			echo $id;
		}
		
		else if($method=='edit_entry') {
			$id = $_POST['id'];
			$type_id = $_POST['type_id'];
			$title = $_POST['title'];
			$url = $_POST['url'];
			$image = $_POST['image'];
			$description = $_POST['description'];
			
			$db1 = new Carousel_wpress_db();
			$db1->edit_carousel_entry(array('type_id'=>$type_id, 'title'=>$title, 'url'=>$url, 'image'=>$image, 'description'=>$description), array('id'=>$id));
		}
		
		else if($method=='delete_entry') {
			$id = $_POST['entry_id'];
			
			$db1 = new Carousel_wpress_db();
			if($id!='') $db1->delete_carousel_entry(array('id'=>$id));
		}
		
		else if($method=='reorder_entries') {
			$ids = $_POST['ids'];
			$db1 = new Carousel_wpress_db();
			$db1->reorder_entries($ids);
		}
		
		else if($method=='save_youtube_entries') {
			$id = $_POST['id'];
			$feed_type = $_POST['feed_type'];
			$username = $_POST['username'];
			$keywords = $_POST['keywords'];
			$feed = $_POST['feed'];
			$playlist = $_POST['playlist'];
			$nb_videos = $_POST['nb_videos'];
			
			$options['youtube'] = array('feed_type'=>$feed_type, 'username'=>$username, 'keywords'=>$keywords, 'feed'=>$feed, 'playlist'=>$playlist, 'nb_videos'=>$nb_videos);
			$options = json_encode($options);
			
			$db1 = new Carousel_wpress_db();
			$db1->edit_carousel(array('options'=>$options), array('id'=>$id));
		}
		
		exit;
	}
	
	function plugin_action_links($links, $file) {
		if ( $file == plugin_basename( dirname(__FILE__).'/carousel_wpress.php' ) ) {
			$links[] = '<a href="plugins.php?page=carousel-wpress">Settings</a>';
		}
		return $links;
	}
	
	//On plugin activation
	function on_plugin_activation() {
		if(self::notify_verification()) {
			$db1 = new Carousel_wpress_db();
			$db1->setup_tables();
		}
	}
	
	function notify_verification() {
		$url = 'http://yougapi.com/updates/?item='.$GLOBALS['ygp_carousel_wpress']['plugin_code'].'&s='.site_url();
		wp_remote_get($url);
		return 1;
	}
	
}

new Carousel_wpress();

?>