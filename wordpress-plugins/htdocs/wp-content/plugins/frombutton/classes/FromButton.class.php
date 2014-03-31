<?php 
/***************************************************************
@
@	From Button WP Class
@	bassem.rabia@hotmail.co.uk
@
/**************************************************************/  
class FromButton{  
	/***************************************************************
	@
	@	Construct
	@
	/**************************************************************/
	public function __construct($name, $ver) {
		$this->plugin_name 					= $name;
		$this->plugin_version				= $ver;   
		/***************************************************************
		@
		@	Methods Call
		@
		/**************************************************************/  
		add_action('init',array(&$this,'FromButton_Init'));
		add_action('admin_enqueue_scripts',array(&$this,'FromButton_BackInit'));   
		add_action('add_meta_boxes', array(&$this,'FromButton_Meta')); 
		add_action('save_post', array(&$this,'FromButton_Save'));   
		add_shortcode('FromButton', array(&$this,'FromButton_Shortcode'));  
		add_shortcode('FromButtonBuy', array(&$this,'FromButton_BuyShortcode')); 
		add_action('init', array(&$this,'FromButton_WYSIWYG')); 
		add_filter('widget_text', 'do_shortcode');  
		add_action('admin_menu', array(&$this,'FromButton_Manage')); 
		// add_action('admin_menu', array(&$this,'FromButton_menu'));  
		add_action('wp_footer', array(&$this,'FromButton_Popup')); 
	}  
	
	/***************************************************************
	@
	@	From Button WYSIWYG 
	@
	/**************************************************************/ 
	public function FromButton_WYSIWYG(){ 
		if(!current_user_can('edit_posts') && ! current_user_can('edit_pages')){
			return;
		} 
		if(get_user_option('rich_editing') == 'true'){
			add_filter('mce_external_plugins', 'add_frombutton_tinymce_plugin');
			add_filter('mce_buttons', 'register_frombutton_button');
		} 
		function register_frombutton_button($buttons) {
		   array_push($buttons, "|", "frombutton_button");
		   return $buttons;
		}

		function add_frombutton_tinymce_plugin($plugin_array) {
		   $plugin_array['frombutton_button'] = plugins_url('js/frombutton_button.js', __FILE__);
		   return $plugin_array;
		}
		function frombutton_refresh_mce($ver) {
			$ver += 3;
			return $ver;
		} 
		add_filter('tiny_mce_version', 'frombutton_refresh_mce'); 
	}
	
	/***************************************************************
	@
	@	From Button Buy Shortcode 
	@
	/**************************************************************/ 
	public function FromButton_BuyShortcode($atts){  
		if(is_single() OR is_page()){
			$FromButton = $this->FromButton_Get();
			// echo '<pre>';
				// print_r($FromButton);
			// echo '</pre>'; 
			
			$FromButton_GetAffiliate = $this->FromButton_GetAffiliate();
			// echo '<pre>';
				// print_r($FromButton_GetAffiliate);
			// echo '</pre>'; 
			
			$HTML_FromButton_GetAffiliate = '';
			while(list($index, $value) = each($FromButton_GetAffiliate)){   
				$HTML_FromButton_GetAffiliate .= '<div id="affiliate_'.$index.'" class="affiliate">	
					
					
					 
					
					
					
					<div class="buy_now button-style-'.$FromButton['frombutton_from_style'].'" style="background-color:'.$FromButton['frombutton_from_background'].'; width:'.$FromButton['frombutton_from_size'].'px; height:'.$FromButton['frombutton_from_height'].'px">
						<a style="color:'.$FromButton['frombutton_from_color'].'" target="_blank" href="'.$FromButton_GetAffiliate[$index]['frombutton_affiliate_link'].'">
							'.$FromButton_GetAffiliate[$index]['frombutton_affiliate_text'].'
						</a>
					</div> 
					<div style="background:url('.$FromButton_GetAffiliate[$index]['frombutton_affiliate_icon'].') no-repeat center;" class="frombutton_icon"></div>
					<div class="frombutton_price">
						<span class="price_me">$ '.$FromButton_GetAffiliate[$index]['frombutton_affiliate_price'].'</span> 
					</div>
				</div>';  
			}    
			
			$price = get_post_meta(get_the_ID(), 'frombutton_price'); 
			$h2_background = get_the_post_thumbnail(get_the_ID(), array(32,32));  
			$HTML_FromButton_Buy = ' 
				<div id="frombutton_buy">  
					<div class="buy_now button-style-'.$FromButton['frombutton_from_style'].'" style="background-color:'.$FromButton['frombutton_from_background'].'; width:'.$FromButton['frombutton_from_size'].'px; height:'.$FromButton['frombutton_from_height'].'px">
						<span style="font-size:'.$FromButton['frombutton_font_size'].'px" class="buy_now_open" id="buy_now_toggle">'.$FromButton['frombutton_from_text'].' $ '.$price[0].'</span> 
					</div> 
					<div class="buy_now_clear"></div>
					<div class="frombutton_buy_content"> 
						<div class="affiliate">
							<div class="affiliate_content">
								<h2>'.$h2_background.'<span>'.__('Shop for', 'FromButton').'</span>'.get_the_title().'</h2>
								'.$HTML_FromButton_GetAffiliate.'
							</div> 
						</div>
					</div> 
				</div>
			'; 
			return($HTML_FromButton_Buy);
		} 
	}
	
	/***************************************************************
	@
	@	From Button BackInit
	@
	/**************************************************************/ 
	public function FromButton_Popup(){   
		?>
		<div class="popup">
			<div class="content">
				<img class="x" id="x" />
					<fieldset id="FromButton_Popup">
						<legend><?php the_title();?></legend> 
						<?php    
						if(!is_user_logged_in()){
							?>
							<p>To submit a Review, please 
							<a href="<?php echo wp_login_url(get_permalink()); ?>" title="Login">Login to view </a> !</p>  
							<?php
						}else{ 
							$this->FromButton_Review(); 
						} 
						?> 
						<div class="FromButton64">  
							<span><?php echo $this->plugin_name.'&nbsp;'.$this->plugin_version;?></span>
						</div>
					</fieldset>  
			</div>
		</div>  
		<?php		
	} 
	
	/***************************************************************
	@
	@	FromButton Survey Update
	@
	/**************************************************************/
	public function FromButton_SurveyCheck($product_id){  
		global $current_user;
		get_currentuserinfo();   
		global $wpdb; 
		$table_name = $wpdb->prefix . 'frombutton';  
		$sql = 'select * from '.$table_name.' where user_id = '.$current_user->ID.' AND post_id = '.$product_id.' ';  
		$wpdb->query($sql);
		$data =	$wpdb->get_results($sql);  
		if(count($data)>0){ 
			foreach($data as $tmp_data ){
				$review_date = $tmp_data->review_date;
			} 
			return($review_date);
		} 
	} 
	 
	/***************************************************************
	@
	@	From Button Set Review 
	@
	/**************************************************************/ 
	public function FromButton_SetReview(){ 
		if(isset($_POST['FrombuttonSurveyHidden']) AND $_POST['FrombuttonSurveyHidden']=='bassem.rabia@hotmail.com'){
			global $wpdb; 
			$table_name = $wpdb->prefix.'frombutton';  
			$GetReview = $this->FromButton_GetReview(); 
			$current_user = wp_get_current_user();
			$review = '';
			while(list($index, $value) = each($GetReview)){ 
				// echo $value.' ==> '.$_POST['frombutton_'.$value['frombutton_review_name']];
				$review .= $_POST['frombutton_'.$value['frombutton_review_name']]; 
				$review .= ($index<count($GetReview)-1)?'-':''; 
			}	 
			$sql = 'insert into '.$table_name.'(
				post_id, 
				user_id, 
				review, 
				review_date
			) 
			values(
				"'.get_the_ID().'", 
				"'.$current_user->ID.'",
				"'.$review.'",
				"'.date('m.d.Y H:i:s').'"
			)'; 		
			$data =	$wpdb->get_results($sql); 
			?>
			<script>
				setTimeout(function() {
				window.location.href = '<?php get_permalink();?>';
			}, 2000);
			</script>
			<?php		
		} 
	}  
	
	/***************************************************************
	@
	@	FromButton Review
	@
	/**************************************************************/
	public function FromButton_Review(){    
		$SurveyCheck = $this->FromButton_SurveyCheck(get_the_ID());
		?>
			<div id="Frombutton_my_review"> 
			<?php 
			if($SurveyCheck>0){
				?> 
				<h2><?php _e( 'You have already reviewed this product ', 'FromButton' );?><?php _e( 'at ', 'FromButton' );?><?php echo $SurveyCheck;?></h2>  
				<?php 
			}else{  
				?>
				<form OnClick="return FrombuttonSurvey()" method="POST" id="frombutton_reviews_survey_form" action="">
				<?php
				$GetReview = $this->FromButton_GetReview();  
				while(list($index, $value) = each($GetReview)){ 
					?> 
						<div class="frombutton_my_review">
							<span class="value">
								<input data-required="true" readonly="readonly" placeholder="<?php echo $value['frombutton_review_name'];?>" type="text" id="frombutton_<?php echo $value['frombutton_review_name']?>_value" name="frombutton_<?php echo $value['frombutton_review_name']?>" /> 
							</span>
							<span class="name"> 
								<input class="frombutton_reviews_input_range" type="range" id="frombutton_<?php echo $value['frombutton_review_name']?>" name="frombutton_<?php echo $value['frombutton_review_name']?>" value="0" min="0" max="10" />  
							</span>
						</div>  
					<?php 
				}
				?>
					<input type="submit" class="write-a-review-submit" OnClick="FrombuttonSurvey()" id="write-a-review-submit" value="Save" /> 
					<input type="hidden" name="FrombuttonSurveyHidden" value="bassem.rabia@hotmail.com" /> 
				</form> 
				<?php
			} 
			?>
			</div>   
		<?php
	}
	
	/***************************************************************
	@
	@	From Button BackInit
	@
	/**************************************************************/ 
	public function FromButton_BackInit(){   
		wp_register_style('FromButton-admin-style', plugins_url('css/FromButton-admin.css', __FILE__));
		wp_enqueue_style('FromButton-admin-style');  
		wp_register_script('FromButton-admin-js', plugins_url('js/FromButton-admin.js', __FILE__));
		wp_enqueue_script('FromButton-admin-js');  
		wp_enqueue_style('farbtastic');
		wp_enqueue_script('farbtastic');
		// wp_register_script('FromButton-upload-js', plugins_url('js/frombutton_upload.js', __FILE__));
		// wp_enqueue_script('FromButton-upload-js'); 		 		
	} 
	
	/***************************************************************
	@
	@	From Button Get Affiliate
	@
	/**************************************************************/ 
	public function FromButton_GetAffiliate(){  
		$my_affiliate = array();
		for($i=0;$i<5;$i++){   
			$frombutton_affiliate_name = get_post_meta(get_the_ID(), 'frombutton_affiliate_name_'.$i);   
			$frombutton_affiliate_icon = get_post_meta(get_the_ID(), 'frombutton_affiliate_icon_'.$i);   
			$frombutton_affiliate_text = get_post_meta(get_the_ID(), 'frombutton_affiliate_text_'.$i);   
			$frombutton_affiliate_link = get_post_meta(get_the_ID(), 'frombutton_affiliate_link_'.$i);   
			$frombutton_affiliate_price = get_post_meta(get_the_ID(), 'frombutton_affiliate_price_'.$i); 
			if(strlen($frombutton_affiliate_name[0])>0){  
				$my_affiliate[$i]['frombutton_affiliate_name']  = $frombutton_affiliate_name[0];
				$my_affiliate[$i]['frombutton_affiliate_icon']  = $frombutton_affiliate_icon[0];
				$my_affiliate[$i]['frombutton_affiliate_text']  = $frombutton_affiliate_text[0];
				$my_affiliate[$i]['frombutton_affiliate_link'] 	= $frombutton_affiliate_link[0];
				$my_affiliate[$i]['frombutton_affiliate_price'] = $frombutton_affiliate_price[0]; 
			} 
		} 
		return($my_affiliate); 	
	}
	
	/***************************************************************
	@
	@	From Button Get Review
	@
	/**************************************************************/ 
	public function FromButton_GetReview(){ 
		$my_review = array();
		for($i=0;$i<5;$i++){  
			$frombutton_review_name = get_post_meta(get_the_ID(), 'frombutton_review_name_'.$i);   
			$frombutton_review_value = get_post_meta(get_the_ID(), 'frombutton_review_value_'.$i);   
			$frombutton_review_color = get_post_meta(get_the_ID(), 'frombutton_review_color_'.$i); 
			if(strlen($frombutton_review_name[0])>0){  
				$my_review[$i]['frombutton_review_name']  = $frombutton_review_name[0];
				$my_review[$i]['frombutton_review_color'] = $frombutton_review_color[0];
				$my_review[$i]['frombutton_review_value'] = $frombutton_review_value[0]; 
			} 
		} 
		return($my_review); 	
	}
	
	/***************************************************************
	@
	@	From Button Count Review 
	@
	/**************************************************************/ 
	public function FromButton_CReview(){
		global $wpdb; 
		$table_name = $wpdb->prefix.'frombutton'; 
		$sql = 'select * from '.$table_name.' where post_id = '.get_the_ID().''; 
		$data =	$wpdb->get_results($sql);  
		return(count($data));
	}	
	
	/***************************************************************
	@
	@	From Button Get Sum 
	@
	/**************************************************************/ 
	public function FromButton_SReview(){
		global $wpdb; 
		$table_name = $wpdb->prefix.'frombutton'; 
		$sql = 'select * from '.$table_name.' where post_id = '.get_the_ID().''; 
		$data =	$wpdb->get_results($sql);  
		$review = '';
		foreach($data as $myData){ 
			$find = array('-', ';');
			$replace = array('*', '*');   
			$review .= str_replace($find, $replace, $myData->review); 
		}   
		return(array_sum(explode('*', $review)));
	}	
	
	/***************************************************************
	@
	@	From Button Count Review 
	@
	/**************************************************************/ 
	public function FromButton_GetSQLReview(){
		global $wpdb; 
		$table_name = $wpdb->prefix.'frombutton';  
		$sql = 'select * from '.$table_name.' where post_id = '.get_the_ID().' AND confirmed = 0'; 
		$data =	$wpdb->get_results($sql);  
		return($data);
	}  
	
	/***************************************************************
	@
	@	From Button Widgets 
	@
	/**************************************************************/ 
	public function FromButton_Shortcode($atts){  
		if(is_single() OR is_page()){
			switch($atts[0]){
				case 'user':
					$this->FromButton_SetReview();
					$FromButton_GetReview = $this->FromButton_GetReview();  
					if($this->FromButton_CReview()>0){
						$FromButton_SC = $this->FromButton_SReview()/($this->FromButton_CReview()*count($this->FromButton_GetReview()));
					}else{
						$FromButton_SC = 0;
					}  
					$FromButton_GetSQLReview = $this->FromButton_GetSQLReview(); 
					function ReviewIndex($table, $index){  
						$myTable = explode('-', $table);  
						return(str_replace(';', '', $myTable[$index]));
					}  
					$htmlUserReview = '';  
					while(list($index, $value) = each($FromButton_GetReview)){ 
						$SUserReview = 0;
						for($FromButton_CR=0;$FromButton_CR<$this->FromButton_CReview();$FromButton_CR++){ 
							$SUserReview = $SUserReview+ReviewIndex($FromButton_GetSQLReview[$FromButton_CR]->review, $index);
						} 
						$width = $value['frombutton_review_value']*16; 
						$CReview = ($this->FromButton_CReview()>0)?$this->FromButton_CReview():1; 
						$htmlUserReview .= 
						'<div class="review">
							<span class="myReview">'.$value['frombutton_review_name'].' </span>
							<span class="myNote"><span style="background:'.$value['frombutton_review_color'].'; width:'.$width.'px" class="myColor">'.number_format($SUserReview/$CReview, 2).'</span> </span>
						</div>';   
					}	 	  
					$UserReview = '
						<div id="FromButtonUserReview"> 
							<div class="UserReview"> 
								'.$htmlUserReview.' 
							</div>
							<div class="UserReviewTotal"> 
								<span class="UserReviewTotalMoy">'.number_format($FromButton_SC, 2).'</span>
								<span class="UserReviewTotalAll">'.$this->FromButton_CReview().'</span> 
							</div> 
						</div><a href="javascript:void(0)" class="write-a-review"><b>Write a Reviews</b></a>';
					return($UserReview);				
				break;
				case 'critic':
					$FromButton_GetReview = $this->FromButton_GetReview();  
					$htmlCriticReview = '';
					while(list($index, $value) = each($FromButton_GetReview)){ 
						$width = $value['frombutton_review_value']*16;
						$htmlCriticReview .= 
						'<div class="review">
							<span class="myReview">'.$value['frombutton_review_name'].'</span>
							<span class="myNote"><span style="background:'.$value['frombutton_review_color'].'; width:'.$width.'px" class="myColor">'.$value['frombutton_review_value'].'</span> </span>
						</div>';  
					}	 	  
					$CriticReview = '
						<div id="FromButtonUserReview"> 
							<div class="UserReview"> 
								'.$htmlCriticReview.'
							</div> 
						</div>';
					return($CriticReview); 
				break;
				default:
				break;
			}  
		} 
	}
	
	/***************************************************************
	@
	@	From Button Meta 
	@
	/**************************************************************/ 
	public function FromButton_Init(){ 
		if(!is_admin()){
			wp_register_script('FromButton-jquery', plugins_url('js/jquery.js', __FILE__));
			wp_enqueue_script('FromButton-jquery' );   
			wp_register_script('FromButton-js', plugins_url('js/FromButton.js', __FILE__));
			wp_enqueue_script('FromButton-js' );
		}  
		// $this->FromButton_Install(); 
		$this->FromButton_Get(); 
		wp_register_style('FromButton-style', plugins_url('css/FromButton.css', __FILE__));
		wp_enqueue_style('FromButton-style' );
	}
	
	/***************************************************************
	@
	@	From Button Install  
	@
	/**************************************************************/ 
	public function FromButton_Install(){  
		global $wpdb; 
		$table_name = $wpdb->prefix.'frombutton'; 
		$sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			`user_id` int(11) NOT NULL,
			`review` text NOT NULL,
			`review_date` text NOT NULL,
			`confirmed` int(11) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;
		"; 
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			require_once(ABSPATH.'wp-admin/includes/upgrade.php' );
			dbDelta($sql); 
		} 
	}
	
	/***************************************************************
	@
	@	From Button Get 
	@
	/**************************************************************/ 
	public function FromButton_Get(){  
		$FromButton_plugin_options = get_option('FromButton_plugin_options');  
		if(strlen($FromButton_plugin_options['FromButton_serail'])<10){ 
			$this->FromButton_Set(); 
		}else{
			return($FromButton_plugin_options);
		}
	}
	
	/***************************************************************
	@
	@	From Button Default 
	@
	/**************************************************************/ 
	public function FromButton_Default(){  
		$FromButton_plugin_options = array(  
			'FromButton_name' 						=> $this->plugin_name, 
			'FromButton_version'					=> $this->plugin_version, 
			'FromButton_serail'						=> md5('demo'), 
			'frombutton_from_text' 					=> 'From',
			'frombutton_from_color' 			=> '#FFF',
			'frombutton_from_style' 			=> 0,
			'frombutton_from_size' 			=> 190,
			'frombutton_from_height' 			=> 30,
			'frombutton_font_size' 			=> 24,
			'frombutton_from_background' 			=> '#0091C1' 
		); 
		return($FromButton_plugin_options);   	
	}
	
	/***************************************************************
	@
	@	From Button Set 
	@
	/**************************************************************/ 
	public function FromButton_Set(){  
		add_option('FromButton_plugin_options', $this->FromButton_Default(), '', 'yes');   	
	}
	
	/***************************************************************
	@
	@	From Button Save Meta 
	@
	/**************************************************************/ 
	public function FromButton_Save($post_id){ 
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
			return $post_id;	
		if(!current_user_can('edit_post', $post_id))
			return $post_id;   
		for($i=0;$i<5;$i++){    
			
			/*
			*	Price
			*/
			update_post_meta($post_id, 'frombutton_price', esc_attr($_POST['frombutton_price']));  
			
			/*
			*	Review
			*/ 
			update_post_meta($post_id, 'frombutton_review_name_'.$i, esc_attr($_POST['frombutton_review_name_'.$i]));  
			update_post_meta($post_id, 'frombutton_review_value_'.$i, esc_attr($_POST['frombutton_review_value_'.$i]));  
			update_post_meta($post_id, 'frombutton_review_color_'.$i, esc_attr($_POST['frombutton_review_color_'.$i])); 
			
			/*
			*	Affiliate
			*/ 
			update_post_meta($post_id, 'frombutton_affiliate_name_'.$i, esc_attr($_POST['frombutton_affiliate_name_'.$i]));  
			update_post_meta($post_id, 'frombutton_affiliate_icon_'.$i, esc_attr($_POST['frombutton_affiliate_icon_'.$i]));  
			update_post_meta($post_id, 'frombutton_affiliate_text_'.$i, esc_attr($_POST['frombutton_affiliate_text_'.$i]));  
			update_post_meta($post_id, 'frombutton_affiliate_link_'.$i, esc_attr($_POST['frombutton_affiliate_link_'.$i]));  
			update_post_meta($post_id, 'frombutton_affiliate_price_'.$i, esc_attr($_POST['frombutton_affiliate_price_'.$i]));   		
		}     
		return $post_id; 
	}
	
	/***************************************************************
	@
	@	From Button Meta 
	@
	/**************************************************************/ 
	public function FromButton_Meta(){   
		/*
			*	Reviews
		*/
		$FromButton_GetReview = $this->FromButton_GetReview();  
		 
		
		/*
			*	Affiliate - Price 
		*/
		$FromButton_GetAffiliate = $this->FromButton_GetAffiliate();
		// echo '<pre>';
			// print_r($FromButton_GetReview);
		// echo '</pre>';
		
		add_meta_box(
			'addons_meta_box_affiliate_price_83542', 
			__('Affiliate - Price', 'FromButton'), 
			'addons_meta_box_affiliate_price_83542', 
			'post',
			'normal', 
			'high', 
			$FromButton_GetAffiliate
		);  
		function addons_meta_box_affiliate_price_83542($post, $FromButton_GetAffiliate){  
			?> 
			<div id="frombutton_custom_meta_post">  
				<div id="frombutton-tab">
					<div class="tab-nav"> 
						<ul>  
							<li class="active" > 
								<a name="manage_affiliates" href="javascript:void(0)"><?php _e('Manage Affiliates', 'FromButton');?></a>
							</li> 
							<li> 
								<a name="manage_price" href="javascript:void(0)"><?php _e('Manage Price', 'FromButton');?></a>
							</li> 
						</ul>   
					</div>
					<div class="tab-content"> 
						<div class="tab-panel" id="manage_affiliates" style="display:block">  
							<span class="line"></span>
							<p>  
								<input OnClick="Affiliate_add('<?php _e('Affiliate Name', 'FromButton');?>', '<?php _e('Affiliate Icon', 'FromButton');?>', '<?php _e('Affiliate Text', 'FromButton');?>', '<?php _e('Affiliate Link', 'FromButton');?>', '<?php _e('Affiliate Price', 'FromButton');?>')" class="button button-primary button-large"type="button" id="frombutton_add_reviews" value="<?php _e('Add Affiliate', 'FromButton');?>" />   
								<div class="FromButton_Clear"></div> 
								<?php 
									while(list($index, $value) = each($FromButton_GetAffiliate['args'])) { 
										// echo '<pre>';
											// print_r($value); 
										// echo '</pre>'; 
										?>  
										<div id="affiliate_<?php echo $index;?>" class="affiliate">	
											<span class="help"><?php _e('Affiliate Name', 'FromButton' ); ?></span>
											<input type="text" class="frombutton_input frombutton_default" name="frombutton_affiliate_name_<?php echo $index;?>" placeholder="<?php _e('Affiliate Name', 'FromButton');?>" value="<?php echo $value['frombutton_affiliate_name'];?>" />
											<span class="help"><?php _e('Affiliate Icon', 'FromButton' ); ?></span>
											<input placeholder="<?php _e('Affiliate Icon', 'FromButton');?>" type="text" class="frombutton_input frombutton_default frombutton_affiliate_icon" id="frombutton_affiliate_icon_<?php echo $index;?>" name="frombutton_affiliate_icon_<?php echo $index;?>" value="<?php echo $value['frombutton_affiliate_icon'];?>" />
											<span class="help"><?php _e('Affiliate Text', 'FromButton' ); ?></span>
											<input placeholder="<?php _e('Affiliate Text', 'FromButton');?>" type="text" class="frombutton_input frombutton_default frombutton_last" name="frombutton_affiliate_text_<?php echo $index;?>" value="<?php echo $value['frombutton_affiliate_text'];?>" />
											<span class="help"><?php _e('Affiliate Link', 'FromButton' ); ?></span>
											<input placeholder="<?php _e('Affiliate Link', 'FromButton');?>" type="text" class="frombutton_input frombutton_default" name="frombutton_affiliate_link_<?php echo $index;?>" value="<?php echo $value['frombutton_affiliate_link'];?>" />
											<span class="help"><?php _e('Affiliate Price', 'FromButton' ); ?></span>
											<input placeholder="<?php _e('Affiliate Price', 'FromButton');?>" type="text" class="frombutton_input frombutton_default" name="frombutton_affiliate_price_<?php echo $index;?>" value="<?php echo $value['frombutton_affiliate_price'];?>" />
											<a class="button button-primary button-large affiliate_delete" OnClick="affiliate_delete('<?php echo $index;?>')" href="javascript:void(0)"><?php _e('Delete', 'FromButton');?></a>
										</div> 
										<?php 
									}  
								?>
								<div id="frombutton_html_affiliate"></div> 
								<input OnClick="affiliate_delete('ALL')" class="button" type="button" id="affiliate_delete_all" value="<?php _e('Delete ALL Affiliate', 'FromButton');?>" />   
								
								<div class="aarproduct_color_picker"> 
									<div id="color_picker_color1"></div>
								</div>
							</p>
						</div> 
						<div class="tab-panel" id="manage_price">  
							<span class="line"></span>
							<p> 
								<span class="help"><?php _e('Please add Price:', 'FromButton');?></span> 
								<?php
								$frombutton_price = get_post_meta(get_the_ID(), 'frombutton_price');  
								?>   
								<input style="width:99%" type="text" id="frombutton_price" name="frombutton_price" value="<?php echo $frombutton_price[0];?> " />   
							</p>
						</div>  
					</div>
				</div> 
			</div> 
			<?php
		}	 
	}  	
	
	/***************************************************************
	@
	@	From Button Manage
	@
	/**************************************************************/ 
	public function FromButton_Manage(){   
		add_menu_page(__('From Button', 'FromButton'),__('From Button', 'FromButton'), 'manage_options', 'frombutton_config', array($this , 'FromButton_Config'), plugins_url('frombutton/classes/images/FromButton16.png' )); 
		add_submenu_page('frombutton_config', __('Management', 'FromButton'), __('Management', 'FromButton'), 'manage_options', 'frombutton_management', array($this ,'FromButton_Config') );
		 
		// add_menu_page('From Button Reviews', __('Management '.$count.'', 'FromButton'), 'manage_options', 'frombutton_config', 
		// array(&$this,'FromButton_Config')
		// , plugins_url('frombutton/classes/images/FromButton16.png' ), 6);  
		
		// add_menu_page(__('From Button', 'FromButton'), __('From Button', 'FromButton'), "edit_posts",
        // 'frombutton_config', array(&$this,'FromButton_Config'), null, 100);
		// add_submenu_page('options-general.php', __('From Button', 'FromButton') , __('From Button', 'FromButton') , 'manage_options', __FILE__, array(&$this,'FromButton_Config'));  
	}  
	
	/***************************************************************
	@
	@	From Button action  
	@
	/**************************************************************/
	public function FromButton_MCount(){   
		global $wpdb;  
		$table_name = $wpdb->prefix.'frombutton'; 
		$sql = 'select * from '.$table_name.' where confirmed = 0 '; 
		$data =	$wpdb->get_results($sql);
		return(count($data));  
	} 
	
	/***************************************************************
	@
	@	From Button Menu  
	@
	/**************************************************************/
	public function FromButton_menu(){   
		$count = ''; 
		if($this->FromButton_MCount()>0){
			$count = '<span class="update-plugins count-2"><span class="plugin-count"> '.$this->FromButton_MCount().' </span></span>';
		} 
		 
		
		add_menu_page('From Button Reviews', __('Management '.$count.'', 'FromButton'), 'manage_options', 'frombutton_config', 
		array(&$this,'FromButton_Config')
		, plugins_url('frombutton/classes/images/FromButton16.png' ), 6);  
	}  
	
	/***************************************************************
	@
	@	From Button Redirect  
	@
	/**************************************************************/
	public function FromButton_Redirect(){ 
		?>
		<script>
			setTimeout(function() {
			window.location.href = '<?php bloginfo('url');?>/wp-admin/admin.php?page=FromButtonReviews';
		}, 2000);
		</script>
		<?php
	}
	
	/***************************************************************
	@
	@	From Button action  
	@
	/**************************************************************/
	public function FromButton_Action(){   
		global $wpdb; 
		$table_name = $wpdb->prefix.'frombutton'; 
		if(isset($_GET['page']) AND $_GET['page']=='FromButtonReviews'){ 
			if(isset($_GET['action']) AND $_GET['action']=='confirm'){  
				echo $sql = 'update '.$table_name.' set confirmed = 1 where id = '.$_GET['ID'].' '; 
				$data =	$wpdb->get_results($sql); 
				$this->FromButton_Redirect();
			}
			if(isset($_GET['action']) AND $_GET['action']=='unconfirm'){  
				echo $sql = 'update '.$table_name.' set confirmed = 0 where id = '.$_GET['ID'].' '; 
				$data =	$wpdb->get_results($sql); 
				$this->FromButton_Redirect();
			}
			if(isset($_GET['action']) AND $_GET['action']=='delete'){  
				echo $sql = 'delete from '.$table_name.' where id = '.$_GET['ID'].' '; 
				$data =	$wpdb->get_results($sql);
				$this->FromButton_Redirect();				
			}  
		}  
	}  

	/***************************************************************
	@
	@	From Button Serial
	@
	/**************************************************************/ 
	public function FromButton_serial(){ 
		$FromButton = $this->FromButton_Get(); 
		if($FromButton['FromButton_serail']!=md5('demo')){
			return(true);
		}else{
			return(false);
		} 
	}
	
	/***************************************************************
	@
	@	From Button Config Page
	@
	/**************************************************************/ 
	public function FromButton_Config(){   
		// echo '<pre>';
			$FromButton = $this->FromButton_Get();
			// print_r($FromButton);
		// echo '</pre>';  
		?>	 
		<div class="wrap columns-2">
			<div id="FromButton-icon" class="icon32"></div>  
			<h2><?php echo $this->plugin_name.' '.$this->plugin_version; ?></h2> 
			<?php $this->FromButton_Update();?>
			
			<div id="frombutton_config">
				<div class="frombutton_config_right">
					<div id="postbox-container-1" class="postbox-container">
						  
						<div class="postbox">
							<h3><span><?php _e('User Guide', 'FromButton'); ?></span></h3>
							<div class="inside"> 
								<ol>
								<li>
									<a target="_blank" href="<?php bloginfo('url');?>/wp-content/plugins/frombutton/classes/docs/user guide.pdf"><?php _e('User Guide', 'FromButton'); ?></a>
								</li> 
                                <li>
									<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="QFQHQ2GR2W3H8">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
								</li>   
								</ol>
							</div>
						</div>
						
						<div class="overlay-bg">
							<div class="overlay-content"> 
								<?php 
								if($this->FromButton_serial()){
									?>
									<h2 class="best_choice"> 
										<a href="<?php echo bloginfo('url').'/wp-admin/edit.php?post_type=adonide_faq&page=html-faq-page/core/html-faq-page-postType.php&html-faq-page-versionPRO='.md5('OK').''?>">
											<?php _e('Vous avez un problème?', 'html-faq-page'); ?>
										</a>
										<br/>
										<p>bassem.rabia[at]hotmail.co.uk</p>
										<p><img src="<?php echo plugins_url('images/rabia-bassem.jpg', __FILE__);?>" /></p>
									</h2> 
									<button class="close-btn button"><?php _e('Annuler', 'html-faq-page'); ?></button>
									<?php
								}else{
									$items = array(
										__('Quisque non arcu dui. Fusce et turpis justo. ', 'FromButton'),
										__('Suspendisse feugiat molestie volutpat.', 'FromButton'),
										__('Vestibulum dapibus, urna a varius laoreet', 'FromButton'),
										__('Ut aliquam fermentum pharetra. Ut a sem mattis. ', 'FromButton'),
										__('Aliquam tempus nec massa ut porttitor. ', 'FromButton'),
										__('Nullam semper purus a mauris vulputate, non mollis diam laoreet. ', 'FromButton'),
										__('Donec ac fermentum tortor.', 'FromButton')
										); 
									?> 
									<table class="widefat" style="width:100%; margin:auto auto 20px;">
										<thead>
										<tr>
											<th><?php echo $this->plugin_name;?> </th>
											<th>Demo</th>
											<th>PRO</th>
										</tr>
										</thead> 
										<tbody> 
											<?php
												foreach ($items as $index => $val) {
													?>
													<tr>
													<td>
														<?php echo $val;?>
													</td>
													<td>
														<?php
															echo ($index<3)?'<img src="'.plugins_url('images/check.png', __FILE__).'" />':'<img src="'.plugins_url('images/uncheck.png', __FILE__).'" />';
														?> 
													</td>
													<td>
														<img src="<?php echo plugins_url('images/uncheck.png', __FILE__);?>" />
													</td>
													</tr> 
													<?php
												}
											?> 
										</tbody>
									</table>  
									<h2 class="best_choice"> 
										<a href="javascript:void(0)">
											<?php _e('Skip to the PRO version', 'FromButton'); ?>
										</a>
									</h2>
									<p>  
										<?php _e("Note: An email will be sent in your name to the development team.", 'FromButton'); ?> 
									</p>
									<button class="close-btn button"><?php _e('Annuler', 'FromButton'); ?></button> 
								<?php
								}
								?>
							</div>
						</div> 
					</div>
				</div>
				<div class="frombutton_config_left">
					<!-- From Button -->
						<?php
						if(isset($_GET['page']) AND $_GET['page']=='frombutton_config'){
							?>
							<div id="postbox-container-2" class="postbox-container"> 
							<?php
								switch($_GET['page']){
									case 'aarProductReviews': 
									case_aarProductReviews();
									?> 
									<div id="aarproducts-tab">
												<div class="stuffbox">
													<h3><label><?php _e('User Review', 'FromButton'); ?></label></h3> 
													<div class="inside" style="overflow: hidden;"> 
														<div style="border:0; padding: 0;" class="tab-content"> 
														<div class="tab-panel" style="display:block" id="confirmed_user_reviews">  
															<?php   
																get_user_reviews('ALL', '0');
															?>
														</div>  
														</div> 
													</div> 
												</div>  
												<?php
											break;
											default: 
												?> 
												<div class="stuffbox">
													<h3><label><?php _e('Design Option', 'FromButton' ); ?></label></h3> 
													<div class="inside" style="overflow: hidden;">  
														<form method="POST" action="">  
															<div class="review">	
																<span class="help"><?php _e('Text', 'FromButton' ); ?></span>
																<input placeholder="<?php _e('From Button Text', 'FromButton' ); ?>" type="text" class="frombutton_input" name="frombutton_from_text" value="<?php echo $FromButton['frombutton_from_text'];?>" />  
																<span class="help"><?php _e('Style', 'FromButton' ); ?></span>
																<select class="frombutton_input frombutton_select" name="frombutton_from_style">
																	<option <?php echo ($FromButton['frombutton_from_style']==0)?"selected='selected'":"";?>value="0" /><?php _e('Default', 'FromButton' ); ?></option>
																	<option <?php echo ($FromButton['frombutton_from_style']==1)?"selected='selected'":"";?> value="1" /><?php _e('Style 2', 'FromButton' ); ?></option>
																	<option <?php echo ($FromButton['frombutton_from_style']==2)?"selected='selected'":"";?> value="2" /><?php _e('Style 3', 'FromButton' ); ?></option>
																	<option <?php echo ($FromButton['frombutton_from_style']==3)?"selected='selected'":"";?> value="3" /><?php _e('Style 4', 'FromButton' ); ?></option>
																	<option <?php echo ($FromButton['frombutton_from_style']==4)?"selected='selected'":"";?> value="4" /><?php _e('Square Style', 'FromButton' ); ?></option>
																</select> 
																<span class="help"><?php _e('Text Color', 'FromButton' ); ?></span>
																<input placeholder="<?php _e('From Button Text Color', 'FromButton' ); ?>" OnClick="frombutton_farbtastic('98')" type="text" id="frombutton_farbtastic_98" class="frombutton_input frombutton_default frombutton_farbtastic" name="frombutton_from_color" value="<?php echo $FromButton['frombutton_from_color'];?>" />
																<span class="help"><?php _e('Text background', 'FromButton' ); ?></span>
																<input placeholder="<?php _e('From Button Text Background', 'FromButton' ); ?>" OnClick="frombutton_farbtastic('99')" type="text" id="frombutton_farbtastic_99" class="frombutton_input frombutton_default frombutton_farbtastic frombutton_last" name="frombutton_from_background" value="<?php echo $FromButton['frombutton_from_background'];?>" /> 
																<span class="help"><?php _e('Button width (px)', 'FromButton' ); ?></span>
																<input placeholder="<?php _e('From Button size', 'FromButton' ); ?>" type="text" class="frombutton_input frombutton_default" name="frombutton_from_size" value="<?php echo $FromButton['frombutton_from_size'];?>" />
																<span class="help"><?php _e('Button height (px)', 'FromButton' ); ?></span>
																<input placeholder="<?php _e('Height (px)', 'FromButton' ); ?>" type="text" class="frombutton_input frombutton_default" name="frombutton_from_height" value="<?php echo $FromButton['frombutton_from_height'];?>" />
																<span class="help"><?php _e('Font size', 'FromButton' ); ?></span>
																<input placeholder="<?php _e('Font size (px)', 'FromButton' ); ?>" type="text" class="frombutton_input frombutton_default" name="frombutton_font_size" value="<?php echo $FromButton['frombutton_font_size'];?>" />  
																
																<br/>  
																<div class="aarproduct_color_picker"> 
																	<div id="color_picker_color1"></div>
																</div> 
																<div class="FromButton_Clear"></div>
																<p class="frombutton_submit"> 
																	<input type="hidden" name="FromButton_noncename" id="FromButton_noncename" value="<?php echo wp_create_nonce('FromButton_noncename');?>" />
																	<input id="frombutton_submit" class="button button-primary" type="submit" value="Save Changes" name="submit">
																</p> 
															</div> 
															<div class="FromButton_Clear" style="clear: both;height: 10px;overflow: auto;"></div> 
														</form>	
													</div> 
												</div> 
												<?php
											break;
										}
										?> 
									</div> 
						</div>
						<?php
						}elseif(isset($_GET['page']) AND $_GET['page']=='frombutton_management'){ 
							?>
							<div id="postbox-container-2" class="postbox-container">  
								<table class="widefat" style="width:100%; margin:auto auto 20px;">
									<thead>
									<tr>   
										<th style="width:50%"><?php _e('Post', 'FromButton'); ?></th> 
										<th style="width:35%"><?php _e('Affiliates', 'FromButton'); ?></th> 
										<th><?php _e('Action', 'FromButton'); ?></th> 
									</tr>
									</thead> 
									<tbody>
									<?php
									$this->FromButton_Action();  
									query_posts('showposts=10');
									if(have_posts()){
									  while(have_posts()){
										the_post(); 
										
										$frombutton_affiliate_name = get_post_meta(get_the_ID(), 'frombutton_affiliate_name_0'); 
										if(strlen($frombutton_affiliate_name[0])>0){  
											?>
											<tr>  
												<td> 
													<a target="_blank" href="<?php bloginfo('url');?>/wp-admin/post.php?post=<?php echo get_the_ID();?>&action=edit">   
														<?php
															echo get_the_title();
														?>
													</a>  
												</td> 
												<td>
													<?php  
													  echo $frombutton_affiliate_name[0]; 
													?>
												</td> 
												<td>  
													<a href="<?php bloginfo('url');?>/wp-admin/post.php?post=<?php echo get_the_ID();?>&action=edit">
														<input id="submit" class="button" type="submit" value="Edit" name="submit"> 
													</a>  
												</td> 
											</tr> 
											<?php	   
										}  
									  }
									}
									?>
									</tbody>
								</table> 
							</div>
						<?php
						}
						?>
					<!-- From Button -->
				</div>
			</div> 
		<?php 
	}  
	
	/***************************************************************
	@
	@	From Button Update Config Page
	@
	/**************************************************************/ 
	public function FromButton_Update(){
		if(wp_verify_nonce($_POST['FromButton_noncename'], 'FromButton_noncename')){  
			$FromButton = $this->FromButton_Default(); 
			while(list($key, $value) = each($FromButton)){ 
				$val = ($_POST[$key]=='')?$FromButton[$key]:$_POST[$key];  
				$FromButton_plugin_options[$key] = $val; 
			}
			// echo '<pre>';
				// print_r($FromButton_plugin_options);
			// echo '</pre>';
			update_option('FromButton_plugin_options', $FromButton_plugin_options); 
			?>  
			<div class="stuffbox" id="frombutton_messgae">
				<h3><span>
					<?php echo $this->plugin_name; _e(' is Updated successfully', 'FromButton'); ?>
				</span></h3>
			</div>  
				<script>
					window.location = '<?php echo bloginfo('url').'/wp-admin/admin.php?page=frombutton_config';?>'
				</script>
			<?php 
		}  
	}  
}
?>