/*************************************************************** 
@  
@	From Button JS 
@ 
/**************************************************************/  
jQuery(document).ready(function(){  
	frombutton_required();   
	frombutton_show_popup();   
	affiliate_count();   
	frombutton_tabs();   
	affiliate_icon();   
	frombutton_help();   
	frombutton_menu();     
 
});

/*------------------------------------*\
	Affiliate Icon
\*------------------------------------*/
function frombutton_menu(){  
	jQuery('#toplevel_page_frombutton_config .wp-first-item .wp-first-item').html('Design Option'); 

}
 
/*------------------------------------*\
	Affiliate Icon
\*------------------------------------*/
function affiliate_icon(){  
	jQuery('.frombutton_affiliate_icon').focus(function() { 
		var name = jQuery(this).attr('name'); 
		affiliate_upload(name);
	}); 
}
/*------------------------------------*\
	Affiliate Icon Helper
\*------------------------------------*/
function affiliate_helper(){  
	jQuery('#manage_affiliates .AffiliateIcon').focus(function(){  
		// jQuery('#manage_affiliates .affiliate span:first').before('<b class="icon"><i><ol><li>upload using media manager</li><li>add the link to the icon input</li></ol></i></b>'); 
	}); 
}

/*------------------------------------*\
	Affiliate Icon
\*------------------------------------*/
function affiliate_upload(formfield){ 
	jQuery('html').addClass('Image');  	
	tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true'); 
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html){
		if (formfield) {
			fileurl = jQuery('img',html).attr('src');
			jQuery('#'+formfield).val(fileurl);
			tb_remove();
			jQuery('html').removeClass('Image');
		}  
	}; 
	return false; 
}
  

function frombutton_show_popup(){  
	jQuery('.show-popup').click(function(event){
		event.preventDefault();  
		jQuery('.overlay-bg').show(); 
		jQuery('.overlay-bg').css('z-index', '1002');  
	}); 
	jQuery('.close-btn').click(function(){
		jQuery('.overlay-bg').hide(); 
	});
}  

function frombutton_farbtastic(element){    
	jQuery.farbtastic('#color_picker_color1').linkTo('#frombutton_farbtastic_'+element);
	jQuery('#color_picker_color1').farbtastic('#frombutton_farbtastic_'+element);       
	jQuery('#color_picker_color1').fadeIn();  
}

function frombutton_required(){ 
	jQuery('#post').submit(function(){  
		var count = jQuery('#addons_meta_box_reviews_83542 .review').size();    
		if(count>0){ 
			var frombutton = true;
			jQuery('#frombutton_custom_meta_post .frombutton_input').each(function(){
				if(jQuery(this).val()==''){
					frombutton = false;
					jQuery(this).addClass('frombutton_required'); 
					jQuery(this).focus();
					jQuery('#publishing-action .spinner').hide(); 
				}  
			}) 
		} 
		var count = jQuery('#addons_meta_box_affiliate_price_83542 .affiliate').size();    
		if(count>0){ 
			var frombutton = true;
			jQuery('#frombutton_custom_meta_post .frombutton_input').each(function(){
				if(jQuery(this).val()==''){
					frombutton = false;
					jQuery(this).addClass('frombutton_required'); 
					jQuery(this).focus();
					jQuery('#publishing-action .spinner').hide(); 
				}  
			}) 
		}   
		jQuery('#publish').removeClass('button-primary-disabled');  
		return(frombutton);   
		return(false);   
    });
}

/*------------------------------------*\
	Affiliate Count
\*------------------------------------*/
function affiliate_count(){  
	var count = jQuery('#addons_meta_box_affiliate_price_83542 .affiliate').size(); 
	if(count==0){
		jQuery('#affiliate_delete_all').attr("disabled", true);
	}else{
		jQuery('#affiliate_delete_all').attr("disabled", false);
	}

/*------------------------------------*\
	Affiliate Delete
\*------------------------------------*/
function frombutton_tabs(){   
	jQuery("#frombutton-tab .tab-nav ul li a").click(function() {   
		jQuery("#frombutton-tab .tab-nav ul li").removeClass('active'); 
		jQuery(this).parent().addClass('active'); 
		jQuery("#frombutton-tab .tab-content .tab-panel").css('display', 'none'); 
		var myTab = jQuery(this).attr('name'); 
		jQuery("#"+myTab).css('display', 'block');
	});
} 

/*------------------------------------*\
	Affiliate Delete
\*------------------------------------*/
function affiliate_delete(ReviewID){  
	if(ReviewID=='ALL'){
		jQuery('#frombutton_custom_meta_post .affiliate').remove(); 
	}else{
		jQuery('#affiliate_'+ReviewID+'').remove(); 
	} 
	affiliate_count();	

/*------------------------------------*\
	Affiliate Help
\*------------------------------------*/
function frombutton_help(help){   
	jQuery("#manage_affiliates .frombutton_input").click(function(){  
		jQuery("#affiliate_add_help").html(help);
	});
}

/*------------------------------------*\
	Affiliate Add
\*------------------------------------*/
	affiliate_count();
	var count = jQuery('#custom_meta_box_reviews_83542 .review').size();   
	html = '<div id="frombutton_'+count+'" class="review"><input placeholder="'+ReviewName+'" type="text" class="frombutton_input frombutton_ajax" name="frombutton_review_name_'+count+'" value="" /><input placeholder="'+ReviewValue+'" type="text" class="frombutton_input frombutton_ajax" name="frombutton_review_value_'+count+'" value="" /><input OnClick="frombutton_farbtastic('+count+')" placeholder="'+ReviewColor+'" type="text" id="frombutton_farbtastic_'+count+'" class="frombutton_input frombutton_ajax frombutton_farbtastic frombutton_last" name="frombutton_review_color_'+count+'" value="#FFFFFF" /></div>';  
	jQuery('#frombutton_html_content').before(html);    
}