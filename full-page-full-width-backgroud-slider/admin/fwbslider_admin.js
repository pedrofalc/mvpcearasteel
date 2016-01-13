/*-- FWB SLIDER ADMIN SCRIPT
------------------------------*/
jQuery(document).ready(function(){

	if(jQuery("#fwbslider_post_metas #fwb_disable").is(':checked')){jQuery('#fwbslider_post_metas  .fwbslider_disable').hide();}
	if(!jQuery("#fwbslider_post_metas #fwb_check").is(':checked')){jQuery('#fwbslider_post_metas  .fwbslider_table').hide();}
	
	jQuery("#fwbslider_post_metas #fwb_check").click(function(){
		if(jQuery("#fwbslider_post_metas #fwb_check").is(':checked')){jQuery('#fwbslider_post_metas  .fwbslider_table').slideDown();}
		else{jQuery('#fwbslider_post_metas  .fwbslider_table').slideUp();}
	});

	jQuery("#fwbslider_post_metas #fwb_disable").click(function(){
		if(jQuery("#fwbslider_post_metas #fwb_disable").is(':checked')){jQuery('#fwbslider_post_metas  .fwbslider_disable').slideUp();jQuery("#fwbslider_post_metas #fwb_check").prop("checked", false);}
		else{jQuery('#fwbslider_post_metas  .fwbslider_disable').slideDown();}
	});
	
	jQuery('.fwblite-wrapper #mce-EMAIL').focus(function(){
		jQuery(this).css({'border-color':'#777','color':'#000','background':'transparent'});
	});

/*-- Upload image jquery start 
--------------------------------------------*/
	var targetfield= '';
	var fwb_send_to_editor = window.send_to_editor;
	jQuery('.fwb_uploadbtn').click(function(){
		targetfield = jQuery(this).prev('.fwb_uploadimg');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		window.send_to_editor = function(html) {
			imgurl = jQuery('img',html).attr('src');
			jQuery(targetfield).val(imgurl);
			tb_remove();
			window.send_to_editor = fwb_send_to_editor;
		}
		return false;
	});	
/*-------------------------------------------*/
/*color picker jquery start*/

	if (jQuery("#fwbslider_post_metas").length){
		jQuery('#fwbslider_post_metas .fwb_bgcolor').farbtastic('#fwb_bgcolor');
	}
	
	jQuery('html').click(function() {jQuery("#fwbslider_post_metas .farbtastic").fadeOut('fast');});
	
	jQuery('#fwbslider_post_metas .fwb_colsel').click(function(event){
		jQuery("#fwbslider_post_metas .farbtastic").hide();
		jQuery(this).find(".farbtastic").fadeIn('fast');event.stopPropagation();
	});
/*color picker jquery end*/

});

function fwblite_wp_jsvalid(){
	var reg= /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var a  = document.getElementById('mce-EMAIL').value;
	if( a == ""){
		jQuery('#mce-EMAIL').css({'border-color':'red','color':'red'});
		return false;
	}else{
		if(reg.test(a)==false){
			jQuery('#mce-EMAIL').css({'border-color':'red','color':'red','background':'#F7DAD9'});
			return false;
		}	
	}		
	return true;
}