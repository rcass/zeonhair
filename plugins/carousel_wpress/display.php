<?php

class Carousel2_wpress_display {
	
	function Carousel2_wpress_display() {
		wp_register_script('roundabout_js', plugin_dir_url( __FILE__ ).'include/js/jquery.roundabout.js', array('jquery'));
		wp_enqueue_script('roundabout_js');
	}
	
	function add_scripts() {
		
		//wp_register_script('roundabout_js', plugin_dir_url( __FILE__ ).'include/js/jquery.roundabout.js', array('jquery'));
		//wp_enqueue_script('roundabout_js');
		
		wp_register_script('carousel_js', plugin_dir_url( __FILE__ ).'include/js/script.js', array('jquery'));
		wp_enqueue_script('carousel_js');
		
		wp_register_script('roundabout_shapes_js', plugin_dir_url( __FILE__ ).'include/js/jquery.roundabout-shapes.min.js', array('jquery'));
		wp_enqueue_script('roundabout_shapes_js');
		
		wp_register_script('roundabout_drag_js', plugin_dir_url( __FILE__ ).'include/js/jquery.event.drag.js', array('jquery'));
		wp_enqueue_script('roundabout_drag_js');
		
		wp_register_script('roundabout_drop_js', plugin_dir_url( __FILE__ ).'include/js/jquery.event.drop.js', array('jquery'));
		wp_enqueue_script('roundabout_drop_js');
		
		wp_enqueue_style( 'roundabout_css', plugin_dir_url( __FILE__ ).'include/css/style.css');
	}
	
	function get_carousel($criteria=array()) {
		$id = $criteria['id'];
		
		$shape = @$criteria['shape'];
		$tilt = @$criteria['tilt'];
		$bearing = @$criteria['bearing'];
		$min_scale = @$criteria['min_scale'];
		$max_scale = @$criteria['max_scale'];
		$autoplay = @$criteria['autoplay'];
		$autoplay_duration = @$criteria['autoplay_duration'];
		$autoplay_pause_on_hover = @$criteria['autoplay_pause_on_hover'];
		$responsive = @$criteria['responsive'];
		$enable_drag = @$criteria['enable_drag'];
		//positioning
		$carousel_width = @$criteria['carousel_width'];
		$carouse_height = @$criteria['carouse_height'];
		$margin_left = @$criteria['margin_left'];
		$margin_top = @$criteria['margin_top'];
		$margin_bottom = @$criteria['margin_bottom'];
		$front_image_width = @$criteria['front_width'];
		$front_image_height = @$criteria['front_height'];
		
		if($shape=='') $shape = 'lazySusan'; //lazySusan, rollerCoaster
		if($tilt=='') $tilt = '0.0';
		if($bearing=='') $bearing = '0.0';
		if($min_scale=='') $min_scale = '0.4';
		if($max_scale=='') $max_scale = '1';
		if($autoplay=='') $autoplay = 'false';
		if($autoplay_duration=='') $autoplay_duration = '3000';
		if($autoplay_pause_on_hover=='') $autoplay_pause_on_hover = 'true';
		if($responsive=='') $responsive = 'true';
		if($enable_drag=='') $enable_drag = 'true';
		//positioning
		if($carousel_width=='') $carousel_width = '720px';
		if($carouse_height=='') $carouse_height = '260px';
		if($margin_left=='') $margin_left = '110px';
		if($margin_top=='') $margin_top = '120px';
		if($margin_bottom=='') $margin_bottom = '80px';
		if($front_image_width=='') $front_image_width = '300px';
		if($front_image_height=='') $front_image_height = '100%';
		
		$description_content = '';
		$display = '';
		
		$display .= "<style>
		.roundabout-holder { max-width: $carousel_width; height: $carouse_height; margin-left: $margin_left !important; margin-top: $margin_top !important; margin-bottom: $margin_bottom !important; }
		.roundabout li { width: $front_image_width; height: $front_image_height; }
		</style>";
		
		if($autoplay=='false') $autoplay_duration = 10000000;
		
		$display .= "
		<script type='text/javascript'> 
		/* <![CDATA[ */
		var Carousel2_wpress = {shape: '$shape', tilt: $tilt, bearing: $bearing, minScale: $min_scale, maxScale: $max_scale, 
		autoplay: $autoplay, autoplayDuration: $autoplay_duration, autoplayPauseOnHover: $autoplay_pause_on_hover, 
		responsive: $responsive, enableDrag: $enable_drag
		};
		/* ]]> */
		</script> 
		";
		
		if(is_admin()) self::add_scripts(); 
		else add_action('wp_footer', array(__CLASS__, 'add_scripts'));
		
		$d1 = new Carousel_wpress_db();
		
		$carousel = $d1->get_carousel_list(array('id'=>$id));
		$type_id = $carousel[0]['type_id'];
		$description_display = $carousel[0]['description_display'];
		$options = $carousel[0]['options'];
				
		//YouTube videos carousel
		if($type_id==2) {
		
			$options = json_decode($options, true);
			$feed_type = $options['youtube']['feed_type'];
			$username = $options['youtube']['username'];
			$keywords = $options['youtube']['keywords'];
			$category = $options['youtube']['category'];
			$standard_feed = $options['youtube']['feed'];
			$time = $options['youtube']['time'];
			$playlist = $options['youtube']['playlist'];
			$nb_display = $options['youtube']['nb_videos'];
			
			$videoData = array();
			//Get the videos from the API
			if($feed_type==1) {
				$criteria2['username'] = $username;
				$criteria2['maxResults'] = $nb_display;
				$criteria2['youtube_api_key'] = $GLOBALS['ygp_youtube_wpress']['youtube_api_key'];
				$v1 = new Youtube_class();
				$videoData = $v1->getYoutubeVideosByChannel($criteria2);
			}
			else if($feed_type==2) {
				$criteria2['q'] = $keywords;
				$criteria2['maxResults'] = $nb_display;
				$criteria2['youtube_api_key'] = $GLOBALS['ygp_youtube_wpress']['youtube_api_key'];
				$v1 = new Youtube_class();
				$videoData = $v1->getYoutubeVideosBySearch($criteria2);
			}
			/*
			else if($feed_type==3) {
				$y1 = new Youtube_class_carousel();
				$url = $y1->getYoutubeVideosByCategory(array('category'=>$category, 'max-results'=>$nb_display));
				$videoData = $y1->returnYoutubeVideosDatasByURL($url);
			}
			*/
			else if($feed_type==4) {
				$criteria2['chart'] = 'mostPopular';
				$criteria2['maxResults'] = $nb_display;
				$criteria2['youtube_api_key'] = $GLOBALS['ygp_youtube_wpress']['youtube_api_key'];
				$v1 = new Youtube_class();
				$videoData = $v1->getYoutubeVideos($criteria2);
			}
			else if($feed_type==5) {
				$criteria2['id'] = $playlist;
				$criteria2['maxResults'] = $nb_display;
				$criteria2['youtube_api_key'] = $GLOBALS['ygp_youtube_wpress']['youtube_api_key'];
				$v1 = new Youtube_class();
				$videoData = $v1->getYoutubeVideosByPlaylist($criteria2);
			}
			
			$url='';
			
			$display .= '<ul class="roundabout" style="display:none;">';
	    	
		    	for($i=0; $i<count($videoData['videos']); $i++) {
		    		$id = $videoData['videos'][$i]['videoid'];
		    		$videoCode = $videoData['videos'][$i]['videoid'];
		    		$title = $videoData['videos'][$i]['title'];
		    		$thumbnail = $videoData['videos'][$i]['thumbnail'];
		    		
		    		//$image = str_replace('default.jpg', '0.jpg', $thumbnail);
		    		
		    		$type_id=2;
		    		$videoType=1;
		    		
		    		//$description_content .= '<div id="description_'.$id.'" style="display:none;">'.$title.'</div>';
		    		
		    		$display .= '<li class="carousel_entry" data-id="'.$id.'" data-type="'.$type_id.'" data-video-type="'.$videoType.'" data-video-code="'.$videoCode.'" data-width="'.$front_image_width.'" data-height="'.$front_image_height.'">';
		    			
		    			if($url!='') $display .= '<a href="'.$url.'">';
				    		
				    		$display .= '<img src="'.$thumbnail.'">';
				    		$display .= '<span class="play_icon"></span>';
				    		
			    		if($url!='') $display .= '</a>';
			    		
		    		$display .= '</li>';
		    	}
	    	
	    	$display .= '</ul>';
	    	
	    	/*
	    	$display .= $description_content;
	    	if($description_display=='2') $display = '<div id="carousel_description_box" style="max-width: '.$carousel_width.'; margin-left: '.$margin_left.'; text-align:center;"></div>'.$display;
	    	else if($description_display=='1') $display .= '<div id="carousel_description_box" style="max-width: '.$carousel_width.'; margin-left: '.$margin_left.'; text-align:center;"></div>';
	    	*/
		}
		
		//Regular carousel
		else {
			
			$list = $d1->get_carousel_entries(array('carousel_id'=>$id));
			
			$display .= '<ul class="roundabout">';
	    	
	    	for($i=0; $i<count($list); $i++) {
	    		$id = $list[$i]['id'];
	    		$type_id = $list[$i]['type_id'];
	    		$url = $list[$i]['url'];
	    		$description = $list[$i]['description'];
	    		
	    		$videoType='';
	    		$videoCode='';
	    		
				if($type_id==2 && $url!='') {
					//YouTube
					preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
					$videoCode = @$matches[0];
					if($videoCode!='') {
						$videoType='1';
					}
					else {
						//Vimeo
	    				preg_match("/^http:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/", $url, $matches);
	    				$videoCode = $matches[3];
	    				if($videoCode!='') {
		    				$videoType='2';
	    				}
	    				else {
		    				preg_match("/^https:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/", $url, $matches);
		    				$videoCode = $matches[3];
		    				if($videoCode!='') {
			    				$videoType='2';
		    				}
	    				}
					}
				}
				
	    		$display .= '<li class="carousel_entry" data-id="'.$id.'" data-type="'.$type_id.'" data-video-type="'.$videoType.'" data-video-code="'.$videoCode.'" data-width="'.$front_image_width.'" data-height="'.$front_image_height.'">';
	    			
	    			$description_content .= '<div id="description_'.$id.'" style="display:none;">'.$description.'</div>';
	    			
	    			if($url!='') {
	    				$display .= '<a href="'.$url.'">';
			    	}
			    	
			    		$display .= '<img src="'.$list[$i]['image'].'">';
			    		//$display .= '<div style="width:600px;">'.$list[$i]['description'].'</div>';
			    		if($type_id==2) {
			    			if($i==0) $display .= '<span class="play_icon play-video"></span>';
			    			else $display .= '<span class="play_icon"></span>';
			    		}
			    		
		    		if($url!='') $display .= '</a>';
		    		
	    		$display .= '</li>';
	    	}
			
	    	$display .= '</ul>';
	    	
	    	$display .= $description_content;
	    	
	    	if($description_display=='2') $display = '<div id="carousel_description_box" style="max-width: '.$carousel_width.'; margin-left: '.$margin_left.'; text-align:center;"></div>'.$display;
	    	else if($description_display=='1') $display .= '<div id="carousel_description_box" style="max-width: '.$carousel_width.'; margin-left: '.$margin_left.'; text-align:center;"></div>';
		}
		    	
		return $display;
	}
	
}

?>