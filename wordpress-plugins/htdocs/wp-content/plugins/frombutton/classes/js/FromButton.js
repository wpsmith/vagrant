/*************************************************************** 
@  
@	From Button JS 
@ 
/**************************************************************/  
		$('#frombutton_buy .frombutton_buy_content').fadeToggle(); 
		$('#frombutton_buy .affiliate_content').fadeToggle(); 
		$('#frombutton_buy .buy_now_clear').fadeToggle(); 
	});
	
	var overlay = $('<div id="overlay"></div>'); 
	
	$('.frombutton_reviews_input_range').change(function() {  
		var myID = $(this).attr('id');  
		$('#'+myID+'_value').val($(this).val() );
	});


function FrombuttonSurvey(){  
	var faults = $('input').filter(function() { 
		return $(this).data('required') && $(this).val() === "";
	}).addClass('frombuttont_reviews_required');  
	if(faults.length) 
		return false;    
	else
		return(true);
} 