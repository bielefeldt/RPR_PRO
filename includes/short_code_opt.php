<?php

function short_code_opt() {
 			wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
    		?><div id="shortside">
			<p>Copy and pase the below shortcode into your post where you would like the form to be displayed.</p>
			<p><strong>[promo-form]</strong></p>
            <p>The form will include name, email address, and promotion code fields that the user will use to enter their infromation.</p>
		</div><?
			 
} // end short_code_opt
