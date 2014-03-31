<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Move Smilies
add_filter('smilies_src','helf_smilies_src', 1, 10);
function helf_smilies_src($img_src, $img, $siteurl) {
    if ( is_plugin_active('new-smileys/new-smileys.php') ) {
    
    }
    else {

        $img = rtrim($img, "gif"); // In case...
        if ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 8' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE 7' ) || strpos( $_SERVER['HTTP_USER_AGENT'], 'Android' ) ) {
            $img = $img;
        }
        else {
            $img = rtrim($img, "png"); // Remove GIF
            $img .= 'svg';
        }
        return $siteurl.'/code/images/smilies/'.$img;
    }
}

// This is in a paren for my sanity....
if ( !is_plugin_active('new-smileys/new-smileys.php') ) {
    global $wpsmiliestrans;

	if ( !get_option( 'use_smilies' ) )
		return;
    
    if ( !isset( $wpsmiliestrans ) ) {
		$wpsmiliestrans = array(
		':mrgreen:' => 'icon_mrgreen.png',
		':neutral:' => 'icon_neutral.png',
	 // removing b/c new versions
        ':twisted:' => 'icon_evil.png',
		  ':arrow:' => 'icon_arrow.png',
		  ':shock:' => 'icon_eek.png',
		  ':smile:' => 'icon_smile.png',
		    ':???:' => 'icon_confused.png',
		   ':cool:' => 'icon_cool.png',
		   ':evil:' => 'icon_evil.png',
		   ':grin:' => 'icon_biggrin.png',
		   ':idea:' => 'icon_idea.png',
		   ':oops:' => 'icon_redface.png',
		   ':razz:' => 'icon_razz.png',
		   ':roll:' => 'icon_rolleyes.png',
		   ':wink:' => 'icon_wink.png',
		    ':cry:' => 'icon_cry.png',
		    ':eek:' => 'icon_surprised.png',
		    ':lol:' => 'icon_lol.png',
		    ':mad:' => 'icon_mad.png',
		    ':sad:' => 'icon_sad.png',
		      '8-)' => 'icon_cool.png',
		      '8-O' => 'icon_eek.png',
		      ':-(' => 'icon_sad.png',
		      ':-)' => 'icon_smile.png',
		      ':-?' => 'icon_confused.png',
		      ':-D' => 'icon_biggrin.png',
		      ':-P' => 'icon_razz.png',
		      ':-o' => 'icon_surprised.png',
		      ':-x' => 'icon_mad.png',
		      ':-|' => 'icon_neutral.png',
		      ';-)' => 'icon_wink.png',
		// This one transformation breaks regular text with frequency.
		//     '8)' => 'icon_cool.png',
		       '8O' => 'icon_eek.png',
		       ':(' => 'icon_sad.png',
		       ':)' => 'icon_smile.png',
		       ':?' => 'icon_confused.png',
		       ':D' => 'icon_biggrin.png',
		       ':P' => 'icon_razz.png',
		       ':o' => 'icon_surprised.png',
		       ':x' => 'icon_mad.png',
		       ':|' => 'icon_neutral.png',
		       ';)' => 'icon_wink.png',
		      ':!:' => 'icon_exclaim.png',
		      ':?:' => 'icon_question.png',
        // New for me
              '>:(' => 'icon_mad.png',
              'o_O' => 'icon_surprised.png',
              'O_o' => 'icon_eek.png',
			  '^^‘' => 'icon_redface.png',
              ':‘(' => 'icon_cry.png',
              ':’(' => 'icon_cry.png',
   ':whiterussian:' => 'icon_whiterussian.png',
              '|_|' => 'icon_whiterussian.png',
               ':/' => 'icon_uneasy.png',
              ':-/' => 'icon_uneasy.png',
      ':developer:' => 'icon_developer.png',
        ':burrito:' => 'icon_burrito.png',
        ':martini:' => 'icon_martini.png',
		      '>-I' => 'icon_martini.png',
          ':blush:' => 'icon_redface.png',
          ':heart:' => 'icon_heart.png',
            //'&lt;3' => 'icon_heart.png',
           ':bear:' => 'icon_bear.png',
           ':star:' => 'icon_star.png',
		      '(w)' => 'icon_wordpress.png',
		      '(W)' => 'icon_wordpress.png',        
		);
    }
    
    if (count($wpsmiliestrans) == 0) {
		return;
	}
}