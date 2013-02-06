<?php

function css_js_metabox() {
		global $post;
		global $wpdb; 
		$css = get_post_meta( $post->ID, 'added-css', true );
		$javascript = get_post_meta( $post->ID, 'added-js', true );
?>
        <p><label for="css"><strong>Add CSS here:</strong><br />
			<textarea id="css" style="width:98%; height:100px;" name="css" type="text" ><?php if( $css ) { echo $css; } ?></textarea></label></p>
        <p><label for="js"><strong>Add JavaScript here:</strong><br />
			<textarea id="js" style="width:98%; height:100px;" name="js" type="text" ><?php if( $javascript ) { echo $javascript; } ?></textarea></label></p>
        
		<?php 
		
	}
