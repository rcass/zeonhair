<?php

class Carousel_wpress_activation
{
	static $opt_name = 'ygp_carousel_wpress';
	
	function __construct(){
		add_action('wp_ajax_nopriv_'.self::$opt_name.'_activation_wpress_listener',array(__CLASS__,'ajax_listener'));
		add_action('wp_ajax_'.self::$opt_name.'_activation_wpress_listener',array(__CLASS__,'ajax_listener'));
		
		if(is_admin()) {
			if($this->verify_activation()) {
				require_once dirname( __FILE__ ).'/admin.php';
			}
			else {
				add_action('admin_menu', array(__CLASS__, 'config_page_init') );
			}
		}		
	}
	
	function config_page_init() {
		if (function_exists('add_submenu_page'))
			add_submenu_page('plugins.php', $GLOBALS[self::$opt_name]['item_name'], $GLOBALS[self::$opt_name]['item_name'], 'manage_options', $GLOBALS[self::$opt_name]['page_slug'], array(__CLASS__, 'display_activation_page'));
	}
	
	function display_activation_page() {
		?>
		<div class="wrap">
		<div class="metabox-holder">
			
			<h2>Activation page</h2>
			<b><?php echo $GLOBALS[self::$opt_name]['item_name']; ?></b>
			<hr style="background:#ddd;color:#ddd;height:1px;border:none;">
			<br>
			
			<?php
			echo self::plugin_activation();
			?>
		
		</div>
		</div>
		
		<?php
	}
	
	function verify_activation(){
		$d=$this->get_domain();
		$o=get_option($GLOBALS[self::$opt_name]['plugin_code'].'-'.substr($d, 0, 20));
		if($o!=''){
			if($o==md5($GLOBALS[self::$opt_name]['plugin_code'])) $code=1;
		}
		return $code;
	}
		
	function get_domain(){
		$s=site_url();
		$p=parse_url($s);
		$d=$p['host'];
		return $d;
	}
	
	function plugin_activation(){
		$d.='<script>
		jQuery( document ).ready(function() {
			jQuery("#purchase_code").focus();
		});
		jQuery("#activate_plugin_btn").live(\'click\', function(event) {
			event.preventDefault();
			var purchase_code = jQuery("#purchase_code").val();
			jQuery.ajax({
				type: \'POST\',
				url: \''.admin_url('admin-ajax.php').'\',
				data: \'action='.self::$opt_name.'_activation_wpress_listener&method=plugin_activation&purchase_code=\'+purchase_code,
				success: function(msg) {
					if(msg!="") alert(msg);
					window.location.reload();
				}
			});
		});
		</script>';
		$d.='<p style="margin-top:0px;"><b>Purchase code:</b></p>';
		$d.='<form><input type="text" id="purchase_code" name="purchase_code" style="width:50%;"> ';
		$d.='<input type="submit" id="activate_plugin_btn" value="Activate my plugin"></form>';
		$d.='<p><a href="http://yougapi.com/2014/07/find-my-envato-item-purchase-code/" target="_blank">Where can I find my purchase code?</a></p>';
		$d.='<p>You have a question or need help with the activation? <a href="mailto:contact@yougapi.com">contact@yougapi.com</a></p>';
		return $d;
	}
	
	function ajax_listener(){
		$method=$_POST['method'];
		$purchase_code=$_POST['purchase_code'];
		$verif_url='http://yougapi.com/updates/verify_purchase.php';
		
		if($method=='plugin_activation'){
			$url=$verif_url.'?item='.$GLOBALS[self::$opt_name]['plugin_code'].'&url='.site_url().'&purchase_code='.$purchase_code;
			$data=wp_remote_get($url);
			$data=json_decode($data['body'],true);
			
			if($data['code']==2){
				echo $data['message'];
			}
			else if($data['code']==1){
				$parse=parse_url(site_url());
				if(md5($parse['host'])==$data['domain']){
					update_option($GLOBALS[self::$opt_name]['plugin_code'].'-'.substr($parse['host'], 0, 20), md5($GLOBALS[self::$opt_name]['plugin_code']));
					update_option($GLOBALS[self::$opt_name]['plugin_code'].'-'.substr($parse['host'], 0, 20).'-license', $purchase_code);
					echo $data['message'];
				}
				else{
					echo 'Error (domain error) activating your plugin. Please contact our support team for assistance';
				}
			}
			else {
				echo 'Error (connection error) activating your plugin. Please contact our support team for assistance';
			}
			exit;
		}
	}
}

new Carousel_wpress_activation();

?>