<?php
/*
Plugin Name: Payment Details
Description: Display Payment details on Receipt for Event Espresso 4
Version: 1.0
Author: Charly Dwyer
Website: https://askcharlyleetham.com/
*/

function acl_ee_paymentextras ( $atts, $content ) {
	$args = shortcode_atts ( 
		array	(
			'paymentid' => NULL,
			'class' => NULL
		), 
		$atts ) ;

		$found_cc_data = ee_show_billing_info_cleaned($subsection, $found_cc_data);
	
		return $found_cc_data;

}
add_shortcode ( 'acl_payment_extras', 'acl_ee_paymentextras' );