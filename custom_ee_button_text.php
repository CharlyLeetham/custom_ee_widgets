<?php
/*
Plugin Name: EE change button text
Description: A custom plugin to change button text for Event Espresso 4
Version: 1.2
Author: Charly Dwyer
Website: https://askcharlyleetham.com/
*/

function ee_view_details_button() {
 return 'View Event and Register';
}

add_filter ('FHEE__EE_Ticket_Selector__display_view_details_btn__btn_text', 'ee_view_details_button');


function my_custom_no_access_message( $content, $tkt, $ticket_price, $tkt_status ) {
  $url = wp_login_url( get_permalink() );
  $content = '<strong>'.$tkt->name() . '</strong> becomes available if you log in to your account. ';
  $content .= 'You can <a href="'. $url .'" title="Log in">log in here</a>. '.$tkt_status;
  return $content;
}

add_filter( 'FHEE__EED_WP_Users_Ticket_Selector__maybe_restrict_ticket_option_by_cap__no_access_msg', 'my_custom_no_access_message', 10, 4 );