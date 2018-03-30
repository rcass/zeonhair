<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Pageoptions Frontend Popup HTML
 */
?>
<div id="themify-pageoptions-popup" class="mfp-hide page-option-popup">
	<div class="mfp-fix-head">
		<div class="custom-popup-close"></div>
		<input type="button" id="pageoptions_submit_settings" value="Save" class="save-button"/>
	</div>
	<div class="mfp-content-form">
		<form class="form" id="ajax-pageoptions-form" action="#">
			<input type="hidden" name="action" value="tf_update_page_options" />
			<input type="hidden" name="post_id" value="<?php echo get_the_id() ?>" />
			<input type="hidden" name="themify_proper_save" value="true" />
			<input type="hidden" name="post_type" value="<?php echo get_post_type(); ?>" />
			<div id="poststuff" class="custom-popup-body">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="postbox-container-2" class="metabox-holder columns-2"> 
						<div id="normal-sortables" class="meta-box-sortables ui-sortable"> 
							<div id="themify-meta-boxes" class="postbox"> 
								<div class="inside">
									<?php $this->get_meta_boxes(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div id="updated-page-html" style="display:none;"></div>
<div id="themify_pageoptios_alert" class="alert"></div>