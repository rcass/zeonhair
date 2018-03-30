<?php

class Carousel_wpress_admin {	
	
	function Carousel_wpress_admin() {
		add_action( 'admin_menu', array(__CLASS__, 'config_page_init') );
		//add_action( 'admin_print_scripts', array(__CLASS__, 'config_page_scripts'));
		
		add_action( 'admin_init', array(__CLASS__, 'my_plugin_admin_init') );
	}
	
	function my_plugin_admin_init() {
		wp_enqueue_script( 'carousel_wpress_admin_js', plugin_dir_url( __FILE__ ).'include/js/script_admin.js');
		wp_localize_script('carousel_wpress_admin_js', 'Carousel_wpress', array('ajaxurl'=>admin_url('admin-ajax.php'), 'admin_url'=>admin_url()));
		
		wp_register_script('ckeditor_js', plugin_dir_url( __FILE__ ).'include/ckeditor/ckeditor.js', array('jquery'));
		wp_enqueue_script('ckeditor_js');
				
		if($_GET['page']=='carousel-wpress-edit-entries' || $_GET['page']=='carousel-wpress-add-entry') {
			wp_register_script('jqueryui_js', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array('jquery'));
			wp_enqueue_script('jqueryui_js');
			
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');			
		}
	}
	
	function config_page_init() {
		if (function_exists('add_submenu_page')) {
			add_menu_page( 'Carousel WPress', 'Carousel WPress', 'manage_options', 'carousel-wpress', array(__CLASS__, 'display_carousel_list'), '', 22 );
			add_submenu_page('carousel-wpress', 'Add a Carousel', 'Add a Carousel', 'manage_options', 'carousel-wpress-add', array(__CLASS__, 'carousel_add'));
			add_submenu_page('carousel-wpress', '', '', 'manage_options', 'carousel-wpress-edit-entries', array(__CLASS__, 'carousel_edit_entries'));
			add_submenu_page('carousel-wpress', '', '', 'manage_options', 'carousel-wpress-edit', array(__CLASS__, 'carousel_edit'));
			add_submenu_page('carousel-wpress', '', '', 'manage_options', 'carousel-wpress-view', array(__CLASS__, 'carousel_display'));
			add_submenu_page('carousel-wpress', '', '', 'manage_options', 'carousel-wpress-add-entry', array(__CLASS__, 'carousel_add_entry'));
		}
	}
	
	//display Carousels list
	public static function display_carousel_list() {
		?>
		
		<div class="wrap">
		<div class="metabox-holder">
		<br>
		
		<?php
		//get carousels list
		$db1 = new Carousel_wpress_db();
		$tables = $db1->get_carousel_list();
		
		//get carousels entries
		$entries = $db1->get_nb_entries_per_carousel();
		$entries_tab = array();
		for($i=0; $i<count($entries); $i++) {
			$entries_tab[$entries[$i]['id']] = $entries[$i]['nb'];
		}
		
		echo '<h1 style="margin-top:0px;">Carousels list';
		if(count($tables)>0) echo ' <font size="-2">( <a href="?page=carousel-wpress-add">Add a Carousel</a> )</font>';
		echo '</h1>';
		echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
		
		if(count($tables)>0) {
			for($i=0; $i<count($tables); $i++) {
				$id = $tables[$i]['id'];
				$name = $tables[$i]['name'];
				$type_id = $tables[$i]['type_id'];
				$options = $tables[$i]['options'];
				
				$options = json_decode($options, true);
				if($entries_tab[$id]=='') $entries_tab[$id]=0;
				
				$nb_entries = 0;
				if($type_id==0) $nb_entries = $entries_tab[$id];
				else if($type_id==2) $nb_entries = $options['youtube']['nb_videos'];
				 
				echo '<table style="border-bottom: 1px solid #e7e7e7; width:100%;"><tr>';
				
				echo '<td><h3>'.$tables[$i]['name'];
				if($type_id==2) {
					echo ' <font size="-2">('.$GLOBALS['ygp_carousel_wpress']['youtube_feed_type'][$options['youtube']['feed_type']].')</font>';
				}
				/*
				if($nb_entries>0) {
					if($nb_entries>1) echo ' <font size="-2">('.$nb_entries.' entries)</font>';
					else echo ' <font size="-2">('.$nb_entries.' entry)</font>';				
				}
				*/
				echo '</h3></td>';
				
				echo '<td align="right">';
				echo '<a href="?page=carousel-wpress-view&id='.$id.'">Preview</a> - ';
				echo '<a href="?page=carousel-wpress-edit&id='.$id.'">Edit Carousel</a> - ';
				echo '<a href="?page=carousel-wpress-edit-entries&carousel_id='.$id.'">Edit entries</a> ('.$nb_entries.') - ';
				echo '<a href="#" id="'.$id.'" class="carousel_wpress_delete_btn" style="color:red;">Delete</a>';
				echo '</td>';
				
				echo '</tr></table>';
			}
		}
		else {
			echo '<br>You don\'t have any Carousel yet: <a href="?page=carousel-wpress-add">Add a new Carousel</a>';
		}
				
		?>
		</div></div>
		<?php
	}
	
	function carousel_add_entry() {
		?>
		
		<div class="wrap">
		<div class="metabox-holder">
		<br>
		
		<?php
		
		//get carousel data
		$db1 = new Carousel_wpress_db();
		$item = $db1->get_carousel_list(array('id'=>$carousel_id));
		$name = $item[0]['name'];
		$type_id = $item[0]['type_id'];
		
		echo '<h1 style="margin-top:0px; margin-bottom:10px;">Add Carousel entry ';
		echo '<font size="-2">( <a href="?page=carousel-wpress">Back to the list</a> )</font>';
		echo '</h1>';
		if($name!='') echo '<b>Name: '.$name.'</b>';
		echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
		
		echo '<form method="post" id="carousel_wpress_entry_form" name="carousel_wpress_entry_form">';
			
			echo '<input type="hidden" id="carousel_id" name="carousel_id" value="'.$_GET['id'].'">';
			
			echo '<div style="margin-bottom:5px;"><label><b>Type:</b></label></div>';
			echo '<div style="margin-bottom:10px;"><select id="type_id" name="type_id" style="width:440px;">';
			foreach($GLOBALS['ygp_carousel_wpress']['carousel_entry_type'] as $ind=>$value) {
				if($entry_type==$ind) echo '<option selected value="'.$ind.'">'.$value.'</option>';
				else echo '<option value="'.$ind.'">'.$value.'</option>';
			}
			echo '</select></div>';
			
			echo '<div style="margin-bottom:5px;"><label><b>Title:</b></label></div>';
			echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="title" name="title" style="width:540px;"></div>';
			
			echo '<div style="margin-bottom:5px;"><label><b>Url:</b></label></div>';
			echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="url" name="url" style="width:540px;"></div>';
			echo '<div style="margin-bottom:10px;">The form of the URL for the "video" types need to be:<br>YouTube: https://www.youtube.com/watch?v=KCHeswihfsI<br>Vimeo: https://vimeo.com/57875089</div>';
			
			echo '<div style="margin-bottom:5px;"><label><b>Image URL:</b></label></div>';
			echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="image" name="image" style="width:540px;"> ';
			echo '<input type="button" class="carousel_upload_image_btn" value="Upload" data-id="image"></div>';
			
			echo '<div style="margin-bottom:5px;"><label><b>Description:</b></label></div>';
			echo '<div><textarea class="widefat ckeditor" id="item_description" name="item_description" style="width:540px; height:120px;"></textarea></div>';
			
			echo '<div class="submit">';
			echo '<input class="button-primary" type="submit" id="carousel_wpress_entry_add_btn" value="Add">';
			echo '</div>';
			
		echo '</form>';
		
		?>
		</div></div>
		<?php
	}
	
	function carousel_edit_entries() {
		?>
		
		<div class="wrap">
		<div class="metabox-holder">
		<br>
		
		<?php
		
		$carousel_id = $_GET['carousel_id'];
		$id = $_GET['id'];
		
		//get carousel data
		$db1 = new Carousel_wpress_db();
		$item = $db1->get_carousel_list(array('id'=>$carousel_id));
		$name = $item[0]['name'];
		$type_id = $item[0]['type_id'];
		
		echo '<h1 style="margin-top:0px; margin-bottom:10px;">Edit Carousel entries <font size="-2">(
		<a href="?page=carousel-wpress-edit&id='.$carousel_id.'">Edit Carousel</a> - 
		<a href="?page=carousel-wpress-view&id='.$carousel_id.'">Preview</a> - 
		<a href="?page=carousel-wpress">Back to the list</a> )</font></h1>';
		if($name!='') echo '<b>Name: '.$name.'</b>';
		echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
		
		if(count($item)>0) {
						
			//Standard Carousel
			if($type_id==0) {
				$entries = $db1->get_carousel_entries(array('carousel_id'=>$carousel_id, 'id'=>$id));
				
				if(count($entries)>0) {
					
					if($id=='') {
						
						echo 'You can click on an entry to edit it or drag & drop the entries to reorder their positions';
						echo '<div><a href="?page=carousel-wpress-add-entry&id='.$carousel_id.'">Add a new entry</a></div>';
						echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
						
						?>
						
						<script>
						jQuery(document).ready(function() {
							jQuery( "#sortable" ).sortable({ containment: "parent", update: function(event, ui) { sortHandler(ui, event); } });
						})
						</script>
						
						<ul id="sortable">
						
							<?php
							for($i=0; $i<count($entries); $i++) {
								$entry_id = $entries[$i]['id'];
								$entry_title = $entries[$i]['title'];
								
								echo '<li style="margin-top:20px; margin-bottom:10px; padding-bottom:10px; border-bottom: 1px solid #e7e7e7;" data-id="'.$entry_id.'" data-title="'.$entry_title.'">';
									if($entries[$i]['image']!='') {
										echo '<a href="?page=carousel-wpress-edit-entries&carousel_id='.$carousel_id.'&id='.$entry_id.'">';
										echo '<img src="'.$entries[$i]['image'].'" style="max-width:120px; max-height:120px; border:0px;">';
										echo '</a><br>';
									}
									echo $entries[$i]['title'];
									echo ' ( <a href="?page=carousel-wpress-edit-entries&carousel_id='.$carousel_id.'&id='.$entry_id.'">Edit entry</a> )';
								echo '</li>';
							}
							?>
						
						</ul>
						
						<?php
					}
					
					else {
						
						echo '<br><form method="post" id="carousel_wpress_entry_form" name="carousel_wpress_entry_form">';
							
							echo '<input type="hidden" id="carousel_id" name="carousel_id" value="'.$carousel_id.'">';
							echo '<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">';
							
							echo '<img src="'.$entries[0]['image'].'" style="max-width:120px; max-height:120px; padding-right:10px; border:0px;">';
							
							echo '<div style="margin-bottom:5px;"><label><b>Type:</b></label></div>';
							echo '<div style="margin-bottom:10px;"><select id="type_id" name="type_id" style="width:140px;">';
							foreach($GLOBALS['ygp_carousel_wpress']['carousel_entry_type'] as $ind=>$value) {
								if($entries[0]['type_id']==$ind) echo '<option selected value="'.$ind.'">'.$value.'</option>';
								else echo '<option value="'.$ind.'">'.$value.'</option>';
							}
							echo '</select></div>';
							
							echo '<div style="margin-bottom:5px;"><label><b>Title:</b></label></div>';
							echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="title" name="title" value="'.$entries[0]['title'].'" style="width:540px;"></div>';
							
							echo '<div style="margin-bottom:5px;"><label><b>Url:</b></label></div>';
							echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="url" name="url" value="'.$entries[0]['url'].'" style="width:540px;"></div>';
							echo '<div style="margin-bottom:10px;">The form of the URL for the "video" types need to be:<br>YouTube: https://www.youtube.com/watch?v=KCHeswihfsI<br>Vimeo: https://vimeo.com/57875089</div>';
							
							echo '<div style="margin-bottom:5px;"><label><b>Image URL:</b></label></div>';
							echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="image" name="image" value="'.$entries[0]['image'].'" style="width:540px;"> <input type="button" class="carousel_upload_image_btn" value="Upload" data-id="image"></div>';
							
							echo '<div style="margin-bottom:5px;"><label><b>Description:</b></label></div>';
							echo '<div style="margin-bottom:10px; max-width:740px;"><textarea class="ckeditor" id="item_description" name="item_description" style="height:120px;">'.$entries[0]['description'].'</textarea></div>';
							
							echo '<div class="submit" style="padding-bottom:0px; padding-top:0px;">';
							echo '<input class="button-primary" type="submit" id="carousel_wpress_entry_edit_btn" value="Edit"> 
							- <a href="?page=carousel-wpress-edit-entries&carousel_id='.$carousel_id.'">Cancel</a>
							- <a href="#" id="'.$entries[0]['id'].'" class="carousel_wpress_delete_entry_btn" style="color:red;">Delete this entry</a>';
							echo '</div>';
							
						echo '</form>';
					}
					
				}
				else {
					echo '<div>No entries found for this Carousel</div>';
					echo '<div><a href="?page=carousel-wpress-add-entry&id='.$carousel_id.'">Add a new entry</a></div>';
				}
			}
			//End Standard Carousel display
			
			//START YouTube carousel
			else if($type_id==2) {
				$options = $item[0]['options'];
				$options = json_decode($options, true);
				$feed_type = $options['youtube']['feed_type'];
				$username = $options['youtube']['username'];
				$keywords = $options['youtube']['keywords'];
				$feed = $options['youtube']['feed'];
				$playlist = $options['youtube']['playlist'];
				$nb_videos = $options['youtube']['nb_videos'];
				
				if($nb_videos=='') $nb_videos = 6;
				
				$datas_youtubeStandardFeeds = array("mostPopular"=>"Most popular");               
				
				?>
				
				<script>
				jQuery(document).ready(function($) {
					carouselSelectYoutubeFeedType(<?php echo $feed_type; ?>);
				});
				</script>
				
				<div style="margin-bottom:5px;">Please note that the YouTube Type Carousel request to have the <a href="http://codecanyon.net/item/youtube-videos-for-wordpress/233547" target="_blank">YouTube Videos for WordPress</a> installed and activated</div>
				<?php
				
				echo '<div>';
				if(class_exists('Youtube_class')) echo '<span style="color:green;">Seems like the YouTube plugin is installed</span> - Make sure the YouTube API key is defined (<a href="?page=youtube_wpress">check it here</a>)';
				else echo '<span style="color:red;">Seems like the YouTube plugin is missing</span>';
				echo '</div><br>';
				
				echo '<form id="youtube_form" name="youtube_form">';
					
					echo '<input type="hidden" id="id" name="id" value="'.$carousel_id.'">';
					
					echo '<div style="margin-bottom:10px;">';
						echo '<label><b>Feed: </b></label><br>';
						echo '<select id="feed_type" name="feed_type"><option></option>';
						foreach($GLOBALS['ygp_carousel_wpress']['youtube_feed_type'] as $ind=>$value) {
							if($ind==$feed_type) echo '<option value="'.$ind.'" selected>'.$value.'</option>';
							else echo '<option value="'.$ind.'">'.$value.'</option>';
						}
						echo '</select>';
					echo '</div>';
					
					echo '<div style="margin-bottom:10px;" class="youtube_username_box">';
						echo '<label><b>Username:</b></label><br>';
						echo '<input type="text" id="username" name="username" value="'.$username.'">';
					echo '</div>';
					
					echo '<div style="margin-bottom:10px;" class="youtube_keywords_box">';
						echo '<label><b>Keywords:</b></label><br>';
						echo '<input type="text" id="keywords" name="keywords" value="'.$keywords.'">';
					echo '</div>';
					
					/*
					echo '<div style="margin-bottom:10px;" class="youtube_standard_feed_box">';
						echo '<label><b>Standard feed:</b></label><br>';
						echo '<select id="feed" name="feed">';
						foreach($datas_youtubeStandardFeeds as $ind=>$value) {
							if($ind==$feed) echo '<option value="'.$ind.'" selected>'.$value.'</option>';
							else echo '<option value="'.$ind.'">'.$value.'</option>';
						}
						echo '</select>';
					echo '</div>';
					*/
					
					echo '<div style="margin-bottom:10px;" class="youtube_playlist_box">';
						echo '<label><b>Playlist id:</b></label><br>';
						echo '<input type="text" id="playlist" name="playlist" value="'.$playlist.'">';
					echo '</div>';
					
					echo '<div style="margin-bottom:10px;">';
						echo '<label><b>Number of videos:</b></label><br>';
						echo '<input type="text" id="nb_videos" name="nb_videos" value="'.$nb_videos.'">';
					echo '</div>';
					
					echo '<input type="submit" id="save_youtube_carousel_btn" class="button-primary" value="Save changes">';
				echo '</form>';
			}
			//END YouTube carousel
		}
		else {
			echo '<div>No Carousel found with the given id.</div>';
			echo '<a href="?page=carousel-wpress">Back to list</a>';
		}
		
		?>
		</div></div>
		<?php
	}
	
	function carousel_add() {
		?>
		
		<div class="wrap">
		<div class="metabox-holder">
		<br>
		
		<?php
		
		echo '<h1 style="margin-top:0px;">Create a Carousel ';
		echo '<font size="-2">( <a href="?page=carousel-wpress">Back to the list</a> )</font>';
		echo '</h1>';
		echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
		
		echo '<form method="post" id="add_carousel_form">';
			
			echo '<div style="margin-bottom:5px;"><label><b>Name:</b></label></div>';
			echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="name" style="width:440px;"></div>';
			
			echo '<div style="margin-bottom:5px;"><b>Type:</b></div>';
			echo '<div style="margin-bottom:10px;"><select class="widefat" type="text" id="type_id" style="width:440px;">';
			foreach($GLOBALS['ygp_carousel_wpress']['carousel_type'] as $ind=>$value) {
				echo '<option value="'.$ind.'">'.$value.'</option>';
			}
			echo '</select></div>';
			
			echo '<div style="margin-bottom:5px;"><b>Description display:</b></div>';
			echo '<div style="margin-bottom:10px;"><select class="widefat" type="text" id="description_display" style="width:440px;">';
			foreach($GLOBALS['ygp_carousel_wpress']['carousel_description_type'] as $ind=>$value) {
				echo '<option value="'.$ind.'">'.$value.'</option>';
			}
			echo '</select></div>';
			
			echo '<div class="submit">';
			echo '<input class="button-primary" type="submit" id="carousel_wpress_add_btn" value="Save and Continue"> - <a href="?page=carousel-wpress">Cancel</a>';
			echo '</div>';
			
		echo '</form>';
		
		?>
		</div></div>
		<?php
	}
	
	function carousel_edit() {
		?>
		
		<div class="wrap">
		<div class="metabox-holder">
		<br>
		
		<?php
		
		$id = $_GET['id'];
		
		echo '<h1 style="margin-top:0px;">Edit Carousel ';
		echo '<font size="-2">( <a href="?page=carousel-wpress-edit-entries&carousel_id='.$id.'">Edit entries</a>
		- <a href="?page=carousel-wpress">Carousels list</a> )</font>';
		echo '</h1>';
		echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
		
		$db1 = new Carousel_wpress_db();
		$item = $db1->get_carousel_list(array('id'=>$id));
		
		if(count($item)>0) {
		
			echo '<form method="post" id="edit_carousel_form" name="edit_carousel_form">';
				
				echo '<input type="hidden" id="id" name="id" value="'.$id.'">';
				
				echo '<div style="margin-bottom:5px;"><label><b>Name:</b></label></div>';
				echo '<div style="margin-bottom:10px;"><input class="widefat" type="text" id="name" name="name" style="width:440px;" value="'.$item[0]['name'].'"></div>';
				
				echo '<div style="margin-bottom:5px;"><b>Type:</b></div>';
				echo '<div style="margin-bottom:10px;"><select class="widefat" type="text" id="type_id" name="type_id" style="width:440px;">';
				foreach($GLOBALS['ygp_carousel_wpress']['carousel_type'] as $ind=>$value) {
					if($ind==$item[0]['type_id']) echo '<option selected value="'.$ind.'">'.$value.'</option>';
					else echo '<option value="'.$ind.'">'.$value.'</option>';
				}
				echo '</select></div>';
				
				echo '<div style="margin-bottom:5px;"><b>Description display:</b></div>';
				echo '<div style="margin-bottom:10px;"><select class="widefat" type="text" id="description_display" name="description_display" style="width:440px;">';
				foreach($GLOBALS['ygp_carousel_wpress']['carousel_description_type'] as $ind=>$value) {
					if($ind==$item[0]['description_display']) echo '<option selected value="'.$ind.'">'.$value.'</option>';
					else echo '<option value="'.$ind.'">'.$value.'</option>';
				}
				echo '</select></div>';
				
				echo '<div class="submit">';
				echo '<input class="button-primary" type="submit" id="carousel_wpress_edit_btn" value="Save changes"> - <a href="'.admin_url('admin.php').'?page=carousel-wpress">Cancel</a>';
				echo '</div>';
				
			echo '</form>';
		}
		else {
			echo 'No carousel found';
		}
		
		?>
		</div></div>
		<?php
	}
	
	function carousel_display() {
		
		$criteria = $_GET;
		unset($criteria['page']);
		unset($criteria['style']);
		
		//predefined styles
		if($_GET['style']=='1') {
			$criteria['tilt'] = '0';
			$criteria['marginTop'] = '30px';
			$criteria['marginBottom'] = '30px';
		}
		else if($_GET['style']=='2') {
			$criteria['tilt'] = '5';
		}
		else if($_GET['style']=='3') {
			$criteria['tilt'] = '-5';
			$criteria['marginTop'] = '40px';
			$criteria['marginBottom'] = '120px';
		}
		else if($_GET['style']=='4') {
			$criteria['shape'] = 'rollerCoaster';
			$criteria['tilt'] = '-5';
			$criteria['marginTop'] = '90px';
			$criteria['marginBottom'] = '100px';
		}
		else if($_GET['style']=='5') {
			$criteria['shape'] = 'rollerCoaster';
			$criteria['tilt'] = '-10';
			$criteria['marginTop'] = '90px';
			$criteria['marginBottom'] = '100px';
		}
		else if($_GET['style']=='6') {
			$criteria['shape'] = 'figure8';
			$criteria['tilt'] = '8';
			$criteria['marginTop'] = '60px';
			$criteria['marginBottom'] = '150px';
		}
		else if($_GET['style']=='7') {
			$criteria['shape'] = 'figure8';
			$criteria['tilt'] = '-5';
			$criteria['marginTop'] = '100px';
			$criteria['marginBottom'] = '60px';
		}
		else if($_GET['style']=='8') {
			$criteria['shape'] = 'diagonalRingLeft';
			$criteria['tilt'] = '0';
			$criteria['marginTop'] = '80px';
			$criteria['marginBottom'] = '120px';
		}
		else if($_GET['style']=='9') {
			$criteria['shape'] = 'diagonalRingRight';
			$criteria['tilt'] = '0';
			$criteria['marginTop'] = '80px';
			$criteria['marginBottom'] = '120px';
		}
		
		$shortcode_param = '';
		foreach($criteria as $ind=>$value) {
			if($value!='') $shortcode_param .= $ind.'="'.$value.'" ';
		}
		
		?>
		
		<div class="wrap">
		<div class="metabox-holder">
		<br>
		
		<?php
		$id = $_GET['id'];
		
		$db1 = new Carousel_wpress_db();
		$item = $db1->get_carousel_list(array('id'=>$id));
		
		echo '<h1 style="margin-top:0px; margin-bottom:10px;">Carousel preview <font size="-2">( <a href="?page=carousel-wpress-edit&id='.$id.'">Edit Carousel</a>
		- <a href="?page=carousel-wpress-edit-entries&carousel_id='.$id.'">Edit entries</a>
		- <a href="?page=carousel-wpress">Back to the list</a>
		)</font></h1>';
		if(count($item)>0) echo '<b>Name: '.$item[0]['name'].'</b>';
		echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
		
		if(count($item)>0) {
			
			echo '<h2><b>Shortcode to use:</b></h2><textarea style="width:800px; height:65px; margin-bottom:30px;">[carousel_wpress '.$shortcode_param.']</textarea><br>';
			
			$predefined_styles_tab = array('1'=>'Style 1', '2'=>'Style 2', '3'=>'Style 3', '4'=>'Style 4', '5'=>'Style 5',
			'6'=>'Style 6', '7'=>'Style 7', '8'=>'Style 8', '9'=>'Style 9');
						
			echo '<form>';
			echo '<input type="hidden" name="page" value="carousel-wpress-view">';
			echo '<input type="hidden" name="id" value="'.$_GET['id'].'">';
			echo 'Predefined styles: <select name="style" onchange="form.submit();"><option></option>';
			foreach($predefined_styles_tab as $ind=>$value) {
				if($_GET['style']==$ind) echo '<option selected value="'.$ind.'">'.$value.'</option>';
				else echo '<option value="'.$ind.'">'.$value.'</option>';
			}
			echo '</select>';
			echo '</form>';
			
			echo '<hr style="background:#ddd;color:#ddd;height:1px;border:none;">';
			
			$d1 = new Carousel2_wpress_display();
			$carousel = $d1->get_carousel($criteria);
			
			echo $carousel;
		}
		
		?>
		
		<div style="clear:both;"></div>
		<h1 style="margin-top:0px;"><b>General settings:</b></h1>
			
			<?php
			$true_false_tab = array('true', 'false');
			$shapes_tab = array('lazySusan', 'rollerCoaster', 'waterWheel', 'figure8', 'diagonalRingLeft', 'diagonalRingRight', 'tearDrop', 'theJuggler');
			
			echo '<form method="get">';
			
			echo '<input type="hidden" name="page" value="carousel-wpress-view">';
			echo '<input type="hidden" name="id" value="'.$_GET['id'].'">';
			
			echo '<table><tr>';
			
			echo '<td valign="top" style="padding-right:50px;">';
				
				echo '<h2><b>Positioning & sizes</b></h2>';
				
				echo '<p><label><b>Carousel width:</b> <small>(default: 720px)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="carousel_width" name="carousel_width" style="width:440px;" value="'.$criteria['carousel_width'].'"></p>';
				
				echo '<p><label><b>Carousel height:</b> <small>(default: 260px)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="carousel_height" name="carousel_height" style="width:440px;" value="'.$criteria['carousel_height'].'"></p>';
				
				echo '<p><label><b>Front image width:</b> <small>(default: 300px)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="front_width" name="front_width" style="width:440px;" value="'.$criteria['front_width'].'"></p>';
				
				echo '<p><label><b>Front image height:</b> <small>(default: 100%)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="front_height" name="front_height" style="width:440px;" value="'.$criteria['front_height'].'"></p>';
				
				echo '<p><label><b>Margin left:</b> <small>(default: 110px)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="margin_left" name="margin_left" style="width:440px;" value="'.$criteria['margin_left'].'"></p>';
				
				echo '<p><label><b>Margin top:</b> <small>(default: 120px)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="margin_top" name="margin_top" style="width:440px;" value="'.$criteria['margin_top'].'"></p>';
				
				echo '<p><label><b>Margin bottom:</b> <small>(default: 80px)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="margin_bottom" name="margin_bottom" style="width:440px;" value="'.$criteria['margin_bottom'].'"></p>';
				
				echo '<h2><b>Other settings</b></h2>';
				
				echo '<p><label><b>Enable Drag & Drop / touch navigation:</b> <small>(default: true)</small></label></p>';
				echo '<p><select id="enable_drag" name="enable_drag"><option></option>';
				foreach($true_false_tab as $ind) {
					if($ind==$criteria['enable_drag']) echo '<option selected value="'.$ind.'">'.$ind.'</option>';
					else echo '<option value="'.$ind.'">'.$ind.'</option>';
				}
				echo '</select></p>';
								
			echo '</td>';
			
			echo '<td valign="top">';
				
				//Slider options
				echo '<h2><b>Slider options</b></h2>';
				
				echo '<p><label><b>Shape:</b> <small>(default: lazySusan)</small></label></p>';
				echo '<p><select id="shape" name="shape"><option></option>';
				foreach($shapes_tab as $ind) {
					if($ind==$criteria['shape']) echo '<option selected value="'.$ind.'">'.$ind.'</option>';
					else echo '<option value="'.$ind.'">'.$ind.'</option>';
				}
				echo '</select></p>';
				
				echo '<p><label><b>Tilt:</b> <small>(default: 0.0)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="tilt" name="tilt" style="width:440px;" value="'.$criteria['tilt'].'"></p>';
				
				echo '<p><label><b>Bearing:</b> <small>(default: 0.0)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="bearing" name="bearing" style="width:440px;" value="'.$criteria['bearing'].'"></p>';
				
				echo '<p><label><b>Min scale:</b> <small>(default: 0.4)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="min_scale" name="min_scale" style="width:440px;" value="'.$criteria['min_scale'].'"></p>';

				echo '<p><label><b>Max scale:</b> <small>(default: 1)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="max_scale" name="max_scale" style="width:440px;" value="'.$criteria['max_scale'].'"></p>';
				
				//Autoplay settings
				echo '<h2><b>Autoplay settings</b></h2>';
				
				echo '<p><label><b>Autoplay:</b> <small>(default: false)</small></label></p>';
				echo '<p><select id="autoplay" name="autoplay"><option></option>';
				foreach($true_false_tab as $ind) {
					if($ind==$criteria['autoplay']) echo '<option selected value="'.$ind.'">'.$ind.'</option>';
					else echo '<option value="'.$ind.'">'.$ind.'</option>';
				}
				echo '</select></p>';
				
				echo '<p><label><b>Autoplay pause on hover:</b> <small>(default: true)</small></label></p>';
				echo '<p><select id="autoplay_pause_on_hover" name="autoplay_pause_on_hover"><option></option>';
				foreach($true_false_tab as $ind) {
					if($ind==$criteria['autoplay_pause_on_hover']) echo '<option selected value="'.$ind.'">'.$ind.'</option>';
					else echo '<option value="'.$ind.'">'.$ind.'</option>';
				}
				echo '</select></p>';
				
				echo '<p><label><b>Autoplay duration:</b> <small>(default: 3000)</small></label></p>';
				echo '<p><input class="widefat" type="text" id="autoplay_duration" name="autoplay_duration" style="width:440px;" value="'.$criteria['autoplay_duration'].'"></p>';
				
			echo '</td>';
			echo '</tr></table>';
			
			echo '<p class="submit" style="padding-bottom:0px; padding-top:0px;">';
			echo '<input class="button-primary" type="submit" value="Preview and generate shortcode">';
			echo '</p>';
			
			echo '</form>';
			
			?>
			
		</div></div>
		<?php
	}
	
}

new Carousel_wpress_admin();

?>