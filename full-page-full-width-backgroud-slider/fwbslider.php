<?php
/*
Plugin Name:Full Width Background Slider
Plugin URI: http://www.wpfruits.com/downloads/wp-plugins/full-page-full-width-background-slider-plugin-for-wordpress/
Description: This plugin will generate full width background slider for individual page and post with the help of custom fields..
Author: Nishant Jain, rahulbrilliant2004, tikendramaitry
Version: 1.2.3
Author URI: http://www.wpfruits.com/
*/
// ----------------------------------------------------------------------------------

// ADD Styles and Script in head section
include_once('admin/fwbups.php');
add_action('admin_init', 'fwbslider_backend_scripts');
add_action('wp_enqueue_scripts', 'fwbslider_frontend_scripts');

define('FWBLITE_URL', plugin_dir_url(__FILE__));

function fwbslider_backend_scripts() {
	if(is_admin()){
		wp_enqueue_script ('jquery');
		wp_enqueue_script( 'fwbslider_backend_scripts',plugins_url('admin/fwbslider_admin.js',__FILE__), array('jquery'));
		wp_enqueue_style( 'fwbslider_backend_scripts',plugins_url('admin/fwbslider_admin.css',__FILE__), false, '1.0.0' );
		wp_enqueue_script('farbtastic');
		wp_enqueue_style('farbtastic');	
		
		if(isset($_GET['page']) && $_GET['page']=="fwbslider"){
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');		
		}
	}
}

function fwbslider_frontend_scripts() {	
	if(!is_admin()){
		wp_enqueue_script('jquery');
		wp_enqueue_script('fwbslider',plugins_url('js/fwbslider.js',__FILE__), array('jquery'));
		wp_enqueue_style('fwbslider',plugins_url('css/fwbslider.css',__FILE__));
	}
}

// get fwbslider version
function fwbslider_get_version(){
	if ( ! function_exists( 'get_plugins' ) )
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}
include_once('admin/fwbups.php');

//--------------------------------------------------------------------------------------------------------------------------------------


//---------------------------------------------------------------------------------------------------------------------------------------
//------------------- MAIN ADMIN MENU OPTIONS -------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
add_action('admin_menu', 'fwbslider_plugin_admin_menu');
function fwbslider_plugin_admin_menu() {
     add_menu_page('FWB Slider Page', 'FWB Slider','administrator', 'fwbslider', 'fwbslider_backend_menu', plugins_url('images/icon.png',__FILE__));
}

//This function will create new database fields with default values
function fwbslider_defaults(){
	    $default = array(
		'fwbBgChkbox'=>0,
		'fwbBgcolor'=>'#F7D2D2',
		'fwbsduration'=>'5',
		'fwbstspeed'=>'2',
		'fwbslide1' => plugins_url('inc/images/slide1.jpg',__FILE__),
        'fwbslide2' => plugins_url('inc/images/slide2.jpg',__FILE__),
    	'fwbslide3' => plugins_url('inc/images/slide3.jpg',__FILE__),
		'fwbslide4' => plugins_url('inc/images/slide4.jpg',__FILE__),
		'fwbslide5' => plugins_url('inc/images/slide5.jpg',__FILE__),
		'fwbslide6' => plugins_url('inc/images/slide6.jpg',__FILE__)
    );
return $default;
}

// Runs when plugin is activated and creates new database field
register_activation_hook(__FILE__,'fwbslider_plugin_install');
add_action('admin_init', 'fwbs_plugin_redirect');

function fwbs_plugin_activate() {
    add_option('fwbs_plugin_do_activation_redirect', true);
}

function fwbs_plugin_redirect() {
    if (get_option('fwbs_plugin_do_activation_redirect', false)) {
        delete_option('fwbs_plugin_do_activation_redirect');
        wp_redirect('admin.php?page=fwbslider');
    }
}

function fwbslider_plugin_install() {
    add_option('fwbslider_options', fwbslider_defaults());
	fwbs_plugin_activate();
}	


// update the fwbslider options
if(isset($_POST['fwbslider_update'])){
	update_option('fwbslider_options', fwbslider_updates());
}

function fwbslider_updates() {
	$options = $_POST['fwbslider_options'];
	    $update_val = array(
		'fwbBgChkbox'=> $options['fwbBgChkbox'],
		'fwbBgcolor'=> $options['fwbBgcolor'],
		'fwbsduration'=> $options['fwbsduration'],
		'fwbstspeed'=> $options['fwbstspeed'],
		'fwbslide1' => $options['fwbslide1'],
		'fwbslide2' => $options['fwbslide2'],
		'fwbslide3' => $options['fwbslide3'],
		'fwbslide4' => $options['fwbslide4'],
		'fwbslide5' => $options['fwbslide5'],
		'fwbslide6' => $options['fwbslide6']
    );
return $update_val;
}

function fwbslider_backend_menu()
{
wp_nonce_field('update-options'); $options = get_option('fwbslider_options'); 
?>
	<div class="wrap">
	<div id="icon-themes" class="icon32"></div>
	<h2><?php _e('Full Width Background Slider '.fwbslider_get_version().' Settings','fwbslider'); ?></h2>
	</div>
	
	<div class="fwblite-wrapper">
	
		<!-- WP-Banner Starts Here -->
		<div id="wp_banner">
			<!-- Top Section Starts Here -->
			<div class="top_section">
				<!-- Begin MailChimp Signup Form -->
				<link type="text/css" rel="stylesheet" href="http://cdn-images.mailchimp.com/embedcode/classic-081711.css">
				<style type="text/css">
					#mc_embed_signup{ clear:left; font:14px Helvetica,Arial,sans-serif; }
					/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
					   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
				</style>
				<div id="mc_embed_signup">
					<form novalidate="" target="_blank" class="validate" name="mc-embedded-subscribe-form" id="mc-embedded-subscribe-form" method="post" action="http://wpfruits.us6.list-manage.com/subscribe/post?u=166c9fed36fb93e9202b68dc3&amp;id=bea82345ae">
						<div class="mc-field-group">
							<input type="email" id="mce-EMAIL" class="required email" name="EMAIL" value="" placeholder="Our Newsletter Just Enter Your Email Here" />
							<input type="submit" class="button" id="mc-embedded-subscribe" name="subscribe" value="" onclick="return fwblite_wp_jsvalid();">
							<div style="clear:both;"></div>
						</div>
						<div class="clear" id="mce-responses">
							<div style="display:none" id="mce-error-response" class="response"></div>
							<div style="display:none" id="mce-success-response" class="response"></div>
						</div>	
						
					</form>
				</div>
				<script type="text/javascript">
					var fnames = new Array();var ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';
					try {
						var jqueryLoaded=jQuery;
						jqueryLoaded=true;
					} catch(err) {
						var jqueryLoaded=false;
					}
					var head= document.getElementsByTagName('head')[0];
					if (!jqueryLoaded) {
						var script = document.createElement('script');
						script.type = 'text/javascript';
						script.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js';
						head.appendChild(script);
						if (script.readyState &amp;&amp; script.onload!==null){
							script.onreadystatechange= function () {
								  if (this.readyState == 'complete') mce_preload_check();
							}    
						}
					}
					var script = document.createElement('script');
					script.type = 'text/javascript';
					script.src = 'http://downloads.mailchimp.com/js/jquery.form-n-validate.js';
					head.appendChild(script);
					var err_style = '';
					try{
						err_style = mc_custom_error_style;
					} catch(e){
						err_style = '#mc_embed_signup input.mce_inline_error{border-color:#6B0505;} #mc_embed_signup div.mce_inline_error{margin: 0 0 1em 0; padding: 5px 10px; background-color:#6B0505; font-weight: bold; z-index: 1; color:#fff;}';
					}
					var head= document.getElementsByTagName('head')[0];
					var style= document.createElement('style');
					style.type= 'text/css';
					if (style.styleSheet) {
						style.styleSheet.cssText = err_style;
					} else {
						style.appendChild(document.createTextNode(err_style));
					}
					head.appendChild(style);
					setTimeout('mce_preload_check();', 250);

					var mce_preload_checks = 0;
					function mce_preload_check(){
						if (mce_preload_checks&gt;40) return;
						mce_preload_checks++;
						try {
							var jqueryLoaded=jQuery;
						} catch(err) {
							setTimeout('mce_preload_check();', 250);
							return;
						}
						try {
							var validatorLoaded=jQuery("#fake-form").validate({});
						} catch(err) {
							setTimeout('mce_preload_check();', 250);
							return;
						}
						mce_init_form();
					}
					function mce_init_form()
					{
						jQuery(document).ready( function($) 
						{
						  var options = { errorClass: 'mce_inline_error', errorElement: 'div', onkeyup: function(){}, onfocusout:function(){}, onblur:function(){}  };
						  var mce_validator = $("#mc-embedded-subscribe-form").validate(options);
						  $("#mc-embedded-subscribe-form").unbind('submit');//remove the validator so we can get into beforeSubmit on the ajaxform, which then calls the validator
						  options = { url: 'http://wpfruits.us6.list-manage.com/subscribe/post-json?u=166c9fed36fb93e9202b68dc3&amp;id=bea82345ae&amp;c=?', type: 'GET', dataType: 'json', contentType: "application/json; charset=utf-8",
										beforeSubmit: function(){
											$('#mce_tmp_error_msg').remove();
											$('.datefield','#mc_embed_signup').each(
												function(){
													var txt = 'filled';
													var fields = new Array();
													var i = 0;
													$(':text', this).each(
														function(){
															fields[i] = this;
															i++;
														});
													$(':hidden', this).each(
														function(){
															var bday = false;
															if (fields.length == 2){
																bday = true;
																fields[2] = {'value':1970};//trick birthdays into having years
															}
															if ( fields[0].value=='MM' &amp;&amp; fields[1].value=='DD' &amp;&amp; (fields[2].value=='YYYY' || (bday &amp;&amp; fields[2].value==1970) ) ){
																this.value = '';
															} else if ( fields[0].value=='' &amp;&amp; fields[1].value=='' &amp;&amp; (fields[2].value=='' || (bday &amp;&amp; fields[2].value==1970) ) ){
																this.value = '';
															} else {
																if (/\[day\]/.test(fields[0].name)){
																	this.value = fields[1].value+'/'+fields[0].value+'/'+fields[2].value;									        
																} else {
																	this.value = fields[0].value+'/'+fields[1].value+'/'+fields[2].value;
																}
															}
														});
												});
											return mce_validator.form();
										}, 
										success: mce_success_cb
									};
						  $('#mc-embedded-subscribe-form').ajaxForm(options);

						});
					}
					function mce_success_cb(resp){
						$('#mce-success-response').hide();
						$('#mce-error-response').hide();
						if (resp.result=="success"){
							$('#mce-'+resp.result+'-response').show();
							$('#mce-'+resp.result+'-response').html(resp.msg);
							$('#mc-embedded-subscribe-form').each(function(){
								this.reset();
							});
						} else {
							var index = -1;
							var msg;
							try {
								var parts = resp.msg.split(' - ',2);
								if (parts[1]==undefined){
									msg = resp.msg;
								} else {
									i = parseInt(parts[0]);
									if (i.toString() == parts[0]){
										index = parts[0];
										msg = parts[1];
									} else {
										index = -1;
										msg = resp.msg;
									}
								}
							} catch(e){
								index = -1;
								msg = resp.msg;
							}
							try{
								if (index== -1){
									$('#mce-'+resp.result+'-response').show();
									$('#mce-'+resp.result+'-response').html(msg);            
								} else {
									err_id = 'mce_tmp_error_msg';
									html = '&lt;div id="'+err_id+'" style="'+err_style+'"&gt; '+msg+'&lt;/div&gt;';
									
									var input_id = '#mc_embed_signup';
									var f = $(input_id);
									if (ftypes[index]=='address'){
										input_id = '#mce-'+fnames[index]+'-addr1';
										f = $(input_id).parent().parent().get(0);
									} else if (ftypes[index]=='date'){
										input_id = '#mce-'+fnames[index]+'-month';
										f = $(input_id).parent().parent().get(0);
									} else {
										input_id = '#mce-'+fnames[index];
										f = $().parent(input_id).get(0);
									}
									if (f){
										$(f).append(html);
										$(input_id).focus();
									} else {
										$('#mce-'+resp.result+'-response').show();
										$('#mce-'+resp.result+'-response').html(msg);
									}
								}
							} catch(e){
								$('#mce-'+resp.result+'-response').show();
								$('#mce-'+resp.result+'-response').html(msg);
							}
						}
					}

				</script>
				<!--End mc_embed_signup-->
			</div>
			<!-- Top Section Ends Here -->
			
			<!-- Bottom Section Starts Here -->
			<div class="bot_section">
				<a href="http://www.wpfruits.com/" class="wplogo" target="_blank" title="WFruits.com"></a>
				<a href="https://www.facebook.com/pages/WPFruitscom/443589065662507" class="fbicon" target="_blank" title="Facebook"></a>
				<a href="http://www.twitter.com/wpfruits" class="twicon" target="_blank" title="Twitter"></a>
				<div style="clear:both;"></div>
			</div>
			<!-- Bottom Section Ends Here -->
		</div>
		<!-- WP-Banner Ends Here -->
	
	</div>
	
	
	<div style="width:700px;">
		
		<div class="postbox fwbslide_wrap" id="fwbslider_post_metas" style="padding:20px;">
		
			<?php echo fwbslider_update(); ?>
			
			<span class="desc"><?php _e('Enter Slides URL: ','fwbslider'); ?><?php _e('These Background Slides will work for all pages and posts until you have not checked option in post/page editor.','fwbslider'); ?></span>
			<form method="post" class="fwbslider_form">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="2">
						<table class="fwb_proFeature" cellpadding="0" cellspacing="0">
							<tr style="line-height:22px;"><th colspan="2"><?php _e('These Features comes with PRO Version. To activate, please purchase PRO version.','fwbslider'); ?> <a href="http://www.wpfruits.com/full-width-background-slider/?fwb_ref=back" target="_blank"><?php _e('Click Here to Purchase Plugin.','fwbslider'); ?></a></th></tr>
						
							<tr>
								<th><?php _e("Show Navigation:",'fwbslider'); ?></th>
								<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
							</tr>
							
							<tr>
								<th><?php _e("Show Bullets:",'fwbslider'); ?></th>
								<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
							</tr>
							
							<tr>
								<th><?php _e("Show Play/Pause Key:",'fwbslider'); ?></th>
								<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
							</tr>
							
							<tr>
								<th><?php _e("Show Thumbnails:",'fwbslider'); ?></th>
								<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
							</tr>
							
							<tr>
								<th><?php _e("Show ProgressBar:",'fwbslider'); ?></th>
								<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
							</tr>
							
							<tr><th><label for="alloverlay"><?php _e('Display Overlay:','fwbslider'); ?></label></th><td>&nbsp;<label for="alloverlay"><input id="alloverlay" type="checkbox"  value="true" />&nbsp;<?php _e('Check it, if you want overlay effect.','fwbslider') ?></label></td></tr>		
							<tr><th><label><?php _e('Set Overlay Effect:','fwbslider'); ?></label></th>
								<td class="clearfix">
									<label class="fwb_rdlb" for="fwe1" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/01.png');"><input type="radio" id="fwe1" ></label>
									<label class="fwb_rdlb" for="fwe2" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/02.png');"><input type="radio" id="fwe2" ></label>
									<label class="fwb_rdlb" for="fwe3" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/03.png');"><input type="radio" id="fwe3" ></label>
									<label class="fwb_rdlb" for="fwe4" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/04.png');"><input type="radio" id="fwe4" ></label>
									<label class="fwb_rdlb" for="fwe5" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/05.png');"><input type="radio" id="fwe5" ></label>
									<label class="fwb_rdlb" for="fwe6" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/06.png');"><input type="radio" id="fwe6" ></label>
									<label class="fwb_rdlb" for="fwe7" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/07.png');"><input type="radio" id="fwe7" ></label>
									<label class="fwb_rdlb" for="fwe8" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/08.png');"><input type="radio" id="fwe8" ></label>
									<label class="fwb_rdlb" for="fwe9" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/09.png');"><input type="radio" id="fwe9" ></label>
									<label class="fwb_rdlb" for="fwe10" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/10.png');"><input type="radio" id="fwe10" ></label>
									<label class="fwb_rdlb" for="fwe11" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/11.png');"><input type="radio" id="fwe11" ></label>
									<label class="fwb_rdlb" for="fwe12" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/12.png');"><input type="radio" id="fwe12" ></label>
									<label class="fwb_rdlb" for="fwe13" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/13.png');"><input type="radio" id="fwe13" ></label>
									<label class="fwb_rdlb" for="fwe14" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/14.png');"><input type="radio" id="fwe14" ></label>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					
					<tr><td colspan="2"><div><input type="checkbox" id="fwbBgChkbox" value="1"  <?php if($options['fwbBgChkbox']){ ?> checked <?php } ?> name="fwbslider_options[fwbBgChkbox]" />&nbsp;<label style="color:#D30E0E;" for="fwbBgChkbox"><b><?php _e('Check it, if you want to use <i>"Background Color instead of slider images."</i>','fwbslider'); ?></b></label></div></td></tr>
		
					<tr>
						<th><?php _e("Background Color",'fwbslider'); ?></th>
						<td>
							<div class="fwb_colwrap">
								<input type="text" id="fwb_bgcolor" class="fwb_color_inp" value="<?php if($options['fwbBgcolor']) echo $options['fwbBgcolor']; else echo "#F7D2D2"; ?>" name="fwbslider_options[fwbBgcolor]" />
								<div class="fwb_colsel fwb_bgcolor"></div>
							</div>
						</td>
					</tr>
					
					<tr><th><label><?php _e('Slide Duration','fwbslider'); ?></label> </th><td><input style="width:50px;" name="fwbslider_options[fwbsduration]" type="text" value="<?php if($options['fwbsduration']) echo $options['fwbsduration']; ?>"  /> <small>(<b><?php _e('in Seconds','fwbslider'); ?></b>)</small></td></tr>				
					<tr><th><label><?php _e('Transition Speed','fwbslider'); ?></label> </th><td><input style="width:50px;" name="fwbslider_options[fwbstspeed]" type="text" value="<?php if($options['fwbstspeed']) echo $options['fwbstspeed']; ?>"  /> <small>(<b><?php _e('in Seconds','fwbslider'); ?></b>)</small></td></tr>
					<tr><th><label><?php _e('FWB Slide1 URL','fwbslider'); ?></label></th><td><input type="text" class="fwb_uploadimg" name="fwbslider_options[fwbslide1]" value="<?php echo $options['fwbslide1'] ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>
					<tr><th><label><?php _e('FWB Slide2 URL','fwbslider'); ?></label></th><td><input type="text" class="fwb_uploadimg" name="fwbslider_options[fwbslide2]" value="<?php echo $options['fwbslide2'] ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>
					<tr><th><label><?php _e('FWB Slide3 URL','fwbslider'); ?></label></th><td><input type="text" class="fwb_uploadimg" name="fwbslider_options[fwbslide3]" value="<?php echo $options['fwbslide3'] ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>
					<tr><th><label><?php _e('FWB Slide4 URL','fwbslider'); ?></label></th><td><input type="text" class="fwb_uploadimg" name="fwbslider_options[fwbslide4]" value="<?php echo $options['fwbslide4'] ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>
					<tr><th><label><?php _e('FWB Slide5 URL','fwbslider'); ?></label></th><td><input type="text" class="fwb_uploadimg" name="fwbslider_options[fwbslide5]" value="<?php echo $options['fwbslide5'] ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>
					<tr><th><label><?php _e('FWB Slide6 URL','fwbslider'); ?></label></th><td><input type="text" class="fwb_uploadimg" name="fwbslider_options[fwbslide6]" value="<?php echo $options['fwbslide6'] ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>
					
					<tr>
						<td colspan="2">
						<table class="fwb_proFeature" cellpadding="0" cellspacing="0">
							<tr style="line-height:22px;"><th colspan="2"><?php _e('These Features comes with PRO Version. To activate, please purchase PRO version.','fwbslider'); ?> <a href="http://www.wpfruits.com/full-width-background-slider/?fwb_ref=back" target="_blank"><?php _e('Click Here to Purchase Plugin.','fwbslider'); ?></a></th></tr>
							<tr><th><label><?php _e('FWB Slide7 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button" value="Browse.." /></td></tr>
							<tr><th><label><?php _e('FWB Slide8 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button" value="Browse.." /></td></tr>
							<tr><th><label><?php _e('FWB Slide9 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button" value="Browse.." /></td></tr>
							<tr><th><label><?php _e('FWB Slide10 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button" value="Browse.." /></td></tr>
						</table>                                                                                                             
					</tr>
				
				</table>	
				 
				<p class="button-controls">
					<input type="submit" value="<?php _e('Save Settings') ?>" class="button-primary" id="fwbslider_update" name="fwbslider_update">	
				</p>
			</form>
		</div>
		
		<iframe frameborder="1" class="fwblite_iframe" src="http://www.sketchthemes.com/sketch-updates/plugin-updates/fwb-lite/fwb-lite.php" width="200px" height="500px" scrolling="no" ></iframe> 		
		
	</div>
<?php
}
//--------------------------------------------------------------------------------------------------------------------------------------


//---------------------------------------------------------------------------------------------------------------------------------------
//-------------------	POST AND PAGE BACKEND OPTIONS -----------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
// Hook for adding page/post custom mata boxes
add_action("admin_menu", "fwbslider_post_meta_box_options");
add_action('save_post', 'fwbslider_post_meta_box_save');

function fwbslider_post_meta_box_options(){
	if( function_exists( 'add_meta_box' ) ) {
		$post_types=get_post_types(); 
		foreach($post_types as $post_type) {
			add_meta_box("fwbslider_post_metas", "Full Width Background Slider Slides", "fwbslider_post_meta_box_add", $post_type, "normal", "high");
		}
	}
	else{
	}
}

function fwbslider_post_meta_box_add()
{
		global $post;
		$custom = get_post_custom($post->ID);
		$fwb_disable = $custom["fwb_disable"][0];
		$fwb_check = $custom["fwb_check"][0];
		$fwbBgChkbox = $custom["fwbBgChkbox"][0];
		$fwbBgcolor = $custom["fwbBgcolor"][0];
		$fwbsduration = $custom["fwbsduration"][0];
		$fwbstspeed = $custom["fwbstspeed"][0];
		$fwbslide1 = $custom["fwbslide1"][0];
		$fwbslide2 = $custom["fwbslide2"][0];
		$fwbslide3 = $custom["fwbslide3"][0];
		$fwbslide4 = $custom["fwbslide4"][0];
		$fwbslide5 = $custom["fwbslide5"][0];
		$fwbslide6 = $custom["fwbslide6"][0];

?>

	<div class="fwbslider_checkbox" style="margin-botom:5px;">
		<input type="checkbox" class="fwbslider_post" name="fwb_disable" id="fwb_disable" <?php if($fwb_disable){ ?> checked <?php } ?> value="true"/>&nbsp;
		<label for="fwb_disable" style="color:red;"><?php _e('Check this if you want to disable slider for this post/page.'); ?> </label>
	</div>

	<div class="fwbslider_disable">
	
		<div class="fwbslider_checkbox">
			<input type="checkbox" class="fwbslider_post" name="fwb_check" id="fwb_check" <?php if($fwb_check){ ?> checked <?php } ?> value="true"/>&nbsp;
			<label for="fwb_check"><?php _e('Add "FWB Slider" to this post/page..'); ?> </label>
		</div>
			
		<div class="fwbslider_table fwbslide_wrap">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="2">
					<table class="fwb_proFeature" cellpadding="0" cellspacing="0">
						<tr style="line-height:22px;"><th colspan="2"><?php _e('These Features comes with PRO Version. To activate, please purchase PRO version.','fwbslider'); ?> <a href="http://www.wpfruits.com/full-width-background-slider/?fwb_ref=back" target="_blank"><?php _e('Click Here to Purchase Plugin.','fwbslider'); ?></a></th></tr>
					
						<tr>
							<th><?php _e("Show Navigation:",'fwbslider'); ?></th>
							<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
						</tr>
						
						<tr>
							<th><?php _e("Show Bullets:",'fwbslider'); ?></th>
							<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
						</tr>
						
						<tr>
							<th><?php _e("Show Play/Pause Key:",'fwbslider'); ?></th>
							<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
						</tr>
						
						<tr>
							<th><?php _e("Show Thumbnails:",'fwbslider'); ?></th>
							<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
						</tr>
						
						<tr>
							<th><?php _e("Show ProgressBar:",'fwbslider'); ?></th>
							<td><select style="width:70px;"><option value="true">Yes</option><option value="false">No</option></select></td>
						</tr>
						
						<tr><th><label for="alloverlay"><?php _e('Display Overlay:','fwbslider'); ?></label></th><td>&nbsp;<label for="alloverlay"><input id="alloverlay" type="checkbox"  value="true" />&nbsp;<?php _e('Check it, if you want overlay effect.','fwbslider') ?></label></td></tr>		
						<tr><th><label><?php _e('Set Overlay Effect:','fwbslider'); ?></label></th>
							<td class="clearfix">
								<label class="fwb_rdlb" for="fwe1" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/01.png');"><input type="radio" id="fwe1" ></label>
								<label class="fwb_rdlb" for="fwe2" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/02.png');"><input type="radio" id="fwe2" ></label>
								<label class="fwb_rdlb" for="fwe3" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/03.png');"><input type="radio" id="fwe3" ></label>
								<label class="fwb_rdlb" for="fwe4" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/04.png');"><input type="radio" id="fwe4" ></label>
								<label class="fwb_rdlb" for="fwe5" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/05.png');"><input type="radio" id="fwe5" ></label>
								<label class="fwb_rdlb" for="fwe6" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/06.png');"><input type="radio" id="fwe6" ></label>
								<label class="fwb_rdlb" for="fwe7" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/07.png');"><input type="radio" id="fwe7" ></label>
								<label class="fwb_rdlb" for="fwe8" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/08.png');"><input type="radio" id="fwe8" ></label>
								<label class="fwb_rdlb" for="fwe9" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/09.png');"><input type="radio" id="fwe9" ></label>
								<label class="fwb_rdlb" for="fwe10" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/10.png');"><input type="radio" id="fwe10" ></label>
								<label class="fwb_rdlb" for="fwe11" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/11.png');"><input type="radio" id="fwe11" ></label>
								<label class="fwb_rdlb" for="fwe12" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/12.png');"><input type="radio" id="fwe12" ></label>
								<label class="fwb_rdlb" for="fwe13" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/13.png');"><input type="radio" id="fwe13" ></label>
								<label class="fwb_rdlb" for="fwe14" style="background:#e7e7e7 url('<?php echo FWBLITE_URL ?>/images/overlay/14.png');"><input type="radio" id="fwe14" ></label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			
			<tr><td colspan="2"><div><input type="checkbox" id="fwbBgChkbox" value="1"  <?php if($fwbBgChkbox){ ?> checked <?php } ?> name="fwbBgChkbox" />&nbsp;<label style="color:#D30E0E;" for="fwbBgChkbox"><b><?php _e('Check it, if you want to use <i>"Background Color instead of slider images."</i>','fwbslider'); ?></b></label></div></td></tr>
			<tr>
				<th><?php _e("Background Color",'fwbslider'); ?></th>
				<td>
					<div class="fwb_colwrap">
						<input type="text" id="fwb_bgcolor" class="fwb_color_inp" value="<?php if($fwbBgcolor) echo $fwbBgcolor; else echo "#F7D2D2"; ?>" name="fwbBgcolor" />
						<div class="fwb_colsel fwb_bgcolor"></div>
					</div>
				</td>
			</tr>
			
			<tr><th><label><?php _e('Slide Duration','fwbslider'); ?></label> </th><td><input style="width:50px;" type="text" name="fwbsduration" value="<?php if($fwbsduration) echo $fwbsduration; ?>"  /> <small>(<b><?php _e('in Seconds','fwbslider'); ?></b>)</small></td></tr>
			<tr><th><label><?php _e('Transition Speed','fwbslider'); ?></label> </th><td><input style="width:50px;" type="text" name="fwbstspeed" value="<?php if($fwbstspeed) echo $fwbstspeed; ?>"  /> <small>(<b><?php _e('in Seconds','fwbslider'); ?></b>)</small></td></tr>
			
			<tr><th width="140px" align="right" ><label><?php _e('FWB Slide1 URL'); ?></label></th><td><input type="text" name="fwbslide1" class="fwb_uploadimg" value="<?php echo $fwbslide1; ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>				
			<tr><th width="140px" align="right" ><label><?php _e('FWB Slide2 URL'); ?></label></th><td><input type="text" name="fwbslide2" class="fwb_uploadimg" value="<?php echo $fwbslide2; ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>				
			<tr><th width="140px" align="right" ><label><?php _e('FWB Slide3 URL'); ?></label></th><td><input type="text" name="fwbslide3" class="fwb_uploadimg" value="<?php echo $fwbslide3; ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>				
			<tr><th width="140px" align="right" ><label><?php _e('FWB Slide4 URL'); ?></label></th><td><input type="text" name="fwbslide4" class="fwb_uploadimg" value="<?php echo $fwbslide4; ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>				
			<tr><th width="140px" align="right" ><label><?php _e('FWB Slide5 URL'); ?></label></th><td><input type="text" name="fwbslide5" class="fwb_uploadimg" value="<?php echo $fwbslide5; ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>				
			<tr><th width="140px" align="right" ><label><?php _e('FWB Slide6 URL'); ?></label></th><td><input type="text" name="fwbslide6" class="fwb_uploadimg" value="<?php echo $fwbslide6; ?>" /><input class="fwb_uploadbtn button" type="button" value="Browse.." /></td></tr>				
					
			<tr>
				<td colspan="2">
				<table class="fwb_proFeature" cellpadding="0" cellspacing="0">
				<tr style="line-height:22px;"><th colspan="2"><?php _e('These Features comes with PRO Version. To activate, please purchase PRO version.','fwbslider'); ?> <a href="http://www.wpfruits.com/full-width-background-slider/?fwb_ref=back" target="_blank"><?php _e('Click Here to Purchase Plugin.','fwbslider'); ?></a></th></tr>
				
				<tr><th><label><?php _e('FWB Slide7 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button"  value="Browse.." /></td></tr>
				<tr><th><label><?php _e('FWB Slide8 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button"  value="Browse.." /></td></tr>
				<tr><th><label><?php _e('FWB Slide9 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button"  value="Browse.." /></td></tr>
				<tr><th><label><?php _e('FWB Slide10 URL:','fwbslider'); ?></label></th><td><input type="text" /><input type="button" class="button" value="Browse.." /></td></tr>
				</table>
				</td>
			</tr>
			
			<input type="hidden" name="meta_is_submit" value="yes" />
			
		   </table>	
		</div> 
		
	</div>
	
<?php
}


//update post custom fields
function fwbslider_post_meta_box_save($post_id)
{
	global $post;
	if(isset($_POST['meta_is_submit'])){
		update_post_meta($post_id, "fwb_disable",$_POST['fwb_disable']);
		update_post_meta($post_id, "fwb_check",$_POST['fwb_check']);
		update_post_meta($post_id, "fwbBgChkbox",$_POST['fwbBgChkbox']);
		update_post_meta($post_id, "fwbBgcolor",$_POST['fwbBgcolor']);		
		update_post_meta($post_id, "fwbsduration",$_POST['fwbsduration']);		
		update_post_meta($post_id, "fwbstspeed",$_POST['fwbstspeed']);		
		update_post_meta($post_id, "fwbslide1",$_POST['fwbslide1']);
		update_post_meta($post_id, "fwbslide2",$_POST['fwbslide2']);
		update_post_meta($post_id, "fwbslide3",$_POST['fwbslide3']);
		update_post_meta($post_id, "fwbslide4",$_POST['fwbslide4']);
		update_post_meta($post_id, "fwbslide5",$_POST['fwbslide5']);
		update_post_meta($post_id, "fwbslide6",$_POST['fwbslide6']);
	}
}

//------------------------------------------------------------------------------------------------------------------------------------------

//------------- FUNCTION TO CALL SLIDER -------------------------------------------------------

function fwb_getAll_options(){
	global $from_this;
	$options = get_option('fwbslider_options'); 
	$fwbBgChkbox_all = $options["fwbBgChkbox"];
	$fwbBgcolor_all = $options["fwbBgcolor"];
	$fwbsduration_all = $options["fwbsduration"];
	$fwbstspeed_all = $options["fwbstspeed"];
	$fwbslide1_all = $options["fwbslide1"];
	$fwbslide2_all = $options["fwbslide2"];
	$fwbslide3_all = $options["fwbslide3"];
	$fwbslide4_all = $options["fwbslide4"];
	$fwbslide5_all = $options["fwbslide5"];
	$fwbslide6_all = $options["fwbslide6"];
	
	$fwbsduration_all = (isset($fwbsduration_all) && $fwbsduration_all !="" && $fwbsduration_all !=0) ? $fwbsduration_all*1000 : 5000;
	$fwbstspeed_all   = (isset($fwbstspeed_all) && $fwbsduration_all !="" && $fwbstspeed_all !=0) ? $fwbstspeed_all*1000 : 2000;
	?>
	<!-- FWB Lite Slider Start here -->
		<?php if(!$fwbBgChkbox_all){ ?>
			<script type="text/javascript">jQuery(document).ready(function(){jQuery.fwbslider('#fwbslider', {'delay':<?php echo $fwbsduration_all; ?>, 'fadeSpeed': <?php echo $fwbstspeed_all; ?>});});</script>
		<?php } ?>
		
		<div id="fwbslider" class="for_all">
			<?php if($fwbBgChkbox_all){ ?><div class="fwb_bgcolor" style="background:<?php echo $fwbBgcolor_all; ?>;"></div><?php
			}//if background color option is checked
			
			else{ ?>
				<?php if($fwbslide1_all){ ?><img src="<?php echo $fwbslide1_all; ?>" /><?php } ?>
				<?php if($fwbslide2_all){ ?><img src="<?php echo $fwbslide2_all; ?>" /><?php } ?>
				<?php if($fwbslide3_all){ ?><img src="<?php echo $fwbslide3_all; ?>" /><?php } ?>
				<?php if($fwbslide4_all){ ?><img src="<?php echo $fwbslide4_all; ?>" /><?php } ?>
				<?php if($fwbslide5_all){ ?><img src="<?php echo $fwbslide5_all; ?>" /><?php } ?>
				<?php if($fwbslide6_all){ ?><img src="<?php echo $fwbslide6_all; ?>" /><?php } ?>	
			<?php
			}
			?>
		</div>
		<a class="fwb_fromthis" target="_blank" href="<?php echo $from_this; ?>" title=""><img src="<?php echo plugins_url('images/FWB-big.png',__FILE__) ?>" /></a>
		<!-- FWB Lite Slider End here -->
	<?php
}

function fwb_getMata_options(){
	global $from_this;
	global $post;
	$custom = get_post_custom($post->ID);
	$fwb_disable  = $custom["fwb_disable"][0];
	$fwb_check    = $custom["fwb_check"][0];
	$fwbBgChkbox  = $custom["fwbBgChkbox"][0];
	$fwbBgcolor   = $custom["fwbBgcolor"][0];
	$fwbsduration = $custom["fwbsduration"][0];
	$fwbstspeed = $custom["fwbstspeed"][0];
	$fwbslide1 = $custom["fwbslide1"][0];
	$fwbslide2 = $custom["fwbslide2"][0];
	$fwbslide3 = $custom["fwbslide3"][0];
	$fwbslide4 = $custom["fwbslide4"][0];
	$fwbslide5 = $custom["fwbslide5"][0];
	$fwbslide6 = $custom["fwbslide6"][0];
	
	$fwbsduration = (isset($fwbsduration) && $fwbsduration !="" && $fwbsduration !=0) ? $fwbsduration*1000 : 5000;
	$fwbstspeed = (isset($fwbstspeed) && $fwbstspeed !="" && $fwbstspeed !=0) ? $fwbstspeed*1000 : 2000;
	
	if($fwb_check && !$fwb_disable){
	?>
		<!-- FWB Lite Slider Start here -->
			<?php if(!$fwbBgChkbox){ ?>
				<script type="text/javascript"> jQuery(document).ready(function(){ 	jQuery.fwbslider('#fwbslider', {'delay':<?php echo $fwbsduration; ?>,'fadeSpeed' : <?php echo $fwbstspeed; ?>}); }); </script> 	
			<?php } ?>

			<div id="fwbslider" class="for_only">
				<?php if($fwbBgChkbox){ ?><div class="fwb_bgcolor" style="background:<?php echo $fwbBgcolor; ?>;"></div>
				<?php
				}//if background color option is checked
				
				else{ ?>
					<?php if($fwbslide1){ ?><img src="<?php echo $fwbslide1; ?>" /><?php } ?>
					<?php if($fwbslide2){ ?><img src="<?php echo $fwbslide2; ?>" /><?php } ?>
					<?php if($fwbslide3){ ?><img src="<?php echo $fwbslide3; ?>" /><?php } ?>
					<?php if($fwbslide4){ ?><img src="<?php echo $fwbslide4; ?>" /><?php } ?>
					<?php if($fwbslide5){ ?><img src="<?php echo $fwbslide5; ?>" /><?php } ?>
					<?php if($fwbslide6){ ?><img src="<?php echo $fwbslide6; ?>" /><?php } ?>
				<?php
				}
				?>
			</div>
			<a class="fwb_fromthis" target="_blank" href="<?php echo $from_this; ?>" title=""><img src="<?php echo plugins_url('images/FWB-big.png',__FILE__) ?>" /></a>
		<!-- FWB Lite Slider End here -->
	<?php
	}
	elseif(!$fwb_disable){
		fwb_getAll_options();
	}
}

function fwbslider(){
	global $from_this;
	$from_this = "http://wpfruits.com/full-width-background-slider/?utm_refs=".$_SERVER['SERVER_NAME'];
		
	if(is_home() || is_category() || is_tax() || is_tag() || is_date() || is_author()){
		fwb_getAll_options();
	}	
	elseif(is_page() || is_front_page() && !is_home()){
		fwb_getMata_options();
	}
	elseif(is_single()){
		fwb_getMata_options();
	}
}

add_action('wp_footer','fwbslider');
//----------------------------------------------------------------------------------------------------
?>