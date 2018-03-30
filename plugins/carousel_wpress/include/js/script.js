jQuery(document).ready(function() {
	jQuery('.roundabout').show();
	jQuery('.roundabout').roundabout({shape: Carousel2_wpress.shape, 'tilt': Carousel2_wpress.tilt, 'bearing':Carousel2_wpress.bearing,  
	'autoplay':Carousel2_wpress.autoplay, 'autoplayDuration':Carousel2_wpress.autoplayDuration, 'autoplayPauseOnHover':Carousel2_wpress.autoplayPauseOnHover, 'responsive':Carousel2_wpress.responsive,
	'minScale':Carousel2_wpress.minScale, 'maxScale':Carousel2_wpress.maxScale,
	'clickToFocusCallback':carouselEntryFocus, 'dropCallback':carouselEntryFocus, 'enableDrag':Carousel2_wpress.enableDrag,
	'debug':false});
	carouselEntryFocus();
});

function autoplayCallback() {
	var itemBox = jQuery('li.roundabout-in-focus');
	var id = itemBox.attr('data-id');
	var description = jQuery('#description_'+id).html();
	jQuery('#carousel_description_box').html(description);
	carouselEntryFocus();
}

function carouselEntryFocus() {
	var itemBox = jQuery('li.roundabout-in-focus');
	var id = itemBox.attr('data-id');
	var type = itemBox.attr('data-type');
	var videoType = itemBox.attr('data-video-type');
	var videoCode = itemBox.attr('data-video-code');
	var width = itemBox.attr('data-width');
	var height = itemBox.attr('data-height');
	var embed;
	var description = jQuery('#description_'+id).html();
	
	jQuery('#carousel_description_box').html(description);
	
	var itemBox2 = jQuery.data(document.body, 'carousel_itemBox');
	var img2 = jQuery.data(document.body, 'carousel_img');
	if(itemBox2!=undefined && itemBox2!='') {
		itemBox2.html(img2);
		jQuery.data(document.body, 'carousel_itemBox', '');
		jQuery.data(document.body, 'carousel_img', '');		
	}
	
	if(videoCode!='') {
		//save image entry
		jQuery.data(document.body, 'carousel_itemBox', itemBox);
		jQuery.data(document.body, 'carousel_img', itemBox.html());
		
		if(videoType=='1') {
			var embed = '<iframe width="'+width+'" height="'+height+'" src="http://www.youtube.com/embed/'+videoCode+'" frameborder="0" allowfullscreen></iframe>';
		}
		else if(videoType=='2') {
			var embed = '<iframe src="http://player.vimeo.com/video/'+videoCode+'" width="'+width+'" height="'+height+'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}
		itemBox.html(embed);
	}
}

jQuery('.carousel_video_display').live('click', function(event) {
	event.preventDefault();
	var videoType = jQuery(this).attr('data-video-type');
	var videoCode = jQuery(this).attr('data-video-code');
	var width = jQuery(this).attr('data-width');
	var height = jQuery(this).attr('data-height');
	
	if(videoType=='1') {
		var embed = '<iframe width="'+width+'" height="'+height+'" src="http://www.youtube.com/embed/'+videoCode+'" frameborder="0" allowfullscreen></iframe>';
	}
	else if(videoType=='2') {
		var embed = '<iframe src="http://player.vimeo.com/video/'+videoCode+'" width="'+width+'" height="'+height+'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}
	
	var itemBox = jQuery(this).closest('li');
	jQuery.data(document.body, 'carousel_itemBox', itemBox);
	jQuery.data(document.body, 'carousel_img', itemBox.html());
	itemBox.html(embed);
});