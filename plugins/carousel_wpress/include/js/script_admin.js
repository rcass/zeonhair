
function carouselSelectYoutubeFeedType(feed_type) {
	jQuery('.youtube_username_box').hide();
	jQuery('.youtube_keywords_box').hide();
	//jQuery('.youtube_standard_feed_box').hide();
	jQuery('.youtube_playlist_box').hide();
	
	if(feed_type==1) {
		jQuery('.youtube_username_box').show();
	}
	else if(feed_type==2) {
		jQuery('.youtube_keywords_box').show();
	}
	/*
	else if(feed_type==4) {
		jQuery('.youtube_standard_feed_box').show();
	}
	*/
	else if(feed_type==5) {
		jQuery('.youtube_playlist_box').show();
	}
}

jQuery('#feed_type').live('change', function(event) {
	event.preventDefault();
	var id = jQuery(this).val();
	carouselSelectYoutubeFeedType(id);
});

jQuery('#save_youtube_carousel_btn').live('click', function(event) {
	event.preventDefault();
	
	var feed_type = jQuery('#youtube_form #feed_type').val();
	var nb_videos = jQuery('#youtube_form #nb_videos').val();
	if(feed_type=='') {
		alert('Please select the feed');
		exit();
	}
	else if(nb_videos=='') {
		alert('Please define a number of videos');
		exit();
	}
	
	var serialized_data = jQuery("#youtube_form").serialize();
	
	jQuery.ajax({
		type: 'POST',
		url: Carousel_wpress.ajaxurl,
		data: 'action=carousel_wpress_listener&method=save_youtube_entries&'+serialized_data,
		success: function(msg) {
			if(msg!='') alert(msg);
			window.location.reload();
		}
	});
});

/*
jQuery('#edit_carousel_form #type_id').live('change', function(event) {
	event.preventDefault();
	var id = jQuery(this).val();
	alert(id);
	//carouselSelectYoutubeFeedType(id);
});
*/

/*
Upload
*/
jQuery(document).ready(function() {
	jQuery('.carousel_upload_image_btn').click(function() {
		var id = jQuery(this).attr('data-id');
		window.send_to_editor = function(html) {
			img_url = jQuery('img', html).attr('src');
			jQuery('#'+id).val(img_url);
			tb_remove();
		}
	 	tb_show('', 'media-upload.php?type=image&TB_iframe=true');
	 	return false;
	});
});

/*
Carousel functions
*/

//Add Carousel
jQuery('#carousel_wpress_add_btn').live('click', function(event) {
	event.preventDefault();
	
	jQuery('#carousel_wpress_add_btn').attr('disabled','disabled');
	
	var name = jQuery('#add_carousel_form #name').val();
	var type_id = jQuery('#add_carousel_form #type_id').val();
	var description_display = jQuery('#add_carousel_form #description_display').val();
	
	if(name=='') {
		alert('Please specify your carousel name');
		jQuery('#carousel_wpress_add_btn').removeAttr('disabled');
		exit();
	}
	
	jQuery.ajax({
		type: 'POST',
		url: Carousel_wpress.ajaxurl,
		data: 'action=carousel_wpress_listener&method=save_carousel&name='+name+'&type_id='+type_id+'&description_display='+description_display,
		success: function(msg) {
			if(msg!='') {
				window.location = Carousel_wpress.admin_url+'admin.php?page=carousel-wpress-edit-entries&carousel_id='+msg;
			}
			else {
				jQuery('#carousel_wpress_add_btn').removeAttr('disabled');
				alert('An error happened adding your carousel');
			}
		}
	});
});

//Edit Carousel
jQuery('#carousel_wpress_edit_btn').live('click', function(event) {
	event.preventDefault();
	
	jQuery('#carousel_wpress_edit_btn').attr('disabled','disabled');
	
	var name = jQuery('#edit_carousel_form #name').val();
	
	if(name=='') {
		alert('Please specify your carousel name');
		jQuery('#carousel_wpress_edit_btn').removeAttr('disabled');
		exit();
	}
	
	var serialized_data = jQuery("#edit_carousel_form").serialize();
	
	jQuery.ajax({
		type: 'POST',
		url: Carousel_wpress.ajaxurl,
		data: 'action=carousel_wpress_listener&method=edit_carousel&'+serialized_data,
		success: function(msg) {
			window.location.reload();
		}
	});
});

//Delete Carousel
jQuery(".carousel_wpress_delete_btn").live('click', function(event) {
	event.preventDefault();
	if (confirm("Are you sure you want to delete this carousel?")) {
		var id = jQuery(this).attr('id');
		jQuery.ajax({
			type: 'POST',
			url: Carousel_wpress.ajaxurl,
			data: 'action=carousel_wpress_listener&method=delete_carousel&id='+id,
			success: function(msg) {
				window.location.reload();
			}
		});
	}
});

//Add Carousel Entry
jQuery('#carousel_wpress_entry_add_btn').live('click', function(event) {
	event.preventDefault();
	
	jQuery('#carousel_wpress_entry_add_btn').attr('disabled','disabled');
	
	var serialized_data = jQuery("#carousel_wpress_entry_form").serialize();
	var description = CKEDITOR.instances['item_description'].getData();
	
	jQuery.ajax({
		type: 'POST',
		url: Carousel_wpress.ajaxurl,
		data: 'action=carousel_wpress_listener&method=add_entry&'+serialized_data+'&description='+escape(description),
		success: function(msg) {
			if(msg!='') {
				window.location = Carousel_wpress.admin_url+'admin.php?page=carousel-wpress-edit-entries&carousel_id='+jQuery('#carousel_id').val();
			}
			else {
				jQuery('#carousel_wpress_entry_add_btn').removeAttr('disabled');
				alert('An error happened adding your entry');
			}
		}
	});
});

//Edit Carousel Entry
jQuery('#carousel_wpress_entry_edit_btn').live('click', function(event) {
	event.preventDefault();
	
	jQuery('#carousel_wpress_entry_edit_btn').attr('disabled','disabled');
	
	var serialized_data = jQuery("#carousel_wpress_entry_form").serialize();
	var description = CKEDITOR.instances['item_description'].getData();
	
	jQuery.ajax({
		type: 'POST',
		url: Carousel_wpress.ajaxurl,
		data: 'action=carousel_wpress_listener&method=edit_entry&'+serialized_data+'&description='+escape(description),
		success: function(msg) {
			window.location = Carousel_wpress.admin_url+'admin.php?page=carousel-wpress-edit-entries&carousel_id='+jQuery('#carousel_id').val();
		}
	});
});

//Delete Carousel Entry
jQuery('.carousel_wpress_delete_entry_btn').live('click', function(event) {
	event.preventDefault();
	
	if (confirm("Are you sure you want to delete this entry?")) {
		var entry_id = jQuery(this).attr('id');
		var carousel_id = jQuery('#carousel_id').val();
		
		jQuery.ajax({
			type: 'POST',
			url: Carousel_wpress.ajaxurl,
			data: 'action=carousel_wpress_listener&method=delete_entry&entry_id='+entry_id,
			success: function(msg) {
				window.location = Carousel_wpress.admin_url+'admin.php?page=carousel-wpress-edit-entries&carousel_id='+carousel_id;
			}
		});
	}
});

//Reorder Carousel Entries
function sortHandler(ui, event) {
	var ids = '';
	jQuery('#sortable').each(function(){
		jQuery(this).find('li').each(function(){
			var id = jQuery(this).attr('data-id');
			ids += id+',';
		});
	});
	
	jQuery.ajax({
		type: 'POST',
		url: Carousel_wpress.ajaxurl,
		data: 'action=carousel_wpress_listener&method=reorder_entries&ids='+ids,
		success: function(msg) {
			//alert(msg);
		}
	});
}