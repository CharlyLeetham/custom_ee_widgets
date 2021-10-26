<?php
/*
Plugin Name: Alternative Event Espresso ShortCode
Description: A custom event list shortcode for Event Espresso 4
Version: 1.0
Author: Charly Dwyer
Website: https://askcharlyleetham.com/
*/

function acl_ee_shortcode( $atts,$content ) {
	extract(shortcode_atts(array(
		'title'         => '',
		'limit'         => 10,
		'css_class'     => '',
		'show_expired'  => false,
		'month'         => '',
		'category_slug' => '',
		'order_by'      => 'start_date',
		'sort'          => 'ASC',
		'show_title'    => true,
		'image_size'	=> 'none',
		'show_dates'	=> true,
		'show_desc'		=> false,
	), $atts));

	$category = $category_slug;

global $post;
// make sure there is some kinda post object
	if ( $post instanceof WP_Post ) {
		$before_widget = '';
		$before_title = '';
		$after_title = '';
		$after_widget = '';
		// but NOT an events archives page, cuz that would be like two event lists on the same page
		// let's use some of the event helper functions'
		// make separate vars out of attributes

		extract($args);

		ob_start();
		// Before widget (defined by themes).
		echo $before_widget;
		// Display the widget title if one was input (before and after defined by themes).
		if ( ! empty( $title )) {
			echo $before_title . $title . $after_title;
		}
		// start to build our where clause
		$where = array(
	//                  'Datetime.DTT_is_primary' => 1,
			'status' => array( 'IN', array( 'publish', 'sold_out' ) )
		);
		// add category
		if ( $category ) {
			$where['Term_Taxonomy.taxonomy'] = 'espresso_event_categories';
			$where['Term_Taxonomy.Term.slug'] = $category;
		}
		// if NOT expired then we want events that start today or in the future
		if ( ! $show_expired ) {
			$where['Datetime.DTT_EVT_end'] = array( '>=', EEM_Datetime::instance()->current_time_for_query( 'DTT_EVT_end' ) );
		}
		// allow $where to be filtered
		$where = apply_filters( 'FHEE__EEW_Upcoming_Events__widget__where', $where, $category, $show_expired );
		// run the query
		$events = EE_Registry::instance()->load_model( 'Event' )->get_all( array(
			$where,
			'limit' => $limit > 0 ? '0,' . $limit : '0,10',
			'order_by' => 'Datetime.DTT_EVT_start',
			'order' => 'ASC',
			'group_by' => 'EVT_ID'
		));

		if ( ! empty( $events )) {
			echo '<ul class="ee-upcoming-events-widget-ul">';
			foreach ( $events as $event ) {
				if ( $event instanceof EE_Event && ( !is_single() || $post->ID != $event->ID() ) ) {
					//printr( $event, '$event  <br /><span style="font-size:10px;font-weight:normal;">' . __FILE__ . '<br />line no: ' . __LINE__ . '</span>', 'auto' );
					echo '<li id="ee-upcoming-events-widget-li-' . $event->ID() . '" class="ee-upcoming-events-widget-li">';
					// how big is the event name ?
					$name_length = strlen( $event->name() );
					switch( $name_length ) {
						case $name_length > 70 :
							$len_class =  ' three-line';
							break;
						case $name_length > 35 :
							$len_class =  ' two-line';
							break;
						default :
							$len_class =  ' one-line';
					}
					$event_url = apply_filters( 'FHEE_EEW_Upcoming_Events__widget__event_url', $event->get_permalink(), $event );
					echo '<h5 class="ee-upcoming-events-widget-title-h5"><a class="ee-widget-event-name-a' . $len_class . '" href="' . $event_url . '">' . $event->name() . '</a></h5>';
					if ( post_password_required( $event->ID() ) ) {
						$pswd_form = apply_filters( 'FHEE_EEW_Upcoming_Events__widget__password_form', get_the_password_form( $event->ID() ), $event );
						echo $pswd_form;
					} else {
						if ( has_post_thumbnail( $event->ID() ) && $image_size != 'none' ) {
							echo '<div class="ee-upcoming-events-widget-img-dv"><a class="ee-upcoming-events-widget-img" href="' . $event_url . '">' . get_the_post_thumbnail( $event->ID(), $image_size ) . '</a></div>';
						}
						$desc = $event->short_description( 25 );
						if ( $show_dates ) {
							$date_format = apply_filters( 'FHEE__espresso_event_date_range__date_format', get_option( 'date_format' ));
							$time_format = apply_filters( 'FHEE__espresso_event_date_range__time_format', get_option( 'time_format' ));
							$single_date_format = apply_filters( 'FHEE__espresso_event_date_range__single_date_format', get_option( 'date_format' ));
							$single_time_format = apply_filters( 'FHEE__espresso_event_date_range__single_time_format', get_option( 'time_format' ));
							if ( $date_range == TRUE ) {
								echo espresso_event_date_range( $date_format, $time_format, $single_date_format, $single_time_format, $event->ID() );
							}else{
								echo espresso_list_of_event_dates( $event->ID(), $date_format, $time_format, FALSE, NULL, TRUE, TRUE, $date_limit );
							}
						}
						if ( $show_desc && $desc ) {
							echo '<p style="margin-top: .5em">' . $desc . '</p>';
						}

						echo '<p class="showmore"><a href="'.$event_url.'">Details and Registration</a></p>';
					}
					echo '</li>';
				}
			}
			echo '</ul>';
		}
		// After widget (defined by themes).
		echo $after_widget;

		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

}
add_shortcode ( 'acl_ee_shortcode', 'acl_ee_shortcode' );
